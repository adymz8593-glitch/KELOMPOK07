<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan proteksi foreign key sementara (untuk sqlite/mysql)
        DB::statement('PRAGMA foreign_keys = OFF'); 
        
        // Bersihkan data user lama
        User::truncate();

        // Akun Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Akun Kabid
        User::create([
            'name' => 'Kepala Bidang',
            'username' => 'kabid',
            'password' => Hash::make('kabid123'),
            'role' => 'kabid',
        ]);

        // Akun Karyawan
        User::create([
            'name' => 'Karyawan',
            'username' => 'karyawan',
            'password' => Hash::make('user123'),
            'role' => 'karyawan',
        ]);
    }
}