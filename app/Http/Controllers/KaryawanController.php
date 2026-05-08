<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Absensi; // Tambahkan ini
use App\Models\Gaji;    // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class KaryawanController extends Controller
{
    /**
     * DASHBOARD UNTUK ROLE KARYAWAN (Baru ditambahkan)
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Ambil data profil karyawan berdasarkan user_id yang login
        $profil = Karyawan::where('user_id', $user->id)->first();

        // Cek apakah hari ini sudah absen?
        $sudahAbsen = Absensi::where('karyawan_id', $profil->id)
                            ->whereDate('tanggal', date('Y-m-d'))
                            ->first();

        // Ambil riwayat gaji terakhir milik karyawan ini
        $riwayatGaji = Gaji::where('karyawan_id', $profil->id)
                          ->latest()
                          ->take(5)
                          ->get();

        return view('karyawan.dashboard', compact('sudahAbsen', 'riwayatGaji', 'profil'));
    }

    /**
     * SISA KODE ADMIN (Tetap biarkan seperti yang kamu punya)
     */
    public function index()
    {
        $karyawan = Karyawan::all();
        return view('admin.karyawan', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:karyawans',
            'nama_karyawan' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'kode_jabatan' => 'required',
        ]);

        $user = User::create([
            'name' => $request->nama_karyawan,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
        ]);

        Karyawan::create([
            'nik' => $request->nik,
            'user_id' => $user->id,
            'nama_karyawan' => $request->nama_karyawan,
            'alamat' => $request->alamat,
            'kode_jabatan' => $request->kode_jabatan,
            'tahun_lahir' => $request->tahun_lahir,
        ]);

        return redirect()->back()->with('success', 'Data Karyawan dan Akun Login berhasil dibuat!');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        User::where('id', $karyawan->user_id)->delete();
        $karyawan->delete();

        return redirect()->back()->with('success', 'Data Karyawan telah dihapus.');
    }
}