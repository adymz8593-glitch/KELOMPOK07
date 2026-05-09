<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * FUNGSI UNTUK ADMIN: Melihat Rekap Absensi Seluruh Karyawan
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

            // Logika potongan sederhana
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
     * FUNGSI UNTUK KARYAWAN: Melihat Riwayat Absensi Pribadi
     */
    public function indexKaryawan()
    {
        $user = Auth::user();
        
        // Cari profil karyawan berdasarkan user_id yang login
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil karyawan tidak ditemukan.');
        }

        // Ambil riwayat absen si karyawan tersebut
        $absensi = Absensi::where('karyawan_id', $profil->id)
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('karyawan.absensi', compact('absensi', 'profil'));
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Klik Tombol Absen (Simpan Mandiri)
     */
    public function storeMandiri(Request $request)
    {
        $user = Auth::user();
        
        // 1. Cari profil karyawan
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        // 2. Cek apakah sudah absen hari ini?
        $cek = Absensi::where('karyawan_id', $profil->id)
                      ->whereDate('tanggal', date('Y-m-d'))
                      ->first();

        if ($cek) {
            return redirect()->back()->with('error', 'Kamu sudah melakukan absen hari ini!');
        }

        // 3. Tentukan status (Contoh: Batas jam 08:00)
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