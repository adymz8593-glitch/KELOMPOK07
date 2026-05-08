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
            $table->string('nik')->primary(); // Primary Key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kode_jabatan');
            $table->string('nama_karyawan');
            $table->text('alamat');          // Revisi alamat
            $table->integer('tahun_lahir');  // Revisi tahun lahir
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