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
     * Helper Function: Mengecek dan mencatat karyawan yang belum absen sebagai Alpha
     */
    private function cekAlphaOtomatis()
    {
        if (Carbon::now('Asia/Jakarta')->format('H:i') >= '17:00') {
            $semuaKaryawan = Karyawan::all();
            $hariIni = date('Y-m-d');

            foreach ($semuaKaryawan as $karyawan) {
                $sudahAda = Absensi::where('karyawan_id', $karyawan->id)
                                   ->where('tanggal', $hariIni)
                                   ->exists();
                
                if (!$sudahAda) {
                    Absensi::create([
                        'karyawan_id' => $karyawan->id,
                        'tanggal'     => $hariIni,
                        'status'      => 'Alpha',
                        'jam_masuk'   => null,
                        'jam_pulang'  => null
                    ]);
                }
            }
        }
    }

    /**
     * Helper Function: Menghitung rekap bulanan
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
            $divisiKataKunci = str_contains($jabatanAsli, 'keuangan') ? 'Keuangan' : 'Admin';

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
                        
                        if ($jamMasuk->greaterThan($batasMasuk)) {
                            $selisihMenit = $batasMasuk->diffInMinutes($jamMasuk);
                            $potonganTelat += (floor($selisihMenit / 10)) * 10000;
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

        $jamSekarang = Carbon::now('Asia/Jakarta');
        $batasWaktu = Carbon::createFromTime(8, 0, 0, 'Asia/Jakarta');
        
        $status = $jamSekarang->greaterThan($batasWaktu) ? 'Telat' : 'Hadir';

        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal'     => date('Y-m-d'),
            'jam_masuk'   => $jamSekarang->format('H:i:s'),
            'status'      => $status
        ]);

        $pesan = ($status == 'Telat') ? 'Anda absen masuk (Terlambat)!' : 'Berhasil absen masuk!';
        return redirect()->back()->with($status == 'Telat' ? 'error' : 'success', $pesan);
    }

    public function absenPulang(Request $request)
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->first();
        $absen = Absensi::where('karyawan_id', $karyawan->id)
                        ->where('tanggal', date('Y-m-d'))->first();
                        
        if ($absen) {
            $jamPulang = Carbon::now('Asia/Jakarta')->format('H:i:s');
            $absen->update(['jam_pulang' => $jamPulang]);
            return redirect()->back()->with('success', 'Berhasil absen pulang!');
        }
        return redirect()->back()->with('error', 'Anda belum absen masuk!');
    }

    public function indexAdmin(Request $request)
    {
        $this->cekAlphaOtomatis();
        
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        if (Auth::user()->role !== 'admin') return redirect()->back();
        
        $rekapAbsensi = $this->getRekapData($bulan, $tahun, Auth::user());
        return view('admin.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }

    public function indexKabid(Request $request)
    {
        $this->cekAlphaOtomatis();
        
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        $rekapAbsensi = $this->getRekapData($bulan, $tahun, Auth::user());
        return view('kabid.absensi', compact('rekapAbsensi', 'bulan', 'tahun'));
    }
}