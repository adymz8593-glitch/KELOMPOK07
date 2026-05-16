@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">Absensi Saya</h4>
                <div class="d-flex gap-2">
                    {{-- FIX: Mengarah ke rute absen masuk terpisah --}}
                    <form action="{{ route('karyawan.absen.masuk') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Absen Masuk
                        </button>
                    </form>

                    {{-- FIX: Mengarah ke rute absen pulang terpisah --}}
                    <form action="{{ route('karyawan.absen.pulang') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4 shadow-sm">
                            <i class="bi bi-box-arrow-left me-2"></i> Absen Pulang
                        </button>
                    </form>
                </div>
            </div>

            {{-- Pesan Sukses / Error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary">Riwayat Absensi</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- FIX: Diubah ke $riwayatAbsensi agar cocok dengan controller --}}
                                @forelse($riwayatAbsensi as $index => $a)
                                    <tr>
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}</td>
                                        
                                        {{-- Memastikan format jam rapi jika sudah absen --}}
                                        <td>{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '--:--' }}</td>
                                        <td>{{ $a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '--:--' }}</td>
                                        
                                        <td>
                                            @if($a->status == 'Hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($a->status == 'Telat')
                                                <span class="badge bg-warning text-dark">Telat</span>
                                            @elseif($a->status == 'Izin' || $a->status == 'Sakit')
                                                <span class="badge bg-info text-dark">{{ $a->status }}</span>
                                            @else
                                                {{-- FIX: Mengikuti enum 'Alpha' database --}}
                                                <span class="badge bg-danger">Alpha</span>
                                            @endif
                                        </td>
                                        <td>{{ $a->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data absensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection