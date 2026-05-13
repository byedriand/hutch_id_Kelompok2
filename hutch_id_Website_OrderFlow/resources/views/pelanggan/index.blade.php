@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 class="mb-0">Daftar Pelanggan</h2>
        <small class="text-muted">Kelola data pelanggan yang dapat dipilih saat membuat PO.</small>
    </div>
    <div class="top-actions">
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Pelanggan
        </a>
    </div>
</div>

<form class="row g-2 mb-4" action="{{ route('pelanggan.index') }}" method="GET">
    <div class="col-md-4">
        <input type="text" name="cari" class="form-control" value="{{ request('cari') }}" placeholder="Cari nama pelanggan...">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-secondary w-100">
            <i class="fas fa-search me-2"></i>Cari
        </button>
    </div>
</form>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
    @forelse($pelanggan as $item)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $item->nama }}</h5>
                            <small class="text-muted">{{ $item->telepon }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ $item->pesanan_count ?? 0 }} PO</span>
                    </div>
                    <p class="card-text mb-1"><strong>Alamat:</strong> {{ $item->alamat }}</p>
                    <p class="card-text mb-0"><strong>Email:</strong> {{ $item->email ?? '-' }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 d-flex gap-2">
                    <a href="{{ route('pelanggan.edit', $item) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('pelanggan.destroy', $item) }}" method="POST" class="m-0 flex-fill">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info mb-0">Belum ada data pelanggan. Silakan tambahkan pelanggan baru.</div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $pelanggan->withQueryString()->links() }}
</div>
@endsection
