<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kabid - E-Payroll Premium</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #1e293b; --bg-body: #f4f7f6; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: #111827; }
        .sidebar { height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; width: 260px; padding: 20px 0; z-index: 100; display: flex; flex-direction: column; justify-content: space-between; }
        .sidebar-brand { padding: 0 25px 30px; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; color: white; text-decoration: none; }
        .nav-link { color: #94a3b8; padding: 12px 25px; display: flex; align-items: center; text-decoration: none; transition: 0.2s; margin: 4px 15px; border-radius: 10px; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        .main-content { margin-left: 260px; padding: 40px; }
        .welcome-banner { background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; }
        .btn-action { transition: 0.3s; border-radius: 12px; border: none; }
        .btn-action:hover { transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <a href="#" class="sidebar-brand"><i class="bi bi-wallet2 me-2 text-primary"></i> E-Payroll</a>
        <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b; letter-spacing: 1px;">Kabid Panel</div>
        <nav>
            <a href="{{ route('kabid.dashboard') }}" class="nav-link {{ Request::routeIs('kabid.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a href="{{ route('kabid.absensi') }}" class="nav-link {{ Request::routeIs('kabid.absensi') ? 'active' : '' }}"><i class="bi bi-calendar2-check-fill me-2"></i> Rekap Absensi</a>
            <a href="{{ route('kabid.gaji') }}" class="nav-link {{ Request::routeIs('kabid.gaji') ? 'active' : '' }}"><i class="bi bi-cash-stack me-2"></i> Kelola Gaji</a>
        </nav>
    </div>
    <div class="pt-3 border-top border-secondary mx-3">
        <a href="#" class="nav-link text-danger fw-bold" onclick="event.preventDefault(); confirmLogout();"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</div>

<div class="main-content">
    <div class="welcome-banner shadow-sm">
        <h1 class="fw-bold">Halo, {{ Auth::user()->name }}! 👋</h1>
        <p class="mb-0 opacity-75">Panel kontrol untuk memantau data departemen Anda.</p>
    </div>

    <div class="card border-0 shadow-sm p-4" style="border-radius: 20px;">
        <h5 class="fw-bold mb-4"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Tindakan Cepat</h5>
        <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('kabid.gaji') }}" class="btn btn-dark btn-lg px-4 py-3 btn-action shadow-sm" style="min-width: 280px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check fs-3 me-3 text-warning"></i>
                    <div class="text-start">
                        <div class="small opacity-75">Gaji & Bonus</div>
                        <div class="fw-bold text-white">Validasi Penggajian</div>
                    </div>
                </div>
            </a>
            <a href="{{ route('kabid.absensi') }}" class="btn btn-light border btn-lg px-4 py-3 btn-action shadow-sm" style="min-width: 280px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-eye fs-3 me-3 text-primary"></i>
                    <div class="text-start">
                        <div class="small opacity-75">Log Kedisiplinan</div>
                        <div class="fw-bold">Monitoring Absensi</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-primary"></i>Gaji Terakhir Diproses</h5>
            <a href="{{ route('kabid.gaji') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>Periode</th>
                        <th>Total Gaji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gajiTerakhir as $gaji)
                    <tr>
                        <td class="fw-semibold">{{ $gaji->karyawan->nama_karyawan ?? 'N/A' }}</td>
                        <td>{{ $gaji->periode_tampil ?? ($gaji->periode ?? 'Data Belum Tersedia') }}</td>
                        <td>Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $gaji->status == 'Disetujui' ? 'bg-success' : 'bg-warning' }}">
                                {{ $gaji->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Belum ada data gaji yang diproses.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmLogout() {
        Swal.fire({ 
            title: 'Ingin Keluar?', 
            text: "Anda akan menyudahi sesi ini.", 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#f43f5e', 
            confirmButtonText: 'Keluar' 
        }).then((result) => {
            if (result.isConfirmed) { document.getElementById('logout-form').submit(); }
        })
    }
</script>
</body>
</html>