@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Beranda Kepala Bidang</h2>
            <p class="text-muted">Pantau kedisiplinan dan setujui penggajian karyawan hari ini.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary px-3 py-2">
                <i class="bi bi-calendar3 me-2"></i> {{ date('d F Y') }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-people-fill text-primary fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Total Anggota</p>
                            <h3 class="fw-bold mb-0">{{ \App\Models\Karyawan::count() }} Orang</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-cash-stack text-warning fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Perlu Persetujuan (ACC)</p>
                            <h3 class="fw-bold mb-0">{{ \App\Models\Gaji::where('status', 'Pending')->count() }} Data</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="bi bi-check2-circle text-success fs-3"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Absensi Hari Ini</p>
                            <h3 class="fw-bold mb-0">Terpantau</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px; background-color: #f8f9fa;">
                <h5 class="fw-bold mb-4"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Tindakan Cepat</h5>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('kabid.gaji') }}" class="btn btn-dark btn-lg px-4 py-3 shadow-sm" style="border-radius: 12px;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check fs-4 me-3"></i>
                            <div class="text-start">
                                <div class="small opacity-75">Kelola Gaji</div>
                                <div class="fw-bold">Halaman ACC Gaji</div>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('kabid.absensi') }}" class="btn btn-white border btn-lg px-4 py-3 shadow-sm" style="border-radius: 12px; background: white;">
                        <div class="d-flex align-items-center text-dark">
                            <i class="bi bi-search fs-4 me-3 text-primary"></i>
                            <div class="text-start">
                                <div class="small opacity-75">Monitoring</div>
                                <div class="fw-bold">Rekap Absensi</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f4f7f6;
    }
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection