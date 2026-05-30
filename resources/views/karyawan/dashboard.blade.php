@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        {{-- CARD WELCOME KARYAWAN --}}
        <div class="col-12 mb-4">
            <div class="card border-0 bg-primary text-white p-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(45deg, #0d6efd, #0dcaf0);">
                <div class="d-flex align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1">Halo, {{ $profil->nama_karyawan }}! 👋</h2>
                        <p class="mb-0 opacity-75">NIK: {{ $profil->nik }} | Jabatan: {{ $profil->kode_jabatan }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD PRESENSI HARI INI --}}
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Presensi Hari Ini</h5>
                <hr>
                <p class="text-muted small text-uppercase fw-bold text-secondary">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>

                {{-- KONDISI 1: Belum Absen Masuk --}}
                @if(!$sudahAbsen)
                    <div class="alert alert-warning border-0 small" style="border-radius: 12px;">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> Kamu belum melakukan absen masuk hari ini.
                    </div>
                    <form action="{{ route('karyawan.absen.masuk') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow" style="border-radius: 15px;">
                            <i class="bi bi-fingerprint me-2"></i> KLIK UNTUK HADIR
                        </button>
                    </form>

                {{-- KONDISI 2: Sudah Absen Masuk, Tapi Belum Absen Pulang --}}
                @elseif($sudahAbsen && is_null($sudahAbsen->jam_pulang))
                    <div class="alert alert-info border-0 small" style="border-radius: 12px;">
                        <i class="bi bi-info-circle-fill me-2"></i> Sudah absen masuk. Jangan lupa absen pulang sebelum meninggalkan tempat kerja!
                    </div>
                    <form action="{{ route('karyawan.absen.pulang') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow" style="border-radius: 15px;">
                            <i class="bi bi-box-arrow-left me-2"></i> KLIK UNTUK PULANG
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <p class="text-muted small mb-1">Status: <span class="badge bg-warning text-dark">{{ $sudahAbsen->status }}</span></p>
                        <div class="badge bg-light text-dark p-2 w-100" style="font-size: 0.9rem; border-radius: 10px;">
                            <i class="bi bi-clock me-1 text-primary"></i> Jam Masuk: 
                            {{ $sudahAbsen->jam_masuk ? date('H:i', strtotime($sudahAbsen->jam_masuk)) : $sudahAbsen->created_at->format('H:i') }} WIB
                        </div>
                    </div>

                {{-- KONDISI 3: Sudah Absen Masuk & Pulang Lengkap --}}
                @else
                    <div class="text-center py-3">
                        <div class="display-6 text-success mb-2">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-2">Absensi Hari Ini Selesai</h5>
                        <p class="text-muted small mb-3">Status: <span class="badge bg-success">{{ $sudahAbsen->status }}</span></p>
                        
                        <div class="badge bg-light text-dark p-2 w-100 mb-2" style="font-size: 0.9rem; border-radius: 10px;">
                            <i class="bi bi-clock me-1 text-success"></i> Jam Masuk: 
                            {{ date('H:i', strtotime($sudahAbsen->jam_masuk)) }} WIB
                        </div>
                        <div class="badge bg-light text-dark p-2 w-100" style="font-size: 0.9rem; border-radius: 10px;">
                            <i class="bi bi-box-arrow-left me-1 text-danger"></i> Jam Pulang: 
                            {{ date('H:i', strtotime($sudahAbsen->jam_pulang)) }} WIB
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD RIWAYAT GAJI TERAKHIR --}}
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i>Gaji Terakhir</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th>BULAN</th>
                                <th>TOTAL DITERIMA</th>
                                <th class="text-center">SLIP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatGaji as $g)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $g->bulan }} {{ $g->tahun }}</span></td>
                                <td class="fw-bold text-primary">Rp {{ number_format($g->total_gaji, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    {{-- FIX: Dialihkan ke route cetak slip ber-prefix karyawan/umum demi keamanan hak akses role --}}
                                    <a href="{{ route('karyawan.gaji.cetak', $g->id) }}" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">
                                    <i class="bi bi-wallet2 d-block mb-2 opacity-50" style="font-size: 1.5rem;"></i>
                                    Belum ada data pembayaran gaji.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection