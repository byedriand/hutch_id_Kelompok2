@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header dashboard-header align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard Pesanan</h1>
            <p class="mb-0 text-muted">Ringkasan aktivitas dan status pesanan dengan insight cepat.</p>
        </div>
        <div class="top-actions text-end">
            <div id="dash-date" class="fw-bold" style="font-size: 0.95rem;"></div>
            <small class="text-muted">Waktu real-time</small>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stat-grid dashboard-stat-grid mb-5">
        <div class="stat-card dashboard-stat-card">
            <div class="stat-value text-primary">{{ $totalAktif }}</div>
            <div class="stat-desc">Total PO Aktif</div>
            <small>Bulan {{ now()->locale('id')->monthName }}</small>
        </div>
        <div class="stat-card dashboard-stat-card">
            <div class="stat-value text-warning">{{ $jumlahMenunggu }}</div>
            <div class="stat-desc">Menunggu Konfirmasi</div>
            <small>Perlu tindakan segera</small>
        </div>
        <div class="stat-card dashboard-stat-card">
            <div class="stat-value text-success">{{ $siapKirim }}</div>
            <div class="stat-desc">Siap Kirim</div>
            <small>Menunggu pengiriman</small>
        </div>
        <div class="stat-card dashboard-stat-card">
            <div class="stat-value text-info">{{ $selesaiBulanIni }}</div>
            <div class="stat-desc">Selesai Bulan Ini</div>
            <small>Rp {{ number_format($nilaiSelesai, 0, ',', '.') }}</small>
        </div>
    </div>

    <div class="row">
        <!-- Menunggu Konfirmasi -->
        <div class="col-lg-6 mb-4">
            <div class="card dashboard-card shadow-sm overflow-hidden">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-hourglass-half me-2" style="color: #f59e0b;"></i>Menunggu Konfirmasi <span class="badge bg-warning text-dark ms-2">{{ $jumlahMenunggu }}</span></h5>
                    @if($jumlahMenunggu > 0)
                        <a href="{{ route('pesanan.index', ['status' => 'menunggu_konfirmasi']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    @endif
                </div>
                <div class="card-body p-0 dashboard-card-body">
                    @forelse($pesananMenunggu as $po)
                        <div class="p-3 border-bottom dashboard-item">
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
                                                <span class="badge b-warn">⚠ Kurang: {{ $po->shortage_total ?? 0 }} unit</span>
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
            <div class="card dashboard-card shadow-sm overflow-hidden">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-cogs me-2" style="color: #2d7dd2;"></i>Dalam Produksi <span class="badge bg-info text-white ms-2">{{ $pesananProduksi->count() }}</span></h5>
                    @if($pesananProduksi->count() > 0)
                        <a href="{{ route('pesanan.index', ['status' => 'dalam_produksi']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    @endif
                </div>
                <div class="card-body p-0 dashboard-card-body">
                    @forelse($pesananProduksi as $po)
                        <div class="p-3 border-bottom dashboard-item">
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
                                        @if(!empty($po->shortage_total) && $po->shortage_total > 0)
                                            <span class="badge b-warn">⚠ Kurang: {{ $po->shortage_total }} unit</span>
                                        @else
                                            <span class="badge" style="background-color: #dbeafe; color: #0c4a6e;">
                                                <i class="fas fa-hourglass-half me-1"></i>Progressing
                                            </span>
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
                            <i class="fas fa-box fa-3x mb-3" style="color: #94a3b8; opacity: 0.3;"></i>
                            <p class="mb-0">Tidak ada pesanan dalam produksi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.dashboard-header {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 0.5rem;
}
.dashboard-header h1 {
    letter-spacing: 0.02em;
}
.dashboard-header .top-actions {
    min-width: 220px;
    text-align: right;
}
.dashboard-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}
.dashboard-stat-card {
    border-radius: 24px;
    padding: 1.4rem 1.5rem;
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(241,245,255,1) 100%);
    border: 1px solid rgba(59, 130, 246, 0.16);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.55s ease both;
}
.dashboard-stat-card:nth-child(1) { animation-delay: 0.05s; }
.dashboard-stat-card:nth-child(2) { animation-delay: 0.1s; }
.dashboard-stat-card:nth-child(3) { animation-delay: 0.15s; }
.dashboard-stat-card:nth-child(4) { animation-delay: 0.2s; }
.dashboard-stat-card::after {
    content: '';
    position: absolute;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    top: -24px;
    right: -28px;
    background: rgba(59, 130, 246, 0.14);
}
.dashboard-stat-card > * {
    position: relative;
    z-index: 1;
}
.dashboard-stat-card .stat-value {
    font-size: 2.8rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.35rem;
}
.dashboard-stat-card .stat-desc {
    color: #475569;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}
.dashboard-stat-card small {
    color: #64748b;
    font-size: 0.88rem;
}
.dashboard-card {
    border-radius: 24px;
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.06);
}
.dashboard-card .card-header {
    background: transparent;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    padding: 1.1rem 1.35rem;
}
.dashboard-card-body .dashboard-item {
    transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
    animation: fadeInUp 0.45s ease both;
}
.dashboard-card-body .dashboard-item:nth-child(1) { animation-delay: 0.08s; }
.dashboard-card-body .dashboard-item:nth-child(2) { animation-delay: 0.12s; }
.dashboard-card-body .dashboard-item:hover {
    transform: translateY(-3px);
    background: rgba(59, 130, 246, 0.04);
    box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
}
.dashboard-card-body .dashboard-item:last-child { border-bottom: none; }
.dashboard-card .mono {
    font-size: 0.95rem;
}
.dashboard-card .badge {
    font-size: 0.82rem;
    padding: 0.45rem 0.75rem;
}
</style>
@endpush

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