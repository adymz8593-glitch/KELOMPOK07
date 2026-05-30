<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Payroll Premium</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --sidebar-bg: #1e293b;
            --bg-body: #f9fafb;
        }

        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; margin: 0; color: #111827; }

        .sidebar { 
            height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; width: 260px; 
            padding: 20px 0; box-shadow: 4px 0 10px rgba(0,0,0,0.05); z-index: 100; display: flex; flex-direction: column; 
        }

        .sidebar-brand {
            padding: 0 25px 30px; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; 
            color: white; text-decoration: none;
        }

        .nav-wrapper { flex-grow: 1; overflow-y: auto; }

        .nav-link { 
            color: #94a3b8; padding: 12px 25px; display: flex; align-items: center; text-decoration: none; 
            transition: 0.2s; margin: 4px 15px; border-radius: 10px;
        }

        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }

        .main-content { margin-left: 260px; padding: 40px; }
        .card { border-radius: 20px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .logout-section { padding: 20px; border-top: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>

@auth
<div class="sidebar">
    {{-- Ambil Role dengan Null-Coalesce agar tidak error --}}
    @php
        $user = auth()->user();
        $role = $user->role ?? 'guest';
        $brandRoute = match($role) {
            'admin' => 'admin.dashboard',
            'kabid' => 'kabid.dashboard',
            default => 'karyawan.dashboard',
        };
    @endphp

    <a href="{{ route($brandRoute) }}" class="sidebar-brand">
        <i class="bi bi-wallet2 me-2 text-primary"></i> E-Payroll
    </a>
    
    <div class="nav-wrapper">
        <nav>
            {{-- Menu Admin --}}
            @if($role == 'admin')
                <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b; letter-spacing: 1px;">Admin Panel</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
                <a href="{{ route('admin.karyawan.index') }}" class="nav-link {{ Request::is('admin/karyawan*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Data Karyawan</a>
                <a href="{{ route('admin.absensi') }}" class="nav-link {{ Request::is('admin/absensi*') ? 'active' : '' }}"><i class="bi bi-calendar2-check-fill"></i> Rekap Absensi</a>
                <a href="{{ route('admin.gaji') }}" class="nav-link {{ Request::is('admin/gaji*') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i> Kelola Gaji</a>
            @endif

            {{-- Menu Kabid --}}
            @if($role == 'kabid')
                <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b; letter-spacing: 1px;">Kabid Panel</div>
                <a href="{{ route('kabid.dashboard') }}" class="nav-link {{ Request::is('kabid/dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
                <a href="{{ route('admin.karyawan.index') }}" class="nav-link {{ Request::is('admin/karyawan*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Monitoring Anggota</a>
                <a href="{{ route('kabid.absensi') }}" class="nav-link {{ Request::is('kabid/absensi*') ? 'active' : '' }}"><i class="bi bi-calendar2-check-fill"></i> Rekap Absensi</a>
                <a href="{{ route('kabid.gaji') }}" class="nav-link {{ Request::is('kabid/gaji*') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i> Validasi Gaji</a>
            @endif

            {{-- Menu Karyawan --}}
            @if($role == 'karyawan')
                <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b; letter-spacing: 1px;">Menu Karyawan</div>
                <a href="{{ route('karyawan.dashboard') }}" class="nav-link {{ Request::is('karyawan/dashboard') ? 'active' : '' }}"><i class="bi bi-house-door-fill"></i> Dashboard</a>
                <a href="{{ route('karyawan.absensi') }}" class="nav-link {{ Request::is('karyawan/absensi*') ? 'active' : '' }}"><i class="bi bi-calendar-check-fill"></i> Absensi Saya</a>
                <a href="{{ route('karyawan.gaji') }}" class="nav-link {{ Request::is('karyawan/gaji*') ? 'active' : '' }}"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Slip Gaji</a>
            @endif
        </nav>
    </div>

    <div class="logout-section">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger border-0 w-100 d-flex align-items-center justify-content-center fw-bold" style="background: rgba(244, 63, 94, 0.1); border-radius: 12px; padding: 10px;">
                <i class="bi bi-box-arrow-left me-2"></i> Keluar
            </button>
        </form>
    </div>
</div>
@endauth

<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>