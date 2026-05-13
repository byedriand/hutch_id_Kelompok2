@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="mb-0">Tambah Pelanggan Baru</h2>
    <small class="text-muted">Simpan data pelanggan agar bisa dipilih saat membuat PO.</small>
</div>

<form action="{{ route('pelanggan.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nama Pelanggan</label>
        <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" placeholder="Contoh: PT. Jaya Sentosa">
        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat') }}</textarea>
        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon') }}" class="form-control @error('telepon') is-invalid @enderror" placeholder="0812xxxxxxx">
            @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email (opsional)</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
    </div>
</form>
@endsection
