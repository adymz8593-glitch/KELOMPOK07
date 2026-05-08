@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Kelola Gaji Karyawan</h2>
        <p class="text-muted">Data pembayaran gaji bulanan dan cetak slip otomatis.</p>
    </div>
    <button type="button" class="btn btn-primary px-4 py-2" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#tambahGajiModal">
        <i class="bi bi-plus-circle me-2"></i> Input Gaji Baru
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 20px;">
    <div class="table-responsive p-4">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0 px-4">Periode</th>
                    <th class="border-0">Tanggal Cair</th>
                    <th class="border-0">Nama Karyawan</th>
                    <th class="border-0 text-end">Nominal</th>
                    <th class="border-0 text-center">Status</th>
                    <th class="border-0 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gajis as $g)
                <tr>
                    <td class="px-4 py-3"><span class="fw-bold">{{ $g->bulan }}</span></td>
                    <td class="text-muted">{{ $g->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($g->karyawan->nama_karyawan) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="35">
                            <span class="fw-semibold">{{ $g->karyawan->nama_karyawan }}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($g->total_gaji, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge bg-emerald-light text-success px-3 py-2 border border-success-subtle">
                            {{ $g->status_pembayaran ?? 'Selesai' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.gaji.cetak', $g->id) }}" class="btn btn-sm btn-outline-danger shadow-sm px-3" style="border-radius: 8px;">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Slip PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat penggajian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection