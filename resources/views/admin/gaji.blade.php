@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Kelola Gaji Karyawan</h2>
        <p class="text-muted">
            {{ Auth::user()->role == 'kabid' ? 'Validasi dan setujui gaji karyawan.' : 'Data pembayaran gaji bulanan dan cetak slip otomatis.' }}
        </p>
    </div>
    
    {{-- TOMBOL INPUT HANYA UNTUK ADMIN --}}
    @if(Auth::user()->role == 'admin')
    <button type="button" class="btn btn-primary px-4 py-2" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#tambahGajiModal">
        <i class="bi bi-plus-circle me-2"></i> Input Gaji Baru
    </button>
    @endif
</div>

{{-- NOTIFIKASI ERROR VALIDASI --}}
@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- NOTIFIKASI SUKSES --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
    </div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 20px;">
    <div class="table-responsive p-4">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0 px-4">Periode</th>
                    <th class="border-0">Nama Karyawan</th>
                    <th class="border-0 text-end">Total Gaji</th>
                    <th class="border-0 text-center">Status</th>
                    <th class="border-0 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gajis as $g)
                <tr>
                    <td class="px-4 py-3"><span class="fw-bold">{{ $g->bulan }} {{ $g->tahun }}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($g->karyawan->nama_karyawan) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="35">
                            <div>
                                <span class="fw-semibold d-block">{{ $g->karyawan->nama_karyawan }}</span>
                                <small class="text-muted">NIK: {{ $g->karyawan->nik }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($g->total_gaji, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($g->status == 'Pending')
                            <span class="badge bg-light text-warning px-3 py-2 border border-warning-subtle rounded-pill">
                                <i class="bi bi-clock-history me-1"></i> Menunggu ACC
                            </span>
                        @else
                            <span class="badge bg-light text-success px-3 py-2 border border-success-subtle rounded-pill">
                                <i class="bi bi-check-circle me-1"></i> Dibayar
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            
                            {{-- AKSI KHUSUS KABID (TOMBOL ACC) --}}
                            @if(Auth::user()->role == 'kabid' && $g->status == 'Pending')
                            <form action="{{ route('kabid.gaji.acc', $g->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success px-3" style="border-radius: 8px;">
                                    <i class="bi bi-check-lg me-1"></i> ACC Gaji
                                </button>
                            </form>
                            @endif

                            {{-- AKSI KHUSUS ADMIN (CETAK & HAPUS) --}}
                            @if(Auth::user()->role == 'admin')
                            <a href="{{ route('admin.gaji.cetak', $g->id) }}" class="btn btn-sm btn-outline-danger {{ $g->status == 'Pending' ? 'disabled' : '' }}" title="Cetak PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            <form action="{{ route('admin.gaji.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus data gaji ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border" style="border-radius: 8px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat penggajian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL INPUT GAJI (HANYA UNTUK ADMIN) --}}
@if(Auth::user()->role == 'admin')
<div class="modal fade" id="tambahGajiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Input Gaji Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.gaji.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Pilih Karyawan</label>
                        <select name="karyawan_id" class="form-select border-0 bg-light py-2" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_karyawan }} ({{ $k->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Bulan</label>
                            <select name="bulan" class="form-select border-0 bg-light py-2" required>
                                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                    <option value="{{ $bulan }}" {{ $bulan == date('F') ? 'selected' : '' }}>{{ $bulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Tahun</label>
                            <input type="number" name="tahun" class="form-control border-0 bg-light py-2" value="{{ date('Y') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light">Rp</span>
                            <input type="number" name="gaji_pokok" class="form-control border-0 bg-light py-2" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Tunjangan</label>
                            <input type="number" name="tunjangan" class="form-control border-0 bg-light py-2" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold text-muted">Potongan</label>
                            <input type="number" name="potongan" class="form-control border-0 bg-light py-2" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan & Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection