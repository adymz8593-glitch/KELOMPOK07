<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Gaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KaryawanController extends Controller
{
    /**
     * PERBAIKAN: Mengambil semua ID karyawan yang relevan dengan divisi Kabid
     * Tanpa mengecualikan ID Kabid itu sendiri agar data tetap muncul
     */
    private function getKaryawanIdsByRole($user)
    {
        $kabidProfil = Karyawan::where('user_id', $user->id)->first();
        
        if (!$kabidProfil) return [];

        $jabatanKabid = strtolower($kabidProfil->kode_jabatan);
        
        if (str_contains($jabatanKabid, 'keuangan')) {
            $kataKunci = 'Keuangan';
        } else {
            $kataKunci = 'Admin'; 
        }

        // Dihapus: ->where('id', '!=', $kabidProfil->id)
        return Karyawan::where('kode_jabatan', 'LIKE', '%' . $kataKunci . '%')
                       ->pluck('id')
                       ->toArray();
    }

    public function cetakLaporan(Request $request)
    {
        $data = Gaji::with('karyawan')->latest()->get(); 
        $pdf = Pdf::loadView('admin.laporan_pdf', ['data' => $data]);
        return $pdf->download('Laporan_Gaji_' . date('Y-m-d') . '.pdf');
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $karyawans = Karyawan::with('user')->latest()->get();
        } else {
            $ids = $this->getKaryawanIdsByRole($user);
            $karyawans = Karyawan::with('user')->whereIn('id', $ids)->latest()->get();
        }
        
        return view('admin.karyawan', compact('karyawans'));
    }

    public function dashboardKabid()
    {
        $user = Auth::user();
        $karyawanIds = $this->getKaryawanIdsByRole($user);
        
        $profilKabid = Karyawan::where('user_id', $user->id)->first();
        $absensiKabid = $profilKabid ? Absensi::where('karyawan_id', $profilKabid->id)->orderBy('tanggal', 'DESC')->get() : collect([]);

        if (empty($karyawanIds)) {
            return view('kabid.dashboard', [
                'totalKaryawan' => 0,
                'gajiPending'   => 0,
                'gajiTerakhir'  => collect([]),
                'absensiKabid'  => $absensiKabid
            ]);
        }

        $totalKaryawan = Karyawan::whereIn('id', $karyawanIds)->count();
        $gajiPending   = Gaji::whereIn('karyawan_id', $karyawanIds)->where('status', 'Pending')->count();
        
        // Mengambil 5 gaji terakhir dari karyawan di divisi tersebut
        $gajiTerakhir = Gaji::with('karyawan')
                            ->whereIn('karyawan_id', $karyawanIds)
                            ->orderBy('created_at', 'DESC')
                            ->limit(5)
                            ->get()
                            ->map(function ($item) {
                                $item->periode_tampil = $item->bulan . ' ' . $item->tahun;
                                return $item;
                            });

        return view('kabid.dashboard', compact('totalKaryawan', 'gajiPending', 'gajiTerakhir', 'absensiKabid'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();
        if (!$profil) return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        
        $sudahAbsen = Absensi::where('karyawan_id', $profil->id)->whereDate('tanggal', date('Y-m-d'))->first();
        $riwayatGaji = Gaji::where('karyawan_id', $profil->id)->latest()->take(5)->get();
        
        return view('karyawan.dashboard', compact('sudahAbsen', 'riwayatGaji', 'profil'));
    }

    public function dashboardAdmin()
    {
        $data = [
            'totalKaryawan'  => Karyawan::count(),
            'hadirHariIni'   => Absensi::whereDate('tanggal', date('Y-m-d'))->whereIn('status', ['Hadir', 'Telat'])->count(),
            'totalGaji'      => Gaji::where('status', 'Disetujui')->sum('total_gaji'),
            'gajiPending'    => Gaji::where('status', 'Pending')->count()
        ];
        
        return view('admin.dashboard', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required',
            'nik'           => 'required|unique:karyawans',
            'jabatan'       => 'required',
            'username'      => 'required|unique:users',
            'password'      => 'required|min:6'
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->nama_karyawan,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => 'karyawan'
            ]);
            
            Karyawan::create([
                'user_id'       => $user->id,
                'nama_karyawan' => $request->nama_karyawan,
                'nik'           => $request->nik,
                'kode_jabatan'  => $request->jabatan,
                'no_hp'         => $request->no_hp,
                'alamat'        => $request->alamat
            ]);
        });
        
        return redirect()->back()->with('success', 'Data berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $request->validate([
            'nama_karyawan' => 'required',
            'username'      => ['required', Rule::unique('users')->ignore($karyawan->user_id)]
        ]);

        DB::transaction(function () use ($request, $karyawan) {
            $karyawan->update([
                'nama_karyawan' => $request->nama_karyawan, 
                'nik'           => $request->nik, 
                'kode_jabatan'  => $request->jabatan,
                'no_hp'         => $request->no_hp,
                'alamat'        => $request->alamat
            ]);
            
            if ($karyawan->user) {
                $userData = [
                    'username' => $request->username, 
                    'name'     => $request->nama_karyawan
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $karyawan->user->update($userData);
            }
        });
        
        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        DB::transaction(function () use ($karyawan) {
            if ($karyawan->user) $karyawan->user->delete();
            $karyawan->delete();
        });
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }
}