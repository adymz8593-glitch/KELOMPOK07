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
        'kode_jabatan', 
        'no_hp',
        'alamat', 
        'tahun_lahir'   
    ];

    /**
     * MUTATOR: Otomatis membersihkan kode_jabatan sebelum disimpan.
     * Ini akan mengubah 'Keuangan ' menjadi 'KEUANGAN' secara otomatis.
     * Ini mencegah masalah data tidak sinkron akibat perbedaan penulisan.
     */
    public function setKodeJabatanAttribute($value)
    {
        $this->attributes['kode_jabatan'] = strtoupper(trim($value));
    }

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