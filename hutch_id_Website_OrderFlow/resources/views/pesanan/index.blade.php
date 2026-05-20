@extends('layouts.app')

@section('content')
<div>
    <style>
        .order-card {
            width: 100%;
            min-height: auto;
            border-radius: 1.5rem;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 18px 40px rgba(15, 64, 124, 0.08);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .order-card:hover {
            transform: translateY(-3px);
            border-color: #cbd5e1;
            box-shadow: 0 22px 48px rgba(15, 64, 124, 0.12);
        }
        .order-card {
            border: 1px solid rgba(219, 229, 241, 1);
        }
        .order-card .order-meta {
            font-size: 0.92rem;
            color: #64748b;
        }
        .order-card .order-label {
            font-size: 0.72rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 0.4rem;
        }
        .order-card .order-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 1rem;
        }
        .pesanan-grid {
            min-height: 0;
            align-items: flex-start;
        }
        .order-card .order-head {
            gap: 1.25rem;
            align-items: flex-start;
        }
        .order-card .order-details {
            margin-top: 1.25rem;
        }
        .order-card .order-foot {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(226, 232, 240, 0.9);
        }
        .order-card p,
        .order-card small {
            margin-bottom: 0;
        }
        .order-card .order-amount {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1d4ed8;
        }
        .page-header.custom-pesanan {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(15, 64, 124, 0.08);
        }
        .page-header.custom-pesanan .top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .order-card .order-foot .btn {
            min-width: 140px;
        }
        .order-badge {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.45rem 0.95rem;
            border-radius: 999px;
        }
        .order-badge.b-wait { background: #eff6ff; color: #1d4ed8; }
        .order-badge.b-conf { background: #dcfce7; color: #166534; }
        .order-badge.b-prod { background: #fef9c3; color: #92400e; }
        .order-badge.b-ready { background: #dbeafe; color: #1e40af; }
        .order-badge.b-done { background: #dcfce7; color: #166534; }
        .order-badge.b-cancel { background: #fee2e2; color: #991b1b; }
        .order-badge.b-ok { background: #e2e8f0; color: #475569; }
        .order-card h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }
        .order-card .order-amount {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1d4ed8;
        }

        @media (max-width: 991px) {
            .page-header.custom-pesanan {
                justify-content: flex-start;
            }

            .order-card .order-head {
                flex-direction: column;
                align-items: stretch;
            }

            .order-card .order-foot {
                flex-direction: column;
                align-items: stretch;
            }

            .order-card .order-foot .btn {
                width: 100%;
            }

            .order-card .order-details {
                margin-top: 1rem;
            }

            .order-card .order-meta,
            .order-card .order-amount,
            .order-card .order-foot .text-muted {
                text-align: left;
            }
        }

        @media (max-width: 767px) {
            .page-header.custom-pesanan {
                padding-bottom: 0.65rem;
            }

            .top-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .page-header.custom-pesanan h1 {
                font-size: 1.35rem;
            }

            .pesanan-grid {
                gap: 1rem;
            }
        }
    </style>
    <div class="page-header custom-pesanan">
        <div>
            <h1 class="h3">Daftar Pesanan</h1>
            <p class="mb-0">Lihat ringkasan dan status semua Purchase Order secara cepat.</p>
        </div>
        <div class="top-actions">
            @if(auth()->user()->role !== 'operator_gudang')
                <a href="{{ route('pesanan.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Buat PO Baru
                </a>
            @endif
        </div>
    </div>

    <div class="card rounded-4 shadow-sm border-0 mb-4">
        <div class="card-body">
            <form id="filter-form" method="GET" action="{{ route('pesanan.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nomor PO, pelanggan, atau produk..." value="{{ request('cari') }}">
                </div>
                <div class="col-md-2">
                    <select id="status-filter" name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="dalam_produksi" {{ request('status') == 'dalam_produksi' ? 'selected' : '' }}>Dalam Produksi</option>
                        <option value="siap_kirim" {{ request('status') == 'siap_kirim' ? 'selected' : '' }}>Siap Kirim</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(request()->filled('cari') || request()->filled('status') || request()->filled('dari') || request()->filled('sampai'))
        <div class="mb-3 d-flex flex-wrap gap-2">
            @if(request()->filled('cari'))
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Cari: {{ request('cari') }}</span>
            @endif
            @if(request()->filled('status'))
                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">Status: {{ str_replace('_', ' ', request('status')) }}</span>
            @endif
            @if(request()->filled('dari'))
                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Dari: {{ request('dari') }}</span>
            @endif
            @if(request()->filled('sampai'))
                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning">Sampai: {{ request('sampai') }}</span>
            @endif
            <a href="{{ route('pesanan.index') }}" class="badge rounded-pill bg-danger bg-opacity-10 text-danger text-decoration-none">Reset Filter</a>
        </div>
    @endif

    <div class="row g-3 pesanan-grid">
        @forelse($pesanan as $po)
            @php
                $firstItem = optional($po->detailPesanan->first());
                $badgeMap = [
                    'menunggu_konfirmasi' => ['class' => 'b-wait', 'text' => 'Menunggu'],
                    'dikonfirmasi' => ['class' => 'b-conf', 'text' => 'Dikonfirmasi'],
                    'dalam_produksi' => ['class' => 'b-prod', 'text' => 'Dalam Produksi'],
                    'siap_kirim' => ['class' => 'b-ready', 'text' => 'Siap Kirim'],
                    'selesai' => ['class' => 'b-done', 'text' => 'Selesai'],
                    'dibatalkan' => ['class' => 'b-cancel', 'text' => 'Dibatalkan'],
                ];
                $badge = $badgeMap[$po->status] ?? ['class' => 'b-ok', 'text' => ucfirst(str_replace('_', ' ', $po->status))];
            @endphp
            <div class="col-12">
                <div class="order-card p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 order-head">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge order-badge bg-primary bg-opacity-10 text-primary">{{ $po->nomor_po }}</span>
                                <small class="text-muted">{{ $po->created_at->format('d M Y') }}</small>
                            </div>
                            <h5 class="mb-1">{{ $po->pelanggan->nama ?? 'Pelanggan N/A' }}</h5>
                            <p class="mb-0 text-muted">{{ $firstItem && $firstItem->produk ? $firstItem->produk->nama . ' (' . $firstItem->jumlah . ' pcs)' : '-' }}
                                @if($po->detailPesanan->count() > 1)
                                    <span class="text-muted">+{{ $po->detailPesanan->count() - 1 }} item lainnya</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge order-badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
                            <div class="order-meta mt-2">Tgl Kirim: {{ $po->tanggal_pengiriman->format('d M Y') }}</div>
                            <div class="order-amount mt-2">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-md-4 g-3 mt-4 order-details">
                        <div class="col">
                            <div class="order-label">Produk Utama</div>
                            <div class="order-value">{{ $firstItem && $firstItem->produk ? $firstItem->produk->nama : '-' }}</div>
                        </div>
                        <div class="col">
                            <div class="order-label">Pelanggan</div>
                            <div class="order-value">{{ $po->pelanggan->nama ?? '-' }}</div>
                        </div>
                        <div class="col">
                            <div class="order-label">Jumlah Item</div>
                            <div class="order-value">{{ $po->detailPesanan->sum('jumlah') }} pcs</div>
                        </div>
                        <div class="col">
                            <div class="order-label">Status</div>
                            <div><span class="badge order-badge {{ $badge['class'] }}">{{ $badge['text'] }}</span></div>
                        </div>
                    </div>

                    <div class="order-foot d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mt-4 pt-3 border-top">
                        <div class="text-muted small">Klik detail untuk melihat informasi lengkap pesanan.</div>
                        <a href="{{ route('pesanan.show', $po) }}" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-eye me-1"></i>Detail Pesanan
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card rounded-4 shadow-sm border-0 p-5 text-center text-muted">
                    <i class="fas fa-folder-open fa-2x mb-3"></i>
                    <p class="mb-0">Tidak ada pesanan ditemukan</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 flex-column flex-md-row gap-3">
        <small class="text-muted">
            Menampilkan {{ $pesanan->firstItem() ?? 0 }}&ndash;{{ $pesanan->lastItem() ?? 0 }} dari {{ $pesanan->total() }} pesanan
        </small>
        <nav>
            {{ $pesanan->withQueryString()->links() }}
        </nav>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filter-form');
        const statusFilter = document.getElementById('status-filter');
        const dateInputs = form.querySelectorAll('input[type="date"]');

        if (statusFilter) {
            statusFilter.addEventListener('change', function () {
                form.submit();
            });
        }

        dateInputs.forEach(input => {
            input.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
@endpush
@endsection
