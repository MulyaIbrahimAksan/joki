@extends('layouts.app')
@section('title', 'Kelola Poli')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-building me-2 text-success"></i>Kelola Poli</h5>
    <button class="btn btn-sv btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Poli
    </button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Kode</th><th>Nama Poli</th><th>Total Hari Ini</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($polis as $p)
                <tr>
                    <td><span class="badge bg-dark">{{ $p->kode_poli }}</span></td>
                    <td class="fw-semibold">{{ $p->nama_poli }}</td>
                    <td>{{ $p->total_hari_ini ?? 0 }} antrian</td>
                    <td>
                        <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('admin.poli.destroy', $p) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus poli ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                {{-- Modal Edit --}}
                <div class="modal fade" id="modalEdit{{ $p->id }}">
                    <div class="modal-dialog"><div class="modal-content">
                        <div class="modal-header"><h6 class="modal-title">Edit Poli</h6>
                            <button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <form action="{{ route('admin.poli.update', $p) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Poli</label>
                                    <input name="nama_poli" class="form-control" value="{{ $p->nama_poli }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kode Poli</label>
                                    <input name="kode_poli" class="form-control" value="{{ $p->kode_poli }}" required>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                           {{ $p->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label">Aktif</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-sv">Simpan</button>
                            </div>
                        </form>
                    </div></div>
                </div>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada poli</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h6 class="modal-title">Tambah Poli</h6>
            <button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('admin.poli.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Poli</label>
                    <input name="nama_poli" class="form-control" placeholder="cth: Poli Umum" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kode Poli</label>
                    <input name="kode_poli" class="form-control" placeholder="cth: U (maks 10 karakter)" maxlength="10" required>
                    <div class="form-text">Kode ini jadi prefix nomor antrian, misal U001, G001</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sv">Tambah</button>
            </div>
        </form>
    </div></div>
</div>
@endsection
