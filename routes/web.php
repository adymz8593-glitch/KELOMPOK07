<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GajiController;

// 1. Halaman Awal - Langsung lempar ke Login
Route::get('/', function () {
    return redirect('/login');
});

// 2. Rute Autentikasi
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// 3. Kelompok Rute Berdasarkan Role (Proteksi Auth)
Route::middleware(['auth'])->group(function () {

    // --- Rute Khusus ADMIN ---
    Route::prefix('admin')->group(function () {
        // Dashboard Utama
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // Menu Admin (Pastikan nama route ini dipanggil di href sidebar)
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('admin.karyawan');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');
        Route::get('/gaji', [GajiController::class, 'index'])->name('admin.gaji');
    });

    // --- Rute Khusus KEPALA BIDANG ---
    Route::prefix('kabid')->group(function () {
        Route::get('/dashboard', function () {
            return view('kabid.dashboard'); // Pastikan ada file resources/views/kabid/dashboard.blade.php
        })->name('kabid.dashboard');
    });

    // --- Rute Khusus KARYAWAN ---
    Route::prefix('karyawan')->group(function () {
        Route::get('/dashboard', function () {
            return view('karyawan.dashboard'); // Pastikan ada file resources/views/karyawan/dashboard.blade.php
        })->name('karyawan.dashboard');
    });
});