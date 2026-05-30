<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Menghitung total seluruh data karyawan di perusahaan
        $totalKaryawan = Karyawan::count();
        
        // 2. Menghitung absensi yang masuk hari ini (Hadir & Telat tetap dihitung masuk)
        $hadirHariIni = Absensi::whereDate('tanggal', Carbon::now()->format('Y-m-d'))
                                ->whereIn('status', ['Hadir', 'Telat'])
                                ->count();

        // 3. Menghitung total pengeluaran gaji berdasarkan Bulan dan Tahun berjalan saat ini
        // FIX: Menyesuaikan struktur tabel gajis yang memisahkan kolom 'bulan' dan 'tahun'
        $bulanSekarang = Carbon::now()->translatedFormat('F'); // Mengambil nama bulan (ex: May)
        $tahunSekarang = Carbon::now()->format('Y');           // Mengambil tahun (ex: 2026)

        $totalGaji = Gaji::where('bulan', $bulanSekarang)
                         ->where('tahun', $tahunSekarang)
                         ->where('status', 'Disetujui') // Hanya menjumlahkan gaji yang sudah di-ACC/Dibayar
                         ->sum('total_gaji');

        // 4. Mengambil 5 data karyawan yang paling baru didaftarkan beserta akun usernya
        $karyawanTerbaru = Karyawan::with('user')->latest()->take(5)->get();

        // Mengirim data ke view admin.dashboard
        return view('admin.dashboard', compact(
            'totalKaryawan', 
            'hadirHariIni', 
            'totalGaji', 
            'karyawanTerbaru'
        ));
    }
}