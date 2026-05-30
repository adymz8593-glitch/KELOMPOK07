<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Gaji;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Utama
        User::create(['name' => 'Administrator Utama', 'username' => 'admin', 'password' => Hash::make('admin123'), 'role' => 'admin']);

        // ============================================================
        // 🌟 AKUN KABID (MANAJERIAL)
        // ============================================================
        $kabidAdmin = User::create(['name' => 'Ahmad Subarjo (Kabid Admin)', 'username' => 'kabid_admin', 'password' => Hash::make('kabid123'), 'role' => 'kabid']);
        Karyawan::create(['user_id' => $kabidAdmin->id, 'nik' => '3201019998887771', 'nama_karyawan' => 'Ahmad Subarjo', 'kode_jabatan' => 'Administrasi']);

        $kabidKeuangan = User::create(['name' => 'Siti Aminah (Kabid Keuangan)', 'username' => 'kabid_keuangan', 'password' => Hash::make('kabid123'), 'role' => 'kabid']);
        Karyawan::create(['user_id' => $kabidKeuangan->id, 'nik' => '3201019998887772', 'nama_karyawan' => 'Siti Aminah', 'kode_jabatan' => 'Keuangan']);

        // ============================================================
        // 🌟 AKUN KARYAWAN (ABSENSI)
        // ============================================================
        $userAhmad = User::create(['name' => 'Ahmad Subarjo', 'username' => 'ahmad_subarjo', 'password' => Hash::make('absen123'), 'role' => 'karyawan']);
        $profilAhmad = Karyawan::create(['user_id' => $userAhmad->id, 'nik' => 'KRY001', 'nama_karyawan' => 'Ahmad Subarjo', 'kode_jabatan' => 'Administrasi']);

        $userSiti = User::create(['name' => 'Siti Aminah', 'username' => 'siti_aminah', 'password' => Hash::make('absen123'), 'role' => 'karyawan']);
        $profilSiti = Karyawan::create(['user_id' => $userSiti->id, 'nik' => 'KRY002', 'nama_karyawan' => 'Siti Aminah', 'kode_jabatan' => 'Keuangan']);

        $k1 = User::create(['name' => 'Ibrizah', 'username' => 'ibrizah', 'password' => Hash::make('karyawan123'), 'role' => 'karyawan']);
        $p1 = Karyawan::create(['user_id' => $k1->id, 'nik' => 'KRY003', 'nama_karyawan' => 'Ibrizah', 'kode_jabatan' => 'Administrasi']);

        $k2 = User::create(['name' => 'Adiba', 'username' => 'adiba', 'password' => Hash::make('karyawan123'), 'role' => 'karyawan']);
        $p2 = Karyawan::create(['user_id' => $k2->id, 'nik' => 'KRY004', 'nama_karyawan' => 'Adiba', 'kode_jabatan' => 'Keuangan']);

        // ============================================================
        // 🌟 DUMMY DATA GAJI (LENGKAP)
        // ============================================================
        if (class_exists(\App\Models\Gaji::class)) {
            $dataGaji = [
                [$profilAhmad->id, 5000000, 500000, 0, 5500000],
                [$profilSiti->id, 5000000, 500000, 0, 5500000],
                [$p1->id, 4500000, 500000, 50000, 4950000],
                [$p2->id, 5000000, 700000, 0, 5700000]
            ];

            foreach ($dataGaji as $g) {
                Gaji::create([
                    'karyawan_id' => $g[0],
                    'bulan'       => Carbon::now()->translatedFormat('F'),
                    'tahun'       => Carbon::now()->format('Y'),
                    'gaji_pokok'  => $g[1],
                    'tunjangan'   => $g[2],
                    'potongan'    => $g[3],
                    'total_gaji'  => $g[4],
                    'status'      => 'Pending'
                ]);
            }
        }
    }
}