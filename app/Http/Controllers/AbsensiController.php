<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * FUNGSI UNTUK ADMIN: Melihat Rekap Absensi
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $rekapAbsensi = $this->getRekapData($bulan, $tahun);

        return view('admin.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }

    /**
     * FUNGSI UNTUK KABID: Menangani route 'kabid.absensi'
     * Ini fungsi yang dipanggil di web.php kamu
     */
    public function indexKabid(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $rekapAbsensi = $this->getRekapData($bulan, $tahun);

        // Diarahkan ke folder kabid agar sidebar tidak hilang
        return view('kabid.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }

    /**
     * Helper Function: Agar tidak menulis logika rekap dua kali
     */
    private function getRekapData($bulan, $tahun)
    {
        return Karyawan::all()->map(function($karyawan) use ($bulan, $tahun) {
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
                'periode'  => date('F', mktime(0, 0, 0, (int)$bulan, 1)) . ' ' . $tahun,
                'hadir'    => $hadir,
                'telat'    => $telat,
                'alpa'     => $alpa,
                'potongan' => $potongan
            ];
        });
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Melihat Riwayat Absensi Pribadi
     */
    public function indexKaryawan()
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        $absensi = Absensi::where('karyawan_id', $profil->id)
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('karyawan.absensi', compact('absensi', 'profil'));
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Klik Tombol Absen Mandiri
     */
    public function storeMandiri(Request $request)
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        $cek = Absensi::where('karyawan_id', $profil->id)
                      ->whereDate('tanggal', date('Y-m-d'))
                      ->first();

        if ($cek) {
            return redirect()->back()->with('error', 'Kamu sudah melakukan absen hari ini!');
        }

        $jamSekarang = date('H:i');
        $status = ($jamSekarang > '08:00') ? 'Telat' : 'Hadir';

        Absensi::create([
            'karyawan_id' => $profil->id,
            'tanggal'     => date('Y-m-d'),
            'status'      => $status,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Kamu tercatat: ' . $status);
    }
}