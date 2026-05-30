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
     * Helper Function: Menghitung rekap bulanan dengan logika filter fleksibel
     * Sinkronisasi dengan GajiController agar hasil filter konsisten
     */
    private function getRekapData($bulan, $tahun, $user)
    {
        $queryKaryawan = Karyawan::query();

        if ($user->role === 'admin') {
            $karyawans = $queryKaryawan->get();
        } else {
            $kabid = Karyawan::where('user_id', $user->id)->first();
            
            if (!$kabid || empty($kabid->kode_jabatan)) {
                return collect([]);
            }

            $jabatanAsli = strtolower($kabid->kode_jabatan);
            
            if (str_contains($jabatanAsli, 'keuangan')) {
                $divisiKataKunci = 'Keuangan';
            } else {
                $divisiKataKunci = 'Admin'; 
            }

            $karyawans = $queryKaryawan->where('kode_jabatan', 'LIKE', '%' . $divisiKataKunci . '%')
                                       ->where('user_id', '!=', $user->id)
                                       ->get();
        }

        return $karyawans->map(function($karyawan) use ($bulan, $tahun) {
            $dataAbsensi = Absensi::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            $hadir = $dataAbsensi->where('status', 'Hadir')->count();
            $telat = $dataAbsensi->where('status', 'Telat')->count();
            $alpha = $dataAbsensi->where('status', 'Alpha')->count();

            $potonganAlpha = $alpha * 50000;
            $potonganTelat = 0;
            
            foreach ($dataAbsensi->where('status', 'Telat') as $absen) {
                if ($absen->jam_masuk) {
                    try {
                        $jamMasuk = Carbon::parse($absen->jam_masuk);
                        $batasMasuk = Carbon::parse($absen->tanggal . ' 08:00:00');
                        $selisihMenit = abs($jamMasuk->diffInMinutes($batasMasuk));
                        if ($selisihMenit > 0) {
                            $potonganTelat += ceil($selisihMenit / 10) * 10000;
                        }
                    } catch (\Exception $e) { continue; }
                }
            }

            return (object) [
                'nama'     => $karyawan->nama_karyawan,
                'nik'      => $karyawan->nik,
                'periode'  => Carbon::create(null, $bulan)->format('F') . ' ' . $tahun,
                'hadir'    => $hadir,
                'telat'    => $telat,
                'alpha'    => $alpha,
                'potongan' => $potonganAlpha + $potonganTelat
            ];
        });
    }

    public function index()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->first();
        
        // MENAMBAHKAN VARIABEL riwayatAbsensi agar view tidak error
        $riwayatAbsensi = Absensi::where('karyawan_id', $karyawan->id ?? 0)
                                ->latest()
                                ->paginate(10);
                                
        return view('karyawan.absensi', compact('riwayatAbsensi'));
    }

    public function absenMasuk(Request $request)
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->first();
        if (!$karyawan) return redirect()->back()->with('error', 'Profil karyawan tidak ditemukan.');
        
        $sudahAbsen = Absensi::where('karyawan_id', $karyawan->id)
                             ->where('tanggal', date('Y-m-d'))
                             ->exists();
                             
        if ($sudahAbsen) return redirect()->back()->with('error', 'Anda sudah absen hari ini.');

        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => date('Y-m-d'),
            'jam_masuk'   => date('H:i:s'),
            'status'      => 'Hadir'
        ]);
        return redirect()->back()->with('success', 'Berhasil absen masuk!');
    }

    public function absenPulang(Request $request)
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->first();
        $absen = Absensi::where('karyawan_id', $karyawan->id)
                        ->where('tanggal', date('Y-m-d'))->first();
        if ($absen) {
            $absen->update(['jam_pulang' => date('H:i:s')]);
            return redirect()->back()->with('success', 'Berhasil absen pulang!');
        }
        return redirect()->back()->with('error', 'Anda belum absen masuk!');
    }

    public function indexAdmin(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        if (Auth::user()->role !== 'admin') return redirect()->back();
        
        $rekapAbsensi = $this->getRekapData($bulan, $tahun, Auth::user());
        return view('admin.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }

    public function indexKabid(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        $rekapAbsensi = $this->getRekapData($bulan, $tahun, Auth::user());
        return view('kabid.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }
}