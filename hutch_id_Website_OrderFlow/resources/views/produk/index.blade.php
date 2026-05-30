@extends('layouts.app')

@section('content')
<style>
    .stok-header {
        background: linear-gradient(180deg, rgba(248, 250, 255, 0.95), rgba(241, 245, 255, 0.95));
        border-radius: 1.75rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(219, 234, 254, 0.5);
        animation: fadeInUp 0.55s ease-out;
    }
    .stok-header::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.08), transparent 70%);
        border-radius: 50%;
    }
    .stok-header > div {
        position: relative;
        z-index: 1;
    }
    .stok-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .stok-header-desc {
        color: #64748b;
        font-size: 1.05rem;
    }
    .stok-summary-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 197, 253, 0.08));
        border-left: 4px solid #3b82f6;
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid rgba(147, 197, 253, 0.2);
        animation: fadeInUp 0.6s ease-out 0.1s both;
    }
    .stok-summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .stok-summary-item:last-child {
        margin-bottom: 0;
    }
    .stok-summary-label {
        color: #64748b;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .stok-summary-value {
        color: #2563eb;
        font-size: 1.5rem;
        font-weight: 800;
    }
    .stok-summary-subtext {
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .stok-card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(219, 234, 254, 0.3);
        border-radius: 1.5rem;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out 0.15s both;
    }
    .stok-card-header {
        background: linear-gradient(90deg, #f8fbff, #ffffff);
        border-bottom: 1px solid rgba(219, 234, 254, 0.3);
        padding: 1.5rem;
    }
    .stok-card-header h5 {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }
    .stok-card-body {
        padding: 0;
    }

    .stok-table {
        margin-bottom: 0;
    }
    .stok-table thead {
        background: #f8fbff;
    }
    .stok-table thead th {
        border: none;
        color: #475569;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 1rem 1.5rem;
        text-transform: none;
    }
    .stok-table tbody tr {
        border: none;
        border-bottom: 1px solid rgba(219, 234, 254, 0.4);
        background: #ffffff;
        transition: all 0.25s ease;
    }
    .stok-table tbody tr:nth-child(odd) {
        background: #fbfdff;
    }
    .stok-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.06);
    }
    .stok-table tbody td {
        padding: 1.25rem 1.5rem;
        color: #334155;
        border: none;
        vertical-align: middle;
    }
    .stok-table .product-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.98rem;
    }
    .stok-table .price {
        font-weight: 600;
        color: #2563eb;
        font-size: 0.95rem;
    }
    .stok-table .stock-number {
        color: #2d7dd2;
        font-size: 1.15rem;
        font-weight: 800;
        font-family: 'Courier New', monospace;
    }

    .badge-status {
        border-radius: 999px;
        padding: 0.55rem 0.95rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .badge-status i {
        font-size: 0.85rem;
    }
    .badge-tersedia {
        background: rgba(34, 197, 94, 0.12);
        color: #166534;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }
    .badge-rendah {
        background: rgba(234, 179, 8, 0.12);
        color: #92400e;
        border: 1px solid rgba(234, 179, 8, 0.3);
    }
    .badge-kosong {
        background: rgba(239, 68, 68, 0.12);
        color: #991b1b;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .btn-stok-aksi {
        border-radius: 0.9rem;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.25s ease;
        border: none;
    }
    .btn-stok-ubah {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }
    .btn-stok-ubah:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .btn-stok-cepat {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
    }
    .btn-stok-cepat:hover {
        background: rgba(59, 130, 246, 0.2);
        color: #1d4ed8;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        animation: fadeInUp 0.6s ease-out 0.2s both;
    }
    .empty-state i {
        color: #d1d5db;
        opacity: 0.5;
        margin-bottom: 1.5rem;
        display: block;
    }
    .empty-state h5 {
        color: #1e293b;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        color: #94a3b8;
    }

    .pagination-wrap {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        animation: fadeInUp 0.6s ease-out 0.25s both;
    }

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

    .stok-table tbody tr {
        animation: fadeInUp 0.45s ease-out;
    }
    .stok-table tbody tr:nth-child(1) { animation-delay: 0.05s; }
    .stok-table tbody tr:nth-child(2) { animation-delay: 0.08s; }
    .stok-table tbody tr:nth-child(3) { animation-delay: 0.11s; }
    .stok-table tbody tr:nth-child(4) { animation-delay: 0.14s; }
    .stok-table tbody tr:nth-child(5) { animation-delay: 0.17s; }
    .stok-table tbody tr:nth-child(n+6) { animation-delay: 0.2s; }
</style>

<div class="container-fluid">
    <div class="stok-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Manajemen Stok Barang</h1>
                <p class="stok-header-desc mb-0">Kelola stok produk dan pantau ketersediaan barang</p>
            </div>
            <a href="javascript:void(0)" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.9rem; padding: 0.75rem 1.5rem; font-weight: 600; transition: all 0.25s ease;" onclick="showAddStokModal()" title="Tambah Stok Baru">
                <i class="fas fa-plus me-2"></i>Tambah Stok
            </a>
        </div>
        <div class="stok-summary-box">
            <div class="stok-summary-item">
                <span class="stok-summary-label">Total Stok Tersedia</span>
                <span class="stok-summary-value">{{ $totalStok }}</span>
            </div>
            <div class="stok-summary-item">
                <span class="stok-summary-label">Produk Terdaftar</span>
                <span class="stok-summary-subtext">{{ $jumlahProduk }} produk</span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 1.25rem; border: 1px solid rgba(34, 197, 94, 0.3); background: rgba(34, 197, 94, 0.08); animation: fadeInUp 0.45s ease-out;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 1.25rem; border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.08); animation: fadeInUp 0.45s ease-out;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="stok-card">
        <div class="stok-card-header">
            <h5>Daftar Produk</h5>
        </div>
        <div class="stok-card-body">
            @if ($produk->count() > 0)
                <div class="table-wrap">
                    <table class="table table-hover stok-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Produk</th>
                                <th class="text-end" width="12%">Harga Jual</th>
                                <th class="text-center" width="10%">Stok Saat Ini</th>
                                <th class="text-center" width="15%">Status</th>
                                <th class="text-center" width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produk as $index => $item)
                                <tr>
                                    <td>{{ $produk->firstItem() + $index }}</td>
                                    <td>
                                        <span class="product-name">{{ $item->nama }}</span>
                                    </td>
                                    <td class="text-end price">
                                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="stock-number">{{ $item->stok }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->stok == 0)
                                            <span class="badge-status badge-kosong">
                                                <i class="fas fa-times-circle"></i>Kosong
                                            </span>
                                        @elseif ($item->stok <= 10)
                                            <span class="badge-status badge-rendah">
                                                <i class="fas fa-exclamation-triangle"></i>Rendah
                                            </span>
                                        @else
                                            <span class="badge-status badge-tersedia">
                                                <i class="fas fa-check-circle"></i>Tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-sm btn-stok-aksi btn-stok-ubah" title="Edit Stok">
                                            <i class="fas fa-edit me-1"></i>Ubah
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-box-open fa-3x"></i>
                                            <h5>Tidak ada produk yang tersedia</h5>
                                            <p class="mb-0">Mulai dengan menambahkan produk baru ke dalam sistem</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($produk->hasPages())
                    <div class="pagination-wrap">
                        {{ $produk->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox fa-4x"></i>
                    <h5>Tidak Ada Data Produk</h5>
                    <p class="mb-0">Tidak ada produk yang dapat dikelola saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Stok Baru -->
<div class="modal fade" id="addStokModal" tabindex="-1" aria-labelledby="addStokModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 1.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.12);">
            <div class="modal-header" style="background: linear-gradient(135deg, #10b981, #059669); border: none; border-radius: 1.5rem 1.5rem 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="addStokModalLabel" style="color: white; font-weight: 700;">
                    <i class="fas fa-box-open me-2"></i>Tambah Produk Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <form id="addStokForm" method="POST" action="{{ route('produk.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label" style="font-weight: 600; color: #1e293b;">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama produk" required style="border-radius: 0.9rem; border: 1px solid #dbe5f1; padding: 0.75rem 1rem;">
                    </div>
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label" style="font-weight: 600; color: #1e293b;">Harga Jual (Rp)</label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" placeholder="Masukkan harga jual" required style="border-radius: 0.9rem; border: 1px solid #dbe5f1; padding: 0.75rem 1rem;">
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label" style="font-weight: 600; color: #1e293b;">Stok Awal</label>
                        <input type="number" class="form-control" id="stok" name="stok" placeholder="Masukkan jumlah stok awal" value="0" required style="border-radius: 0.9rem; border: 1px solid #dbe5f1; padding: 0.75rem 1rem;">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label" style="font-weight: 600; color: #1e293b;">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Masukkan keterangan produk (opsional)" style="border-radius: 0.9rem; border: 1px solid #dbe5f1; padding: 0.75rem 1rem;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dbe5f1; padding: 1.5rem; background: #f8fbff; border-radius: 0 0 1.5rem 1.5rem;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #e8eef7; color: #2d7dd2; border: none; border-radius: 0.9rem; padding: 0.6rem 1.5rem; font-weight: 600; transition: all 0.25s ease;">
                    Batal
                </button>
                <button type="submit" form="addStokForm" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 0.9rem; padding: 0.6rem 1.5rem; font-weight: 600; transition: all 0.25s ease;">
                    <i class="fas fa-check me-2"></i>Simpan Produk
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddStokModal() {
        const modal = new bootstrap.Modal(document.getElementById('addStokModal'), {
            keyboard: false
        });
        modal.show();
    }

    // Format harga jual otomatis
    document.getElementById('harga_jual').addEventListener('input', function(e) {
        let value = this.value;
        if (value && !isNaN(value)) {
            this.value = parseInt(value);
        }
    });
</script>

@endsection

