<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi - Kabid Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #1e293b; --bg-body: #f4f7f6; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styling */
        .sidebar { height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; width: 260px; padding: 20px 0; z-index: 100; }
        .sidebar-brand { padding: 0 25px 30px; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; color: white; text-decoration: none; }
        .nav-link { color: #94a3b8; padding: 12px 25px; display: flex; align-items: center; text-decoration: none; margin: 4px 15px; border-radius: 10px; transition: 0.3s; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        
        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; }
        .card { border-radius: 20px; border: none; }
        
        /* Table Styling */
        .table thead th { background-color: #f8fafc; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #64748b; border: none; }
        
        @media print {
            .sidebar, .btn, form { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="#" class="sidebar-brand"><i class="bi bi-wallet2 me-2 text-primary"></i> E-Payroll</a>
    <div class="px-4 mb-2 text-uppercase fw-bold" style="font-size: 0.7rem; color: #64748b;">KABID PANEL</div>
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
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Rekapitulasi Absensi</h2>
            <p class="text-muted">Monitoring performa kehadiran karyawan bulanan.</p>
        </div>
        <button onclick="window.print()" class="btn btn-white border shadow-sm px-4 py-2" style="border-radius: 12px; background: white;">
            <i class="bi bi-printer me-2 text-primary"></i> Cetak Laporan
        </button>
    </div>

    {{-- FILTER PERIODE --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('kabid.absensi') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Pilih Bulan</label>
                    <select name="bulan" class="form-select border-0 bg-light py-2" style="border-radius: 10px;">
                        @foreach(range(1, 12) as $m)
                            @php $mPadded = sprintf('%02d', $m); @endphp
                            <option value="{{ $mPadded }}" {{ $bulan == $mPadded ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Tahun</label>
                    <select name="tahun" class="form-select border-0 bg-light py-2" style="border-radius: 10px;">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px; background: var(--primary);">
                        <i class="bi bi-filter-circle me-2"></i> Tampilkan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4 py-3">Karyawan</th>
                        <th class="text-center">Periode</th>
                        <th class="text-center text-success">Hadir</th>
                        <th class="text-center text-warning">Telat</th>
                        <th class="text-center text-danger">Alpa</th>
                        <th class="text-end px-4">Potongan Gaji</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapAbsensi as $r)
                    <tr>
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($r->nama) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="35">
                                <div>
                                    <div class="fw-bold">{{ $r->nama }}</div>
                                    <small class="text-muted">NIK: #{{ $r->nik }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                {{ $r->periode }}
                            </span>
                        </td>
                        <td class="text-center fw-bold text-success">{{ $r->hadir }} Hari</td>
                        <td class="text-center fw-bold text-warning">{{ $r->telat }}</td>
                        <td class="text-center fw-bold text-danger">{{ $r->alpa }}</td>
                        <td class="text-end px-4 fw-bold text-danger">
                            Rp {{ number_format($r->potongan, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Data absensi tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>