@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @php
        $statusClasses = [
            'menunggu_konfirmasi' => 'bg-warning text-dark',
            'dikonfirmasi' => 'bg-info text-white',
            'dalam_produksi' => 'bg-primary text-white',
            'siap_kirim' => 'bg-success text-white',
            'selesai' => 'bg-success text-white',
            'dibatalkan' => 'bg-danger text-white',
        ];
        $statusClass = $statusClasses[$pesanan->status] ?? 'bg-secondary text-white';
        $formatDate = fn($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('d M Y') : '-';
    @endphp

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

        .detail-header {
            gap: 1rem;
            align-items: flex-start;
            animation: fadeInUp 0.6s ease-out;
        }
        .detail-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b 0%, #2d7dd2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.01em;
        }
        .detail-header .btn-group .btn {
            min-width: 130px;
        }
        .detail-header .btn-toolbar {
            display: flex;
            gap: 0.75rem;
        }
        .detail-header .btn-toolbar .btn {
            padding: 0.7rem 1.25rem;
            font-weight: 700;
            border-radius: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .detail-header .btn-toolbar .btn::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: none;
            transition: all 0.3s ease;
        }
        .detail-header .btn-toolbar .btn:hover::before {
            animation: shimmer 0.6s ease-in-out;
        }
        .detail-header .btn-outline-primary {
            border: 2px solid #2d7dd2;
            color: #2d7dd2;
        }
        .detail-header .btn-outline-primary:hover {
            background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
            border-color: #2d7dd2;
            color: white;
            transform: translateY(-2px);
        }
        .detail-header .btn-outline-secondary {
            border: 2px solid #64748b;
            color: #64748b;
        }
        .detail-header .btn-outline-secondary:hover {
            background: #64748b;
            border-color: #64748b;
            color: white;
            transform: translateY(-2px);
        }
        .detail-header .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #f97316) !important;
            border: none;
            color: white;
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);
        }
        .detail-header .btn-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);
        }
        .status-pill {
            border-radius: 999px;
            padding: 0.6rem 1.1rem;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        /* Status Colors */
        .bg-warning {
            background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
            color: white !important;
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25) !important;
        }
        .bg-info {
            background: linear-gradient(135deg, #60a5fa, #3b82f6) !important;
            color: white !important;
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25) !important;
        }
        .bg-primary {
            background: linear-gradient(135deg, #2d7dd2, #1e5aa8) !important;
            color: white !important;
            box-shadow: 0 6px 16px rgba(45, 125, 210, 0.25) !important;
        }
        .bg-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: white !important;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25) !important;
        }
        .bg-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25) !important;
        }

        .info-panel {
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            border: 2px solid rgba(45, 125, 210, 0.15);
            border-radius: 1.75rem;
            padding: 1.5rem;
            min-height: 100%;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(45, 125, 210, 0.1);
        }
        .info-panel::before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            top: -40px;
            right: -40px;
            background: radial-gradient(circle, rgba(45, 125, 210, 0.15) 0%, transparent 70%);
            transition: all 0.6s ease;
        }
        .info-panel:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(45, 125, 210, 0.15);
            border-color: rgba(45, 125, 210, 0.25);
        }
        .info-panel:hover::before {
            transform: translate(-30px, -30px);
        }
        .info-panel h6 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #2d7dd2;
            margin-bottom: 1.2rem;
            font-weight: 800;
            position: relative;
            z-index: 1;
        }
        .info-panel p {
            margin-bottom: 0.65rem;
            color: #475569;
            position: relative;
            z-index: 1;
            font-weight: 500;
        }
        .info-panel p strong {
            color: #1e293b;
            font-weight: 700;
            letter-spacing: -0.01em;
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
        .card-body {
            position: relative;
            z-index: 1;
        }

        .table-modern {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 2px solid rgba(45, 125, 210, 0.15);
            animation: fadeInUp 0.6s ease-out 0.1s both;
            box-shadow: 0 10px 30px rgba(45, 125, 210, 0.08);
        }
        .table-modern thead {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.15) 0%, rgba(59, 130, 246, 0.1) 100%);
        }
        .table-modern th {
            border: none;
            color: #1e293b;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.1rem 1.25rem;
            font-weight: 800;
            background: transparent;
        }
        .table-modern td {
            border: none;
            background: transparent;
            padding: 1.1rem 1.25rem;
            vertical-align: middle;
            color: #475569;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .table-modern tbody tr {
            transition: all 0.3s ease;
            animation: slideInLeft 0.5s ease-out both;
        }
        .table-modern tbody tr:nth-child(1) { animation-delay: 0.15s; }
        .table-modern tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .table-modern tbody tr:nth-child(3) { animation-delay: 0.25s; }
        .table-modern tbody tr:hover {
            background: rgba(45, 125, 210, 0.05);
        }
        .table-modern tbody tr:hover td {
            color: #2d7dd2;
        }
        .table-modern tbody tr:not(:last-child) td {
            border-bottom: 1px solid rgba(219, 229, 241, 0.5);
        }

        .history-item {
            border: 2px solid rgba(45, 125, 210, 0.15);
            border-radius: 1.5rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            position: relative;
            overflow: hidden;
            animation: slideInLeft 0.5s ease-out both;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .history-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #2d7dd2, #1e5aa8);
            transform: scaleY(0);
            transform-origin: top;
            transition: transform 0.3s ease;
        }
        .history-item:hover {
            transform: translateX(6px);
            box-shadow: 0 10px 30px rgba(45, 125, 210, 0.15);
            border-color: rgba(45, 125, 210, 0.3);
        }
        .history-item:hover::before {
            transform: scaleY(1);
        }
        .history-item:nth-child(1) { animation-delay: 0s; }
        .history-item:nth-child(2) { animation-delay: 0.08s; }
        .history-item:nth-child(3) { animation-delay: 0.16s; }
        .history-item:last-child {
            margin-bottom: 0;
        }
        .history-item strong {
            color: #2d7dd2;
            font-weight: 800;
            letter-spacing: -0.01em;
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

        .product-name {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.2rem;
            letter-spacing: -0.01em;
        }
        .product-meta {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
        }

        .summary-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(240, 249, 255, 1) 100%);
        }

        .summary-card .list-group-item {
            border: none;
            padding: 1rem 1.1rem;
            background: transparent;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
        }
        .summary-card .list-group-item:hover {
            background: rgba(45, 125, 210, 0.05);
            padding-left: 1.3rem;
        }
        .summary-card .list-group-item:hover span:first-child {
            color: #2d7dd2;
            font-weight: 600;
        }
        .summary-card .list-group-item + .list-group-item {
            border-top: 1px solid rgba(45, 125, 210, 0.1);
        }
        .summary-card .list-group-item span {
            color: #475569;
            transition: all 0.3s ease;
        }
        .summary-card .list-group-item strong {
            color: #1e293b;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .detail-header h1 {
                font-size: 1.4rem;
            }
            .detail-header .btn-toolbar {
                width: 100%;
                flex-wrap: wrap;
            }
            .detail-header .btn-toolbar .btn {
                flex: 1;
                min-width: 100px;
                font-size: 0.9rem;
                padding: 0.6rem 1rem;
            }
            .info-panel {
                padding: 1.25rem;
                margin-bottom: 1rem;
            }
            .table-modern th,
            .table-modern td {
                padding: 0.85rem 0.75rem;
                font-size: 0.85rem;
            }
            .product-thumb {
                width: 48px;
                height: 48px;
            }
        }
    </style>

    <div class="d-flex detail-header justify-content-between mb-4 flex-wrap">
        <div>
            <h1>Detail Pesanan</h1>
            <p class="text-muted mb-0">{{ $pesanan->nomor_po }}</p>
        </div>
        <div class="btn-toolbar gap-2">
            <a href="{{ route('pesanan.pdf', $pesanan) }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf me-1"></i>Unduh PDF
            </a>
            <form action="{{ route('pesanan.shareLink', $pesanan) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-share-alt me-1"></i>Bagikan
                </button>
            </form>
            @if(auth()->user()->role !== 'operator_gudang')
                <a href="{{ route('pesanan.edit', $pesanan) }}" class="btn btn-warning text-white">
                    <i class="fas fa-edit me-1"></i>Edit

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const statusSelect = document.querySelector('form[action*="updateStatus"] select[name="status"]');
                    const siapKirimFields = document.getElementById('siap-kirim-fields');

                    if (!statusSelect) return;

                    function toggleSiapKirim() {
                        if (statusSelect.value === 'siap_kirim') {
                            siapKirimFields.style.display = '';
                        } else {
                            siapKirimFields.style.display = 'none';
                        }
                    }

                    statusSelect.addEventListener('change', toggleSiapKirim);
                    // initial
                    toggleSiapKirim();
                });
            </script>
            @endpush
                </a>
            @endif
        </div>
    </div>

    @if(!empty($detail_kurang))
        <div class="alert alert-warning rounded-4 mb-4">
            <strong>Perhatian:</strong> Stok beberapa produk pada PO ini tidak mencukupi.
            <div class="mt-2">
                <ul class="mb-0">
                    @foreach($detail_kurang as $d)
                        <li>{{ $d['nama_produk'] }}: Dipesan {{ $d['jumlah_dipesan'] }} unit — Stok tersedia {{ $d['stok_tersedia'] }} (kurang {{ $d['kurang'] }} unit)</li>
                    @endforeach
                </ul>
            </div>
            @if(auth()->user()->role === 'operator_gudang')
                <div class="mt-3">
                    <button class="btn btn-sm btn-success" onclick="location.href='{{ route('produk.index') }}'">Tambah Stok</button>
                </div>
            @endif
        </div>
    @endif

    @if(session('share_link'))
        <div class="alert alert-success rounded-4">Link share siap: <a href="{{ session('share_link') }}" target="_blank">{{ session('share_link') }}</a></div>
    @endif

    @foreach(['success','error','info'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} rounded-4">{{ session($msg) }}</div>
        @endif
    @endforeach

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-panel">
                                <h6>Informasi PO</h6>
                                <p><strong>Nomor PO:</strong> {{ $pesanan->nomor_po }}</p>
                                <p><strong>Tanggal Pesanan:</strong> {{ $formatDate($pesanan->tanggal_pesanan) }}</p>
                                <p><strong>Tanggal Pengiriman:</strong> {{ $formatDate($pesanan->tanggal_pengiriman) }}</p>
                                <p><strong>Status:</strong> <span class="status-pill {{ $statusClass }} text-capitalize">{{ str_replace('_', ' ', $pesanan->status) }}</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-panel">
                                <h6>Pelanggan</h6>
                                <p><strong>Nama:</strong> {{ $pesanan->pelanggan->nama }}</p>
                                <p><strong>Telepon:</strong> {{ $pesanan->pelanggan->telepon }}</p>
                                <p><strong>Alamat:</strong> {{ $pesanan->pelanggan->alamat }}</p>
                                <p><strong>Email:</strong> {{ $pesanan->pelanggan->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="mb-1">Item Pesanan</h5>
                                <small class="text-muted">Detail produk dan biaya per item.</small>
                            </div>
                        </div>

                        <div class="table-modern table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Produk</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pesanan->detailPesanan as $index => $item)
                                                <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if(optional($item->produk)->foto_url)
                                                        <img src="{{ $item->produk->foto_url }}" alt="{{ $item->produk->nama }}" class="product-thumb">
                                                    @else
                                                        <div class="product-thumb d-flex align-items-center justify-content-center text-muted small">No</div>
                                                    @endif
                                                    <div>
                                                        <div class="product-name">{{ $item->produk->nama ?? 'Produk tidak tersedia' }}</div>
                                                        <div class="product-meta">{{ $item->spesifikasi ?? 'Tanpa spesifikasi' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->jumlah * $item->harga_satuan, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 rounded-4 bg-light p-3">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <p class="text-muted mb-1">Total Nilai</p>
                                <h4 class="mb-0">Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</h4>
                            </div>
                            <div class="col-sm-6">
                                <p class="text-muted mb-1">Catatan</p>
                                <p class="mb-0">{{ $pesanan->catatan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="mb-4">Histori Status</h5>
                    @forelse($pesanan->historiStatus as $history)
                        <div class="history-item">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                                <div>
                                    <strong>{{ ucfirst(str_replace('_', ' ', $history->status)) }}</strong>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">{{ $history->created_at ? $history->created_at->format('d M Y H:i') . ' WIB' : '-' }}</small>
                                    <small class="text-muted-light" style="font-size: 0.75rem; color: #9ca3af;">{{ $history->created_at ? $history->created_at->diffForHumans() : '' }}</small>
                                </div>
                            </div>
                            <p class="mb-1 text-muted">{{ $history->keterangan }}</p>
                            <small class="text-muted">oleh {{ $history->user->name ?? 'Sistem' }}</small>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada histori status.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card summary-card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Ringkasan</h5>
                        <span class="status-pill {{ $statusClass }} text-capitalize">{{ str_replace('_', ' ', $pesanan->status) }}</span>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Total Item</span>
                            <strong>{{ $pesanan->detailPesanan->sum('jumlah') }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Total Nilai</span>
                            <strong>Rp {{ number_format($pesanan->total_nilai, 0, ',', '.') }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Disimpan oleh</span>
                            <strong>{{ $pesanan->creator->name ?? 'System' }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>PO dibuat</span>
                            <strong>{{ $formatDate($pesanan->created_at) }}</strong>
                        </div>
                        @if($pesanan->tanggal_dikirim)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Tanggal Dikirim</span>
                            <strong>{{ $formatDate($pesanan->tanggal_dikirim) }}</strong>
                        </div>
                        @endif
                        @if($pesanan->nomor_resi)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Nomor Resi</span>
                            <strong>{{ $pesanan->nomor_resi }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'administrator' || auth()->user()->role === 'pemilik_umkm')
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Batalkan Pesanan</h5>
                        <button type="button" class="btn btn-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalBatalkanPesanan">
                            Batalkan Pesanan
                        </button>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="mb-3">Ubah Status</h5>

                    @if(count($statusOptions) > 0)
                        <form action="{{ route('pesanan.updateStatus', $pesanan) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Status Baru</label>
                                <select class="form-select" name="status">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="3" placeholder="Keterangan singkat..."></textarea>
                            </div>
                            <div id="siap-kirim-fields" class="mb-3" style="display: none;">
                                <label class="form-label">Detail Pengiriman</label>
                                <div class="mb-2">
                                    <input type="date" class="form-control" name="tanggal_dikirim" placeholder="Tanggal pengiriman">
                                </div>
                                <div>
                                    <input type="text" class="form-control" name="nomor_resi" placeholder="Nomor resi / tracking (opsional)">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">Simpan Status</button>
                        </form>
                    @else
                        <div class="alert alert-secondary mb-0">
                            Status pesanan tidak dapat diubah lagi karena sudah selesai, dibatalkan, atau Anda tidak memiliki izin untuk mengubah status di tahapan ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batalkan Pesanan -->
<div class="modal fade" id="modalBatalkanPesanan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>Batalkan Pesanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pesanan.batalkan', $pesanan) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Anda akan membatalkan pesanan <strong>{{ $pesanan->nomor_po }}</strong>. 
                        Mohon jelaskan alasan pembatalan ini untuk dokumentasi.
                    </p>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea 
                            class="form-control rounded-3 @error('alasan_pembatalan') is-invalid @enderror" 
                            name="alasan_pembatalan" 
                            rows="4"
                            placeholder="Jelaskan alasan pembatalan pesanan ini (minimal 5 karakter)..."
                            required
                        >{{ old('alasan_pembatalan') }}</textarea>
                        @error('alasan_pembatalan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="fas fa-check me-2"></i>Ya, Batalkan Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
