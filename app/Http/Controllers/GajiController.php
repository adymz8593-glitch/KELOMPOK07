<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index() {
    return view('admin.penggajian'); // Nama file: penggajian.blade.php
}
}