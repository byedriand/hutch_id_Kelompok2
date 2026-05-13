@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1 class="h3">Daftar Pesanan</h1>
            <p class="mb-0">Semua Purchase Order</p>
        </div>
        @if(auth()->user()->role !== 'operator_gudang')
            <div class="top-actions">
                <a href="{{ route('pesanan.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Buat PO Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('pesanan.index') }}" class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="cari" class="form-control" placeholder="🔍 Cari nama pelanggan..." value="{{ request('cari') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>⏳ Menunggu Konfirmasi</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>✅ Dikonfirmasi</option>
                        <option value="dalam_produksi" {{ request('status') == 'dalam_produksi' ? 'selected' : '' }}>🔄 Dalam Produksi</option>
                        <option value="siap_kirim" {{ request('status') == 'siap_kirim' ? 'selected' : '' }}>📦 Siap Kirim</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>🏁 Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>✕ Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                        <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-wrap mb-4">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 120px;">Nomor PO</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 150px;">Pelanggan</th>
                    <th>Produk Utama</th>
                    <th style="width: 120px;">Total Nilai</th>
                    <th style="width: 100px;">Tgl Kirim</th>
                    <th style="width: 130px;">Status</th>
                    <th style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $po)
                    <tr>
                        <td>
                            <span class="mono fw-bold">{{ $po->nomor_po }}</span>
                        </td>
                        <td>
                            <small>{{ $po->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            <small>{{ $po->pelanggan->nama ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <small>
                                @php
                                    $firstItem = optional($po->detailPesanan->first());
                                @endphp
                                @if($firstItem && $firstItem->produk)
                                    {{ $firstItem->produk->nama }} ({{ $firstItem->jumlah }} pcs)
                                    @if($po->detailPesanan->count() > 1)
                                        <br><small class="text-muted">+{{ $po->detailPesanan->count() - 1 }} item lainnya</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </small>
                        </td>
                        <td>
                            <span class="mono">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <small>{{ $po->tanggal_pengiriman->format('d M Y') }}</small>
                        </td>
                        <td>
                            @php
                                $badgeMap = [
                                    'menunggu_konfirmasi' => ['class' => 'b-wait', 'text' => '⏳ Menunggu'],
                                    'dikonfirmasi' => ['class' => 'b-conf', 'text' => '✅ Dikonfirmasi'],
                                    'dalam_produksi' => ['class' => 'b-prod', 'text' => '🔄 Produksi'],
                                    'siap_kirim' => ['class' => 'b-ready', 'text' => '📦 Siap Kirim'],
                                    'selesai' => ['class' => 'b-done', 'text' => '🏁 Selesai'],
                                    'dibatalkan' => ['class' => 'b-cancel', 'text' => '✕ Dibatalkan'],
                                ];
                                $badge = $badgeMap[$po->status] ?? ['class' => '', 'text' => $po->status];
                            @endphp
                            <span class="badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                        </td>
                        <td>
                            <a href="{{ route('pesanan.show', $po) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-2"></i>
                            <p>Tidak ada pesanan ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $pesanan->firstItem() ?? 0 }}–{{ $pesanan->lastItem() ?? 0 }} dari {{ $pesanan->total() }} pesanan
        </small>
        <nav>
            {{ $pesanan->withQueryString()->links() }}
        </nav>
    </div>
</div>
@endsection