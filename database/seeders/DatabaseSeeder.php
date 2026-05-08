<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil UserSeeder agar akun admin kita dibuat
        $this->call([
            UserSeeder::class,
        ]);

        // JANGAN masukkan kode pembuatan User::factory() di sini 
        // karena itu yang mencari kolom 'email'
    }
}