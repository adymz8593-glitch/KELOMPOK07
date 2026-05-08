<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib tambah ini untuk deteksi siapa yang login

class AbsensiController extends Controller
{
    /**
     * FUNGSI UNTUK ADMIN: Melihat Rekap Absensi
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $rekapAbsensi = Karyawan::all()->map(function($karyawan) use ($bulan, $tahun) {
            $dataAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $hadir = $dataAbsensi->where('status', 'Hadir')->count();
            $telat = $dataAbsensi->where('status', 'Telat')->count();
            $alpa  = $dataAbsensi->where('status', 'Alpa')->count();

            $potongan = ($alpa * 50000) + ($telat * 10000);

            return (object) [
                'nama'     => $karyawan->nama_karyawan,
                'nik'      => $karyawan->nik,
                'periode'  => date('F', mktime(0, 0, 0, $bulan, 1)) . ' ' . $tahun,
                'hadir'    => $hadir,
                'telat'    => $telat,
                'alpa'     => $alpa,
                'potongan' => $potongan
            ];
        });

        return view('admin.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Klik Tombol Hadir (Baru)
     */
    public function storeMandiri(Request $request)
    {
        $user = Auth::user();
        
        // 1. Cari data profil karyawan berdasarkan user_id yang login
        $profil = Karyawan::where('user_id', $user->id)->first();

        // 2. Cek apakah sudah absen hari ini? (mencegah absen double)
        $cek = Absensi::where('karyawan_id', $profil->id)
                      ->whereDate('tanggal', date('Y-m-d'))
                      ->first();

        if ($cek) {
            return redirect()->back()->with('error', 'Kamu sudah melakukan absen hari ini!');
        }

        // 3. Tentukan status (Batas jam 08:00)
        $jamSekarang = date('H:i');
        $status = ($jamSekarang > '08:00') ? 'Telat' : 'Hadir';

        // 4. Simpan ke database
        Absensi::create([
            'karyawan_id' => $profil->id,
            'tanggal'     => date('Y-m-d'),
            'status'      => $status,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Kamu tercatat: ' . $status);
    }
}