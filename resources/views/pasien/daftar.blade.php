@extends('layouts.pasien')
@section('title', 'Daftar Antrian — Puskesmas Sancta Vita')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="text-center mb-4">
            <i class="bi bi-clipboard2-pulse fs-1 text-success"></i>
            <h4 class="fw-bold mt-2">Pendaftaran Antrian</h4>
            <p class="text-muted">Isi form di bawah untuk mengambil nomor antrian</p>
        </div>

        <div class="card p-4">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('pasien.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                           placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Usia <span class="text-danger">*</span></label>
                        <input type="number" name="usia" class="form-control" value="{{ old('usia') }}"
                               placeholder="Tahun" min="0" max="150" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="tel" name="no_hp" class="form-control" value="{{ old('no_hp') }}"
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Poli <span class="text-danger">*</span></label>
                    <select name="poli_id" id="poli_id" class="form-select" required>
                        <option value="">-- Pilih Poli --</option>
                        @foreach($polis as $poli)
                            <option value="{{ $poli->id }}" {{ old('poli_id') == $poli->id ? 'selected' : '' }}>
                                {{ $poli->nama_poli }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3" id="dokter-wrap" style="display:none;">
                    <label class="form-label fw-semibold">Pilih Dokter</label>
                    <select name="dokter_id" id="dokter_id" class="form-select">
                        <option value="">-- Semua Dokter --</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Keluhan <span class="text-danger">*</span></label>
                    <textarea name="keluhan" class="form-control" rows="3"
                              placeholder="Ceritakan keluhan Anda..." required>{{ old('keluhan') }}</textarea>
                </div>

                <button type="submit" class="btn btn-sv w-100 py-2 fw-semibold">
                    <i class="bi bi-check2-circle me-2"></i>Ambil Nomor Antrian
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('poli_id').addEventListener('change', function () {
    const poliId = this.value;
    const wrap   = document.getElementById('dokter-wrap');
    const sel    = document.getElementById('dokter_id');

    if (!poliId) { wrap.style.display = 'none'; return; }

    fetch(`/poli/${poliId}/dokter`)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">-- Semua Dokter --</option>';
            data.forEach(d => {
                sel.innerHTML += `<option value="${d.id}">${d.nama}${d.spesialis ? ' — ' + d.spesialis : ''}</option>`;
            });
            wrap.style.display = data.length ? 'block' : 'none';
        });
});
</script>
@endpush
