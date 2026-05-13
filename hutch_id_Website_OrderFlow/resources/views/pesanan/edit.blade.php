@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Pesanan</h1>
            <small class="text-muted">{{ $pesanan->nomor_po }}</small>
        </div>
        <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pesanan.update', $pesanan) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nomor PO</label>
                    <input type="text" class="form-control" value="{{ $pesanan->nomor_po }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Pengiriman</label>
                    <input type="date" name="tanggal_pengiriman" class="form-control @error('tanggal_pengiriman') is-invalid @enderror" value="{{ old('tanggal_pengiriman', $pesanan->tanggal_pengiriman->format('Y-m-d')) }}" required>
                    @error('tanggal_pengiriman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="4">{{ old('catatan', $pesanan->catatan) }}</textarea>
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
@endsection
