@extends('layouts.app')

@section('content')
<div>
    @push('styles')
    <style>
        /* Filter Form Styling */
        .card-filter-wrapper {
            background: #ffffff;
            border: 1px solid rgba(219, 229, 241, 0.95);
            border-radius: 1.25rem;
            box-shadow: 0 12px 28px rgba(15, 64, 124, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .filter-header h6 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            align-items: flex-end;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .filter-group label {
            font-weight: 600;
            color: #334155;
            font-size: 0.9rem;
            margin: 0;
        }

        .filter-group .form-control,
        .filter-group .form-select {
            border-radius: 0.75rem;
            border: 1.5px solid #dbeafe;
            padding: 0.65rem 0.9rem;
            font-size: 0.95rem;
            background: #f8fbff;
            transition: all 0.2s ease;
        }

        .filter-group .form-control:focus,
        .filter-group .form-select:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-group .input-group .input-group-text {
            background: transparent;
            border-right: 0;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .filter-group .input-group .form-control {
            border-left: 0;
            background: #f8fbff;
        }

        .filter-actions {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .filter-actions .btn {
            padding: 0.65rem 1.15rem;
            font-weight: 600;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .filter-actions .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .filter-actions .btn-primary:hover {
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        .filter-actions .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .filter-actions .btn-success:hover {
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
            transform: translateY(-2px);
        }

        .filter-actions .btn-outline-secondary {
            border: 1.5px solid #cbd5e1;
            color: #64748b;
            background: #ffffff;
        }

        .filter-actions .btn-outline-secondary:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* Advanced Filters Collapse */
        .advanced-filters-collapse {
            margin-top: 1rem;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .advanced-filters-body {
            background: linear-gradient(135deg, #f8fbff 0%, #f0f4ff 100%);
            border: 1px solid #dbeafe;
            border-radius: 1rem;
            padding: 1.25rem;
        }

        .advanced-filters-body .filter-group {
            padding: 0;
        }

        .advanced-filters-label {
            font-weight: 700;
            color: #1e40af;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.9rem;
            background: #ffffff;
            border: 1.5px solid #dbeafe;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            height: fit-content;
        }

        .checkbox-wrapper:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .checkbox-wrapper input[type="checkbox"] {
            cursor: pointer;
            width: 1.1rem;
            height: 1.1rem;
            accent-color: #2563eb;
        }

        .checkbox-wrapper label {
            margin: 0;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            font-size: 0.9rem;
        }

        /* Layout tweaks for pesanan list */
        .pesanan-grid { gap: 1.25rem; }

        .order-card {
            width: 100%;
            min-height: auto;
            border-radius: 1.25rem;
            overflow: hidden;
            background: linear-gradient(180deg,#ffffff 0%, #fbfdff 100%);
            box-shadow: 0 18px 40px rgba(15, 64, 124, 0.06);
            transition: transform 0.22s ease, border-color 0.2s ease, box-shadow 0.22s ease;
            border: 1px solid rgba(219,229,241,0.9);
            padding: 1rem 1.25rem; /* restore inner padding for consistent layout */
        }
        .order-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 28px 60px rgba(15,64,124,0.10);
        }

        .order-card .order-head { gap: 1.1rem; align-items: center; }
        .order-card h5 { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.2rem; }
        .order-card .order-meta { font-size: 0.92rem; color: #64748b; }
        .order-card .order-label { font-size: 0.72rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 0.35rem; }
        .order-card .order-value { font-weight: 700; color: #0f172a; font-size: 1rem; }
        .order-card .order-details { margin-top: 0.5rem; }
        .order-card .order-foot { margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid rgba(226,232,240,0.8); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
        .order-card p, .order-card small { margin-bottom: 0; }

        .order-badge { font-size: 0.78rem; font-weight: 700; padding: 0.45rem 0.85rem; border-radius: 999px; display: inline-block; }
        .order-badge.b-wait { background: #fef3c7; color: #92400e; }
        .order-badge.b-conf { background: #dcfce7; color: #166534; }
        .order-badge.b-prod { background: #fef9c3; color: #92400e; }
        .order-badge.b-ready { background: #dbeafe; color: #1e40af; }
        .order-badge.b-done { background: #dcfce7; color: #166534; }
        .order-badge.b-cancel { background: #fee2e2; color: #991b1b; }

        .order-amount { font-size: 1.15rem; font-weight: 700; color: #1d4ed8; }

        /* Constrain header columns so right column doesn't push content down */
        .order-card .order-head > div:first-child { flex: 1 1 auto; min-width: 0; }
        .order-card .order-head .text-end { width: 220px; flex: 0 0 220px; text-align: right; }

        @media (max-width: 991px) {
            .order-card .order-head .text-end { width: 100%; flex: 1 1 100%; text-align: left; margin-top: 0.6rem; }
            .order-card .order-details { margin-top: 0.85rem; }
            .order-card { padding: 0.9rem 1rem; }
        }

        .page-header.custom-pesanan { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:0.85rem; padding-bottom:0.75rem; border-bottom:1px solid rgba(15,64,124,0.06); }

        /* Responsive adjustments */
        @media (max-width:991px){
            .page-header.custom-pesanan { justify-content:flex-start; }
            .order-card .order-head { flex-direction:column; align-items:stretch; }
            .order-card .order-foot { flex-direction:column; align-items:stretch; }
            .order-card .order-foot .btn { width:100%; }
        }

        /* Pagination polish */
        .pagination .page-link { border-radius: 10px; padding: 0.4rem 0.7rem; color: #0b3d7f; }
        .pagination .page-item.active .page-link { background: #2d7dd2; border-color: #2d7dd2; color: #fff; }

        /* Small utilities */
        .order-card .order-foot .btn { min-width: 140px; }

        /* Filter input icon tweaks */
        .input-group-text.bg-white { border-radius: 12px 0 0 12px; border-right: 0; }
        .form-control.form-control-sm { border-radius: 0 12px 12px 0; }
        .form-select.form-select-sm { border-radius: 0 12px 12px 0; }

        /* Masonry layout (JS) - items set to percentage width */
        .masonry { position: relative; }
        .masonry-item { width: 48%; margin-bottom: 1rem; display: block; opacity: 0; transform: translateY(12px); transition: opacity 360ms cubic-bezier(.2,.8,.2,1), transform 360ms cubic-bezier(.2,.8,.2,1); }
        .masonry-sizer { width: 48%; }

        @media (max-width: 1199px) { .masonry-item, .masonry-sizer { width: 48%; } }
        @media (max-width: 991px) { .masonry-item, .masonry-sizer { width: 100%; } }

        /* Reveal state for staggered animation */
        .masonry-item.show { opacity: 1; transform: none; }

        /* Skeleton loader styles */
        .skeleton-wrapper { display: block; }
        .skeleton-card { background: #fff; border-radius: 12px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 12px 24px rgba(15,64,124,0.05); border:1px solid rgba(219,229,241,0.9); }
        .s-line { height: 12px; background: linear-gradient(90deg,#f3f4f6 25%,#e6eefb 50%,#f3f4f6 75%); background-size: 200% 100%; border-radius: 6px; animation: shimmer 1.2s linear infinite; }
        .s-title { width: 55%; height: 18px; margin-bottom: 0.6rem; }
        .s-sub { width: 40%; height: 12px; margin-bottom: 0.5rem; }
        .s-row { display:flex; gap:0.8rem; margin-top:0.6rem; }
        .s-col { flex:1; }

        @keyframes shimmer { from { background-position: 200% 0 } to { background-position: -200% 0 } }

        /* Responsive Filter */
        @media (max-width: 1199px) {
            .filter-row {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 0.9rem;
            }

            .filter-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .filter-actions .btn {
                flex: 1;
                min-width: 120px;
            }
        }

        @media (max-width: 767px) {
            .card-filter-wrapper {
                padding: 1rem;
                border-radius: 1rem;
            }

            .filter-row {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }

            .filter-actions {
                flex-direction: column;
                width: 100%;
            }

            .filter-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .advanced-filters-body {
                padding: 1rem;
            }

            .advanced-filters-body .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush
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

    <div class="card-filter-wrapper">
        <form id="filter-form" method="GET" action="{{ route('pesanan.index') }}">
            <div class="filter-header">
                <h6><i class="fas fa-search me-2"></i>Filter & Pencarian</h6>
            </div>

            <!-- Basic Filters Row -->
            <div class="filter-row">
                <div class="filter-group">
                    <label for="cari">Cari PO, Pelanggan, Produk</label>
                    <input type="text" id="cari" name="cari" class="form-control" 
                           placeholder="Ketik kata kunci..." value="{{ request('cari') }}">
                </div>
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="menunggu_konfirmasi" {{ request('status') == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="dalam_produksi" {{ request('status') == 'dalam_produksi' ? 'selected' : '' }}>Dalam Produksi</option>
                        <option value="siap_kirim" {{ request('status') == 'siap_kirim' ? 'selected' : '' }}>Siap Kirim</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="dari">Tanggal Dari</label>
                    <input type="date" id="dari" name="dari" class="form-control" value="{{ request('dari') }}">
                </div>
                <div class="filter-group">
                    <label for="sampai">Tanggal Sampai</label>
                    <input type="date" id="sampai" name="sampai" class="form-control" value="{{ request('sampai') }}">
                </div>
            </div>

            <!-- Action Buttons & Advanced Toggle -->
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div class="filter-actions">
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#advancedFilters" aria-expanded="false">
                        <i class="fas fa-sliders-h me-2"></i>Filter Lanjutan
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Terapkan
                    </button>
                    <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="collapse advanced-filters-collapse" id="advancedFilters">
                <div class="advanced-filters-body">
                    <div class="filter-row">
                        <div class="filter-group">
                            <span class="advanced-filters-label">Nilai Minimum (Rp)</span>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="min_total" class="form-control" 
                                       placeholder="0" value="{{ request('min_total') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <span class="advanced-filters-label">Nilai Maksimum (Rp)</span>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="max_total" class="form-control" 
                                       placeholder="0" value="{{ request('max_total') }}">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label for="produk">Nama Produk</label>
                            <input type="text" id="produk" name="produk" class="form-control" 
                                   placeholder="Cari nama produk..." value="{{ request('produk') }}">
                        </div>
                        <div class="filter-group">
                            <label>&nbsp;</label>
                            <div class="checkbox-wrapper">
                                <input class="form-check-input" type="checkbox" name="multi_item" 
                                       id="multi_item" {{ request('multi_item') ? 'checked' : '' }}>
                                <label class="form-check-label" for="multi_item">
                                    Hanya multi-item
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Badge -->
    @if(request()->filled('cari') || request()->filled('status') || request()->filled('dari') || request()->filled('sampai') || request()->filled('min_total') || request()->filled('max_total') || request()->filled('produk') || request()->filled('multi_item'))
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
            @if(request()->filled('min_total'))
                <span class="badge rounded-pill bg-info bg-opacity-10 text-info">Min: Rp {{ number_format(request('min_total'), 0, ',', '.') }}</span>
            @endif
            @if(request()->filled('max_total'))
                <span class="badge rounded-pill bg-info bg-opacity-10 text-info">Max: Rp {{ number_format(request('max_total'), 0, ',', '.') }}</span>
            @endif
            @if(request()->filled('produk'))
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Produk: {{ request('produk') }}</span>
            @endif
            @if(request()->filled('multi_item'))
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary">Multi-item</span>
            @endif
            <a href="{{ route('pesanan.index') }}" class="badge rounded-pill bg-danger bg-opacity-10 text-danger text-decoration-none">Reset Filter</a>
        </div>
    @endif

    <!-- Skeleton shown while page 'loads' briefly -->
    <div id="skeleton" class="skeleton-wrapper">
        <div class="masonry">
            @for($i=0;$i<4;$i++)
            <div class="skeleton-card">
                <div class="s-line s-title"></div>
                <div class="s-line s-sub"></div>
                <div class="s-row">
                    <div class="s-col"><div class="s-line" style="height:10px;width:80%"></div></div>
                    <div class="s-col"><div class="s-line" style="height:10px;width:60%"></div></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <div id="pesananList" class="masonry pesanan-grid">
        <div class="masonry-sizer"></div>
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
            <div class="masonry-item">
                <div class="order-card">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3 order-head">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge order-badge bg-primary bg-opacity-10 text-primary">{{ $po->nomor_po }}</span>
                                <small class="text-muted">{{ $po->created_at->format('d M Y') }}</small>
                            </div>
                            <h5 class="mb-1">{{ $po->pelanggan->nama ?? 'Pelanggan N/A' }}</h5>
                            <p class="mb-0 text-muted">{{ $firstItem && $firstItem->produk ? $firstItem->produk->nama . ' (' . $firstItem->jumlah . ' pcs)' : '-' }}
                                    @if(!empty($po->shortage_total) && $po->shortage_total > 0)
                                        <span class="badge bg-danger ms-2" style="font-size:0.75rem;">Kurang: {{ $po->shortage_total }} unit</span>
                                    @endif
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
            <div class="masonry-item">
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
<script>
    // Simple skeleton toggle: show skeleton for a short moment then reveal content
    document.addEventListener('DOMContentLoaded', function () {
        const skeleton = document.getElementById('skeleton');
        const list = document.getElementById('pesananList');
        // show skeleton at first, then replace quickly
        setTimeout(() => {
            if (skeleton) skeleton.style.display = 'none';
            if (list) {
                list.style.display = '';
                // initialize masonry after revealing content
                if (typeof imagesLoaded !== 'undefined' && typeof Masonry !== 'undefined') {
                    imagesLoaded(list, function() {
                        var msnry = new Masonry(list, {
                            itemSelector: '.masonry-item',
                            columnWidth: '.masonry-sizer',
                            percentPosition: true,
                            gutter: 16
                        });
                        // staggered reveal after layout
                        var items = list.querySelectorAll('.masonry-item');
                        items.forEach(function(it, idx){
                            setTimeout(function(){ it.classList.add('show'); msnry.layout(); }, idx * 80);
                        });
                    });
                }
            }
        }, 350);
    });
</script>
<!-- Load imagesLoaded and Masonry from CDN for better masonry layout -->
<script src="https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
@endpush
@endsection
