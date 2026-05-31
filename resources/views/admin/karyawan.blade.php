@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">Data Karyawan</h2>
        <p class="text-muted">Kelola informasi profil dan akun login karyawan.</p>
    </div>
    @if(Auth::user()->role === 'admin')
        <button type="button" class="btn btn-primary px-4 py-2" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Karyawan
        </button>
    @endif
</div>

{{-- Alert Sukses --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
@endif

{{-- Alert Error --}}
@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
@endif

{{-- Validasi Error --}}
@if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-x-circle me-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm p-4 border-0" style="border-radius: 20px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0 px-4">Nama Karyawan</th>
                    <th class="border-0">Jabatan</th>
                    <th class="border-0">NIK</th>
                    <th class="border-0">No. HP</th>
                    <th class="border-0">Alamat</th>
                    @if(Auth::user()->role === 'admin')
                        <th class="border-0 text-center" style="width: 140px;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($karyawans as $k)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($k->nama_karyawan) }}&background=4f46e5&color=fff" class="rounded-circle me-3" width="40">
                            <div>
                                <span class="fw-semibold d-block">{{ $k->nama_karyawan }}</span>
                                <small class="text-muted">User: {{ $k->user->username ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2" style="border-radius: 8px;">
                            {{ $k->kode_jabatan ?? 'Staff' }}
                        </span>
                    </td>
                    <td class="text-muted">#{{ $k->nik }}</td>
                    <td class="text-medium fw-medium text-dark">
                        {{ $k->no_hp ?? '-' }}
                    </td>
                    <td class="text-muted">{{ $k->alamat ? Str::limit($k->alamat, 25) : '-' }}</td>
                    
                    @if(Auth::user()->role === 'admin')
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold px-2 py-1" style="border-radius: 6px; font-size: 0.8rem;" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editKaryawanModal{{ $k->id }}">
                                    Edit
                                </button>

                                <form action="{{ route('admin.karyawan.destroy', $k->id) }}" method="POST" class="m-0" id="delete-form-{{ $k->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger text-white fw-bold px-2 py-1" style="border-radius: 6px; font-size: 0.8rem;" onclick="confirmDelete('{{ $k->id }}')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->role === 'admin' ? 6 : 5 }}" class="text-center py-5 text-muted">
                        <i class="bi bi-people mb-2 d-block" style="font-size: 2rem; opacity: 0.3;"></i>
                        Belum ada data karyawan terdaftar di bagian ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL EDIT KARYAWAN --}}
@if(Auth::user()->role === 'admin')
    @foreach($karyawans as $k)
    <div class="modal fade" id="editKaryawanModal{{ $k->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Edit Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.karyawan.update', $k->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Nomor Induk Karyawan (NIK)</label>
                                <input type="text" name="nik" class="form-control bg-light border-0 py-2" value="{{ $k->nik }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Nama Lengkap</label>
                                <input type="text" name="nama_karyawan" class="form-control bg-light border-0 py-2" value="{{ $k->nama_karyawan }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Username (Untuk Login)</label>
                                <input type="text" name="username" class="form-control bg-light border-0 py-2" value="{{ $k->user->username ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Password Baru (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="Masukkan password baru jika ingin ganti">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Jabatan / Bagian</label>
                                <select name="jabatan" class="form-select bg-light border-0 py-2" required>
                                    <optgroup label="Bidang Administrasi">
                                        <option value="Teknisi Administrasi" {{ $k->kode_jabatan == 'Teknisi Administrasi' ? 'selected' : '' }}>Teknisi Administrasi</option>
                                        <option value="Staf Administrasi" {{ $k->kode_jabatan == 'Staf Administrasi' ? 'selected' : '' }}>Staf Administrasi</option>
                                        <option value="Guru Administrasi" {{ $k->kode_jabatan == 'Guru Administrasi' ? 'selected' : '' }}>Guru Administrasi</option>
                                        <option value="Arsiparis Administrasi" {{ $k->kode_jabatan == 'Arsiparis Administrasi' ? 'selected' : '' }}>Arsiparis Administrasi</option>
                                        <option value="Sekretaris Administrasi" {{ $k->kode_jabatan == 'Sekretaris Administrasi' ? 'selected' : '' }}>Sekretaris Administrasi</option>
                                        <option value="Resepsionis Administrasi" {{ $k->kode_jabatan == 'Resepsionis Administrasi' ? 'selected' : '' }}>Resepsionis Administrasi</option>
                                        <option value="Operator Administrasi" {{ $k->kode_jabatan == 'Operator Administrasi' ? 'selected' : '' }}>Operator Administrasi</option>
                                        <option value="Data Entry Administrasi" {{ $k->kode_jabatan == 'Data Entry Administrasi' ? 'selected' : '' }}>Data Entry Administrasi</option>
                                        <option value="Logistik Administrasi" {{ $k->kode_jabatan == 'Logistik Administrasi' ? 'selected' : '' }}>Logistik Administrasi</option>
                                        <option value="Supervisor Administrasi" {{ $k->kode_jabatan == 'Supervisor Administrasi' ? 'selected' : '' }}>Supervisor Administrasi</option>
                                    </optgroup>
                                    <optgroup label="Bidang Keuangan">
                                        <option value="Teknisi Keuangan" {{ $k->kode_jabatan == 'Teknisi Keuangan' ? 'selected' : '' }}>Teknisi Keuangan</option>
                                        <option value="Staf Keuangan" {{ $k->kode_jabatan == 'Staf Keuangan' ? 'selected' : '' }}>Staf Keuangan</option>
                                        <option value="Guru Keuangan" {{ $k->kode_jabatan == 'Guru Keuangan' ? 'selected' : '' }}>Guru Keuangan</option>
                                        <option value="Akuntan Keuangan" {{ $k->kode_jabatan == 'Akuntan Keuangan' ? 'selected' : '' }}>Akuntan Keuangan</option>
                                        <option value="Kasir Keuangan" {{ $k->kode_jabatan == 'Kasir Keuangan' ? 'selected' : '' }}>Kasir Keuangan</option>
                                        <option value="Auditor Keuangan" {{ $k->kode_jabatan == 'Auditor Keuangan' ? 'selected' : '' }}>Auditor Keuangan</option>
                                        <option value="Analis Keuangan" {{ $k->kode_jabatan == 'Analis Keuangan' ? 'selected' : '' }}>Analis Keuangan</option>
                                        <option value="Perencana Keuangan" {{ $k->kode_jabatan == 'Perencana Keuangan' ? 'selected' : '' }}>Perencana Keuangan</option>
                                        <option value="Admin Keuangan" {{ $k->kode_jabatan == 'Admin Keuangan' ? 'selected' : '' }}>Admin Keuangan</option>
                                        <option value="Supervisor Keuangan" {{ $k->kode_jabatan == 'Supervisor Keuangan' ? 'selected' : '' }}>Supervisor Keuangan</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">No. HP (WhatsApp)</label>
                                <input type="text" name="no_hp" class="form-control bg-light border-0 py-2" value="{{ $k->no_hp }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted text-uppercase">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control bg-light border-0" rows="2">{{ $k->alamat }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark px-4 py-2 shadow-sm" style="border-radius: 10px;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- MODAL TAMBAH KARYAWAN --}}
@if(Auth::user()->role === 'admin')
<div class="modal fade" id="tambahKaryawanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.karyawan.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nomor Induk Karyawan (NIK)</label>
                            <input type="text" name="nik" class="form-control bg-light border-0 py-2" placeholder="Contoh: 3201..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Nama Lengkap</label>
                            <input type="text" name="nama_karyawan" class="form-control bg-light border-0 py-2" placeholder="Nama sesuai KTP" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Username (Untuk Login)</label>
                            <input type="text" name="username" class="form-control bg-light border-0 py-2" placeholder="username_karyawan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Password</label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="Min. 6 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Jabatan / Bagian</label>
                            <select name="jabatan" class="form-select bg-light border-0 py-2" required>
                                <option value="" selected disabled>Pilih Jabatan</option>
                                <optgroup label="Bidang Administrasi">
                                    <option value="Teknisi Administrasi">Teknisi Administrasi</option>
                                    <option value="Staf Administrasi">Staf Administrasi</option>
                                    <option value="Guru Administrasi">Guru Administrasi</option>
                                    <option value="Arsiparis Administrasi">Arsiparis Administrasi</option>
                                    <option value="Sekretaris Administrasi">Sekretaris Administrasi</option>
                                    <option value="Resepsionis Administrasi">Resepsionis Administrasi</option>
                                    <option value="Operator Administrasi">Operator Administrasi</option>
                                    <option value="Data Entry Administrasi">Data Entry Administrasi</option>
                                    <option value="Logistik Administrasi">Logistik Administrasi</option>
                                    <option value="Supervisor Administrasi">Supervisor Administrasi</option>
                                </optgroup>
                                <optgroup label="Bidang Keuangan">
                                    <option value="Teknisi Keuangan">Teknisi Keuangan</option>
                                    <option value="Staf Keuangan">Staf Keuangan</option>
                                    <option value="Guru Keuangan">Guru Keuangan</option>
                                    <option value="Akuntan Keuangan">Akuntan Keuangan</option>
                                    <option value="Kasir Keuangan">Kasir Keuangan</option>
                                    <option value="Auditor Keuangan">Auditor Keuangan</option>
                                    <option value="Analis Keuangan">Analis Keuangan</option>
                                    <option value="Perencana Keuangan">Perencana Keuangan</option>
                                    <option value="Admin Keuangan">Admin Keuangan</option>
                                    <option value="Supervisor Keuangan">Supervisor Keuangan</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">No. HP (WhatsApp)</label>
                            <input type="text" name="no_hp" class="form-control bg-light border-0 py-2" placeholder="0812...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted text-uppercase">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control bg-light border-0" rows="2" placeholder="Alamat domisili sekarang"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 10px;">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data karyawan dan akun loginnya akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection