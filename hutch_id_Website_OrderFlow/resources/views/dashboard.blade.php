@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header dashboard-header align-items-center justify-content-between mb-4">
        <div class="dashboard-header-left">
            <div class="dashboard-title-wrapper">
                <div class="dashboard-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h1 class="dashboard-title">Dashboard Pesanan</h1>
                    <p class="dashboard-subtitle">Ringkasan aktivitas dan status pesanan dengan insight cepat.</p>
                </div>
            </div>
        </div>
        <div class="dashboard-datetime">
            <div class="datetime-wrapper">
                <div class="datetime-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="datetime-content">
                    <div id="dash-date" class="datetime-display"></div>
                    <small class="datetime-label">Waktu real-time</small>
                </div>
            </div>
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
    background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.05) 50%, rgba(30, 41, 59, 0.03) 100%);
    padding: 2.25rem;
    border-radius: 1.75rem;
    border: 1.5px solid rgba(45, 125, 210, 0.15);
    box-shadow: 0 16px 48px rgba(45, 125, 210, 0.1), inset 0 1px 1px rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(12px);
    display: flex;
    flex-wrap: wrap;
    gap: 2.5rem;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
    animation: dashHeaderFadeIn 0.6s ease-out;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(45, 125, 210, 0.08) 0%, transparent 30%),
        radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.05) 0%, transparent 35%);
    pointer-events: none;
}

.dashboard-header-left {
    position: relative;
    z-index: 1;
    flex: 1;
    min-width: 300px;
}

.dashboard-title-wrapper {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.dashboard-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(45, 125, 210, 0.2), rgba(45, 125, 210, 0.1));
    color: #2d7dd2;
    font-size: 1.5rem;
    box-shadow: 0 8px 20px rgba(45, 125, 210, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.4);
    border: 1px solid rgba(45, 125, 210, 0.2);
    flex-shrink: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

.dashboard-title {
    font-size: 1.85rem;
    font-weight: 800;
    background: linear-gradient(135deg, #1e293b 0%, #2d7dd2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.dashboard-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0.5rem 0 0;
    font-weight: 500;
    letter-spacing: 0.01em;
}

.dashboard-datetime {
    position: relative;
    z-index: 1;
}

.datetime-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.6));
    border-radius: 1.25rem;
    border: 1.5px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08), inset 0 1px 1px rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(8px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: datetimeSlideIn 0.6s ease-out 0.2s both;
}

.datetime-wrapper:hover {
    box-shadow: 0 12px 40px rgba(45, 125, 210, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.8);
    transform: translateY(-2px);
    border-color: rgba(45, 125, 210, 0.2);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.75));
}

.datetime-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
    color: white;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(45, 125, 210, 0.3);
    flex-shrink: 0;
    animation: clockTick 1s ease-in-out infinite;
}

@keyframes clockTick {
    0%, 100% { transform: scale(1) rotate(0deg); }
    50% { transform: scale(1.05) rotate(5deg); }
}

.datetime-content {
    text-align: right;
    min-width: 180px;
}

.datetime-display {
    font-size: 1.1rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.3;
    letter-spacing: -0.01em;
    font-family: 'Plus Jakarta Sans', sans-serif;
    animation: textPulse 2s ease-in-out infinite;
}

@keyframes textPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.9; }
}

.datetime-label {
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 600;
    display: block;
    margin-top: 0.25rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

@keyframes dashHeaderFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes datetimeSlideIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
.dashboard-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
}

.dashboard-stat-card {
    border-radius: 1.5rem;
    padding: 1.75rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 1.5px solid rgba(45, 125, 210, 0.15);
    box-shadow: 0 8px 24px rgba(45, 125, 210, 0.08), inset 0 1px 1px rgba(255, 255, 255, 0.8);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out both;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(4px);
}

.dashboard-stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top left, rgba(45, 125, 210, 0.05), transparent 50%);
    pointer-events: none;
}

.dashboard-stat-card:nth-child(1) { animation-delay: 0.1s; }
.dashboard-stat-card:nth-child(2) { animation-delay: 0.2s; }
.dashboard-stat-card:nth-child(3) { animation-delay: 0.3s; }
.dashboard-stat-card:nth-child(4) { animation-delay: 0.4s; }

.dashboard-stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 16px 40px rgba(45, 125, 210, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.8);
    border-color: rgba(45, 125, 210, 0.25);
}

.dashboard-stat-card::before {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    top: -80px;
    right: -60px;
    background: radial-gradient(circle, rgba(45, 125, 210, 0.25) 0%, transparent 70%);
    transition: all 0.6s ease;
}

.dashboard-stat-card:hover::before {
    transform: translate(-20px, -20px);
}

.dashboard-stat-card > * {
    position: relative;
    z-index: 1;
}

.dashboard-stat-card .stat-value {
    font-size: 3rem;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: pulse 2s ease-in-out infinite;
}

