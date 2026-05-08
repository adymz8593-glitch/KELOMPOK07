<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan - Sistem Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        /* Sidebar Styling */
        .sidebar { height: 100vh; background: #4e73df; color: white; position: fixed; width: 250px; transition: all 0.3s; }
        .main-content { margin-left: 250px; padding: 25px; transition: all 0.3s; }
        .nav-link { color: rgba(255,255,255,0.8); margin-bottom: 10px; border-radius: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.15); }
        .card { border: none; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .btn-absensi { height: 100px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: bold; }
    </style>
</head>
<body>

<!-- Sidebar Menu di Samping -->
<div class="sidebar d-flex flex-column p-3">
    <div class="text-center mb-4">
        <i class="bi bi-person-badge-fill" style="font-size: 3rem;"></i>
        <h5 class="mt-2 text-white">E-Payroll</h5>
        <small class="text-white-50">Panel Karyawan</small>
    </div>
    <hr class="text-white-50">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link active"><i class="bi bi-house-door me-2"></i> Beranda</a>
        </li>
        <li>
            <a href="#" class="nav-link"><i class="bi bi-clock-history me-2"></i> Presensi Saya</a>
        </li>
        <li>
            <a href="#" class="nav-link"><i class="bi bi-file-earmark-pdf me-2"></i> Slip Gaji</a>
        </li>
        <li>
            <a href="#" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a>
        </li>
    </ul>
    <hr class="text-white-50">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger w-100"><i class="bi bi-box-arrow-left me-2"></i> Logout</button>
    </form>
</div>

<!-- Isi Konten Utama -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-gray-800">Selamat Datang, {{ Auth::user()->username }}!</h2>
            <p class="text-muted">Pantau kehadiran dan unduh slip gaji Anda di sini.</p>
        </div>
        <div class="text-end">
            <div class="p-2 bg-white rounded shadow-sm">
                <i class="bi bi-calendar3 me-2"></i> {{ date('d M Y') }}
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Riwayat Gaji -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran Gaji</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mei 2026</td>
                                    <td>Rp 4.500.000</td>
                                    <td><span class="badge bg-success">Diterima</span></td>
                                    <td><button class="btn btn-sm btn-primary"><i class="bi bi-download"></i> PDF</button></td>
                                </tr>
                                <tr>
                                    <td>April 2026</td>
                                    <td>Rp 4.500.000</td>
                                    <td><span class="badge bg-success">Diterima</span></td>
                                    <td><button class="btn btn-sm btn-primary"><i class="bi bi-download"></i> PDF</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Menu Absensi Masuk & Keluar -->
        <div class="col-lg-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Presensi Hari Ini</h6>
                </div>
                <div class="card-body text-center">
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-success w-100 btn-absensi shadow-sm">
                                <i class="bi bi-box-arrow-in-right fs-2 mb-2"></i>
                                MASUK
                                <span class="small d-block fw-normal mt-1">--:--</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-danger w-100 btn-absensi shadow-sm" disabled>
                                <i class="bi bi-box-arrow-left fs-2 mb-2"></i>
                                KELUAR
                                <span class="small d-block fw-normal mt-1">--:--</span>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted italic">Lokasi terdeteksi: Kantor Pusat</small>
                    </div>
                </div>
            </div>

            <!-- Panel Info Tambahan -->
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Info Payroll</h5>
                    <p class="small mb-0">Pastikan Anda sudah melakukan absensi keluar sebelum pukul 17:00 WIB agar kehadiran tercatat penuh.</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>