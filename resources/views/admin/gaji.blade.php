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
                    <td class="px-4 py-3"><span class="fw-bold">{{ $g->bulan }} {{ $g->tahun }}</span></td>
                    <td class="text-muted">{{ $g->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($g->karyawan->nama_karyawan) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="35">
                            <span class="fw-semibold">{{ $g->karyawan->nama_karyawan }}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($g->total_gaji, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge bg-light text-success px-3 py-2 border border-success-subtle">
                            {{ $g->status ?? 'Selesai' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('admin.gaji.cetak', $g->id) }}" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <form action="{{ route('admin.gaji.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus data gaji ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
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

{{-- MODAL INPUT GAJI BARU --}}
<div class="modal fade" id="tambahGajiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Input Gaji Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.gaji.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Karyawan</label>
                        <select name="karyawan_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Karyawan</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_karyawan }} ({{ $k->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Bulan</label>
                            <select name="bulan" class="form-select" required>
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bln)
                                    <option value="{{ $bln }}" {{ date('F') == $bln ? 'selected' : '' }}>{{ $bln }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="{{ date('Y') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="gaji_pokok" class="form-control" placeholder="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-success">Tunjangan</label>
                            <input type="number" name="tunjangan" class="form-control" value="0">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold text-danger">Potongan</label>
                            <input type="number" name="potongan" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Simpan & Cairkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection