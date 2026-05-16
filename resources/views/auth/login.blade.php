<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Payroll System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --btn-navy: #111c43; /* Warna navy pekat sesuai foto target */
            --bg-body: #f3f4f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        /* Container Pembungkus Box */
        .login-wrapper {
            max-width: 820px;
            width: 100%;
        }

        /* Kartu Utama Box Split */
        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: none;
        }

        /* SISI KIRI: Banner Instansi dengan Lapisan Kegelapan Konstan */
        .login-left {
            /* Menggunakan gambar kolaborasi kantor premium dengan perpaduan warna linier gelap agar teks kontras */
            background: linear-gradient(rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.78)), 
                        url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?q=80&w=600&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.3;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3); /* Bayangan teks tambahan agar makin tajam */
        }

        .brand-desc {
            font-size: 0.88rem;
            color: #f1f5f9;
            line-height: 1.6;
            font-weight: 400;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        /* SISI KANAN: Form Workspace */
        .login-right {
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header h2 {
            font-weight: 700;
            color: #111827;
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .login-header p {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 0.85rem;
        }

        /* Label Input */
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 6px;
        }

        /* Struktur Kotak Input Menyatu */
        .input-group {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            transition: all 0.2s ease;
        }

        .input-group-text {
            background-color: transparent;
            border: none;
            color: #9ca3af;
            padding-left: 15px;
            padding-right: 10px;
        }

        .form-control {
            background-color: transparent;
            border: none;
            padding: 12px 14px;
            font-size: 0.9rem;
            color: #111827;
        }

        .form-control:focus {
            background-color: transparent;
            box-shadow: none;
        }

        /* Efek Fokus Melingkar pada Input Group */
        .input-group:focus-within {
            border-color: #2563eb;
            background-color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* Gaya Khusus untuk Fitur Centang */
        .form-check-label {
            font-size: 0.85rem;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }
        
        .form-check-input:checked {
            background-color: var(--btn-navy);
            border-color: var(--btn-navy);
        }

        /* Tombol Navy Pekat */
        .btn-login {
            background: var(--btn-navy);
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 15px;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #091026;
            color: white;
        }

        .footer-text {
            margin-top: 25px;
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="card login-card">
        <div class="row g-0 align-items-stretch">
            
            {{-- SISI KIRI: BANNER INFORMASI DENGAN TEKS JELAS --}}
            <div class="col-md-5 d-none d-md-block">
                <div class="login-left">
                    <div class="brand-title">E-Payroll Portal</div>
                    <div class="brand-desc">
                        Sistem Informasi Manajemen absensi dan penggajian karyawan yang modern dan efiesien berbasis web.
                    </div>
                </div>
            </div>

            {{-- SISI KANAN: FORM LOGIN UTAMA --}}
            <div class="col-md-7 col-12">
                <div class="login-right">
                    <div class="login-header">
                        <h2>Selamat Datang</h2>
                        <p>Silakan masuk ke akun E-Payroll Anda</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        {{-- INPUT USERNAME --}}
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" 
                                       id="username" placeholder="Masukkan username anda" 
                                       value="{{ old('username') }}" required autofocus>
                            </div>
                        </div>

                        {{-- INPUT PASSWORD --}}
                        <div class="mb-2">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" 
                                       id="password" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        {{-- FITUR CENTANG TAMPILKAN SANDI --}}
                        <div class="mb-4 form-check text-start">
                            <input type="checkbox" class="form-check-input" id="show-password-check">
                            <label class="form-check-label" for="show-password-check">Tampilkan Sandi</label>
                        </div>

                        {{-- NOTIFIKASI ERROR JIKA DATA SALAH --}}
                        @if($errors->any())
                            <div class="alert alert-danger border-0 p-2 d-flex align-items-center mb-3" style="font-size: 12px; border-radius: 8px; background-color: #fff1f2; color: #e11d48;">
                                <i class="bi bi-exclamation-circle-fill me-2"></i> 
                                <div>Username atau password salah.</div>
                            </div>
                        @endif

                        {{-- TOMBOL SUBMIT --}}
                        <button type="submit" class="btn btn-login">
                            Masuk Dashboard
                        </button>
                    </form>

                    <div class="footer-text">
                        &copy; 2026 E-Payroll System v1.0
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK AKSI CENTANG PASSWORD --}}
<script>
    document.getElementById('show-password-check').addEventListener('change', function() {
        const passwordInput = document.getElementById('password');
        if (this.checked) {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>