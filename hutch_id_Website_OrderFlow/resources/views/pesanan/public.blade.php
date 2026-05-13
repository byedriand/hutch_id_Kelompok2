@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="card mx-auto" style="max-width: 760px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <h2 class="mb-1">Detail Pesanan</h2>
                <p class="text-muted mb-0">{{ $pesanan->nomor_po }}</p>
            </div>

            <div class="mb-3">
                <h5>Pelanggan</h5>
                <p class="mb-1"><strong>{{ $pesanan->pelanggan->nama }}</strong></p>
                <p class="mb-1">{{ $pesanan->pelanggan->alamat }}</p>
                <p class="mb-1">{{ $pesanan->pelanggan->telepon }}</p>
                <p class="mb-0">{{ $pesanan->pelanggan->email ?? '-' }}</p>
            </div>

            <div class="mb-3">
                <h5>Ringkasan PO</h5>
                <p class="mb-1">Tanggal Pesanan: {{ $pesanan->tanggal_pesanan->format('d M Y') }}</p>
                <p class="mb-1">Tanggal Pengiriman: {{ $pesanan->tanggal_pengiriman->format('d M Y') }}</p>
                <p class="mb-0">Status: <strong>{{ str_replace('_', ' ', $pesanan->status) }}</strong></p>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanan->detailPesanan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->produk->nama }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <p class="mb-1"><strong>Total Nilai:</strong> Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</p>
                <p class="mb-0 text-muted">Terakhir diperbarui {{ $pesanan->updated_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
