<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - E-Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 25px; }
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 5px; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.1); }
        .nav-link.active { background: #4e73df; color: white; border-radius: 8px; }
        .category-label { font-size: 0.7rem; font-weight: bold; color: rgba(255,255,255,0.4); padding: 0 15px; text-transform: uppercase; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <h4 class="text-center mb-4">E-Payroll</h4>
    <hr>
    <p class="category-label mb-2">Menu Admin</p>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="{{ route('admin.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</a></li>
        <li><a href="{{ route('admin.karyawan') }}" class="nav-link"><i class="bi bi-people me-2"></i> Data Karyawan</a></li>
        <li><a href="{{ route('admin.absensi') }}" class="nav-link"><i class="bi bi-calendar-check me-2"></i> Rekap Absensi</a></li>
        <li><a href="{{ route('admin.gaji') }}" class="nav-link"><i class="bi bi-cash-stack me-2"></i> Kelola Gaji</a></li>
    </ul>
    <hr>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </form>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Ringkasan Administrator</h3>
        <span class="badge bg-primary p-2">Role: Admin</span>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0" style="border-left: 5px solid #4e73df !important;">
                <h6 class="text-primary fw-bold">Total Karyawan</h6>
                <p class="h3 mb-0">150 Orang</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0" style="border-left: 5px solid #1cc88a !important;">
                <h6 class="text-success fw-bold">Hadir Hari Ini</h6>
                <p class="h3 mb-0">142 Orang</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0" style="border-left: 5px solid #f6c23e !important;">
                <h6 class="text-warning fw-bold">Menunggu ACC</h6>
                <p class="h3 mb-0">5 Berkas</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>