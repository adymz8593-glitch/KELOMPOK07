<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// --- BAGIAN INI WAJIB ADA DI ATAS CLASS ---
// Pastikan file-file ini ada di folder app/Models/
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\User;
// -------------------------------------------

class AdminController extends Controller
{
    public function index()
    {
        // 1. Menghitung total data karyawan
        // Jika error di sini, pastikan file app/Models/Karyawan.php ada
        $totalKaryawan = Karyawan::count();
        
        // 2. Menghitung absensi hadir hari ini
        $hadirHariIni = Absensi::where('tanggal', date('Y-m-d'))
                                ->where('status', 'Hadir')
                                ->count();

        // 3. Menghitung total pengeluaran gaji bulan ini
        $totalGaji = Gaji::where('bulan', 'LIKE', '%' . date('Y-m') . '%')
                         ->sum('total_gaji');

        // 4. Mengambil 5 data karyawan terbaru
        $karyawanTerbaru = Karyawan::latest()->take(5)->get();

        // Mengirim data ke view
        return view('admin.dashboard', compact(
            'totalKaryawan', 
            'hadirHariIni', 
            'totalGaji', 
            'karyawanTerbaru'
        ));
    }
}