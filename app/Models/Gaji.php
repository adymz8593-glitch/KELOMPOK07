<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gaji extends Model
{
    use HasFactory;

    protected $table = 'gajis';

    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',           // <--- WAJIB DITAMBAHKAN
        'gaji_pokok',
        'tunjangan',
        'potongan',
        'total_gaji',
        'status'           // <--- Sesuaikan dengan nama kolom di migrasi (status atau status_pembayaran)
    ];

    /**
     * Relasi ke model Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}