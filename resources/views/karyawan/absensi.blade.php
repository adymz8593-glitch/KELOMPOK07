@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">Absensi Saya</h4>
                <div class="d-flex gap-2">
                    {{-- Rute absen masuk --}}
                    <form action="{{ route('karyawan.absen.masuk') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Absen Masuk
                        </button>
                    </form>

                    {{-- Rute absen pulang --}}
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
                                @forelse($riwayatAbsensi as $index => $a)
                                    <tr>
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}</td>
                                        
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
                                                <span class="badge bg-danger">Alpha</span>
                                            @endif
                                        </td>
                                        
                                        {{-- LOGIKA FORMAT JAM DAN MENIT SECARA DINAMIS --}}
                                        <td>
                                            @if($a->keterangan && !str_contains($a->keterangan, '-'))
                                                {{ $a->keterangan }}
                                            @else
                                                @if($a->status == 'Telat' && $a->jam_masuk)
                                                    @php
                                                        $jamMasuk = \Carbon\Carbon::parse($a->jam_masuk);
                                                        $batasMasuk = \Carbon\Carbon::parse($a->tanggal . ' 08:00:00');
                                                        
                                                        // Hitung total menit abs (agar tidak minus)
                                                        $totalMenit = abs($jamMasuk->diffInMinutes($batasMasuk));
                                                        
                                                        // Konversi ke jam dan sisa menit
                                                        $jam = floor($totalMenit / 60);
                                                        $menit = $totalMenit % 60;
                                                    @endphp

                                                    @if($jam > 0)
                                                        Terlambat {{ $jam }} Jam {{ $menit }} Menit
                                                    @else
                                                        Terlambat {{ $menit }} Menit
                                                    @endif
                                                @elseif($a->status == 'Hadir')
                                                    Tepat Waktu
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
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