<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Payroll System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --bg-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
        }

        .login-header h2 {
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #6b7280;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #374151;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn-login {
            background: var(--primary);
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background: var(--primary-dark);
            transform: scale(1.02);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo shadow-sm">
        <i class="bi bi-wallet2"></i>
    </div>
    
    <div class="login-header">
        <h2>Selamat Datang</h2>
        <p>Silakan masuk ke akun E-Payroll Anda</p>
    </div>

    <form action="{{ route('login') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" name="username" class="form-control border-start-0" 
                       id="username" placeholder="Masukkan username" required 
                       style="border-radius: 0 12px 12px 0;">
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" class="form-control border-start-0" 
                       id="password" placeholder="••••••••" required 
                       style="border-radius: 0 12px 12px 0;">
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger p-2" style="font-size: 12px; border-radius: 10px;">
                <i class="bi bi-exclamation-circle me-1"></i> Username atau password salah.
            </div>
        @endif

        <button type="submit" class="btn btn-login shadow-sm">
            Masuk Sekarang
        </button>
    </form>

    <div class="footer-text">
        &copy; 2026 E-Payroll System v1.0
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>