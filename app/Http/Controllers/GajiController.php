<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GajiController extends Controller
{
    /**
     * FUNGSI UNTUK ADMIN & KABID: Melihat Daftar Gaji
     */
    public function index()
    {
        $gajis = Gaji::with('karyawan')->latest()->get();
        $karyawans = Karyawan::all();
        
        return view('admin.gaji', compact('gajis', 'karyawans'));
    }

    /**
     * FUNGSI STORE (ADMIN): Menyimpan gaji dengan status 'Pending'
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan'       => 'required',
            'tahun'       => 'required|numeric',
            'gaji_pokok'  => 'required|numeric',
            'tunjangan'   => 'nullable|numeric',
            'potongan'    => 'nullable|numeric',
        ]);

        $cekGaji = Gaji::where('karyawan_id', $request->karyawan_id)
                       ->where('bulan', $request->bulan)
                       ->where('tahun', $request->tahun)
                       ->first();

        if ($cekGaji) {
            return redirect()->back()->with('error', 'Gaji periode tersebut sudah pernah diinput!');
        }

        $total = ($request->gaji_pokok + ($request->tunjangan ?? 0)) - ($request->potongan ?? 0);

        Gaji::create([
            'karyawan_id' => $request->karyawan_id,
            'bulan'       => $request->bulan,
            'tahun'       => $request->tahun,
            'gaji_pokok'  => $request->gaji_pokok,
            'tunjangan'   => $request->tunjangan ?? 0,
            'potongan'    => $request->potongan ?? 0,
            'total_gaji'  => $total,
            'status'      => 'Pending', // <--- Default status untuk menunggu ACC Kabid
        ]);

        return redirect()->back()->with('success', 'Data gaji berhasil dikirim untuk menunggu persetujuan Kabid!');
    }

    /**
     * FUNGSI ACC (KABID): Menyetujui Gaji
     */
    public function accGaji($id)
    {
        $gaji = Gaji::findOrFail($id);
        
        // Hanya ubah status jika masih Pending
        if ($gaji->status == 'Pending') {
            $gaji->update(['status' => 'Dibayar']);
            return redirect()->back()->with('success', 'Gaji berhasil disetujui (ACC) oleh Kabid!');
        }

        return redirect()->back()->with('error', 'Gaji sudah pernah diproses sebelumnya.');
    }

    /**
     * FUNGSI DESTROY: Menghapus data gaji
     */
    public function destroy($id)
    {
        $gaji = Gaji::findOrFail($id);
        $gaji->delete();

        return redirect()->back()->with('success', 'Data gaji berhasil dihapus.');
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Melihat Daftar Slip Gaji Sendiri
     */
    public function indexKaryawan()
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil data karyawan belum diatur.');
        }

        // Karyawan hanya bisa melihat gaji yang sudah 'Dibayar' (sudah di-ACC Kabid)
        $gajis = Gaji::where('karyawan_id', $profil->id)
                     ->where('status', 'Dibayar') 
                     ->latest()
                     ->get();
        
        return view('karyawan.gaji', compact('gajis', 'profil'));
    }

    /**
     * FUNGSI CETAK SLIP PDF
     */
    public function cetakPdf($id)
    {
        $gaji = Gaji::with('karyawan')->findOrFail($id);
        
        // Validasi: Slip hanya bisa dicetak jika sudah di-ACC (Dibayar)
        if ($gaji->status != 'Dibayar') {
            return redirect()->back()->with('error', 'Slip gaji belum bisa dicetak karena belum di-ACC Kabid.');
        }

        $pdf = Pdf::loadView('admin.slip_gaji_pdf', compact('gaji'))->setPaper('a5', 'landscape');
        return $pdf->download('Slip_Gaji_'.$gaji->karyawan->nama_karyawan.'_'.$gaji->bulan.'.pdf');
    }
}