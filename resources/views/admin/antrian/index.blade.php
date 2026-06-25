@extends('layouts.app')
@section('title', 'Kelola Antrian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-list-ol me-2 text-success"></i>Antrian Hari Ini</h5>
</div>

{{-- Filter poli --}}
<div class="card mb-3 p-3">
    <form class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label small mb-1">Filter Poli</label>
            <select name="poli_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Poli</option>
                @foreach($polis as $p)
                    <option value="{{ $p->id }}" {{ $poliId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_poli }}
                    </option>
                @endforeach
            </select>
        </div>
        @if($poliId)
        <div class="col-auto">
            <form action="{{ route('admin.antrian.reset') }}" method="POST"
                  onsubmit="return confirm('Reset semua antrian menunggu di poli ini?')">
                @csrf
                <input type="hidden" name="poli_id" value="{{ $poliId }}">
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Antrian
                </button>
            </form>
        </div>
        @endif
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Antrian</th>
                    <th>Nama Pasien</th>
                    <th>Usia</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Keluhan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antreans as $a)
                <tr id="row-{{ $a->id }}">
                    <td><span class="badge bg-dark fs-6">{{ $a->nomor_antrian }}</span></td>
                    <td class="fw-semibold">{{ $a->pasien->nama }}</td>
                    <td>{{ $a->pasien->usia }} thn</td>
                    <td>{{ $a->poli->nama_poli }}</td>
                    <td>{{ $a->dokter?->nama ?? '-' }}</td>
                    <td class="small text-muted" style="max-width:180px;">
                        <span class="text-truncate d-block">{{ $a->pasien->keluhan }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $a->status }}" id="badge-{{ $a->id }}">
                            {{ ucfirst($a->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            @if($a->status === 'menunggu')
                            <button class="btn btn-outline-info" onclick="ubahStatus({{ $a->id }}, 'dipanggil')">
                                <i class="bi bi-megaphone"></i> Panggil
                            </button>
                            @elseif($a->status === 'dipanggil')
                            <button class="btn btn-outline-primary" onclick="ubahStatus({{ $a->id }}, 'dilayani')">
                                <i class="bi bi-person-check"></i> Layani
                            </button>
                            <button class="btn btn-outline-warning" onclick="ubahStatus({{ $a->id }}, 'menunggu')">
                                <i class="bi bi-arrow-counterclockwise"></i> Stop
                            </button>
                            @elseif($a->status === 'dilayani')
                            <button class="btn btn-outline-success" onclick="ubahStatus({{ $a->id }}, 'selesai')">
                                <i class="bi bi-check2"></i> Selesai
                            </button>
                            @endif
                            @if(!in_array($a->status, ['selesai','batal']))
                            <button class="btn btn-outline-danger" onclick="ubahStatus({{ $a->id }}, 'batal')">
                                <i class="bi bi-x-lg"></i> Batal
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada antrian hari ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($antreans->hasPages())
    <div class="card-footer">{{ $antreans->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function ubahStatus(id, status) {
    fetch(`/admin/antrian/${id}/status`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
    });
}

// Soketi realtime listener
if (typeof Echo !== 'undefined') {
    Echo.channel('antreean').listen('.status.updated', e => {
        const badge = document.getElementById('badge-' + e.id);
        if (badge) {
            badge.textContent = e.status.charAt(0).toUpperCase() + e.status.slice(1);
            badge.className = 'badge badge-' + e.status;
        }
    });
}
</script>
@endpush
