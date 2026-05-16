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
        Schema::create('karyawans', function (Blueprint $table) {
            // 1. Primary Key Utama (Auto Increment)
            $table->id(); 
            
            // 2. Relasi ke tabel users (Akun Login)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // 3. Data Identitas Karyawan
            $table->string('nik')->unique(); // NIK tetap unik, tapi bukan primary key
            $table->string('nama_karyawan');
            $table->string('kode_jabatan');
            $table->string('no_hp')->nullable(); // 🌟 FIX: Kolom No. HP ditambahkan di sini
            $table->text('alamat')->nullable();
            $table->integer('tahun_lahir')->nullable();
            
            // 4. Timestamp (created_at & updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};