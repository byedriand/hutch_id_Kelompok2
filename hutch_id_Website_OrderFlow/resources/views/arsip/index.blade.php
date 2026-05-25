@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
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
            padding: 1.6rem 1.5rem;
            border-radius: 1.75rem;
            background: linear-gradient(180deg, rgba(248, 250, 255, 0.95), rgba(241, 245, 255, 0.95));
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 24px 60px rgba(15, 64, 124, 0.06);
            animation: fadeInUp 0.55s ease both;
        }
        .page-header::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1.75rem;
            background: radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 38%);
            pointer-events: none;
        }
        .page-header > * {
            position: relative;
            z-index: 1;
        }
        .page-header h1 {
            font-size: 2rem;
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
            font-weight: 700;
        }
        .page-header p {
            color: #475569;
            margin-bottom: 0;
            font-size: 1rem;
        }
        .filter-card,
        .data-card {
            border-radius: 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 20px 55px rgba(15, 64, 124, 0.05);
            background: #ffffff;
            animation: fadeInUp 0.45s ease both;
        }
        .filter-card {
            padding: 1.3rem 1.4rem;
        }
        .filter-card .form-label {
            font-weight: 700;
            color: #334155;
        }
        .filter-card .form-control,
        .filter-card .form-select,
        .filter-card .input-group-text {
            border-radius: 1rem;
            min-height: 56px;
            border: 1px solid #d8e2ef;
            box-shadow: inset 0 1px 2px rgba(15, 64, 124, 0.04);
            background: #f8fbff;
        }
        .filter-card .input-group-text {
            border-right: 0;
            color: #2563eb;
            background: rgba(255,255,255,0.92);
        }
        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: rgba(59, 130, 246, 0.8);
            box-shadow: 0 0 0 0.15rem rgba(59, 130, 246, 0.12);
        }
        .filter-card .btn {
            min-height: 56px;
            border-radius: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        .filter-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(15, 64, 124, 0.12);
        }
        .filter-card .btn-primary {
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            border: none;
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.18);
        }
        .filter-card .btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.4);
            color: #334155;
            background: rgba(248, 250, 255, 0.9);
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
            background: #eef4ff;
        }
        .archive-table th,
        .archive-table td {
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            padding: 1rem 1rem;
            vertical-align: middle;
        }
        .archive-table th {
            color: #475569;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .archive-table td {
            background: #ffffff;
            color: #334155;
        }
        .archive-table tbody tr {
            animation: fadeInUp 0.4s ease both;
        }
        .archive-table tbody tr:nth-child(odd) td {
            background: #fbfdff;
        }
        .archive-table tbody tr:hover td {
            background: rgba(59, 130, 246, 0.08);
        }
        .archive-table tbody tr:last-child td {
            border-bottom: none;
        }
        .archive-table .mono {
            font-family: 'Fira Code', monospace;
            font-size: 0.92rem;
        }
        .archive-table .btn-group .btn {
            border-radius: 0.95rem;
            min-width: 38px;
            height: 38px;
            padding: 0 0.75rem;
        }
        .archive-table .btn-group .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }
        .archive-table .btn-group .btn-secondary {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #334155;
        }
        .archive-table .btn-group .btn:hover {
            transform: translateY(-1px);
        }
        .summary-footer {
            border-radius: 1.5rem;
            background: linear-gradient(90deg, rgba(238, 246, 255, 0.9), rgba(255, 255, 255, 0.92));
            padding: 1rem 1.25rem;
            border: 1px solid rgba(219, 234, 254, 0.9);
            box-shadow: inset 0 0 0 rgba(59, 130, 246, 0.05);
            animation: fadeInUp 0.45s ease both;
        }
        .summary-footer .fw-semibold {
            font-size: 0.95rem;
        }
        .status-badge {
            border-radius: 999px;
            padding: 0.55rem 0.95rem;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: capitalize;
        }
        .b-done {
            background: rgba(34, 197, 94, 0.12);
            color: #166534;
        }
        .b-cancel {
            background: rgba(239, 68, 68, 0.12);
            color: #991b1b;
        }
        .animated-block {
            animation: fadeInScale 0.45s ease both;
        }
        .filter-card,
        .data-card,
        .summary-footer,
        .table-responsive {
            animation: fadeInUp 0.45s ease both;
        }
        .filter-card {
            animation-delay: 0.08s;
        }
        .data-card {
            animation-delay: 0.12s;
        }
        .summary-footer {
            animation-delay: 0.18s;
        }
        .btn {
            transition: transform 0.25s ease, box-shadow 0.25s ease, background-color 0.25s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(18px);
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
        @media (max-width: 991px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-card {
                padding: 1rem;
            }
            .archive-table th,
            .archive-table td {
                padding: 0.9rem 0.75rem;
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
                                            'selesai' => ['class' => 'b-done text-white bg-success', 'text' => 'Selesai'],
                                            'dibatalkan' => ['class' => 'b-cancel text-white bg-danger', 'text' => 'Dibatalkan'],
                                        ];
                                        $badge = $badgeMap[$po->status] ?? ['class' => 'badge bg-secondary', 'text' => ucfirst(str_replace('_', ' ', $po->status))];
                                    @endphp
                                    <span class="status-badge {{ $badge['class'] }}">{{ $badge['text'] }}</span>
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
