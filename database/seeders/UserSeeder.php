<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// BARIS DI BAWAH INI ADALAH KUNCI UNTUK MEMPERBAIKI ERROR TADI:
use App\Models\User; 

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'username' => 'admin',
            'gmail' => 'admin@gmail.com',
            'password' => Hash::make('12345'),
            'role' => 'Admin'
        ]);

        // 2. Akun Kepala Bidang (Role hasil revisi sidang)
        User::create([
            'username' => 'kabid',
            'gmail' => 'kabid@gmail.com',
            'password' => Hash::make('12345'),
            'role' => 'Kepala Bidang'
        ]);

        // 3. Akun Karyawan
        User::create([
            'username' => 'karyawan',
            'gmail' => 'karyawan@gmail.com',
            'password' => Hash::make('12345'),
            'role' => 'Karyawan'
        ]);
    }
}