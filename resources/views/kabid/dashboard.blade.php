<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kabid - Verifikasi & Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        /* Sidebar Styling Khusus Kabid */
        .sidebar { height: 100vh; background: #1a252f; color: white; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 25px; }
        .nav-link { color: rgba(255,255,255,0.6); margin-bottom: 10px; }
        .nav-link:hover, .nav-link.active { color: white; background: #2c3e50; border-radius: 5px; }
        .card-kabid { border-left: 4px solid #f6c23e; } /* Warna Kuning Emas untuk Kabid */
    </style>
</head>
<body>

<!-- Sidebar Menu Samping -->
<div class="sidebar d-flex flex-column p-3">
    <div class="text-center mb-4">
        <i class="bi bi-check2-all" style="font-size: 3rem; color: #f6c23e;"></i>
        <h5 class="mt-2 text-white">Kabid Panel</h5>
        <small class="text-white-50 text-uppercase">Otorisasi Gaji</small>
    </div>
    <hr class="text-white-50">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active"><i class="bi bi-patch-check me-2"></i> Persetujuan Gaji</a>
        </li>
        <li>
            <a href="#" class="nav-link"><i class="bi bi-clipboard-data me-2"></i> Laporan Kehadiran</a>
        </li>
        <li>
            <a href="#" class="nav-link"><i class="bi bi-file-earmark-text me-2"></i> Rekapitulasi Bulanan</a>
        </li>
    </ul>
    <hr class="text-white-50">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-warning w-100 btn-sm"><i class="bi bi-box-arrow-left me-2"></i> Logout</button>
    </form>
</div>

<!-- Konten Utama -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800">Panel Verifikasi</h2>
            <p class="text-muted small">Halo, <strong>{{ Auth::user()->username }}</strong>. Silakan tinjau pengajuan gaji hari ini.</p>
        </div>
        <div class="p-2 bg-white rounded shadow-sm border">
            <i class="bi bi-shield-check text-warning me-2"></i> <strong>Role: Kepala Bidang</strong>
        </div>
    </div>

    <!-- Alert Notifikasi Revisi -->
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div>
            Anda memiliki <strong>3 pengajuan gaji</strong> yang menunggu verifikasi untuk bulan Mei 2026.
        </div>
    </div>

    <!-- Tabel Approval (Persetujuan) -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">Daftar Tunggu Persetujuan (ACC)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Periode</th>
                            <th>Total Karyawan</th>
                            <th>Total Nominal</th>
                            <th>Status</th>
                            <th>Aksi Otorisasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mei 2026</td>
                            <td>150 Orang</td>
                            <td>Rp 450.000.000</td>
                            <td><span class="badge bg-secondary">Menunggu ACC</span></td>
                            <td>
                                <button class="btn btn-sm btn-success me-1"><i class="bi bi-check-lg"></i> Setujui</button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i> Tolak</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>