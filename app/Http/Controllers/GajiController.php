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
     * FUNGSI UNTUK ADMIN: Melihat Daftar Gaji & Form Input
     */
    public function index()
    {
        // Pastikan hanya admin yang bisa akses index ini
        $gajis = Gaji::with('karyawan')->latest()->get();
        $karyawans = Karyawan::all();
        
        return view('admin.gaji', compact('gajis', 'karyawans'));
    }

    /**
     * FUNGSI UNTUK KABID: Monitoring & ACC Gaji
     */
    public function indexKabid()
    {
        // Kabid melihat semua data gaji untuk melakukan validasi
        $gajis = Gaji::with('karyawan')->latest()->get();
        
        return view('kabid.gaji', compact('gajis'));
    }

    /**
     * FUNGSI STORE (ADMIN): Menyimpan gaji baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan'       => 'required',
            'tahun'       => 'required|numeric',
            'gaji_pokok'  => 'required|numeric|min:0',
            'tunjangan'   => 'nullable|numeric|min:0',
            'potongan'    => 'nullable|numeric|min:0',
        ]);

        // 2. Cek apakah gaji periode ini sudah pernah diinput (Double Input Protection)
        $cekGaji = Gaji::where('karyawan_id', $request->karyawan_id)
                       ->where('bulan', $request->bulan)
                       ->where('tahun', $request->tahun)
                       ->first();

        if ($cekGaji) {
            return redirect()->back()->with('error', 'Gaji karyawan tersebut untuk periode ' . $request->bulan . ' ' . $request->tahun . ' sudah ada!');
        }

        // 3. Hitung Total Gaji
        $gaji_pokok = $request->gaji_pokok;
        $tunjangan  = $request->tunjangan ?? 0;
        $potongan   = $request->potongan ?? 0;
        $total      = ($gaji_pokok + $tunjangan) - $potongan;

        // 4. Simpan ke Database
        Gaji::create([
            'karyawan_id' => $request->karyawan_id,
            'bulan'       => $request->bulan,
            'tahun'       => $request->tahun,
            'gaji_pokok'  => $gaji_pokok,
            'tunjangan'   => $tunjangan,
            'potongan'    => $potongan,
            'total_gaji'  => $total,
            'status'      => 'Pending', // Default selalu pending agar di-ACC Kabid
        ]);

        return redirect()->route('admin.gaji')->with('success', 'Data gaji berhasil disimpan! Menunggu persetujuan Kabid.');
    }

    /**
     * FUNGSI ACC (KABID): Mengubah status menjadi Dibayar
     */
    public function accGaji($id)
    {
        $gaji = Gaji::findOrFail($id);
        
        if ($gaji->status == 'Pending') {
            $gaji->update(['status' => 'Dibayar']);
            return redirect()->back()->with('success', 'Gaji berhasil disetujui (ACC).');
        }

        return redirect()->back()->with('error', 'Gaji ini sudah diproses sebelumnya.');
    }

    /**
     * FUNGSI DESTROY (ADMIN): Menghapus data gaji
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
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil data karyawan tidak ditemukan.');
        }

        // Karyawan hanya bisa melihat gaji yang sudah disetujui (Dibayar)
        $gajis = Gaji::where('karyawan_id', $profil->id)
                     ->where('status', 'Dibayar') 
                     ->latest()
                     ->get();
        
        return view('karyawan.gaji', compact('gajis', 'profil'));
    }

    /**
     * FUNGSI CETAK SLIP PDF (Admin & Karyawan)
     */
    public function cetakPdf($id)
    {
        $gaji = Gaji::with('karyawan')->findOrFail($id);
        
        if ($gaji->status != 'Dibayar') {
            return redirect()->back()->with('error', 'Slip gaji belum bisa dicetak karena belum disetujui Kabid.');
        }

        $pdf = Pdf::loadView('admin.slip_gaji_pdf', compact('gaji'))->setPaper('a5', 'landscape');
        return $pdf->download('Slip_Gaji_'.$gaji->karyawan->nama_karyawan.'_'.$gaji->bulan.'.pdf');
    }
}