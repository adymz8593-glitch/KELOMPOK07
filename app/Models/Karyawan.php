<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawans';

    protected $fillable = [
        'nik', 
        'user_id', 
        'kode_jabatan', 
        'nama_karyawan', 
        'alamat', 
        'tahun_lahir'
    ];

    /**
     * Relasi ke model User (Akun Login)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Gaji (Jika ingin menarik data gaji dari karyawan)
     */
    public function gajis()
    {
        return $this->hasMany(Gaji::class, 'karyawan_id');
    }
}