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
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy');

        // Rekap Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');
        Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('admin.absensi.store');

        // Kelola Gaji & Cetak PDF
        Route::get('/gaji', [GajiController::class, 'index'])->name('admin.gaji');
        Route::post('/gaji/store', [GajiController::class, 'store'])->name('admin.gaji.store');
        Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('admin.gaji.destroy');
        Route::get('/gaji/cetak/{id}', [GajiController::class, 'cetakPdf'])->name('admin.gaji.cetak');
    });

    // --- AREA KARYAWAN ---
    Route::prefix('karyawan')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
        
        // Menu Absensi & Gaji untuk Karyawan
        Route::get('/absensi', [AbsensiController::class, 'indexKaryawan'])->name('karyawan.absensi');
        Route::get('/gaji', [GajiController::class, 'indexKaryawan'])->name('karyawan.gaji');
        
        Route::post('/absen', [AbsensiController::class, 'storeMandiri'])->name('karyawan.absen');
    });

    // --- AREA KABID ---
    Route::prefix('kabid')->group(function () {
        // Dashboard Kabid
        Route::get('/dashboard', function () { 
            return view('kabid.dashboard'); 
        })->name('kabid.dashboard');

        // Kabid melihat data gaji & absensi
        Route::get('/gaji', [GajiController::class, 'index'])->name('kabid.gaji');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('kabid.absensi');

        // Fitur Utama Kabid: ACC Gaji
        Route::post('/gaji/acc/{id}', [GajiController::class, 'accGaji'])->name('kabid.gaji.acc');
    });
});