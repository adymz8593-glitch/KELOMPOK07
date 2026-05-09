@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h4 class="mb-4 fw-bold">Daftar Slip Gaji</h4>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Bulan/Tahun</th>
                                    <th>Total Gaji</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gajis as $g)
                                    <tr>
                                        <td>{{ $g->bulan }} / {{ $g->tahun }}</td>
                                        <td>Rp {{ number_format($g->total_gaji, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('admin.gaji.cetak', $g->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-download"></i> Cetak Slip
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada data gaji yang tersedia.</td>
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