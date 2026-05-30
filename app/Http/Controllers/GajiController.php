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
        
        // Menampilkan list karyawan di drop-down form input (Sembunyikan akun kabid demi kerapian)
        $karyawans = Karyawan::whereHas('user', function($query) {
            $query->where('role', 'karyawan');
        })->get();
        
        return view('admin.gaji', compact('gajis', 'karyawans'));
    }

    /**
     * FUNGSI UNTUK KABID: Monitoring & ACC Gaji Berdasarkan Bidang Masing-Masing
     * FIX: Menggunakan LIKE query agar mencakup 20 sub-jabatan baru di Administrasi & Keuangan
     */
    public function indexKabid()
    {
        $user = Auth::user();

        // 1. Ambil profil Kabid yang login di tabel karyawan untuk mendeteksi string jabatannya
        $profilKabid = Karyawan::where('user_id', $user->id)->first();

        if ($profilKabid) {
            // Ambil nama jabatan dasar (misal dari 'Kabid Keuangan' ambil kata 'Keuangan')
            // Atau jika isi jabatannya sudah spesifik seperti 'Staf Keuangan', kita cari kata kuncinya
            $jabatanAsli = $profilKabid->kode_jabatan;
            
            // Tentukan kata kunci pemotong otomatis agar pencarian LIKE lebih aman
            if (str_contains(strtolower($jabatanAsli), 'keuangan')) {
                $divisiKataKunci = 'Keuangan';
            } else {
                $divisiKataKunci = 'Admin'; // Menjangkau 'Administrasi' maupun 'Admin'
            }
        } else {
            // Fallback Cadangan: Jika relasi profil kosong, tebak berdasarkan nama role user
            $divisiKataKunci = ($user->role === 'kabid_keuangan') ? 'Keuangan' : 'Admin';
        }

        // 2. Tarik semua data pengajuan gaji staf berdasarkan kecocokan kata kunci bidang (Kecuali Kabid itu sendiri)
        $gajis = Gaji::with('karyawan')
            ->whereHas('karyawan', function($query) use ($divisiKataKunci, $user) {
                $query->where('kode_jabatan', 'LIKE', '%' . $divisiKataKunci . '%')
                      ->where('user_id', '!=', $user->id);
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
            'status'      => 'Pending', // Menunggu ACC Kabid
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