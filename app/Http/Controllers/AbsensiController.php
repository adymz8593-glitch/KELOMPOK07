<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * FUNGSI UNTUK ADMIN: Melihat Rekap Absensi
     */
    public function indexAdmin(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $rekapAbsensi = $this->getRekapData($bulan, $tahun);

        return view('admin.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }

    /**
     * FUNGSI UNTUK KABID: Menangani route 'kabid.absensi'
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
     * Helper Function: Agar tidak menulis logika rekap dua kali (Fix: Alpha)
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
            $alpha = $dataAbsensi->where('status', 'Alpha')->count(); // FIX: Menggunakan 'Alpha' sesuai Enum DB

            $potongan = ($alpha * 50000) + ($telat * 10000);

            return (object) [
                'nama'     => $karyawan->nama_karyawan,
                'nik'      => $karyawan->nik,
                'periode'  => date('F', mktime(0, 0, 0, (int)$bulan, 1)) . ' ' . $tahun,
                'hadir'    => $hadir,
                'telat'    => $telat,
                'alpha'    => $alpha,
                'potongan' => $potongan
            ];
        });
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Melihat Riwayat Absensi Pribadi
     */
    public function index()
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        // Variabel disamakan menjadi $riwayatAbsensi agar sinkron dengan file Blade
        $riwayatAbsensi = Absensi::where('karyawan_id', $profil->id)
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('karyawan.absensi', compact('riwayatAbsensi', 'profil'));
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Klik Tombol Absen Masuk
     */
    public function absenMasuk(Request $request)
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        // Ambil waktu real-time Zona Waktu Indonesia Barat (WIB)
        $waktuIndo = Carbon::now('Asia/Jakarta');
        $hariIni = $waktuIndo->format('Y-m-d');
        $jamSekarang = $waktuIndo->format('H:i:s');

        // Cek double-absen masuk hari ini
        $cek = Absensi::where('karyawan_id', $profil->id)
                      ->whereDate('tanggal', $hariIni)
                      ->first();

        if ($cek) {
            return redirect()->back()->with('error', 'Kamu sudah melakukan absen masuk hari ini!');
        }

        // Batas toleransi jam masuk (contoh: lewat jam 08:00 dianggap telat)
        $status = ($waktuIndo->format('H:i') > '08:00') ? 'Telat' : 'Hadir';

        Absensi::create([
            'karyawan_id' => $profil->id,
            'tanggal'     => $hariIni,
            'jam_masuk'   => $jamSekarang,
            'status'      => $status,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Masuk pukul ' . $waktuIndo->format('H:i') . ' (' . $status . ')');
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Klik Tombol Absen Pulang
     */
    public function absenPulang(Request $request)
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        // Ambil waktu real-time Zona Waktu Indonesia Barat (WIB)
        $waktuIndo = Carbon::now('Asia/Jakarta');
        $hariIni = $waktuIndo->format('Y-m-d');
        $jamSekarang = $waktuIndo->format('H:i:s');

        // Cari data absensi hari ini yang sudah terbuat saat absen masuk
        $absen = Absensi::where('karyawan_id', $profil->id)
                        ->whereDate('tanggal', $hariIni)
                        ->first();

        if (!$absen) {
            return redirect()->back()->with('error', 'Kamu belum melakukan absen masuk hari ini!');
        }

        if ($absen->jam_pulang != null) {
            return redirect()->back()->with('error', 'Kamu sudah melakukan absen pulang hari ini!');
        }

        // Update jam pulang di baris absensi hari ini
        $absen->update([
            'jam_pulang' => $jamSekarang,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Pulang pukul ' . $waktuIndo->format('H:i'));
    }
}