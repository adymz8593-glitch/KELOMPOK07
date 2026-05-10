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

class KaryawanController extends Controller
{
    /**
     * DASHBOARD ADMIN
     */
    public function dashboardAdmin()
    {
        $totalKaryawan = Karyawan::count();
        $hadirHariIni = Absensi::whereDate('tanggal', date('Y-m-d'))->whereIn('status', ['Hadir', 'Telat'])->count();
        $totalGaji = Gaji::where('status', 'Dibayar')->sum('total_gaji');
        $gajiPending = Gaji::where('status', 'Pending')->count();
        $karyawanTerbaru = Karyawan::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalKaryawan', 'hadirHariIni', 'totalGaji', 'gajiPending', 'karyawanTerbaru'));
    }

    /**
     * DASHBOARD KABID
     */
    public function dashboardKabid()
    {
        $totalKaryawan = Karyawan::count();
        $hadirHariIni = Absensi::whereDate('tanggal', date('Y-m-d'))->whereIn('status', ['Hadir', 'Telat'])->count();
        $gajiPending = Gaji::where('status', 'Pending')->count();

        return view('kabid.dashboard', compact('totalKaryawan', 'hadirHariIni', 'gajiPending'));
    }

    /**
     * DASHBOARD KARYAWAN
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return "Profil karyawan belum ditemukan. Silakan hubungi Admin.";
        }

        $sudahAbsen = Absensi::where('karyawan_id', $profil->id)->whereDate('tanggal', date('Y-m-d'))->first();
        $riwayatGaji = Gaji::where('karyawan_id', $profil->id)->latest()->take(5)->get();

        return view('karyawan.dashboard', compact('sudahAbsen', 'riwayatGaji', 'profil'));
    }

    /**
     * DATA KARYAWAN (Index Admin)
     */
    public function index()
    {
        $karyawans = Karyawan::with('user')->latest()->get();
        return view('admin.karyawan', compact('karyawans'));
    }

    /**
     * STORE: Tambah Karyawan Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nik'           => 'required|unique:karyawans,nik',
            'jabatan'       => 'required', 
            'username'      => 'required|string|unique:users,username', 
            'password'      => 'required|min:6',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel 'users' (Tanpa Email sesuai struktur DB kamu)
            $user = User::create([
                'name'     => $request->nama_karyawan,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => 'karyawan', 
            ]);

            // 2. Simpan ke tabel 'karyawans' (Tanpa No_HP sesuai struktur DB kamu)
            Karyawan::create([
                'user_id'       => $user->id,
                'nama_karyawan' => $request->nama_karyawan,
                'nik'           => $request->nik,
                'kode_jabatan'  => $request->jabatan, 
                'alamat'        => $request->alamat,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambah karyawan: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY: Hapus Karyawan
     */
    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        if ($karyawan->user_id) {
            User::where('id', $karyawan->user_id)->delete();
        }
        
        $karyawan->delete();

        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus.');
    }
}