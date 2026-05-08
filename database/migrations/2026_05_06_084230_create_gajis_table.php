<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji; // Pastikan Model Gaji sudah dibuat

class GajiController extends Controller
{
    public function index()
    {
        // Mengambil semua data gaji dari database
        $semuaGaji = Gaji::all(); 
        
        // Mengirim data ke view admin/penggajian.blade.php
        return view('admin.penggajian', compact('semuaGaji'));
    }
}