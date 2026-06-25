@extends('layouts.pasien')
@section('title', 'Tiket Antrian — ' . $antreean->nomor_antrian)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card text-center p-4 mb-3" id="tiket-card">
            <div class="mb-2">
                <span class="badge bg-success px-3 py-2 fs-6">
                    <i class="bi bi-hospital me-1"></i>{{ $antreean->poli->nama_poli }}
                </span>
            </div>

            <div class="my-3">
                <p class="text-muted mb-1">Nomor Antrian Anda</p>
                <h1 class="display-3 fw-bold" style="color: #1a6b3c; letter-spacing: 4px;">
                    {{ $antreean->nomor_antrian }}
                </h1>
            </div>

            <div class="mb-3">
                <img src="data:image/png;base64,{{ $antreean->barcode_image }}"
                     alt="Barcode {{ $antreean->barcode_code }}"
                     class="img-fluid" style="max-height: 80px;">
                <p class="text-muted small mt-1">{{ $antreean->barcode_code }}</p>
            </div>

            <hr>

            <div class="text-start small">
                <div class="row g-2">
                    <div class="col-5 text-muted">Nama</div>
                    <div class="col-7 fw-semibold">{{ $antreean->pasien->nama }}</div>
                    <div class="col-5 text-muted">Dokter</div>
                    <div class="col-7">{{ $antreean->dokter?->nama ?? 'Semua Dokter' }}</div>
                    <div class="col-5 text-muted">Status</div>
                    <div class="col-7">
                        <span class="badge" id="status-badge"
                              style="background:{{ $antreean->status === 'menunggu' ? '#ffc107' : '#198754' }};
                                     color:{{ $antreean->status === 'menunggu' ? '#000' : '#fff' }};">
                            {{ ucfirst($antreean->status) }}
                        </span>
                    </div>
                    <div class="col-5 text-muted">Antrian di depan</div>
                    <div class="col-7 fw-semibold" id="antrian-depan">{{ max(0, $posisi - 1) }} orang</div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pasien.daftar') }}" class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-plus-circle me-1"></i> Daftar Lagi
            </a>
            <button onclick="window.print()" class="btn btn-sv flex-fill">
                <i class="bi bi-printer me-1"></i> Cetak Tiket
            </button>
        </div>

        <p class="text-center text-muted small mt-3">
            <i class="bi bi-info-circle me-1"></i>
            Simpan screenshot tiket ini sebagai bukti pendaftaran.
        </p>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .top-bar, .btn, p.text-muted.small { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>
@endpush

@push('scripts')
<script>
setInterval(() => {
    fetch('/status/{{ $antreean->id }}')
        .then(r => r.json())
        .then(d => {
            document.getElementById('status-badge').textContent =
                d.status.charAt(0).toUpperCase() + d.status.slice(1);
            document.getElementById('antrian-depan').textContent = d.antrian_depan + ' orang';
        });
}, 10000);
</script>
@endpush
