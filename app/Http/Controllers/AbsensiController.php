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
     * Helper Function: Menghitung rekap bulanan dengan Potongan Progresif Kelipatan 10 Menit
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
            $alpha = $dataAbsensi->where('status', 'Alpha')->count(); // Sesuai Enum DB

            // 1. Potongan Alpa Tetap (Rp 50.000 / Alpa)
            $potonganAlpha = $alpha * 50000;

            // 2. Potongan Telat Progresif (Kelipatan Rp 10.000 tiap 10 menit dari jam masuk asli)
            $potonganTelat = 0;
            $dataTelatKaryawan = $dataAbsensi->where('status', 'Telat');

            foreach ($dataTelatKaryawan as $absen) {
                if ($absen->jam_masuk) {
                    $jamMasuk = Carbon::parse($absen->jam_masuk);
                    $batasMasuk = Carbon::parse($absen->tanggal . ' 08:00:00');
                    
                    // Hitung selisih menit mutlak (absolut)
                    $selisihMenit = abs($jamMasuk->diffInMinutes($batasMasuk));
                    
                    if ($selisihMenit > 0) {
                        // Rumus Kelipatan: Telat 30 menit -> ceil(30/10) = 3 * 10.000 = Rp 30.000
                        // Telat 31 menit -> ceil(31/10) = 4 * 10.000 = Rp 40.000
                        $potonganTelat += ceil($selisihMenit / 10) * 10000;
                    }
                }
            }

            // Gabungkan total potongan akumulatif
            $potongan = $potonganAlpha + $potonganTelat;

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

        $riwayatAbsensi = Absensi::where('karyawan_id', $profil->id)
                    ->orderBy('tanggal', 'desc')
                    ->get();

        return view('karyawan.absensi', compact('riwayatAbsensi', 'profil'));
    }

    /**
     * FUNGSI UNTUK KARYAWAN: Klik Tombol Absen Masuk (Format Jam & Menit Rapi)
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

        // Tentukan batas waktu masuk kerja (jam 08:00 pagi hari ini)
        $batasAbsen = Carbon::createFromFormat('Y-m-d H:i:s', $hariIni . ' 08:00:00', 'Asia/Jakarta');

        // --- LOGIKA STATUS DAN FORMAT TEXT KETERANGAN BERSIH ---
        if ($waktuIndo->greaterThan($batasAbsen)) {
            $status = 'Telat';
            $totalMenit = abs($waktuIndo->diffInMinutes($batasAbsen));
            
            // Konversi total menit ke Jam dan sisa Menit bulat
            $jam = floor($totalMenit / 60);
            $menit = $totalMenit % 60;

            if ($jam > 0) {
                $keterangan = 'Terlambat ' . $jam . ' Jam ' . $menit . ' Menit';
            } else {
                $keterangan = 'Terlambat ' . $menit . ' Menit';
            }
        } else {
            $status = 'Hadir';
            $keterangan = 'Tepat Waktu';
        }

        // Buat data absensi masuk baru ke database
        Absensi::create([
            'karyawan_id' => $profil->id,
            'tanggal'     => $hariIni,
            'jam_masuk'   => $jamSekarang,
            'status'      => $status,
            'keterangan'  => $keterangan,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Masuk pukul ' . $waktuIndo->format('H:i') . ' (' . $keterangan . ')');
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

        $waktuIndo = Carbon::now('Asia/Jakarta');
        $hariIni = $waktuIndo->format('Y-m-d');
        $jamSekarang = $waktuIndo->format('H:i:s');

        // Cari data absensi hari ini
        $absen = Absensi::where('karyawan_id', $profil->id)
                        ->whereDate('tanggal', $hariIni)
                        ->first();

        if (!$absen) {
            return redirect()->back()->with('error', 'Kamu belum melakukan absen masuk hari ini!');
        }

        if ($absen->jam_pulang != null) {
            return redirect()->back()->with('error', 'Kamu sudah melakukan absen pulang hari ini!');
        }

        // Update jam pulang
        $absen->update([
            'jam_pulang' => $jamSekarang,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Pulang pukul ' . $waktuIndo->format('H:i'));
    }
}