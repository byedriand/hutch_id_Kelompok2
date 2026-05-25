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
        .detail-header {
            gap: 1rem;
            align-items: flex-start;
        }
        .detail-header h1 {
            font-size: 1.65rem;
            font-weight: 700;
        }
        .detail-header .btn-group .btn {
            min-width: 130px;
        }
        .status-pill {
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            font-size: 0.82rem;
            letter-spacing: 0.01em;
        }
        .info-panel {
            background: #f7fbff;
            border: 1px solid #e3ecf8;
            border-radius: 1.5rem;
            padding: 1.25rem;
            min-height: 100%;
        }
        .info-panel h6 {
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #718096;
            margin-bottom: 1rem;
        }
        .info-panel p {
            margin-bottom: 0.5rem;
            color: #475569;
        }
        .info-panel p strong {
            color: #0f172a;
        }
        .table-modern {
            border-radius: 1.25rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .table-modern thead {
            background: #eef4ff;
        }
        .table-modern th {
            border: none;
            color: #475569;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 1rem 1rem;
        }
        .table-modern td {
            border: none;
            background: #ffffff;
            padding: 1rem 1rem;
            vertical-align: middle;
            color: #334155;
        }
        .table-modern tbody tr:not(:last-child) td {
            border-bottom: 1px solid #f1f5f9;
        }
        .history-item {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #ffffff;
        }
        .history-item:last-child {
            margin-bottom: 0;
        }
        .history-item strong {
            color: #0f172a;
        }
        .product-thumb {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .product-name {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.15rem;
        }
        .product-meta {
            font-size: 0.9rem;
            color: #64748b;
        }
        .summary-card .list-group-item {
            border: none;
            padding: 0.9rem 1.1rem;
            background: transparent;
        }
        .summary-card .list-group-item + .list-group-item {
            border-top: 1px solid #e2e8f0;
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
                            <span class="status-pill {{ $statusClass }} text-capitalize">{{ str_replace('_', ' ', $pesanan->status) }}</span>
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
                                                    @if(optional($item->produk)->gambar)
                                                        <img src="{{ filter_var($item->produk->gambar, FILTER_VALIDATE_URL) ? $item->produk->gambar : asset('storage/' . ltrim($item->produk->gambar, '/')) }}" alt="{{ $item->produk->nama }}" class="product-thumb">
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
                                <small class="text-muted">{{ $history->created_at ? \Illuminate\Support\Carbon::parse($history->created_at)->format('d M Y H:i') : '-' }}</small>
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
                        <form action="{{ route('pesanan.batalkan', $pesanan) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 rounded-pill" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                Batalkan Pesanan
                            </button>
                        </form>
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
@endsection
