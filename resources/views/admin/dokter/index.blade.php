@extends('layouts.app')
@section('title', 'Kelola Dokter')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-success"></i>Kelola Dokter</h5>
    <button class="btn btn-sv btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Dokter
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nama</th><th>Spesialis</th><th>Poli</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($dokters as $d)
                <tr>
                    <td class="fw-semibold">{{ $d->nama }}</td>
                    <td class="text-muted">{{ $d->spesialis ?? '-' }}</td>
                    <td><span class="badge bg-success bg-opacity-75">{{ $d->poli->nama_poli }}</span></td>
                    <td>
                        <span class="badge {{ $d->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $d->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('admin.dokter.destroy', $d) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus dokter ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="modalEdit{{ $d->id }}">
                    <div class="modal-dialog"><div class="modal-content">
                        <div class="modal-header"><h6 class="modal-title">Edit Dokter</h6>
                            <button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <form action="{{ route('admin.dokter.update', $d) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input name="nama" class="form-control" value="{{ $d->nama }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Spesialis</label>
                                    <input name="spesialis" class="form-control" value="{{ $d->spesialis }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Poli</label>
                                    <select name="poli_id" class="form-select" required>
                                        @foreach($polis as $p)
                                        <option value="{{ $p->id }}" {{ $d->poli_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_poli }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           {{ $d->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label">Aktif</label>
                                </div>
                            </div>
                            <div class="modal-footer"><button class="btn btn-sv">Simpan</button></div>
                        </form>
                    </div></div>
                </div>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada dokter</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h6 class="modal-title">Tambah Dokter</h6>
            <button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('admin.dokter.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Dokter</label>
                    <input name="nama" class="form-control" placeholder="dr. Nama Dokter" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Spesialis</label>
                    <input name="spesialis" class="form-control" placeholder="cth: Umum, Gigi, Anak">
                </div>
                <div class="mb-3">
                    <label class="form-label">Poli</label>
                    <select name="poli_id" class="form-select" required>
                        <option value="">-- Pilih Poli --</option>
                        @foreach($polis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_poli }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-sv">Tambah</button></div>
        </form>
    </div></div>
</div>
@endsection
