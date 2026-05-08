<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - E-Payroll Premium</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --sidebar-bg: #1e293b;
            --bg-body: #f9fafb;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Inter', sans-serif; 
            margin: 0;
            color: #111827;
        }

        /* Sidebar Styling */
        .sidebar { 
            height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            position: fixed; 
            width: 260px; 
            padding: 20px 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
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
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .bg-indigo-light { background: #eef2ff; color: #4f46e5; }
        .bg-emerald-light { background: #ecfdf5; color: #10b981; }
        .bg-amber-light { background: #fffbeb; color: #f59e0b; }
        .bg-rose-light { background: #fff1f2; color: #f43f5e; }

        /* Welcome Section */
        .welcome-banner {
            background: var(--primary);
            color: white;
            padding: 30px;
            border-radius: 24px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner h1 { font-weight: 700; font-size: 1.75rem; }
        .welcome-banner p { opacity: 0.8; margin-bottom: 0; }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="#" class="sidebar-brand">
        <i class="bi bi-wallet2 me-2 text-primary"></i> E-Payroll
    </a>
    
    <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b; letter-spacing: 1px;">Admin Panel</div>
    <nav>
        <a href="{{ route('admin.dashboard') }}" class="nav-link active">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="{{ route('admin.karyawan') }}" class="nav-link">
            <i class="bi bi-people-fill"></i> Data Karyawan
        </a>
        <a href="{{ route('admin.absensi') }}" class="nav-link">
            <i class="bi bi-calendar2-check-fill"></i> Rekap Absensi
        </a>
        <a href="{{ route('admin.gaji') }}" class="nav-link">
            <i class="bi bi-cash-stack"></i> Kelola Gaji
        </a>
    </nav>

    <div style="position: absolute; bottom: 20px; width: 100%;" class="px-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger border-0 w-100 d-flex align-items-center justify-content-center fw-600" style="background: rgba(244, 63, 94, 0.1);">
                <i class="bi bi-box-arrow-left me-2"></i> Keluar
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    
    <div class="welcome-banner shadow-sm">
        <h1>Halo, {{ Auth::user()->name }}! 👋</h1>
        <p>Selamat datang di sistem E-Payroll. Semua data di bawah ini diperbarui secara otomatis dari database.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-indigo-light">
                    <i class="bi bi-people"></i>
                </div>
                <h6 class="text-muted mb-1">Total Karyawan</h6>
                <h3 class="fw-bold mb-0">{{ $totalKaryawan }} Orang</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-emerald-light">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <h6 class="text-muted mb-1">Hadir Hari Ini</h6>
                <h3 class="fw-bold mb-0">{{ $hadirHariIni }} Orang</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-rose-light">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <h6 class="text-muted mb-1">Total Pengeluaran Gaji</h6>
                <h3 class="fw-bold mb-0">Rp {{ number_format($totalGaji, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Karyawan Baru Terdaftar</h5>
                    <a href="{{ route('admin.karyawan') }}" class="btn btn-sm btn-light border text-primary fw-bold">Kelola Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">Nama</th>
                                <th class="border-0">Jabatan</th>
                                <th class="border-0">NIK</th>
                                <th class="border-0">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawanTerbaru as $k)
                            <tr>
                                <td>
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama) }}&background=4f46e5&color=fff" class="rounded-circle me-2" width="30"> 
                                    {{ $k->nama }}
                                </td>
                                <td><span class="badge bg-light text-dark border px-3 py-2">{{ $k->jabatan }}</span></td>
                                <td class="text-muted">#{{ $k->nik }}</td>
                                <td class="small text-muted">{{ $k->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data karyawan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>