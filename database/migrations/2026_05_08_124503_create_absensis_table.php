<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            // Gunakan unsignedBigInteger untuk keamanan ekstra di SQLite
            $table->unsignedBigInteger('karyawan_id'); 
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable(); // Saya ganti ke jam_pulang biar sinkron dengan Controller
            $table->enum('status', ['Hadir', 'Telat', 'Izin', 'Sakit', 'Alpha'])->default('Alpha');
            $table->string('keterangan')->nullable(); // Tambahkan kolom keterangan sesuai permintaan
            $table->timestamps();

            // Definisi Foreign Key manual agar lebih stabil
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};