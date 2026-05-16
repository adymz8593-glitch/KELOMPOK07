<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Gaji - Kabid Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #1e293b; --bg-body: #f4f7f6; }
        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styling (Flexbox untuk mengunci posisi logout di bawah) */
        .sidebar { 
            height: 100vh; 
            background: var(--sidebar-bg); 
            color: white; 
            position: fixed; 
            width: 260px; 
            padding: 20px 0; 
            z-index: 100; 
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar-brand { padding: 0 25px 30px; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; color: white; text-decoration: none; }
        .nav-link { color: #94a3b8; padding: 12px 25px; display: flex; align-items: center; text-decoration: none; margin: 4px 15px; border-radius: 10px; transition: 0.3s; }
        .nav-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .nav-link.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
        
        /* Logout Button Style */
        .text-danger-soft { color: #f43f5e; opacity: 0.85; }
        .text-danger-soft:hover { background: rgba(244, 63, 94, 0.1) !important; color: #f43f5e !important; opacity: 1; }

        /* Main Content */
        .main-content { margin-left: 260px; padding: 40px; }
        .card { border-radius: 20px; border: none; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-success { background-color: #dcfce7; color: #166534; }
    </style>
</head>
<body>

<div class="sidebar">
    {{-- BAGIAN ATAS: Navigasi Menu --}}
    <div>
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

    {{-- BAGIAN BAWAH: Tombol Keluar (Logout) --}}
    <div class="pt-3 border-top border-secondary mx-3">
        <a href="#" class="nav-link text-danger-soft fw-bold" onclick="event.preventDefault(); confirmLogout();">
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>

<div class="main-content">
    <div class="mb-4">
        <h2 class="fw-bold mb-0">Validasi Gaji Karyawan</h2>
        <p class="text-muted">Setujui pembayaran gaji yang telah diinput oleh Admin.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 small text-uppercase">Karyawan</th>
                        <th class="text-center small text-uppercase">Periode</th>
                        <th class="text-center small text-uppercase">Total Gaji</th>
                        <th class="text-center small text-uppercase">Status</th>
                        <th class="text-end px-4 small text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gajis as $g)
                    <tr>
                        <td class="px-4">
                            <div class="fw-bold">{{ $g->karyawan->nama_karyawan }}</div>
                            <small class="text-muted">#{{ $g->karyawan->nik }}</small>
                        </td>
                        <td class="text-center">{{ $g->bulan }} {{ $g->tahun }}</td>
                        <td class="text-center fw-bold text-primary">
                            Rp {{ number_format($g->total_gaji, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($g->status == 'Pending')
                                <span class="badge badge-pending px-3 py-2 rounded-pill">Menunggu ACC</span>
                            @else
                                <span class="badge badge-success px-3 py-2 rounded-pill">Sudah Dibayar</span>
                            @endif
                        </td>
                        <td class="text-end px-4">
                            @if($g->status == 'Pending')
                                <form id="acc-form-{{ $g->id }}" action="{{ route('kabid.gaji.acc', $g->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 8px;" onclick="confirmAcc('{{ $g->id }}', '{{ $g->karyawan->nama_karyawan }}')">
                                        <i class="bi bi-check-circle me-1"></i> ACC Gaji
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-light btn-sm px-3 disabled" style="border-radius: 8px;">
                                    <i class="bi bi-check-all me-1"></i> Selesai
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data gaji yang diinput Admin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Fungsi Konfirmasi ACC Gaji
    function confirmAcc(id, nama) {
        Swal.fire({
            title: 'Validasi Gaji?',
            text: "Apakah Anda yakin ingin menyetujui (ACC) pembayaran gaji untuk " + nama + "?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('acc-form-' + id).submit();
            }
        })
    }

    // Fungsi Konfirmasi Keluar (Logout)
    function confirmLogout() {
        Swal.fire({
            title: 'Ingin Keluar?',
            text: "Anda akan menyudahi sesi masuk di panel Kabid ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Keluar',
            cancelButtonText: 'Kembali',
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        })
    }
</script>
</body>
</html>