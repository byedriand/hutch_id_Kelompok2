@extends('layouts.app')

@section('content')
<style>
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

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @keyframes pulseGlow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(45, 125, 210, 0.7);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(45, 125, 210, 0);
        }
    }

    .container-fluid {
        animation: fadeInUp 0.6s ease-out;
    }

    .d-flex.flex-column {
        animation: fadeInUp 0.6s ease-out;
    }

    .d-flex.flex-column h1 {
        font-size: 1.75rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1e293b 0%, #2d7dd2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.01em;
    }

    .btn-outline-secondary {
        border: 2px solid #64748b;
        color: #64748b;
        padding: 0.7rem 1.25rem;
        font-weight: 700;
        border-radius: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-outline-secondary::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: #64748b;
        border-color: #64748b;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(100, 116, 139, 0.25);
    }

    .btn-outline-secondary:hover::before {
        animation: shimmer 0.6s ease-in-out;
    }

    .card {
        animation: fadeInUp 0.6s ease-out;
        border: 2px solid rgba(45, 125, 210, 0.12) !important;
        border-radius: 1.75rem !important;
        box-shadow: 0 15px 40px rgba(45, 125, 210, 0.08) !important;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%) !important;
    }

    .card:nth-child(2) {
        animation-delay: 0.1s;
    }

    .card:nth-child(3) {
        animation-delay: 0.2s;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
        z-index: 0;
    }

    .card:hover::before {
        left: 100%;
    }

    .card:hover {
        box-shadow: 0 25px 60px rgba(45, 125, 210, 0.15) !important;
        border-color: rgba(45, 125, 210, 0.25) !important;
    }

    .card-header {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.05) 100%) !important;
        border-bottom: 2px solid rgba(45, 125, 210, 0.15) !important;
        padding: 1.25rem 1.5rem !important;
        position: relative;
        z-index: 1;
    }

    .card-header h2,
    .card-header h6 {
        color: #1e293b;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin: 0;
    }

    .card-body {
        position: relative;
        z-index: 1;
    }

    .text-muted {
        color: #64748b !important;
    }

    .badge {
        font-weight: 700;
        padding: 0.6rem 1.1rem;
        border-radius: 999px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: white !important;
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #60a5fa, #3b82f6) !important;
        color: white !important;
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
    }

    .card-body > div {
        animation: slideInLeft 0.5s ease-out both;
    }

    .card-body > div:nth-child(1) { animation-delay: 0.1s; }
    .card-body > div:nth-child(2) { animation-delay: 0.15s; }
    .card-body > div:nth-child(3) { animation-delay: 0.2s; }
    .card-body > div:nth-child(4) { animation-delay: 0.25s; }
    .card-body > div:nth-child(5) { animation-delay: 0.3s; }

    .mb-3 {
        transition: all 0.3s ease;
    }

    .mb-3:hover small {
        color: #2d7dd2;
        font-weight: 600;
    }

    .border.rounded-3 {
        background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%) !important;
        border: 2px solid rgba(45, 125, 210, 0.15) !important;
        border-radius: 1.5rem !important;
        transition: all 0.3s ease;
    }

    .border.rounded-3:hover {
        box-shadow: 0 10px 30px rgba(45, 125, 210, 0.1);
        border-color: rgba(45, 125, 210, 0.3) !important;
    }

    .table {
        animation: slideInLeft 0.5s ease-out 0.1s both;
    }

    .table thead {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.15) 0%, rgba(59, 130, 246, 0.1) 100%);
    }

    .table thead th {
        color: #1e293b;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.8rem;
        border-bottom: 2px solid rgba(45, 125, 210, 0.15);
        padding: 1.1rem 1rem;
    }

    .table tbody tr {
        transition: all 0.3s ease;
        animation: slideInLeft 0.5s ease-out both;
    }

    .table tbody tr:nth-child(1) { animation-delay: 0.15s; }
    .table tbody tr:nth-child(2) { animation-delay: 0.2s; }
    .table tbody tr:nth-child(3) { animation-delay: 0.25s; }

    .table tbody tr:hover {
        background: rgba(45, 125, 210, 0.05);
    }

    .table tbody tr:hover td {
        color: #2d7dd2;
    }

    .table tbody td {
        padding: 1.1rem 1rem;
        color: #475569;
        font-weight: 500;
        vertical-align: middle;
        border-bottom: 1px solid rgba(219, 229, 241, 0.5);
    }

    .product-thumb {
        width: 56px;
        height: 56px;
        border-radius: 1rem;
        object-fit: cover;
        border: 2px solid rgba(45, 125, 210, 0.2);
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(45, 125, 210, 0.1);
    }

    .product-thumb:hover {
        transform: scale(1.08);
        box-shadow: 0 8px 20px rgba(45, 125, 210, 0.2);
    }

    .fw-semibold {
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.01em;
    }

    .form-label {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 0.6rem;
        letter-spacing: -0.01em;
    }

    .form-control,
    .form-select {
        border: 2px solid rgba(45, 125, 210, 0.15);
        border-radius: 1rem;
        padding: 0.75rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2d7dd2;
        box-shadow: 0 0 0 3px rgba(45, 125, 210, 0.1);
        outline: none;
    }

    .form-text {
        color: #64748b;
        font-weight: 500;
        margin-top: 0.4rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
        border: none;
        padding: 0.7rem 1.5rem;
        font-weight: 700;
        border-radius: 1rem;
        box-shadow: 0 6px 16px rgba(45, 125, 210, 0.25);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e5aa8, #1e3a8a);
        box-shadow: 0 10px 25px rgba(45, 125, 210, 0.35);
        transform: translateY(-2px);
    }

    .btn-primary:hover::before {
        animation: shimmer 0.6s ease-in-out;
    }

    .alert {
        border-radius: 1.5rem;
        border: 2px solid;
        animation: slideInLeft 0.5s ease-out;
    }

    .alert-danger {
        border-color: rgba(239, 68, 68, 0.2);
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        color: #991b1b;
    }

    .alert-heading {
        color: #7f1d1d;
        font-weight: 800;
    }

    .d-flex.flex-column.flex-sm-row {
        gap: 1rem;
    }

    .d-flex.flex-column.flex-sm-row .btn {
        min-width: 140px;
        animation: fadeInUp 0.6s ease-out both;
    }

    .d-flex.flex-column.flex-sm-row .btn:nth-child(1) { animation-delay: 0.4s; }
    .d-flex.flex-column.flex-sm-row .btn:nth-child(2) { animation-delay: 0.45s; }

    /* Layout Improvements */
    .row.g-4 {
        animation: fadeInUp 0.6s ease-out 0.2s both;
    }

    .col-lg-6 {
        animation: slideInLeft 0.6s ease-out both;
    }

    .col-lg-6:nth-child(2) {
        animation-delay: 0.15s;
    }

    /* Summary Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .summary-item {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.04) 100%);
        border: 2px solid rgba(45, 125, 210, 0.15);
        border-radius: 1.25rem;
        padding: 1.25rem;
        transition: all 0.3s ease;
        animation: slideInLeft 0.5s ease-out both;
    }

    .summary-item:nth-child(1) { animation-delay: 0.1s; }
    .summary-item:nth-child(2) { animation-delay: 0.15s; }
    .summary-item:nth-child(3) { animation-delay: 0.2s; }
    .summary-item:nth-child(4) { animation-delay: 0.25s; }

    .summary-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(45, 125, 210, 0.15);
        border-color: rgba(45, 125, 210, 0.25);
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.12) 0%, rgba(59, 130, 246, 0.08) 100%);
    }

    .summary-item small {
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .summary-item .value {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: -0.01em;
    }

    .summary-item .value.text-primary {
        background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .customer-info {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.04) 100%);
        border: 2px solid rgba(45, 125, 210, 0.15);
        border-radius: 1.25rem;
        padding: 1.25rem;
        animation: slideInLeft 0.5s ease-out 0.3s both;
        margin-top: 1rem;
    }

    .customer-info h6 {
        color: #2d7dd2;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .customer-name {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .customer-details {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .card-header {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.05) 100%) !important;
        border-bottom: 2px solid rgba(45, 125, 210, 0.15) !important;
        padding: 1.5rem 1.75rem !important;
        position: relative;
        z-index: 1;
    }

    .card-header h2 {
        color: #2d7dd2;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin: 0;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-header h2::before {
        content: '';
        width: 4px;
        height: 24px;
        background: linear-gradient(180deg, #2d7dd2, #1e5aa8);
        border-radius: 999px;
    }

    .notes-box {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08) 0%, rgba(59, 130, 246, 0.04) 100%) !important;
        border: 2px dashed rgba(45, 125, 210, 0.2) !important;
        border-radius: 1.25rem !important;
        padding: 1.25rem !important;
        margin-top: 1.5rem;
        animation: slideInLeft 0.5s ease-out 0.35s both;
    }

    .notes-box h3 {
        color: #2d7dd2;
        font-weight: 800;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }

    .notes-box p {
        color: #64748b;
        margin: 0;
        font-weight: 500;
        line-height: 1.6;
    }

    /* Form Styling Enhancement */
    .card-body form {
        animation: slideInLeft 0.6s ease-out 0.1s both;
    }

    .mb-3 {
        transition: all 0.3s ease;
        animation: slideInLeft 0.5s ease-out both;
    }

    .mb-3:nth-child(1) { animation-delay: 0.15s; }
    .mb-3:nth-child(2) { animation-delay: 0.2s; }
    .mb-3:nth-child(3) { animation-delay: 0.25s; }
    .mb-3:nth-child(4) { animation-delay: 0.3s; }

    .form-label {
        color: #2d7dd2;
        font-weight: 800;
        margin-bottom: 0.75rem;
        letter-spacing: -0.01em;
        text-transform: uppercase;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label::before {
        content: '';
        width: 3px;
        height: 18px;
        background: linear-gradient(180deg, #2d7dd2, #1e5aa8);
        border-radius: 999px;
    }

    .form-control,
    .form-select {
        border: 2px solid rgba(45, 125, 210, 0.15);
        border-radius: 1rem;
        padding: 0.85rem 1.15rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, rgba(255,255,255,1) 0%, rgba(248,251,255,1) 100%);
    }

    .form-control::placeholder {
        color: #cbd5e1;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2d7dd2;
        box-shadow: 0 0 0 4px rgba(45, 125, 210, 0.1);
        outline: none;
    }

    .btn-group-custom {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        animation: slideInLeft 0.6s ease-out 0.35s both;
    }

    .btn-group-custom .btn {
        flex: 1;
        padding: 0.85rem 1.5rem;
        font-weight: 700;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
        border: none;
        box-shadow: 0 6px 16px rgba(45, 125, 210, 0.25);
    }

    .btn-outline-secondary {
        border: 2px solid #cbd5e1;
        color: #64748b;
    }

    .btn-outline-secondary:hover {
        background: #64748b;
        border-color: #64748b;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(100, 116, 139, 0.25);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .summary-item {
            padding: 1rem;
        }

        .summary-item .value {
            font-size: 1.1rem;
        }

        .card-header {
            padding: 1.25rem 1.5rem !important;
        }

        .card-header h2 {
            font-size: 0.95rem;
        }

        .btn-group-custom {
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn-group-custom .btn {
            width: 100%;
        }

        .table thead th,
        .table tbody td {
            padding: 0.85rem 0.75rem;
            font-size: 0.85rem;
        }
    }

</style>
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Edit Pesanan</h1>
            <div class="text-muted">Nomor PO: <strong>{{ $pesanan->nomor_po }}</strong></div>
            <div class="text-muted">Status: <span class="badge {{ $pesanan->status === 'menunggu_konfirmasi' ? 'bg-warning' : 'bg-info' }} text-uppercase">{{ str_replace('_', ' ', $pesanan->status) }}</span></div>
        </div>
        <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-outline-secondary">Kembali ke Detail</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <!-- Summary Grid -->
            <div class="summary-grid">
                <div class="summary-item">
                    <small>📅 Tanggal Pesanan</small>
                    <div class="value">{{ $pesanan->tanggal_pesanan->format('d M Y') }}</div>
                </div>
                <div class="summary-item">
                    <small>🚚 Tanggal Pengiriman</small>
                    <div class="value">{{ optional($pesanan->tanggal_pengiriman)->format('d M Y') ?? 'Belum' }}</div>
                </div>
                <div class="summary-item">
                    <small>💰 Total Nilai</small>
                    <div class="value text-primary">Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</div>
                </div>
                <div class="summary-item">
                    <small>📦 Jumlah Item</small>
                    <div class="value">{{ $pesanan->detailPesanan->count() }} item</div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="customer-info">
                <h6>👤 Informasi Pelanggan</h6>
                <div class="customer-name">{{ optional($pesanan->pelanggan)->nama ?? 'Tidak tersedia' }}</div>
                @if($pesanan->pelanggan)
                    <div class="customer-details">
                        <div><strong>📱 Telepon:</strong> {{ $pesanan->pelanggan->telepon }}</div>
                        <div style="margin-top: 0.5rem;"><strong>📍 Alamat:</strong> {{ $pesanan->pelanggan->alamat }}</div>
                    </div>
                @endif
            </div>

            <!-- Current Notes -->
            <div class="notes-box">
                <h3>📝 Catatan Saat Ini</h3>
                <p>{{ $pesanan->catatan ?: 'Belum ada catatan tambahan untuk pesanan ini.' }}</p>
            </div>

            <!-- Item List -->
            <div class="card shadow-sm" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2 class="mb-0">📋 Daftar Item Pesanan</h2>
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
                                                @if(optional($item->produk)->foto_url)
                                                    <img src="{{ $item->produk->foto_url }}" alt="{{ $item->produk->nama }}" class="product-thumb">
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

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="mb-0">✏️ Perbarui Informasi Pengiriman</h2>
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
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="6" placeholder="Masukkan catatan tambahan untuk tim produksi atau logistik...">{{ old('catatan', $pesanan->catatan) }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="btn-group-custom">
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
