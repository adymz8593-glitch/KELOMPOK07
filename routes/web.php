<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GajiController;

// 1. Redirect Awal
Route::get('/', function () { return redirect()->route('login'); });

// 2. Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 3. Area Auth
Route::middleware(['auth'])->group(function () {

    // --- AREA ADMIN ---
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan.index');
        Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('admin.karyawan.store');
        Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('admin.karyawan.update');
        Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('admin.karyawan.destroy');
        Route::get('/absensi', [AbsensiController::class, 'indexAdmin'])->name('admin.absensi');
        Route::post('/absensi/store', [AbsensiController::class, 'store'])->name('admin.absensi.store');
        Route::get('/gaji', [GajiController::class, 'index'])->name('admin.gaji');
        Route::post('/gaji/store', [GajiController::class, 'store'])->name('admin.gaji.store');
        Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('admin.gaji.destroy');
        Route::get('/gaji/cetak/{id}', [GajiController::class, 'cetakPdf'])->name('admin.gaji.cetak');
        
        // RUTE BARU: Cetak Laporan Global (bukan per ID)
        Route::get('/cetak-laporan', [KaryawanController::class, 'cetakLaporan'])->name('admin.cetak.laporan');
    });

    // --- AREA KARYAWAN ---
    Route::prefix('karyawan')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('karyawan.dashboard');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('karyawan.absensi');
        Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk'])->name('karyawan.absen.masuk');
        Route::post('/absensi/pulang', [AbsensiController::class, 'absenPulang'])->name('karyawan.absen.pulang');
        Route::get('/gaji', [GajiController::class, 'indexKaryawan'])->name('karyawan.gaji');
        Route::get('/gaji/cetak/{id}', [GajiController::class, 'cetakPdf'])->name('karyawan.gaji.cetak');
    });

    // --- AREA KABID ---
    Route::prefix('kabid')->group(function () {
        Route::get('/dashboard', [KaryawanController::class, 'dashboardKabid'])->name('kabid.dashboard');
        Route::get('/absensi', [AbsensiController::class, 'indexKabid'])->name('kabid.absensi');
        Route::get('/gaji', [GajiController::class, 'indexKabid'])->name('kabid.gaji');
        Route::post('/gaji/acc/{id}', [GajiController::class, 'accGaji'])->name('kabid.gaji.acc');
        Route::get('/gaji/cetak/{id}', [GajiController::class, 'cetakPdf'])->name('kabid.gaji.cetak');
        
        // RUTE BARU: Cetak Laporan untuk Kabid
        Route::get('/cetak-laporan', [KaryawanController::class, 'cetakLaporan'])->name('kabid.cetak.laporan');
    });

    Route::get('/debug-jabatan', function() {
        return \App\Models\Karyawan::distinct()->pluck('kode_jabatan');
    });
});