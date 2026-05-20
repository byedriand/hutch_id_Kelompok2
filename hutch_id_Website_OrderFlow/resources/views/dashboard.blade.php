@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1 class="h3">Dashboard Pesanan</h1>
            <p class="mb-0">Ringkasan aktivitas dan status pesanan</p>
        </div>
        <div class="top-actions">
            <div class="text-end">
                <div id="dash-date" class="fw-bold" style="font-size: 0.95rem;"></div>
                <small class="text-muted">Waktu real-time</small>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stat-grid mb-5">
        <div class="stat-card">
            <div class="stat-value text-primary">{{ $totalAktif }}</div>
            <div class="stat-desc">Total PO Aktif</div>
            <small>Bulan {{ now()->locale('id')->monthName }}</small>
        </div>
        <div class="stat-card">
            <div class="stat-value text-warning">{{ $jumlahMenunggu }}</div>
            <div class="stat-desc">Menunggu Konfirmasi</div>
            <small>Perlu tindakan segera</small>
        </div>
        <div class="stat-card">
            <div class="stat-value text-success">{{ $siapKirim }}</div>
            <div class="stat-desc">Siap Kirim</div>
            <small>Menunggu pengiriman</small>
        </div>
        <div class="stat-card">
            <div class="stat-value text-info">{{ $selesaiBulanIni }}</div>
            <div class="stat-desc">Selesai Bulan Ini</div>
            <small>Rp {{ number_format($nilaiSelesai, 0, ',', '.') }}</small>
        </div>
    </div>

    <div class="row">
        <!-- Menunggu Konfirmasi -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-hourglass-half me-2" style="color: #f59e0b;"></i>Menunggu Konfirmasi <span class="badge bg-warning text-dark ms-2">{{ $jumlahMenunggu }}</span></h5>
                    @if($jumlahMenunggu > 0)
                        <a href="{{ route('pesanan.index', ['status' => 'menunggu_konfirmasi']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse($pesananMenunggu as $po)
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="mono fw-bold" style="color: #2d7dd2;">{{ $po->nomor_po }}</span>
                                    <small class="text-muted d-block">{{ $po->pelanggan->nama ?? 'N/A' }}</small>
                                </div>
                                <span class="badge b-wait">⏳ Menunggu</span>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <small class="text-muted">
                                        @foreach($po->detailPesanan as $detail)
                                            <i class="fas fa-box-open me-1" style="color: #94a3b8;"></i>{{ $detail->produk->nama ?? 'N/A' }} ({{ $detail->jumlah }} pcs)<br>
                                        @endforeach
                                    </small>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="mono fw-bold" style="color: #17233d;">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</div>
                                    <small class="text-muted d-block">Target: {{ $po->tanggal_pengiriman->format('d M Y') }}</small>
                                    <div class="mt-2">
                                        @if($po->stok_cukup)
                                            <span class="badge b-ok">✓ Tersedia</span>
                                        @else
                                            <span class="badge b-warn">⚠ Kurang</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('pesanan.show', $po->id) }}" class="btn btn-secondary btn-sm w-100"><i class="fas fa-eye me-1"></i>Lihat Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3" style="color: #16a34a; opacity: 0.5;"></i>
                            <p class="mb-0">Tidak ada pesanan menunggu konfirmasi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Dalam Produksi -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-cogs me-2" style="color: #2d7dd2;"></i>Dalam Produksi <span class="badge bg-info text-white ms-2">{{ $pesananProduksi->count() }}</span></h5>
                    @if($pesananProduksi->count() > 0)
                        <a href="{{ route('pesanan.index', ['status' => 'dalam_produksi']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse($pesananProduksi as $po)
                        <div class="p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="mono fw-bold" style="color: #2d7dd2;">{{ $po->nomor_po }}</span>
                                    <small class="text-muted d-block">{{ $po->pelanggan->nama ?? 'N/A' }}</small>
                                </div>
                                <span class="badge b-prod">🔄 Produksi</span>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <small class="text-muted">
                                        @foreach($po->detailPesanan as $detail)
                                            <i class="fas fa-box-open me-1" style="color: #94a3b8;"></i>{{ $detail->produk->nama ?? 'N/A' }} ({{ $detail->jumlah }} pcs)<br>
                                        @endforeach
                                    </small>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="mono fw-bold" style="color: #17233d;">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</div>
                                    <small class="text-muted d-block">Target: {{ $po->tanggal_pengiriman->format('d M Y') }}</small>
                                    <div class="mt-2">
                                        <span class="badge" style="background-color: #dbeafe; color: #0c4a6e;">
                                            <i class="fas fa-hourglass-half me-1"></i>Progressing
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('pesanan.show', $po->id) }}" class="btn btn-secondary btn-sm w-100"><i class="fas fa-eye me-1"></i>Lihat Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="fas fa-box fa-3x mb-3" style="color: #94a3b8; opacity: 0.3;"></i>
                            <p class="mb-0">Tidak ada pesanan dalam produksi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta'
    };
    const timeString = now.toLocaleDateString('id-ID', options) + ' · ' + now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) + ' WIB';
    document.getElementById('dash-date').textContent = timeString;
}

updateTime();
setInterval(updateTime, 60000); // Update every minute
</script>
@endpush
@endsection