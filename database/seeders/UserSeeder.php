<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat Akun Admin Default
        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'), // Silakan ganti password admin sesuai keinginanmu
            'role'     => 'admin',
        ]);

        // 2. Membuat Akun Kabid Default (Jika ada)
        User::create([
            'name'     => 'Kepala Bidang',
            'username' => 'kabid',
            'password' => Hash::make('kabid123'),
            'role'     => 'kabid',
        ]);

        // 3. Membuat Akun Karyawan Default (Contoh: Ibrizah)
        $karyawan1 = User::create([
            'name'     => 'Ibrizah',
            'username' => 'ibrizah',
            'password' => Hash::make('karyawan123'),
            'role'     => 'karyawan',
        ]);

        // Hubungkan langsung ke tabel karyawans dan masukkan No. HP barunya
        Karyawan::create([
            'user_id'       => $karyawan1->id,
            'nik'           => '3201012345678901',
            'nama_karyawan' => 'Ibrizah',
            'kode_jabatan'  => 'Staff',
            'no_hp'         => '081234567890', // 🌟 Kolom baru sekarang langsung terisi lewat seeder
            'alamat'        => 'Jl. Merdeka No. 10',
            'tahun_lahir'   => 2000
        ]);

        // 4. Membuat Akun Karyawan Default Kedua (Contoh: Adiba)
        $karyawan2 = User::create([
            'name'     => 'Adiba',
            'username' => 'adiba',
            'password' => Hash::make('karyawan123'),
            'role'     => 'karyawan',
        ]);

        Karyawan::create([
            'user_id'       => $karyawan2->id,
            'nik'           => '3201012345678902',
            'nama_karyawan' => 'Adiba',
            'kode_jabatan'  => 'Manager',
            'no_hp'         => '085712345678', // 🌟 Kolom baru terisi
            'alamat'        => 'Jl. Sudirman No. 5',
            'tahun_lahir'   => 1998
        ]);
    }
}