.dashboard-stat-card .stat-desc {
    color: #1e293b;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    letter-spacing: -0.01em;
}

.dashboard-stat-card small {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
}

.dashboard-card {
    border-radius: 1.75rem;
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 2px solid rgba(45, 125, 210, 0.12);
    box-shadow: 0 15px 40px rgba(45, 125, 210, 0.1);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
    transition: all 0.4s ease;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.6s ease;
}

.dashboard-card:hover::before {
    left: 100%;
}

.dashboard-card .card-header {
    background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.05) 100%);
    border-bottom: 2px solid rgba(45, 125, 210, 0.15);
    padding: 1.25rem 1.5rem;
    position: relative;
    z-index: 1;
}

.dashboard-card .card-header h5 {
    color: #1e293b;
    font-weight: 800;
    letter-spacing: -0.01em;
    margin-bottom: 0;
}

.dashboard-card-body {
    position: relative;
    z-index: 1;
}

.dashboard-card-body .dashboard-item {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideInLeft 0.5s ease-out both;
    padding: 1rem 1.25rem !important;
    border-bottom: 2px solid rgba(219, 229, 241, 0.5) !important;
    position: relative;
    overflow: hidden;
}

.dashboard-card-body .dashboard-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    background: linear-gradient(180deg, #2d7dd2, #1e5aa8);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.dashboard-card-body .dashboard-item:hover::before {
    transform: scaleY(1);
}

.dashboard-card-body .dashboard-item:nth-child(1) { animation-delay: 0.1s; }
.dashboard-card-body .dashboard-item:nth-child(2) { animation-delay: 0.15s; }

.dashboard-card-body .dashboard-item:hover {
    transform: translateX(6px);
    background: linear-gradient(135deg, rgba(45, 125, 210, 0.06) 0%, rgba(59, 130, 246, 0.03) 100%);
    box-shadow: inset 0 0 20px rgba(45, 125, 210, 0.1);
}

.dashboard-card-body .dashboard-item:last-child { border-bottom: none !important; }

.dashboard-card .mono {
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.dashboard-card .badge {
    font-size: 0.85rem;
    padding: 0.55rem 0.9rem;
    font-weight: 700;
    border-radius: 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.3s ease;
    animation: badgeFloat 2s ease-in-out infinite;
}

.dashboard-card .badge:hover {
    transform: scale(1.05) translateY(-2px);
}

/* Badge Colors */
.b-wait {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid rgba(180, 83, 9, 0.2);
    box-shadow: 0 4px 12px rgba(180, 83, 9, 0.15);
}

.b-ok {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #166534;
    border: 1px solid rgba(34, 197, 94, 0.2);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
}

.b-warn {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
    border: 1px solid rgba(220, 38, 38, 0.2);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
}

.bg-warning {
    background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
    color: white !important;
    font-weight: 700;
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25) !important;
}

.bg-info {
    background: linear-gradient(135deg, #60a5fa, #3b82f6) !important;
    color: white !important;
    font-weight: 700;
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25) !important;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

@keyframes badgeFloat {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-2px);
    }
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .dashboard-header-left {
        min-width: unset;
        width: 100%;
    }

    .dashboard-title-wrapper {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .dashboard-icon {
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
    }

    .dashboard-title {
        font-size: 1.5rem;
    }

    .dashboard-subtitle {
        font-size: 0.9rem;
    }

    .dashboard-datetime {
        width: 100%;
    }

    .datetime-wrapper {
        padding: 0.85rem 1.25rem;
        width: 100%;
        justify-content: space-between;
    }

    .datetime-content {
        text-align: left;
        min-width: unset;
    }

    .datetime-display {
        font-size: 1rem;
    }

    .datetime-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    .dashboard-stat-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
    }

    .dashboard-stat-card {
        padding: 1.25rem;
        border-radius: 1.25rem;
    }

    .dashboard-stat-card .stat-value {
        font-size: 2.2rem;
    }

    .dashboard-stat-card .stat-desc {
        font-size: 0.9rem;
    }

    .dashboard-card .card-header {
        padding: 1rem 1.25rem;
    }

    .dashboard-card .card-header h5 {
        font-size: 1rem;
    }

    .dashboard-card-body .dashboard-item {
        padding: 0.85rem 1rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
function updateTime() {
    const now = new Date();
    const dateOptions = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Jakarta'
    };
    const timeOptions = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Jakarta'
    };
    
    const dateString = now.toLocaleDateString('id-ID', dateOptions);
    const timeString = now.toLocaleTimeString('id-ID', timeOptions) + ' WIB';
    
    const displayText = dateString + '<br><span style="font-weight: 700; font-size: 1.2em;">' + timeString + '</span>';
    document.getElementById('dash-date').innerHTML = displayText;
}

updateTime();
setInterval(updateTime, 1000); // Update every second for real-time effect
</script>
@endpush
@endsection