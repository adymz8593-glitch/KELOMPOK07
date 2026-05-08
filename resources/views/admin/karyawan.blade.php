@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Data Karyawan</h2>
        <p class="text-muted">Kelola informasi profil dan akun login karyawan.</p>
    </div>
    <button type="button" class="btn btn-primary px-4 py-2" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
        <i class="bi bi-plus-lg me-2"></i> Tambah Karyawan
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0 px-4">Nama Karyawan</th>
                    <th class="border-0">Jabatan</th>
                    <th class="border-0">NIK</th>
                    <th class="border-0">Alamat</th>
                    <th class="border-0 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawan as $k)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama_karyawan) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="40">
                            <span class="fw-semibold">{{ $k->nama_karyawan }}</span>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-primary border border-primary-subtle px-3 py-2">{{ $k->kode_jabatan }}</span></td>
                    <td class="text-muted">#{{ $k->nik }}</td>
                    <td class="text-muted">{{ Str::limit($k->alamat, 30) }}</td>
                    <td class="text-center">
                        {{-- PERBAIKAN: Pastikan parameter ID tidak null --}}
                        <form action="{{ route('admin.karyawan.destroy', $k->id ?? 0) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light text-danger border" onclick="return confirm('Hapus data ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-people mb-2 d-block" style="font-size: 2rem; opacity: 0.3;"></i>
                        Belum ada data karyawan terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="tambahKaryawanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- PERBAIKAN: Rute store disesuaikan dengan web.php --}}
            <form action="{{ route('admin.karyawan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">NIK</label>
                            <input type="text" name="nik" class="form-control" placeholder="Contoh: 3201..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Nama Lengkap</label>
                            <input type="text" name="nama_karyawan" class="form-control" placeholder="Nama sesuai KTP" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Untuk login" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Jabatan</label>
                            <select name="kode_jabatan" class="form-select" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                <option value="Manager">Manager</option>
                                <option value="Staff">Staff</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Tahun Lahir</label>
                            <input type="number" name="tahun_lahir" class="form-control" placeholder="Contoh: 1998">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat domisili sekarang"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection