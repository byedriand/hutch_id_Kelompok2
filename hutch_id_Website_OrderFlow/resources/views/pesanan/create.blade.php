@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .page-title-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.4rem;
        }
        .page-title-bar .page-heading h1 {
            margin-bottom: 0.4rem;
            font-size: 1.9rem;
            letter-spacing: -0.03em;
            font-weight: 700;
        }
        .page-title-bar .page-heading p {
            color: #5b7088;
            margin-bottom: 0;
            font-size: 1rem;
        }
        .page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: center;
        }
        .page-actions .btn {
            min-width: 140px;
            border-radius: 999px;
            font-weight: 600;
            padding: 0.65rem 1.15rem;
        }
        .page-actions .btn-primary {
            box-shadow: 0 14px 26px rgba(13, 110, 253, 0.18);
        }
        .page-actions .btn-outline-secondary {
            background: #ffffff;
            border-color: rgba(108, 117, 125, 0.18);
            color: #3b4a67;
        }
        .card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1.8rem;
            box-shadow: 0 18px 40px rgba(15, 64, 124, 0.07);
            background: #ffffff;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            padding: 1rem 1.25rem;
        }
        .card-header h5 {
            margin-bottom: 0;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            font-weight: 700;
        }
        .card-body {
            padding: 1.25rem 1.35rem 1.4rem;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
        }
        .form-control,
        .form-select {
            border-radius: 1rem;
            border: 1px solid #d8e2ef;
            min-height: 46px;
            box-shadow: inset 0 1px 2px rgba(15, 64, 124, 0.04);
        }
        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.18);
            border-color: #93c5fd;
        }
        .form-control[readonly] {
            background-color: #f8fbff;
        }
        .input-group-text {
            border: none;
            background: transparent;
            color: #2563eb;
        }
        .cust-autocomplete {
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 18px 40px rgba(15, 64, 124, 0.12);
            margin-top: 0.15rem;
            background: #ffffff;
            border: 1px solid #d8e2ef;
        }
        #cust-dropdown .dropdown-item {
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s ease, color 0.2s ease;
            background: #ffffff;
            color: #0f172a;
            padding: 0.9rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }
        #cust-dropdown .dropdown-item:last-child {
            border-bottom: none;
        }
        #cust-dropdown .dropdown-item:hover {
            background-color: #eff6ff;
            color: #0f172a;
        }
        #cust-dropdown .customer-name {
            font-weight: 700;
            font-size: 0.97rem;
        }
        #cust-dropdown .customer-meta {
            font-size: 0.85rem;
            color: #64748b;
        }
        #cust-dropdown .no-results {
            color: #475569;
            padding: 1rem;
        }
        .table-wrap {
            overflow-x: auto;
        }
        .item-table {
            min-width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #f8fbff;
            border-radius: 1.2rem;
            overflow: hidden;
        }
        .item-table thead {
            background: #eef4ff;
        }
        .item-table th,
        .item-table td {
            padding: 1rem 0.95rem;
        }
        .item-table th {
            color: #455a64;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.78rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        }
        .item-table td {
            background: #ffffff;
            color: #334155;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        }
        .item-table tbody tr:last-child td {
            border-bottom: none;
        }
        .item-table .form-control-sm,
        .item-table .form-select-sm {
            border-radius: 0.9rem;
        }
        .item-table tbody tr:hover td {
            background: #f1f7ff;
        }
        .product-preview {
            display: flex;
            gap: 0.85rem;
            align-items: center;
        }
        .product-thumbnail {
            width: 56px;
            height: 56px;
            border-radius: 1.25rem;
            object-fit: cover;
            flex-shrink: 0;
            background: linear-gradient(135deg, #eff6ff, #ffffff);
            border: 1px solid #dbeafe;
        }
        .product-preview-info {
            width: 100%;
        }
        .product-preview-info .product-info {
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 0.35rem;
        }
        .product-preview-info .product-name {
            display: block;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .summary-widget {
            border-radius: 1.5rem;
            background: #ffffff;
            padding: 1.3rem 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 28px rgba(15, 64, 124, 0.06);
        }
        .summary-widget h6 {
            margin-bottom: 0.75rem;
            font-size: 0.82rem;
            color: #64748b;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .summary-widget .value {
            font-size: 1.55rem;
            font-weight: 800;
            color: #1d4ed8;
        }
        .summary-widget .caption {
            margin-top: 0.5rem;
            color: #6b7280;
            font-size: 0.92rem;
        }
        .summary-footer {
            border-radius: 1.5rem;
            background: #f8fbff;
            border: 1px solid #dbeafe;
            padding: 1.25rem;
        }
        .summary-card {
            position: sticky;
            top: 1.5rem;
        }
        @media (max-width: 1199px) {
            .summary-card {
                position: static;
            }
        }

        @media (max-width: 991px) {
            .page-title-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .page-actions {
                justify-content: flex-start;
                width: 100%;
            }

            .page-actions .btn {
                min-width: 0;
                width: 100%;
            }

            .summary-summary {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 0.85rem;
            }

            .summary-card {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .page-title-bar {
                gap: 0.75rem;
            }

            .summary-summary {
                grid-template-columns: 1fr;
            }

            .product-preview {
                flex-direction: column;
                align-items: stretch;
            }

            .product-thumbnail {
                width: 48px;
                height: 48px;
            }

            .product-preview-info {
                width: 100%;
            }

            .item-table thead th,
            .item-table td {
                padding: 0.8rem 0.7rem;
            }

            .item-table th:nth-child(2),
            .item-table th:nth-child(3),
            .item-table td:nth-child(2),
            .item-table td:nth-child(3) {
                min-width: 130px;
            }

            .item-table td {
                white-space: normal;
            }

            .stock-verification .table-wrap {
                overflow-x: auto;
            }
        }
        .summary-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1d4ed8;
        }
        .summary-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #111827;
        }
    </style>

    <div class="page-title-bar">
        <div class="page-heading">
            <h1>Buat Pesanan Baru</h1>
            <p>Isi data pelanggan, pilih produk, dan kelola pesanan dengan cepat dalam satu laman.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('pesanan.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" form="form-po" class="btn btn-primary" id="btn-simpan">
                <i class="fas fa-save me-2"></i>Simpan Pesanan
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="form-po" method="POST" action="{{ route('pesanan.store') }}">
        @csrf
        <div class="row g-4 align-items-start">
            <div class="col-12 col-xl-8">
                <!-- Card Informasi PO -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi PO</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nomor_po" class="form-label">Nomor PO</label>
                                <input type="text" id="nomor_po" class="form-control mono" value="{{ $nomorPo }}" readonly>
                                <input type="hidden" name="nomor_po" value="{{ $nomorPo }}">
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_pesanan" class="form-label">Tanggal Pesanan</label>
                                <input type="date" id="tanggal_pesanan" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                                <input type="hidden" name="tanggal_pesanan" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cust-input" class="form-label">Pelanggan *</label>
                            <div class="input-group position-relative">
                                <span class="input-group-text bg-white border-0 text-primary"><i class="fas fa-search"></i></span>
                                <input type="text" id="cust-input" class="form-control border-start-0" placeholder="Ketik nama pelanggan..." autocomplete="off">
                                <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                                <div id="cust-dropdown" class="cust-autocomplete position-absolute w-100" style="top: 100%; left: 0; display: none; max-height: 220px; overflow-y: auto; z-index: 1000;"></div>
                            </div>
                            <div class="mt-2 small text-muted">Pilih pelanggan yang sudah ada atau tambahkan dari menu Pelanggan.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cust-alamat" class="form-label">Alamat</label>
                                <textarea id="cust-alamat" class="form-control" readonly rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="cust-telepon" class="form-label">Telepon</label>
                                <input type="text" id="cust-telepon" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="cust-email" class="form-label">Email</label>
                                <input type="text" id="cust-email" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pengiriman" class="form-label">Tanggal Pengiriman *</label>
                            <input type="date" id="tanggal_pengiriman" name="tanggal_pengiriman" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('tanggal_pengiriman') }}" required>
                        </div>
                    </div>
                </div>

            <!-- Card Item Pesanan -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">Item Pesanan</h5>
                            <small class="text-muted">Tambah produk dan atur jumlah pesanan dengan mudah.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" onclick="tambahItem()">
                            <i class="fas fa-plus me-1"></i>Tambah Item
                        </button>
                    </div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table class="table table-sm item-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 30%;">Nama Produk</th>
                                    <th style="width: 25%;">Spesifikasi</th>
                                    <th style="width: 10%;">Qty</th>
                                    <th style="width: 15%;">Harga Satuan</th>
                                    <th style="width: 10%;">Subtotal</th>
                                    <th style="width: 5%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                <tr id="item-1">
                                    <td>1</td>
                                    <td>
                                        <div class="product-preview">
                                            <img id="preview-1" src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='96' height='96'%3E%3Crect width='96' height='96' fill='%23eef2ff'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%2364748b' font-size='12' font-family='Arial, sans-serif'%3ENo Image%3C/text%3E%3C/svg%3E" alt="Preview Produk" class="product-thumbnail">
                                            <div class="product-preview-info">
                                                <select name="items[1][produk_id]" class="form-select form-select-sm" onchange="updateHarga(this, 1)" required>
                                                    <option value="">-- Pilih Produk --</option>
                                                    @forelse($produk as $p)
                                                        <option value="{{ $p->id }}" data-harga="{{ $p->harga_jual }}" data-stok="{{ $p->stok }}">{{ $p->nama }}</option>
                                                    @empty
                                                        <option value="" disabled>Tidak ada produk tersedia. Tambahkan produk di menu Produk.</option>
                                                    @endforelse
                                                </select>
                                                <div id="preview-info-1" class="product-info">Pilih produk untuk melihat detail dan gambar.</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="items[1][spesifikasi]" class="form-control form-control-sm" placeholder="Optional">
                                    </td>
                                    <td>
                                        <input type="number" name="items[1][jumlah]" class="form-control form-control-sm" min="1" value="1" onchange="hitungBaris(1)" required>
                                    </td>
                                    <td>
                                        <input type="text" id="harga-1" class="form-control form-control-sm mono" readonly>
                                        <input type="hidden" name="items[1][harga_satuan]" id="harga-hidden-1" value="0">
                                    </td>
                                    <td>
                                        <input type="text" id="sub-1" class="form-control form-control-sm mono" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(1)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="stock-verification mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0">Verifikasi Stok</h6>
                            <span id="stock-status-badge" class="badge rounded-pill bg-success text-white px-3">Semua stok cukup</span>
                        </div>
                        <div class="table-wrap">
                            <table class="table table-sm item-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Stok Tersedia</th>
                                        <th>Kebutuhan</th>
                                        <th>Selisih</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="stock-verification-body">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Pilih produk untuk melihat verifikasi stok.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="stok-warning" class="alert alert-danger mt-3 d-none" role="alert"></div>
                    </div>
                    <div class="text-end mt-3 rounded-4 bg-light p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <span class="fw-semibold">Total PO</span>
                            <span id="total-po" class="mono text-primary fs-5">Rp 0</span>
                        </div>
                        <input type="hidden" name="total_nilai" id="total-nilai" value="0">
                    </div>
                </div>
            </div>

            </div>
            <div class="col-12 col-xl-4">
                <div class="card summary-card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <span class="summary-label">Ringkasan Pesanan</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">Draft</span>
                            </div>
                            <p class="mb-3 text-muted">Lihat total item dan nilai pesanan sebelum menyimpan.</p>
                        </div>
                        <div class="row gx-3 gy-3 align-items-center mb-4 summary-summary">
                            <div class="col-sm-6">
                                <div>
                                    <div class="text-muted small">Total Item</div>
                                    <div class="summary-value" id="total-item">1</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div>
                                    <div class="text-muted small">Total Nilai</div>
                                    <div class="summary-value" id="summary-total">Rp 0</div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" form="form-po" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-save me-2"></i>Simpan PO Sekarang
                        </button>
                        <div class="mt-3 small text-muted">
                            Pastikan semua item telah terisi dengan benar sebelum menyimpan pesanan.
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Catatan Khusus (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan tambahan untuk pesanan ini..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

@push('scripts')
@php
    $dataProduk = $produk->map(function ($p) {
        $gambar = $p->gambar ?? null;
        if ($gambar && !filter_var($gambar, FILTER_VALIDATE_URL)) {
            $gambar = asset('storage/' . ltrim($gambar, '/'));
        }

        return [
            'id' => $p->id,
            'nama' => $p->nama,
            'harga' => $p->harga_jual,
            'stok' => $p->stok,
            'gambar' => $gambar,
        ];
    });
@endphp
<script>
const dataProduk = @json($dataProduk);
const placeholderProductImage = 'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'96\' height=\'96\'%3E%3Crect width=\'96\' height=\'96\' fill=\'%23eef2ff\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%2364748b\' font-size=\'12\' font-family=\'Arial, sans-serif\'%3ENo Image%3C/text%3E%3C/svg%3E';
let itemCount = 1;

const custDropdown = document.getElementById('cust-dropdown');
let autocompleteTimeout;

document.getElementById('cust-input').addEventListener('input', function() {
    clearTimeout(autocompleteTimeout);
    const q = this.value.trim();

    if (q.length < 2) {
        custDropdown.style.display = 'none';
        return;
    }

    autocompleteTimeout = setTimeout(() => {
        fetch(`/api/pelanggan/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                custDropdown.innerHTML = '';
                custDropdown.style.maxHeight = '220px';
                custDropdown.style.overflowY = 'auto';

                if (data.length === 0) {
                    const div = document.createElement('div');
                    div.className = 'text-muted p-3';
                    div.textContent = 'Tidak ada pelanggan ditemukan';
                    custDropdown.appendChild(div);
                    custDropdown.style.display = 'block';
                    return;
                }

                data.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'dropdown-item';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `
                        <span class="customer-name">${p.nama}</span>
                        <span class="customer-meta">${p.telepon || '-'} · ${p.email || 'Email tidak tersedia'}</span>
                    `;
                    div.onclick = () => pilihPelanggan(p);
                    custDropdown.appendChild(div);
                });

                custDropdown.style.display = 'block';
            })
            .catch(() => {
                custDropdown.style.display = 'none';
            });
    }, 250);
});

function pilihPelanggan(pelanggan) {
    document.getElementById('pelanggan_id').value = pelanggan.id;
    document.getElementById('cust-input').value = pelanggan.nama;
    document.getElementById('cust-alamat').value = pelanggan.alamat;
    document.getElementById('cust-telepon').value = pelanggan.telepon;
    document.getElementById('cust-email').value = pelanggan.email;
    custDropdown.style.display = 'none';
}

function updateHarga(select, id) {
    const option = select.options[select.selectedIndex];
    const harga = parseInt(option.getAttribute('data-harga')) || 0;
    const produk = dataProduk.find(p => p.id === parseInt(select.value));

    document.getElementById(`harga-${id}`).value = harga ? 'Rp ' + formatNumber(harga) : '';
    document.getElementById(`harga-hidden-${id}`).value = harga;
    document.getElementById(`preview-${id}`).src = produk && produk.gambar ? produk.gambar : placeholderProductImage;
    document.getElementById(`preview-info-${id}`).textContent = produk ? `${produk.nama} · Stok ${produk.stok ?? '-'}` : 'Pilih produk untuk melihat detail dan gambar.';
    hitungBaris(id);
}

function hitungBaris(id) {
    const qty = parseInt(document.querySelector(`input[name="items[${id}][jumlah]"]`).value) || 0;
    const harga = parseInt(document.getElementById(`harga-hidden-${id}`).value) || 0;
    const sub = qty * harga;

    document.getElementById(`sub-${id}`).value = sub ? 'Rp ' + formatNumber(sub) : '';
    hitungTotal();
}

function updateStockVerification() {
    const stockBody = document.getElementById('stock-verification-body');
    const warningBox = document.getElementById('stok-warning');
    const statusBadge = document.getElementById('stock-status-badge');

    const summary = {};
    let hasWarning = false;

    document.querySelectorAll('#items-tbody tr').forEach(row => {
        const select = row.querySelector('select[name^="items["]');
        const qtyInput = row.querySelector('input[type="number"]');
        if (!select || !qtyInput) return;

        const produkId = parseInt(select.value);
        const qty = parseInt(qtyInput.value) || 0;
        const produk = dataProduk.find(p => p.id === produkId);
        if (!produk || !qty) return;

        if (!summary[produkId]) {
            summary[produkId] = {produk, kebutuhan: 0};
        }
        summary[produkId].kebutuhan += qty;
    });

    if (Object.keys(summary).length === 0) {
        stockBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Pilih produk untuk melihat verifikasi stok.</td></tr>';
        warningBox.classList.add('d-none');
        statusBadge.className = 'badge rounded-pill bg-success text-white px-3';
        statusBadge.textContent = 'Semua stok cukup';
        document.getElementById('btn-simpan').disabled = false;
        return;
    }

    const rows = Object.values(summary).map(entry => {
        const tersedia = entry.produk.stok || 0;
        const kebutuhan = entry.kebutuhan;
        const selisih = tersedia - kebutuhan;
        const status = selisih >= 0 ? 'Cukup' : 'Kurang';
        if (selisih < 0) hasWarning = true;

        return `
            <tr class="${selisih < 0 ? 'table-danger' : ''}">
                <td>${entry.produk.nama}</td>
                <td>${tersedia}</td>
                <td>${kebutuhan}</td>
                <td>${selisih}</td>
                <td>${status}</td>
            </tr>
        `;
    });

    stockBody.innerHTML = rows.join('');

    if (hasWarning) {
        warningBox.textContent = 'Beberapa item melebihi stok tersedia. Periksa kebutuhan dan ubah jumlah produk.';
        warningBox.classList.remove('d-none');
        statusBadge.className = 'badge rounded-pill bg-danger text-white px-3';
        statusBadge.textContent = 'Stok tidak cukup';
        document.getElementById('btn-simpan').disabled = true;
    } else {
        warningBox.classList.add('d-none');
        statusBadge.className = 'badge rounded-pill bg-success text-white px-3';
        statusBadge.textContent = 'Semua stok cukup';
        document.getElementById('btn-simpan').disabled = false;
    }
}

function hitungTotal() {
    let total = 0;
    let totalItens = 0;

    document.querySelectorAll('#items-tbody tr').forEach(row => {
        const hargaHidden = row.querySelector('input[id^="harga-hidden-"]');
        const qtyInput = row.querySelector('input[type="number"]');
        if (!hargaHidden || !qtyInput) return;

        const qty = parseInt(qtyInput.value) || 0;
        const harga = parseInt(hargaHidden.value) || 0;
        total += qty * harga;
        if (qty > 0) totalItens++;
    });

    document.getElementById('total-po').textContent = total ? 'Rp ' + formatNumber(total) : 'Rp 0';
    document.getElementById('total-nilai').value = total;
    document.getElementById('summary-total').textContent = total ? 'Rp ' + formatNumber(total) : 'Rp 0';
    document.getElementById('total-item').textContent = totalItens;
    updateStockVerification();
}

function tambahItem() {
    itemCount++;
    const tbody = document.getElementById('items-tbody');
    const newRow = document.createElement('tr');
    newRow.id = `item-${itemCount}`;
    newRow.innerHTML = `
        <td>${itemCount}</td>
        <td>
            <div class="product-preview">
                <img id="preview-${itemCount}" src="${placeholderProductImage}" alt="Preview Produk" class="product-thumbnail">
                <div class="product-preview-info">
                    <select name="items[${itemCount}][produk_id]" class="form-select form-select-sm" onchange="updateHarga(this, ${itemCount})" required>
                        <option value="">-- Pilih Produk --</option>
                        ${dataProduk.map(p => `<option value="${p.id}" data-harga="${p.harga}" data-stok="${p.stok}">${p.nama}</option>`).join('')}
                    </select>
                    <div id="preview-info-${itemCount}" class="product-info">Pilih produk untuk melihat detail dan gambar.</div>
                </div>
            </div>
        </td>
        <td>
            <input type="text" name="items[${itemCount}][spesifikasi]" class="form-control form-control-sm" placeholder="Optional">
        </td>
        <td>
            <input type="number" name="items[${itemCount}][jumlah]" class="form-control form-control-sm" min="1" value="1" onchange="hitungBaris(${itemCount})" required>
        </td>
        <td>
            <input type="text" id="harga-${itemCount}" class="form-control form-control-sm mono" readonly>
            <input type="hidden" name="items[${itemCount}][harga_satuan]" id="harga-hidden-${itemCount}" value="0">
        </td>
        <td>
            <input type="text" id="sub-${itemCount}" class="form-control form-control-sm mono" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="hapusItem(${itemCount})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    hitungTotal();
}

function hapusItem(id) {
    const row = document.getElementById(`item-${id}`);
    const totalRows = document.querySelectorAll('#items-tbody tr').length;

    if (totalRows > 1) {
        row.remove();
        hitungTotal();
    } else {
        alert('Minimal ada 1 item pesanan');
    }
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const formPo = document.getElementById('form-po');
formPo.addEventListener('submit', function(e) {
    const pelangganId = document.getElementById('pelanggan_id').value;
    if (!pelangganId) {
        e.preventDefault();
        alert('Pilih pelanggan terlebih dahulu');
        return;
    }

    const btn = document.getElementById('btn-simpan');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    }
});
</script>
@endpush
@endsection
