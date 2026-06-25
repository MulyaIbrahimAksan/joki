@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h5 class="fw-bold mb-4">
    <i class="bi bi-speedometer2 me-2 text-success"></i>Dashboard
    <small class="text-muted fw-normal fs-6 ms-2">{{ now()->translatedFormat('l, d F Y') }}</small>
</h5>

<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label'=>'Total Hari Ini', 'val'=>$stats['total_hari_ini'], 'icon'=>'people', 'color'=>'primary'],
        ['label'=>'Menunggu',       'val'=>$stats['menunggu'],       'icon'=>'hourglass-split', 'color'=>'warning'],
        ['label'=>'Sedang Dilayani','val'=>$stats['dilayani'],       'icon'=>'activity',         'color'=>'info'],
        ['label'=>'Selesai',        'val'=>$stats['selesai'],        'icon'=>'check-circle',     'color'=>'success'],
    ];
    @endphp
    @foreach($cards as $c)
    <div class="col-6 col-md-3">
        <div class="card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-2 bg-{{ $c['color'] }} bg-opacity-10">
                    <i class="bi bi-{{ $c['icon'] }} fs-4 text-{{ $c['color'] }}"></i>
                </div>
                <div>
                    <div class="fs-2 fw-bold lh-1">{{ $c['val'] }}</div>
                    <div class="text-muted small">{{ $c['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header bg-transparent fw-semibold">
                <i class="bi bi-building me-2"></i>Status Per Poli
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($polis as $p)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $p->nama_poli }}</span>
                        <div class="d-flex gap-2">
                            <span class="badge bg-warning text-dark">{{ $p->antrian_menunggu }} menunggu</span>
                            <span class="badge bg-secondary">{{ $p->antrian_total }} total</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="bi bi-clock-history me-2"></i>Antrian Terbaru</span>
                <a href="{{ route('admin.antrian.index') }}" class="btn btn-sm btn-sv">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th><th>Nama</th><th>Poli</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($terbaru as $a)
                            <tr>
                                <td><span class="badge bg-dark">{{ $a->nomor_antrian }}</span></td>
                                <td>{{ $a->pasien->nama }}</td>
                                <td>{{ $a->poli->nama_poli }}</td>
                                <td><span class="badge badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada antrian hari ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
