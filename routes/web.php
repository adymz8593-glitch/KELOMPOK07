<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GajiController;

// 1. Redirect Awal
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 3. Area Berbasis Login (Auth Middleware)
Route::middleware(['auth'])->group(function () {

    // --- AREA ADMIN ---
    Route::prefix('admin')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Kelola Data Karyawan
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan');
        Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('admin.karyawan.store');
        // Baris di bawah ini yang tadi bikin error, sekarang sudah diperbaiki:
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy');

        // Rekap Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');
        Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('admin.absensi.store');

        // Kelola Gaji & Cetak PDF
        Route::get('/gaji', [GajiController::class, 'index'])->name('admin.gaji');
        Route::post('/gaji/store', [GajiController::class, 'store'])->name('admin.gaji.store');
        Route::get('/gaji/cetak/{id}', [GajiController::class, 'cetakPdf'])->name('admin.gaji.cetak');
    });

    // --- AREA KARYAWAN ---
    Route::get('/karyawan/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
    Route::post('/karyawan/absen', [AbsensiController::class, 'storeMandiri'])->name('karyawan.absen');

    // --- AREA KABID ---
    Route::get('/kabid/dashboard', function () { 
        return view('kabid.dashboard'); 
    })->name('kabid.dashboard');
});