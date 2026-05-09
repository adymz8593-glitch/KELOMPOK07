<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Gaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk transaksi database

class KaryawanController extends Controller
{
    /**
     * DASHBOARD UNTUK ROLE KARYAWAN
     */
    public function dashboard()
    {
        $user = Auth::user();
        $profil = Karyawan::where('user_id', $user->id)->first();

        if (!$profil) {
            return "Profil karyawan belum dibuat. Hubungi Admin.";
        }

        $sudahAbsen = Absensi::where('karyawan_id', $profil->id)
                            ->whereDate('tanggal', date('Y-m-d'))
                            ->first();

        $riwayatGaji = Gaji::where('karyawan_id', $profil->id)
                          ->latest()
                          ->take(5)
                          ->get();

        return view('karyawan.dashboard', compact('sudahAbsen', 'riwayatGaji', 'profil'));
    }

    /**
     * HALAMAN INDEX ADMIN: Menampilkan Daftar Karyawan
     */
    public function index()
    {
        // Gunakan nama variabel $karyawans (jamak) agar lebih umum di view
        $karyawans = Karyawan::with('user')->latest()->get();
        return view('admin.karyawan', compact('karyawans'));
    }

    /**
     * STORE DATA KARYAWAN + USER LOGIN
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:karyawans,nik',
            'nama_karyawan' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'kode_jabatan' => 'required',
        ]);

        // Gunakan Transaction agar jika salah satu gagal, data tidak tanggung masuknya
        DB::transaction(function () use ($request) {
            // 1. Buat User Login
            $user = User::create([
                'name' => $request->nama_karyawan,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'karyawan',
            ]);

            // 2. Buat Data Profil Karyawan
            Karyawan::create([
                'nik' => $request->nik,
                'user_id' => $user->id,
                'nama_karyawan' => $request->nama_karyawan,
                'alamat' => $request->alamat,
                'kode_jabatan' => $request->kode_jabatan,
                'tahun_lahir' => $request->tahun_lahir,
            ]);
        });

        return redirect()->route('admin.karyawan')->with('success', 'Data Karyawan berhasil ditambah!');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        // Hapus usernya juga
        User::where('id', $karyawan->user_id)->delete();
        $karyawan->delete();

        return redirect()->back()->with('success', 'Data Karyawan telah dihapus.');
    }
}