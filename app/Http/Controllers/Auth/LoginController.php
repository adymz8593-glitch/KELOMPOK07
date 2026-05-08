<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $role = Auth::user()->role;

            // Redirect sesuai 3 role revisi
            return match($role) {
                'Admin' => redirect()->intended('/admin/dashboard'),
                'Kepala Bidang' => redirect()->intended('/kabid/dashboard'),
                'Karyawan' => redirect()->intended('/karyawan/dashboard'),
                default => redirect('/login'),
            };
        }

        return back()->withErrors(['username' => 'Username atau password salah!']);
    }
}