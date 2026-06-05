@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-blue-light: #0ea5e9;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #0ea5e9 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .container-fluid {
            animation: fadeInUp 0.5s ease both;
        }
        
        .page-header {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1.6rem;
            padding: 1.8rem 1.8rem;
            border-radius: 1.75rem;
            background: linear-gradient(135deg, rgba(248, 250, 255, 0.98) 0%, rgba(225, 243, 254, 0.95) 100%);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.08), 0 0 0 1px rgba(59, 130, 246, 0.1);
            animation: fadeInUp 0.55s ease both;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1.75rem;
            background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.15), transparent 50%),
                        radial-gradient(circle at bottom left, rgba(14, 165, 233, 0.1), transparent 50%);
            pointer-events: none;
        }
        
        .page-header > * {
            position: relative;
            z-index: 1;
        }
        
        .page-header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
            font-weight: 800;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .page-header p {
            color: #64748b;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .filter-card {
            border-radius: 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 55px rgba(15, 64, 124, 0.08);
            background: linear-gradient(135deg, #ffffff 0%, rgba(248, 250, 255, 0.5) 100%);
            animation: fadeInUp 0.45s ease both;
            padding: 1.5rem 1.6rem;
            transition: all 0.3s ease;
        }
        
        .filter-card:hover {
            box-shadow: 0 25px 70px rgba(37, 99, 235, 0.12);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .filter-card .form-label {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        
        .filter-card .form-control,
        .filter-card .form-select,
        .filter-card .input-group-text {
            border-radius: 1.1rem;
            min-height: 56px;
            border: 1.5px solid #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(15, 64, 124, 0.04);
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
            transition: all 0.3s ease;
        }
        
        .filter-card .input-group-text {
            border-right: 0;
            color: var(--primary-blue);
            background: rgba(255, 255, 255, 0.95);
            border-left-width: 1.5px;
        }
        
        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1), inset 0 2px 4px rgba(15, 64, 124, 0.04);
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
        }
        
        .filter-card .btn {
            min-height: 56px;
            border-radius: 1.1rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .filter-card .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(15, 64, 124, 0.15);
        }
        
        .filter-card .btn-primary {
            background: var(--gradient-primary);
            border: none;
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.25);
        }
        
        .filter-card .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%);
        }
        
        .filter-card .btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.5);
            color: #475569;
            background: rgba(248, 250, 255, 0.95);
            border-width: 1.5px;
        }
        
        .filter-card .btn-outline-secondary:hover {
            background: rgba(226, 232, 240, 0.8);
            border-color: rgba(100, 116, 139, 0.5);
            color: #1e293b;
        }
        
        .data-card {
            border-radius: 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 55px rgba(15, 64, 124, 0.08);
            background: linear-gradient(135deg, #ffffff 0%, rgba(248, 250, 255, 0.5) 100%);
            animation: fadeInUp 0.45s ease both;
            animation-delay: 0.12s;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .data-card:hover {
            box-shadow: 0 25px 70px rgba(37, 99, 235, 0.12);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .archive-table {
            width: 100%;
            min-width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 1.75rem;
        }
        
        .archive-table thead {
            background: linear-gradient(135deg, rgba(226, 232, 240, 0.5) 0%, rgba(214, 228, 248, 0.5) 100%);
            border-bottom: 2px solid rgba(59, 130, 246, 0.15);
        }
        
        .archive-table th,
        .archive-table td {
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
            padding: 1.2rem 1rem;
            vertical-align: middle;
            transition: all 0.3s ease;
        }
        
        .archive-table th {
            color: #1e293b;
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: linear-gradient(135deg, rgba(236, 242, 255, 0.8) 0%, rgba(224, 242, 254, 0.8) 100%);
        }
        
        .archive-table td {
            background: #ffffff;
            color: #334155;
        }
        
        .archive-table tbody tr {
            animation: fadeInUp 0.4s ease both;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .archive-table tbody tr:nth-child(odd) td {
            background: rgba(248, 250, 255, 0.8);
        }
        
        .archive-table tbody tr:hover {
            box-shadow: 0 0 0 1000px rgba(59, 130, 246, 0.05) inset;
        }
        
        .archive-table tbody tr:hover td {
            background: rgba(59, 130, 246, 0.08);
            color: #0f172a;
        }
        
        .archive-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .archive-table .mono {
            font-family: 'Fira Code', monospace;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--primary-blue);
        }
        
        .archive-table .btn-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .archive-table .btn-group .btn {
            border-radius: 1rem;
            min-width: 44px;
            height: 44px;
            padding: 0 0.85rem;
            border: 1.5px solid transparent;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .archive-table .btn-group .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }
        
        .archive-table .btn-group .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.4);
        }
        
        .archive-table .btn-group .btn-secondary {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-color: #cbd5e1;
            color: #475569;
            box-shadow: 0 4px 8px rgba(15, 64, 124, 0.08);
        }
        
        .archive-table .btn-group .btn-secondary:hover {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border-color: #94a3b8;
            color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(15, 64, 124, 0.15);
        }
        
        .summary-footer {
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(225, 243, 254, 0.8) 0%, rgba(240, 249, 255, 0.9) 100%);
            padding: 1.4rem 1.6rem;
            border: 1.5px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.1);
            animation: fadeInUp 0.45s ease both;
            animation-delay: 0.18s;
            transition: all 0.3s ease;
        }
        
        .summary-footer:hover {
            box-shadow: 0 16px 40px rgba(37, 99, 235, 0.15);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .summary-footer .fw-semibold {
            font-size: 0.95rem;
            color: #0f172a;
        }
        
        .summary-footer .text-muted {
            color: #64748b;
        }
        
        /* Enhanced Status Badge Styling */
        .status-badge {
            border-radius: 999px;
            padding: 0.7rem 1.2rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-transform: capitalize;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1.5px solid transparent;
        }
        
        .status-badge::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: translateX(-100%);
        }
        
        .status-badge:hover::before {
            animation: shimmer 0.6s ease;
        }
        
        .b-done {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.08) 100%);
            color: #065f46;
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        
        .b-done:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.25) 0%, rgba(5, 150, 105, 0.15) 100%);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }
        
        .b-done::after {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.2rem;
            height: 1.2rem;
            background: rgba(16, 185, 129, 0.3);
            border-radius: 50%;
            font-size: 0.7rem;
            margin-right: -0.3rem;
        }
        
        .b-cancel {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.08) 100%);
            color: #7f1d1d;
            border-color: rgba(239, 68, 68, 0.3);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }
        
        .b-cancel:hover {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.25) 0%, rgba(220, 38, 38, 0.15) 100%);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }
        
        .b-cancel::after {
            content: '✕';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.2rem;
            height: 1.2rem;
            background: rgba(239, 68, 68, 0.3);
            border-radius: 50%;
            font-size: 0.7rem;
            margin-right: -0.3rem;
        }
        
        .animated-block {
            animation: fadeInScale 0.45s ease both;
        }
        
        .table-responsive {
            animation: fadeInUp 0.45s ease both;
        }
        
        .btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
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
        
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(37, 99, 235, 0);
            }
        }
        
        @media (max-width: 991px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .filter-card {
                padding: 1.2rem;
            }
            
            .archive-table th,
            .archive-table td {
                padding: 1rem 0.75rem;
            }
            
            .archive-table .btn-group .btn {
                min-width: 40px;
                height: 40px;
                font-size: 0.85rem;
            }
        }
    </style>

    <div class="page-header animated-block">
        <div>
            <h1>Arsip PDF</h1>
            <p>Daftar Purchase Order yang telah selesai atau dibagikan</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('pesanan.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i>Buat PO Baru
            </a>
            <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-list me-1"></i>Daftar Pesanan
            </a>
        </div>
    </div>

    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('arsip.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Cari Nama Pelanggan</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-primary"></i></span>
                    <input type="text" name="cari" class="form-control" placeholder="Cari nama pelanggan..." value="{{ request('cari') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-md-2 d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="{{ route('arsip.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-redo me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="data-card mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table archive-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Nomor PO</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th style="width: 160px;">Pelanggan</th>
                            <th>Produk Utama</th>
                            <th style="width: 130px;">Total Nilai</th>
                            <th style="width: 100px;">Tgl Kirim</th>
                            <th style="width: 140px;">Status</th>
                            <th style="width: 100px;">Aksi</th>
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
                                    <div class="fw-semibold">{{ $po->pelanggan->nama ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ optional($po->pelanggan)->telepon ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        @php
                                            $firstItem = optional($po->detailPesanan->first());
                                        @endphp
                                        @if($firstItem && $firstItem->produk)
                                            {{ $firstItem->produk->nama }} ({{ $firstItem->jumlah }} pcs)
                                            @if($po->detailPesanan->count() > 1)
                                                <br><span class="text-muted">+{{ $po->detailPesanan->count() - 1 }} item lainnya</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </div>
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
                                            'selesai' => ['class' => 'b-done', 'text' => 'Selesai', 'icon' => 'fas fa-check-circle'],
                                            'dibatalkan' => ['class' => 'b-cancel', 'text' => 'Dibatalkan', 'icon' => 'fas fa-times-circle'],
                                        ];
                                        $badge = $badgeMap[$po->status] ?? ['class' => 'badge bg-secondary', 'text' => ucfirst(str_replace('_', ' ', $po->status)), 'icon' => 'fas fa-circle'];
                                    @endphp
                                    <span class="status-badge {{ $badge['class'] }}">
                                        <i class="{{ $badge['icon'] }}"></i>
                                        {{ $badge['text'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('pesanan.pdf', $po) }}" class="btn btn-sm btn-primary" title="Unduh PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('pesanan.show', $po) }}" class="btn btn-sm btn-secondary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada pesanan di arsip</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 summary-footer">
        <div>
            <div class="text-muted">Menampilkan</div>
            <div class="fw-semibold">{{ $pesanan->firstItem() ?? 0 }}–{{ $pesanan->lastItem() ?? 0 }} dari {{ $pesanan->total() }} pesanan</div>
        </div>
        <nav>
            {{ $pesanan->withQueryString()->links() }}
        </nav>
    </div>
</div>
@endsection
