<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kabid - E-Payroll Premium</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --sidebar-bg: #1e293b;
            --bg-body: #f4f7f6;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Inter', sans-serif; 
            color: #111827;
        }

        /* Sidebar (Konsisten dengan Admin) */
        .sidebar { 
            height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            position: fixed; 
            width: 260px; 
            padding: 20px 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 0 25px 30px;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
        }

        .nav-link { 
            color: #94a3b8; 
            padding: 12px 25px; 
            display: flex; 
            align-items: center; 
            text-decoration: none; 
            transition: 0.2s;
            margin: 4px 15px;
            border-radius: 10px;
        }

        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { background: var(--primary); color: white; }

        .main-content { margin-left: 260px; padding: 40px; }

        /* Card Styling */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .welcome-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="#" class="sidebar-brand">
        <i class="bi bi-wallet2 me-2 text-primary"></i> E-Payroll
    </a>
    
    <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b; letter-spacing: 1px;">
        Kabid Panel
    </div>

    <nav>
        <a href="{{ route('kabid.dashboard') }}" class="nav-link {{ Request::routeIs('kabid.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
        </a>
        <a href="{{ route('kabid.absensi') }}" class="nav-link {{ Request::routeIs('kabid.absensi') ? 'active' : '' }}">
            <i class="bi bi-calendar2-check-fill me-2"></i> Rekap Absensi
        </a>
        <a href="{{ route('kabid.gaji') }}" class="nav-link {{ Request::routeIs('kabid.gaji') ? 'active' : '' }}">
            <i class="bi bi-cash-stack me-2"></i> Kelola Gaji
        </a>
    </nav>

    <div style="position: absolute; bottom: 20px; width: 100%;" class="px-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger border-0 w-100 d-flex align-items-center justify-content-center">
                <i class="bi bi-box-arrow-left me-2"></i> Keluar
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Beranda Kepala Bidang</h2>
            <p class="text-muted">Pantau kedisiplinan dan validasi penggajian.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-primary shadow-sm px-3 py-2" style="border-radius: 10px;">
                <i class="bi bi-calendar3 me-2"></i> {{ date('d F Y') }}
            </span>
        </div>
    </div>

    <div class="welcome-banner shadow-sm">
        <h1 class="fw-bold">Halo, {{ Auth::user()->name }}! 👋</h1>
        <p class="mb-0 opacity-75">Terdapat <strong>{{ $gajiPending }} data gaji</strong> yang memerlukan persetujuan Anda segera.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-people-fill text-primary fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Total Anggota</p>
                        <h3 class="fw-bold mb-0">{{ $totalKaryawan }} Orang</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-cash-stack text-warning fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Perlu ACC</p>
                        <h3 class="fw-bold mb-0 text-warning">{{ $gajiPending }} Data</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-check2-circle text-success fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Hadir Hari Ini</p>
                        <h3 class="fw-bold mb-0 text-success">{{ $hadirHariIni }} Orang</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background-color: white;">
                <h5 class="fw-bold mb-4"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Tindakan Cepat</h5>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('kabid.gaji') }}" class="btn btn-dark btn-lg px-4 py-3 shadow-sm" style="border-radius: 12px; min-width: 250px;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-3 me-3 text-warning"></i>
                            <div class="text-start">
                                <div class="small opacity-75">Gaji & Bonus</div>
                                <div class="fw-bold text-white">Validasi Penggajian</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('kabid.absensi') }}" class="btn btn-white border btn-lg px-4 py-3 shadow-sm" style="border-radius: 12px; min-width: 250px; background: white;">
                        <div class="d-flex align-items-center text-dark">
                            <i class="bi bi-eye fs-3 me-3 text-primary"></i>
                            <div class="text-start">
                                <div class="small opacity-75">Log Kedisiplinan</div>
                                <div class="fw-bold">Monitoring Absensi</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>