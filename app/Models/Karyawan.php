<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';
    
    protected $primaryKey = 'id'; 

    protected $fillable = [
        'user_id', 
        'nik', 
        'nama_karyawan', 
        'kode_jabatan', // Sudah sesuai dengan error SQLite tadi
        'no_hp',        // Tambahkan ini agar nomor HP tersimpan
        'alamat', 
        'tahun_lahir'   // Tetap ada tidak apa-apa jika di DB memang ada kolomnya
    ];

    /**
     * Relasi ke model User (Akun Login)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Gaji
     */
    public function gajis()
    {
        return $this->hasMany(Gaji::class, 'karyawan_id');
    }

    /**
     * Relasi ke model Absensi
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }
}