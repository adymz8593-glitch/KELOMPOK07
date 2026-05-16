<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel karyawans (menggunakan unsignedBigInteger agar aman di SQLite)
            $table->unsignedBigInteger('karyawan_id'); 
            
            // Data Waktu Absensi
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();  // Nullable jika statusnya Izin/Sakit/Alpha
            $table->time('jam_pulang')->nullable(); // Nullable sebelum karyawan menekan tombol pulang
            
            // Status Absensi & Keterangan Tambahan
            $table->enum('status', ['Hadir', 'Telat', 'Izin', 'Sakit', 'Alpha'])->default('Alpha');
            $table->string('keterangan')->nullable(); 
            
            // Timestamp (created_at & updated_at)
            $table->timestamps();

            // Definisi Foreign Key manual untuk menjaga integritas data
            $table->foreign('karyawan_id')
                  ->references('id')
                  ->on('karyawans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};