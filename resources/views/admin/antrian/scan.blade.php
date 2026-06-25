@extends('layouts.app')
@section('title', 'Scan Barcode')

@section('content')
<h5 class="fw-bold mb-4"><i class="bi bi-upc-scan me-2 text-success"></i>Scan Barcode Pasien</h5>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card p-4">
            <label class="form-label fw-semibold">Scan / Ketik Kode Barcode</label>
            <input type="text" id="barcode-input" class="form-control form-control-lg mb-3"
                   placeholder="Arahkan scanner ke barcode..." autofocus autocomplete="off">
            <p class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Scanner USB otomatis terdeteksi. Atau ketik manual lalu tekan Enter.
            </p>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card p-4" id="result-card" style="display:none;">
            <h6 class="fw-bold mb-3 text-success">
                <i class="bi bi-person-check me-2"></i>Data Pasien Ditemukan
            </h6>
            <div class="row g-2 mb-4 small" id="result-body"></div>
            <div class="btn-group w-100" id="action-buttons"></div>
        </div>

        <div class="card p-4 text-center text-muted" id="empty-card">
            <i class="bi bi-upc fs-1 mb-2 d-block"></i>
            Hasil scan akan muncul di sini
        </div>

        <div class="alert alert-danger mt-3" id="error-card" style="display:none;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <span id="error-msg">Barcode tidak ditemukan</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let scanTimer;
let currentId = null;

document.getElementById('barcode-input').addEventListener('input', function () {
    clearTimeout(scanTimer);
    scanTimer = setTimeout(() => cariBarcode(this.value.trim()), 400);
});

document.getElementById('barcode-input').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') cariBarcode(this.value.trim());
});

function cariBarcode(kode) {
    if (!kode) return;

    fetch('/admin/antrian/scan/cari', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ barcode: kode }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.error) {
            showError(d.error);
            return;
        }
        currentId = d.id;
        showResult(d);
    });
}

function showResult(d) {
    document.getElementById('error-card').style.display  = 'none';
    document.getElementById('empty-card').style.display  = 'none';
    document.getElementById('result-card').style.display = 'block';

    document.getElementById('result-body').innerHTML = `
        <div class="col-4 text-muted">No. Antrian</div><div class="col-8 fw-bold fs-5">${d.nomor_antrian}</div>
        <div class="col-4 text-muted">Nama</div><div class="col-8 fw-semibold">${d.nama_pasien}</div>
        <div class="col-4 text-muted">Usia</div><div class="col-8">${d.usia} tahun</div>
        <div class="col-4 text-muted">Poli</div><div class="col-8">${d.poli}</div>
        <div class="col-4 text-muted">Dokter</div><div class="col-8">${d.dokter}</div>
        <div class="col-4 text-muted">Keluhan</div><div class="col-8">${d.keluhan}</div>
        <div class="col-4 text-muted">Status</div>
        <div class="col-8"><span class="badge badge-${d.status}">${d.status.charAt(0).toUpperCase()+d.status.slice(1)}</span></div>
    `;

    const btns = document.getElementById('action-buttons');
    btns.innerHTML = '';

    const actions = {
        menunggu:  [['dipanggil','info','megaphone','Panggil']],
        dipanggil: [['dilayani','primary','person-check','Layani']],
        dilayani:  [['selesai','success','check2-circle','Selesai']],
    };

    (actions[d.status] || []).forEach(([st, color, icon, label]) => {
        const btn = document.createElement('button');
        btn.className = `btn btn-${color}`;
        btn.innerHTML = `<i class="bi bi-${icon} me-2"></i>${label}`;
        btn.onclick = () => ubahStatus(currentId, st);
        btns.appendChild(btn);
    });
}

function ubahStatus(id, status) {
    fetch(`/admin/antrian/${id}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById('barcode-input').value = '';
            document.getElementById('result-card').style.display = 'none';
            document.getElementById('empty-card').style.display  = 'block';
            document.getElementById('barcode-input').focus();
        }
    });
}

function showError(msg) {
    document.getElementById('result-card').style.display = 'none';
    document.getElementById('empty-card').style.display  = 'none';
    document.getElementById('error-msg').textContent     = msg;
    document.getElementById('error-card').style.display  = 'block';
}
</script>
@endpush
