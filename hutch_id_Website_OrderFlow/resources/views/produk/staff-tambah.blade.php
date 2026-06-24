@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header dengan gradient yang lebih menarik -->
    <div class="page-header dashboard-header align-items-center justify-content-between mb-5">
        <div class="dashboard-header-left">
            <div class="dashboard-title-wrapper">
                <div class="dashboard-icon" style="background: linear-gradient(135deg, #2d7dd2 0%, #1e56a0 100%); border-radius: 14px; box-shadow: 0 8px 20px rgba(45, 125, 210, 0.3);">
                    <i class="fas fa-cube"></i>
                </div>
                <div>
                    <h1 class="dashboard-title">Kelola Produk</h1>
                    <p class="dashboard-subtitle">Tambah produk baru, lihat detail, dan kelola dengan mudah.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show custom-alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show custom-alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Tambah Produk - 40% width -->
        <div class="col-lg-5 mb-4">
            <div class="card staff-form-card shadow-lg">
                <div class="card-header staff-form-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Tambah Produk Baru</h5>
                            <small class="text-white-50">Isi form di bawah untuk menambah produk</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="produk-form" action="{{ route('produk.staff.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Produk -->
                        <div class="mb-4">
                            <label for="nama" class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-2"></i>Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" placeholder="Contoh: Tas Kulit Premium" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga Jual -->
                        <div class="mb-4">
                            <label for="harga_jual" class="form-label fw-semibold">
                                <i class="fas fa-money-bill text-success me-2"></i>Harga Jual (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-0 bg-light">Rp</span>
                                <input type="number" class="form-control @error('harga_jual') is-invalid @enderror" 
                                       id="harga_jual" name="harga_jual" placeholder="0" value="{{ old('harga_jual') }}" min="0" step="1000" required>
                            </div>
                            @error('harga_jual')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold">
                                <i class="fas fa-note-sticky text-info me-2"></i>Keterangan Produk
                            </label>
                            <textarea class="form-control form-control-lg @error('keterangan') is-invalid @enderror" 
                                      id="keterangan" name="keterangan" rows="4" placeholder="Deskripsi detail produk, bahan, fitur, dll...">{{ old('keterangan') }}</textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>Ini akan ditampilkan di detail produk
                            </small>
                            @error('keterangan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto Produk -->
                        <div class="mb-4">
                            <label for="foto" class="form-label fw-semibold">
                                <i class="fas fa-image text-danger me-2"></i>Foto Produk
                            </label>
                            <div class="photo-upload-area">
                                <div class="photo-click-zone">
                                    <input type="file" class="form-control photo-input @error('foto') is-invalid @enderror" 
                                           id="foto" name="foto" accept="image/*">
                                    <div class="photo-placeholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-1">Klik atau drag foto di sini</p>
                                        <small>JPG, PNG, GIF (Max 10MB)</small>
                                    </div>
                                </div>
                                <div id="foto-preview" class="mt-3 p-3 bg-light rounded" style="display: none; border: 2px solid #3b82f6;">
                                    <div class="mb-3">
                                        <img id="preview-image" src="" alt="Preview" class="preview-img" style="width: 100%; max-height: 300px; object-fit: cover; display: block;">
                                    </div>
                                    <p class="text-success mb-2 font-weight-bold">
                                        <i class="fas fa-check-circle me-2"></i>Foto siap untuk di-upload
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="remove-foto">
                                        <i class="fas fa-trash-alt me-1"></i>Hapus Foto
                                    </button>
                                </div>
                            </div>
                            @error('foto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg w-100 submit-btn mt-3">
                            <i class="fas fa-check-circle me-2"></i>Simpan Produk Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Produk - 60% width -->
        <div class="col-lg-7 mb-4">
            <div class="card staff-products-card shadow-lg">
                <div class="card-header staff-products-header-blue">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle-blue">
                                <i class="fas fa-list"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Daftar Produk</h5>
                                <small class="text-white-50">{{ $produk->count() }} Produk Tersedia</small>
                            </div>
                        </div>
                        <span class="badge bg-white text-primary fs-6 px-3 py-2">{{ $produk->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($produk->count() > 0)
                        <div class="products-grid-container p-4">
                            @foreach($produk as $index => $p)
                                <div class="product-card-animated" style="animation-delay: {{ $index * 0.05 }}s;">
                                    <button type="button" class="product-card-button" data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $p->id }}" 
                                            data-produk-id="{{ $p->id }}"
                                            data-produk-nama="{{ $p->nama }}"
                                            data-produk-harga="{{ $p->harga_jual }}"
                                            data-produk-stok="{{ $p->stok }}"
                                            data-produk-keterangan="{{ $p->keterangan }}"
                                            data-produk-foto="{{ $p->foto_url }}"
                                            title="Klik untuk melihat detail produk">
                                        <div class="product-image-container">
                                            @if($p->foto_url)
                                                <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="product-image">
                                            @else
                                                <div class="product-placeholder-box">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <div class="product-overlay">
                                                <i class="fas fa-eye"></i>
                                                <span class="ms-2">Lihat Detail</span>
                                            </div>
                                            <div class="product-stock-badge-blue">
                                                <i class="fas fa-boxes me-1"></i>{{ $p->stok }}
                                            </div>
                                        </div>
                                        <div class="product-info-section">
                                            <h6 class="product-name">{{ $p->nama }}</h6>
                                            <p class="product-price">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                                            @if($p->keterangan)
                                                <p class="product-desc">{{ str($p->keterangan)->limit(50) }}...</p>
                                            @else
                                                <p class="product-desc text-muted">Tidak ada keterangan</p>
                                            @endif
                                        </div>
                                    </button>
                                </div>

                                <!-- Modal Detail Produk -->
                                <div class="modal fade" id="detailModal{{ $p->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $p->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content modal-content-custom">
                                            <div class="modal-header modal-header-gradient border-0">
                                                <div>
                                                    <h5 class="modal-title" id="detailModalLabel{{ $p->id }}">{{ $p->nama }}</h5>
                                                    <small class="text-white-50">ID Produk: #{{ $p->id }}</small>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="row">
                                                    <!-- Foto -->
                                                    <div class="col-md-5 mb-4 mb-md-0">
                                                        <div class="modal-image-container">
                                                            @if($p->foto_url)
                                                                <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="modal-image">
                                                            @else
                                                                <div class="modal-placeholder">
                                                                    <i class="fas fa-image"></i>
                                                                    <p>Tidak ada foto</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Informasi Produk -->
                                                    <div class="col-md-7">
                                                        <div class="product-detail-info">
                                                            <div class="detail-item mb-3 pb-3 border-bottom">
                                                                <small class="text-muted text-uppercase fw-semibold">Harga</small>
                                                                <p class="h5 text-primary mb-0">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                                                            </div>

                                                            <div class="detail-item mb-3 pb-3 border-bottom">
                                                                <small class="text-muted text-uppercase fw-semibold">Stok Tersedia</small>
                                                                <p class="h5 text-success mb-0">
                                                                    <i class="fas fa-boxes me-2"></i>{{ $p->stok }} Unit
                                                                </p>
                                                            </div>

                                                            <div class="detail-item detail-keterangan">
                                                                <small class="text-muted text-uppercase fw-semibold d-block mb-2">Keterangan</small>
                                                                <p>
                                                                    {{ $p->keterangan ?? 'Tidak ada keterangan' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-2"></i>Tutup
                                                </button>
                                                <a href="{{ route('produk.staff.edit', $p->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-edit me-2"></i>Edit Produk
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-container">
                            <div class="empty-state-content">
                                <i class="fas fa-box-open"></i>
                                <h5>Belum Ada Produk</h5>
                                <p class="text-muted">Mulai tambahkan produk baru dari form di sebelah kiri</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ========== Card Styling ========== */
    .staff-form-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        background: white;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .staff-form-card:hover {
        box-shadow: 0 20px 50px rgba(15, 64, 124, 0.15) !important;
        transform: translateY(-4px);
    }

    .staff-form-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        border-bottom: none;
        position: relative;
        overflow: hidden;
    }

    .staff-form-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: -100px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .staff-products-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        background: white;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .staff-products-card:hover {
        box-shadow: 0 20px 50px rgba(15, 64, 124, 0.15) !important;
    }

    .staff-products-header-blue {
        background: linear-gradient(135deg, #2d7dd2 0%, #1e56a0 100%);
        color: white;
        padding: 1.5rem;
        font-weight: 600;
        border-bottom: none;
        position: relative;
        overflow: hidden;
    }

    .staff-products-header-blue::before {
        content: '';
        position: absolute;
        top: 0;
        right: -100px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .icon-circle-blue {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    /* ========== Form Styling ========== */
    .form-control-lg {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.9rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f8fbff;
    }

    .form-control-lg:focus {
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
    }

    .form-label {
        font-size: 0.95rem;
        color: #17233d;
        margin-bottom: 0.7rem;
        letter-spacing: 0.3px;
    }

    .input-group-lg .input-group-text {
        font-weight: 600;
        color: #6c7a93;
    }

    .input-group-lg .form-control {
        border-radius: 0 10px 10px 0;
        border: 2px solid #e2e8f0;
    }

    .input-group-lg .input-group-text {
        border-radius: 10px 0 0 10px;
        background: #f8fbff;
        border: 2px solid #e2e8f0;
    }

    .submit-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        padding: 0.95rem 1.5rem;
        font-weight: 700;
        font-size: 1.05rem;
        border-radius: 10px;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        letter-spacing: 0.5px;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
        color: white;
    }

    .submit-btn:active {
        transform: translateY(-1px);
    }

    /* ========== Photo Upload ========== */
    .photo-upload-area {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(135deg, #f0f7ff 0%, #f7fbff 100%);
        border: 2px dashed #3b82f6;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .photo-upload-area:hover {
        background: linear-gradient(135deg, #e6f2ff 0%, #f0f7ff 100%);
        border-color: #2563eb;
    }

    .photo-click-zone {
        position: relative;
    }

    .photo-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .photo-placeholder {
        position: relative;
        z-index: 1;
        padding: 3rem 1rem;
        text-align: center;
        color: #3b82f6;
    }

    .photo-placeholder i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    #foto-preview {
        animation: slideInUp 0.3s ease-out forwards;
        min-height: 150px !important;
        display: none !important;
        opacity: 0;
    }

    #foto-preview.show {
        display: block !important;
        opacity: 1 !important;
        animation: slideInUp 0.3s ease-out forwards;
    }

    #foto-preview img {
        width: 100% !important;
        max-height: 350px !important;
        object-fit: cover !important;
        border-radius: 8px !important;
        display: block !important;
        opacity: 1 !important;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .preview-img {
        max-width: 100%;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: fadeInScale 0.3s ease-in-out;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ========== Products Grid ========== */
    .products-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1.5rem;
    }

    .product-card-animated {
        animation: slideInUp 0.6s ease forwards;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card-button {
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: all 0.3s ease;
    }

    .product-card-button:hover {
        transform: translateY(-10px);
    }

    .product-card-button:focus {
        outline: none;
    }

    .product-image-container {
        position: relative;
        width: 100%;
        height: 160px;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        margin-bottom: 1rem;
        box-shadow: 0 4px 12px rgba(15, 64, 124, 0.1);
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-card-button:hover .product-image {
        transform: scale(1.15) rotate(2deg);
    }

    .product-placeholder-box {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
        color: white;
        font-size: 2.5rem;
    }

    .product-overlay {
        position: absolute;
        inset: 0;
        background: rgba(45, 125, 210, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        font-weight: 700;
        gap: 0.5rem;
    }

    .product-card-button:hover .product-overlay {
        opacity: 1;
    }

    .product-stock-badge-blue {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #2d7dd2 0%, #1e56a0 100%);
        color: white;
        padding: 0.5rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(45, 125, 210, 0.3);
        transition: all 0.3s ease;
    }

    .product-card-button:hover .product-stock-badge-blue {
        transform: scale(1.1);
    }

    .product-info-section {
        padding: 0.5rem 0;
    }

    .product-name {
        font-weight: 700;
        color: #17233d;
        font-size: 0.9rem;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.4rem;
    }

    .product-price {
        color: #2d7dd2;
        font-weight: 800;
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }

    .product-desc {
        color: #6c7a93;
        font-size: 0.75rem;
        margin: 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ========== Modal Styling ========== */
    .modal-content-custom {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .modal-header-gradient {
        background: linear-gradient(135deg, #2d7dd2 0%, #1e56a0 100%);
        color: white;
        padding: 1.5rem;
    }

    .modal-image-container {
        border-radius: 12px;
        overflow: hidden;
        background: #f0f7ff;
        aspect-ratio: 3/4;
    }

    .modal-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
    }

    .modal-placeholder i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .product-detail-info {
        padding: 0.5rem 0;
    }

    .detail-item {
        animation: fadeIn 0.3s ease forwards;
    }

    .detail-item p {
        word-break: break-word;
        white-space: pre-line;
        line-height: 1.6;
        color: #17233d;
        font-size: 0.95rem;
    }

    /* Keterangan section dengan better formatting */
    .detail-keterangan {
        padding: 1rem;
        background: linear-gradient(135deg, #f7fbff 0%, #eef4fb 100%);
        border-radius: 8px;
        border-left: 4px solid #3b82f6;
        max-height: 300px;
        overflow-y: auto;
    }

    .detail-keterangan p {
        margin-bottom: 0;
        word-wrap: break-word;
        white-space: pre-line;
        line-height: 1.7;
        color: #17233d;
        font-size: 0.95rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* ========== Empty State ========== */
    .empty-state-container {
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f7fbff 0%, #eef4fb 100%);
        border-radius: 12px;
        margin: 1.5rem;
    }

    .empty-state-content {
        text-align: center;
        color: #94a3b8;
    }

    .empty-state-content i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
        display: block;
    }

    .empty-state-content h5 {
        color: #6c7a93;
        margin-bottom: 0.5rem;
    }

    /* ========== Alerts ========== */
    .custom-alert-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
        color: #065f46;
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-left: 4px solid #10b981;
        border-radius: 12px;
    }

    .custom-alert-danger {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(185, 28, 28, 0.05) 100%);
        color: #7f1d1d;
        border: 1px solid rgba(220, 38, 38, 0.3);
        border-left: 4px solid #dc2626;
        border-radius: 12px;
    }

    /* ========== Responsive ========== */
    @media (max-width: 992px) {
        .products-grid-container {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1.2rem;
        }
    }

    @media (max-width: 768px) {
        .products-grid-container {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            padding: 1rem !important;
        }

        .product-image-container {
            height: 140px;
        }

        .modal-dialog {
            margin: 1rem;
        }

        .modal-dialog-centered {
            align-items: center;
        }
    }

    .text-danger {
        color: #dc2626;
        font-weight: 600;
    }
</style>

<script>
// Photo upload dengan drag & drop - wrap in DOMContentLoaded for safety
document.addEventListener('DOMContentLoaded', function() {
    const photoArea = document.querySelector('.photo-upload-area');
    const photoInput = document.getElementById('foto');
    const photoPlaceholder = document.querySelector('.photo-placeholder');
    const photoPreview = document.getElementById('foto-preview');
    const previewImage = document.getElementById('preview-image');
    const removePhotoBtn = document.getElementById('remove-foto');

    // Verify all elements exist
    if (!photoArea || !photoInput || !photoPreview || !previewImage) {
        console.error('Photo upload elements not found');
        return;
    }

    function handlePhotoSelect() {
        if (photoInput.files && photoInput.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                photoPreview.classList.add('show');
                if (photoArea) {
                    const clickZone = photoArea.querySelector('.photo-click-zone');
                    if (clickZone) clickZone.style.display = 'none';
                }
                console.log('✅ Photo preview loaded');
            };
            reader.onerror = () => {
                console.error('❌ Failed to read photo file');
            };
            reader.readAsDataURL(photoInput.files[0]);
        }
    }

    // Event listeners
    // Catatan: tidak perlu lagi photoArea.addEventListener('click', ...)
    // karena <input type="file"> sekarang jadi overlay transparan yang
    // langsung menerima klik pengguna. Ini penting agar WebView Android
    // (flutter_inappwebview) menganggapnya sebagai user-gesture yang sah
    // dan memicu file chooser; trigger lewat JS .click() pada elemen lain
    // sering tidak dianggap valid oleh WebView Android.

    photoArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        photoArea.style.borderColor = '#2563eb';
        photoArea.style.background = 'linear-gradient(135deg, #e6f2ff 0%, #f0f7ff 100%)';
    });

    photoArea.addEventListener('dragleave', () => {
        photoArea.style.borderColor = '#3b82f6';
        photoArea.style.background = 'linear-gradient(135deg, #f0f7ff 0%, #f7fbff 100%)';
    });

    photoArea.addEventListener('drop', (e) => {
        e.preventDefault();
        photoArea.style.borderColor = '#3b82f6';
        photoInput.files = e.dataTransfer.files;
        handlePhotoSelect();
    });

    photoInput.addEventListener('change', handlePhotoSelect);

    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', () => {
            photoInput.value = '';
            photoPreview.classList.remove('show');
            if (photoArea) {
                const clickZone = photoArea.querySelector('.photo-click-zone');
                if (clickZone) clickZone.style.display = 'block';
            }
            console.log('✅ Photo preview cleared');
        });
    }

    // Format currency input
    const hargaInput = document.getElementById('harga_jual');
    if (hargaInput) {
        hargaInput.addEventListener('blur', function() {
            if (this.value) {
                this.value = Math.round(this.value / 1000) * 1000;
            }
        });
    }
});
</script>
@endsection
