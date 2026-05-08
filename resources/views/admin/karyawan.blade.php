<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Karyawan - E-Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 250px; }
        .main-content { margin-left: 250px; padding: 25px; }
        .nav-link { color: rgba(255,255,255,0.7); margin-bottom: 5px; }
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
        <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.karyawan') }}" class="nav-link active"><i class="bi bi-people me-2"></i> Data Karyawan</a></li>
        <li><a href="{{ route('admin.absensi') }}" class="nav-link"><i class="bi bi-calendar-check me-2"></i> Rekap Absensi</a></li>
        <li><a href="{{ route('admin.gaji') }}" class="nav-link"><i class="bi bi-cash-stack me-2"></i> Kelola Gaji</a></li>
    </ul>
    <hr>
    <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-danger btn-sm w-100">Logout</button></form>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Manajemen Data Karyawan</h3>
        <button class="btn btn-primary shadow-sm"><i class="bi bi-person-plus-fill"></i> Tambah Karyawan</button>
    </div>
    
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024001</td>
                        <td>Budi Santoso</td>
                        <td>Staff IT</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>