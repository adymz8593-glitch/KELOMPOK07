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
        // Admin utama berhak melihat seluruh riwayat pengajuan gaji staf
        $gajis = Gaji::with('karyawan')->latest()->get();
        
        // Mengambil semua data dari tabel karyawan agar semua karyawan (termasuk Kabid) muncul di drop-down
        $karyawans = Karyawan::all(); 
        
        return view('admin.gaji', compact('gajis', 'karyawans'));
    }

    /**
     * FUNGSI UNTUK KABID: Monitoring & ACC Gaji Berdasarkan Bidang Masing-Masing
     */
    public function indexKabid()
    {
        $user = Auth::user();

        // 1. Ambil profil Kabid yang login di tabel karyawan untuk mendeteksi string jabatannya
        $profilKabid = Karyawan::where('user_id', $user->id)->first();

        if ($profilKabid) {
            $jabatanAsli = $profilKabid->kode_jabatan;
            
            if (str_contains(strtolower($jabatanAsli), 'keuangan')) {
                $divisiKataKunci = 'Keuangan';
            } else {
                $divisiKataKunci = 'Admin'; 
            }
        } else {
            $divisiKataKunci = ($user->role === 'kabid_keuangan') ? 'Keuangan' : 'Admin';
        }

        // 2. Tarik semua data pengajuan gaji staf berdasarkan kecocokan kata kunci bidang
        $gajis = Gaji::with('karyawan')
            ->whereHas('karyawan', function($query) use ($divisiKataKunci) {
                $query->where('kode_jabatan', 'LIKE', '%' . $divisiKataKunci . '%');
            })->latest()->get();
        
        return view('kabid.gaji', compact('gajis'));
    }

    /**
     * FUNGSI STORE (ADMIN): Menyimpan gaji baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'bulan'       => 'required',
            'tahun'       => 'required|numeric',
            'gaji_pokok'  => 'required|numeric|min:0',
            'tunjangan'   => 'nullable|numeric|min:0',
            'potongan'    => 'nullable|numeric|min:0',
        ]);

        // Double Input Protection
        $cekGaji = Gaji::where('karyawan_id', $request->karyawan_id)
                       ->where('bulan', $request->bulan)
                       ->where('tahun', $request->tahun)
                       ->first();

        if ($cekGaji) {
            return redirect()->back()->with('error', 'Gaji karyawan tersebut untuk periode ' . $request->bulan . ' ' . $request->tahun . ' sudah ada!');
        }

        $gaji_pokok = $request->gaji_pokok;
        $tunjangan  = $request->tunjangan ?? 0;
        $potongan   = $request->potongan ?? 0;
        $total      = ($gaji_pokok + $tunjangan) - $potongan;

        Gaji::create([
            'karyawan_id' => $request->karyawan_id,
            'bulan'       => $request->bulan,
            'tahun'       => $request->tahun,
            'gaji_pokok'  => $gaji_pokok,
            'tunjangan'   => $tunjangan,
            'potongan'    => $potongan,
            'total_gaji'  => $total,
            'status'      => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Data gaji berhasil disimpan! Menunggu persetujuan Kabid.');
    }

    /**
     * FUNGSI ACC (KABID): Mengubah status menjadi Disetujui
     */
    public function accGaji($id)
    {
        $gaji = Gaji::findOrFail($id);
        
        if ($gaji->status == 'Pending') {
            $gaji->update(['status' => 'Disetujui']); 
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

        // Karyawan hanya bisa melihat gaji yang sudah di-ACC ('Disetujui')
        $gajis = Gaji::where('karyawan_id', $profil->id)
                     ->where('status', 'Disetujui') 
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
        
        if ($gaji->status != 'Disetujui') {
            return redirect()->back()->with('error', 'Slip gaji belum bisa dicetak karena belum disetujui Kabid.');
        }

        $pdf = Pdf::loadView('admin.slip_gaji_pdf', compact('gaji'))->setPaper('a5', 'landscape');
        return $pdf->download('Slip_Gaji_'.$gaji->karyawan->nama_karyawan.'_'.$gaji->bulan.'.pdf');
    }
}