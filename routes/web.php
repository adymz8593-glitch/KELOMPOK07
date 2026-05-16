<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GajiController;

// 1. Redirect Awal
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Auth: Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 3. Area Berbasis Login
Route::middleware(['auth'])->group(function () {

    // --- AREA ADMIN ---
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'dashboardAdmin'])->name('admin.dashboard');

        // Kelola Data Karyawan
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan');
        Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('admin.karyawan.store');
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('admin.karyawan.update'); // <-- BERHASIL DITAMBAHKAN
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy');

        // Rekap Absensi Admin
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');
        Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('admin.absensi.store');

        // Kelola Gaji Admin
        Route::get('/gaji', [GajiController::class, 'index'])->name('admin.gaji');
        Route::post('/gaji/store', [GajiController::class, 'store'])->name('admin.gaji.store');
        Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('admin.gaji.destroy');
        Route::get('/gaji/cetak/{id}', [GajiController::class, 'cetakPdf'])->name('admin.gaji.cetak');
    });

    // --- AREA KARYAWAN ---
    Route::prefix('karyawan')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
        Route::get('/absensi', [AbsensiController::class, 'indexKaryawan'])->name('karyawan.absensi');
        Route::get('/gaji', [GajiController::class, 'indexKaryawan'])->name('karyawan.gaji');
        Route::post('/absen', [AbsensiController::class, 'storeMandiri'])->name('karyawan.absen');
    });

    // --- AREA KABID ---
    Route::prefix('kabid')->group(function () {
        // Dashboard Khusus Kabid
        Route::get('/dashboard', [KaryawanController::class, 'dashboardKabid'])->name('kabid.dashboard');

        // Monitoring Gaji & Absensi Khusus Kabid
        Route::get('/gaji', [GajiController::class, 'indexKabid'])->name('kabid.gaji');
        Route::get('/absensi', [AbsensiController::class, 'indexKabid'])->name('kabid.absensi');

        // Fitur Utama Kabid: ACC Gaji
        Route::post('/gaji/acc/{id}', [GajiController::class, 'accGaji'])->name('kabid.gaji.acc');
    });
});