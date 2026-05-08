<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index() {
    return view('admin.rekap_absensi'); // Nama file: rekap_absensi.blade.php
}
}