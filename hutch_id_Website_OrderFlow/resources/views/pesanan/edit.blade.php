@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Edit Pesanan</h1>
            <div class="text-muted">Nomor PO: <strong>{{ $pesanan->nomor_po }}</strong></div>
            <div class="text-muted">Status: <span class="badge bg-info text-dark text-uppercase">{{ str_replace('_', ' ', $pesanan->status) }}</span></div>
        </div>
        <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-outline-secondary">Kembali ke Detail</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h2 class="h6 mb-0">Ringkasan Pesanan</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Tanggal Pesanan</small>
                        <div>{{ $pesanan->tanggal_pesanan->format('d M Y') }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Tanggal Pengiriman</small>
                        <div>{{ optional($pesanan->tanggal_pengiriman)->format('d M Y') ?? 'Belum ditentukan' }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Total Nilai</small>
                        <div class="fw-semibold">Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Jumlah Item</small>
                        <div>{{ $pesanan->detailPesanan->count() }} item</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Pelanggan</small>
                        <div class="fw-semibold">{{ optional($pesanan->pelanggan)->nama ?? 'Tidak tersedia' }}</div>
                        @if($pesanan->pelanggan)
                            <div class="small text-muted">{{ $pesanan->pelanggan->telepon }}</div>
                            <div class="small text-muted">{{ $pesanan->pelanggan->alamat }}</div>
                        @endif
                    </div>

                    <div class="border rounded-3 p-3 bg-light">
                        <h3 class="h6 mb-2">Catatan Saat Ini</h3>
                        <p class="mb-0 text-muted">{{ $pesanan->catatan ?: 'Belum ada catatan tambahan untuk pesanan ini.' }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h2 class="h6 mb-0">Daftar Item Pesanan</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 3rem;">#</th>
                                    <th>Produk</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pesanan->detailPesanan as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(optional($item->produk)->gambar)
                                                    <img src="{{ filter_var($item->produk->gambar, FILTER_VALIDATE_URL) ? $item->produk->gambar : asset('storage/' . ltrim($item->produk->gambar, '/')) }}" alt="{{ $item->produk->nama }}" class="product-thumb">
                                                @else
                                                    <div class="product-thumb d-flex align-items-center justify-content-center text-muted small">No</div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $item->produk->nama ?? 'Produk tidak tersedia' }}</div>
                                                    <div class="text-muted small">{{ $item->spesifikasi ?: 'Tanpa spesifikasi' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $item->jumlah }}</td>
                                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Tidak ada item pesanan untuk ditampilkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h2 class="h6 mb-0">Perbarui Informasi Pengiriman</h2>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h5 class="alert-heading mb-2">Periksa kembali input Anda</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pesanan.update', $pesanan) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nomor PO</label>
                            <input type="text" class="form-control" value="{{ $pesanan->nomor_po }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pengiriman</label>
                            <input type="date" name="tanggal_pengiriman" class="form-control @error('tanggal_pengiriman') is-invalid @enderror" value="{{ old('tanggal_pengiriman', optional($pesanan->tanggal_pengiriman)->format('Y-m-d')) }}" required>
                            @error('tanggal_pengiriman')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Tanggal pengiriman harus sama atau setelah tanggal pesanan.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Pesanan</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="5" placeholder="Masukkan catatan tambahan untuk tim produksi atau logistik.">{{ old('catatan', $pesanan->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
