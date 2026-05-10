@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Rekapitulasi Absensi Bulanan</h2>
        <p class="text-muted">
            @if(Auth::user()->role == 'kabid')
                Monitoring performa kehadiran karyawan untuk validasi penggajian.
            @else
                Laporan performa kehadiran dan kalkulasi potongan gaji otomatis.
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        {{-- Tombol Cetak (Muncul untuk Admin & Kabid) --}}
        <button onclick="window.print()" class="btn btn-light border px-4 py-2" style="border-radius: 12px;">
            <i class="bi bi-printer me-2"></i> Cetak Laporan
        </button>
    </div>
</div>

{{-- FILTER PERIODE --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
    <div class="card-body p-4">
        {{-- Logika Route Dinamis: Admin atau Kabid --}}
        @php
            $routeTarget = Auth::user()->role == 'kabid' ? route('kabid.absensi') : route('admin.absensi');
        @endphp

        <form action="{{ $routeTarget }}" method="GET" class="row g-3 align-items-end">
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
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted text-uppercase">Tahun</label>
                <select name="tahun" class="form-select border-0 bg-light py-2" style="border-radius: 10px;">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px;">
                    <i class="bi bi-filter-circle me-2"></i> Tampilkan Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- TABEL DATA REKAP --}}
<div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 px-4 py-3">Karyawan</th>
                    <th class="border-0 text-center">Periode</th>
                    <th class="border-0 text-center text-success">Hadir</th>
                    <th class="border-0 text-center text-warning">Telat</th>
                    <th class="border-0 text-center text-danger">Alpa</th>
                    <th class="border-0 text-end px-4">Potongan Gaji</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapAbsensi as $r)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            {{-- UI Avatars untuk Foto Profil Otomatis --}}
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($r->nama) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="38">
                            <div>
                                <div class="fw-bold text-dark">{{ $r->nama }}</div>
                                <small class="text-muted">NIK: #{{ $r->nik }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge rounded-pill bg-primary-soft text-primary px-3 border border-primary-subtle">
                            {{ $r->periode }}
                        </span>
                    </td>
                    <td class="text-center fw-bold text-success">{{ $r->hadir }} <small class="text-muted">Hari</small></td>
                    <td class="text-center fw-bold text-warning">{{ $r->telat }}</td>
                    <td class="text-center fw-bold text-danger">{{ $r->alpa }}</td>
                    <td class="text-end px-4 fw-bold text-danger">
                        Rp {{ number_format($r->potongan, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x mb-2 d-block" style="font-size: 3rem; opacity: 0.2;"></i>
                        Tidak ada data ditemukan untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Badge Styling */
    .bg-primary-soft { 
        background-color: #eef2ff; 
        color: #4f46e5;
    }
    
    /* Utility */
    .text-danger { color: #f43f5e !important; }
    .text-success { color: #10b981 !important; }
    .text-warning { color: #f59e0b !important; }

    /* Print Logic */
    @media print {
        /* Sembunyikan Sidebar, Tombol, dan Form Filter saat cetak */
        nav, .sidebar, .btn, .card-body form, .navbar { 
            display: none !important; 
        }
        
        .main-content { 
            margin-left: 0 !important; 
            padding: 0 !important; 
        }

        .container {
            max-width: 100% !important;
            width: 100% !important;
        }

        .card { 
            box-shadow: none !important; 
            border: 1px solid #eee !important; 
            border-radius: 0 !important;
        }

        h2 { font-size: 20pt; }
        
        /* Pastikan tabel memenuhi halaman kertas */
        .table-responsive { overflow: visible !important; }
        table { width: 100% !important; border-collapse: collapse; }
        th, td { border: 1px solid #ddd !important; padding: 8px !important; }
    }
</style>
@endsection