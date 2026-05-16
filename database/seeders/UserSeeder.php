<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // 2. Membuat Akun Kabid Default
        User::create([
            'name'     => 'Kepala Bidang',
            'username' => 'kabid',
            'password' => Hash::make('kabid123'),
            'role'     => 'kabid',
        ]);

        // 3. Membuat Akun Karyawan Default (Ibrizah)
        $karyawan1 = User::create([
            'name'     => 'Ibrizah',
            'username' => 'ibrizah',
            'password' => Hash::make('karyawan123'),
            'role'     => 'karyawan',
        ]);

        $profilIbrizah = Karyawan::create([
            'user_id'       => $karyawan1->id,
            'nik'           => '3201012345678901',
            'nama_karyawan' => 'Ibrizah',
            'kode_jabatan'  => 'Staff',
            'no_hp'         => '081234567890',
            'alamat'        => 'Jl. Merdeka No. 10',
            'tahun_lahir'   => 2000
        ]);

        // 4. Membuat Akun Karyawan Default Kedua (Adiba)
        $karyawan2 = User::create([
            'name'     => 'Adiba',
            'username' => 'adiba',
            'password' => Hash::make('karyawan123'),
            'role'     => 'karyawan',
        ]);

        $profilAdiba = Karyawan::create([
            'user_id'       => $karyawan2->id,
            'nik'           => '3201012345678902',
            'nama_karyawan' => 'Adiba',
            'kode_jabatan'  => 'Manager',
            'no_hp'         => '085712345678',
            'alamat'        => 'Jl. Sudirman No. 5',
            'tahun_lahir'   => 1998
        ]);

        // ============================================================
        // 🌟 TAMBAHAN AUTOMATIS: DUMMY DATA ABSENSI BULAN INI
        // ============================================================
        $bulanIni = Carbon::now()->format('Y-m');

        // Dummy Absen untuk Ibrizah (Contoh: Hadir Tepat Waktu, Telat, Alpha)
        Absensi::create([
            'karyawan_id' => $profilIbrizah->id,
            'tanggal'     => $bulanIni . '-11',
            'jam_masuk'   => '07:45:00',
            'jam_pulang'  => '16:00:00',
            'status'      => 'Hadir'
        ]);

        Absensi::create([
            'karyawan_id' => $profilIbrizah->id,
            'tanggal'     => $bulanIni . '-12',
            'jam_masuk'   => '08:15:00', // Lewat jam 08:00 -> Potongan Rp 10.000
            'jam_pulang'  => '16:00:00',
            'status'      => 'Telat'
        ]);

        Absency::create([
            'karyawan_id' => $profilIbrizah->id,
            'tanggal'     => $bulanIni . '-13',
            'jam_masuk'   => null,
            'jam_pulang'  => null,
            'status'      => 'Alpha' // Potongan Rp 50.000
        ]);


        // Dummy Absen untuk Adiba (Contoh: Rajin Hadir Semua)
        Absensi::create([
            'karyawan_id' => $profilAdiba->id,
            'tanggal'     => $bulanIni . '-11',
            'jam_masuk'   => '07:50:00',
            'jam_pulang'  => '16:00:00',
            'status'      => 'Hadir'
        ]);

        Absensi::create([
            'karyawan_id' => $profilAdiba->id,
            'tanggal'     => $bulanIni . '-12',
            'jam_masuk'   => '07:30:00',
            'jam_pulang'  => '16:05:00',
            'status'      => 'Hadir'
        ]);
    }
}