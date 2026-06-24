@extends('layouts.app')

@section('content')
<style>
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
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* ========== HEADER ========== */
    .edit-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
        border-radius: 2rem;
        padding: 3rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .edit-header::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent 70%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .edit-header > div {
        position: relative;
        z-index: 1;
    }

    .edit-header h1 {
        font-size: 2.2rem;
        font-weight: 900;
        color: #ffffff;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .edit-header-desc {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1rem;
        font-weight: 500;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 1rem;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    /* ========== MAIN CONTAINER ========== */
    .edit-container {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 2.5rem;
        margin-bottom: 3rem;
    }

    @media (max-width: 1024px) {
        .edit-container {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
    }

    /* ========== PRODUCT IMAGE CARD ========== */
    .product-image-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 1.5rem;
        padding: 1.75rem;
        box-shadow: 0 8px 32px rgba(15, 23, 42, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.1);
        animation: slideInLeft 0.6s ease-out 0.1s both;
        position: relative;
        overflow: hidden;
    }

    .product-image-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1), transparent 70%);
        border-radius: 50%;
    }

    .product-image-card::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -50%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(34, 197, 94, 0.08), transparent 70%);
        border-radius: 50%;
    }

    .product-image-container {
        position: relative;
        z-index: 1;
    }

    .product-image {
        width: 100%;
        height: 350px;
        object-fit: cover;
        border-radius: 1.25rem;
        border: 1px solid rgba(59, 130, 246, 0.1);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
        margin-bottom: 1.5rem;
        transition: transform 0.4s ease;
    }

    .product-image-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-image-placeholder {
        width: 100%;
        height: 350px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
        border-radius: 1.25rem;
        border: 2px dashed rgba(59, 130, 246, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }

    .product-quick-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(147, 197, 253, 0.08));
        border: 1px solid rgba(147, 197, 253, 0.3);
        border-left: 4px solid #3b82f6;
        border-radius: 1.25rem;
        padding: 1.5rem;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }

    .quick-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.9rem 0;
        border-bottom: 1px solid rgba(226, 232, 240, 0.4);
    }

    .quick-info-item:last-child {
        border-bottom: none;
    }

    .quick-info-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .quick-info-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1e293b;
    }

    .quick-info-value.price {
        color: #2563eb;
    }

    .quick-info-value.stock {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.5rem 0.9rem;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
        border-radius: 0.75rem;
        color: #2563eb;
    }

    /* ========== FORM CARD ========== */
    .edit-form-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(15, 23, 42, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.1);
        animation: slideInRight 0.6s ease-out 0.1s both;
        position: relative;
        overflow: hidden;
    }

    .edit-form-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1), transparent 70%);
        border-radius: 50%;
    }

    .form-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .form-title i {
        color: #2563eb;
        font-size: 1.5rem;
    }

    /* ========== FORM SECTIONS ========== */
    .change-type-section {
        margin-bottom: 2.5rem;
        position: relative;
        z-index: 1;
    }

    .section-label {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        display: block;
    }

    .change-type-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .change-type-option {
        position: relative;
        padding: 1.2rem;
        background: #ffffff;
        border: 2px solid rgba(226, 232, 240, 0.6);
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .change-type-option:hover {
        border-color: rgba(59, 130, 246, 0.4);
        background: rgba(59, 130, 246, 0.02);
    }

    .change-type-option.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.05));
        border-color: #3b82f6;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.15);
    }

    .form-check-input {
        width: 1.3rem;
        height: 1.3rem;
        margin-top: 0.2rem;
    }

    .change-type-option strong {
        color: #1e293b;
        font-weight: 700;
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    /* ========== INPUT SECTION ========== */
    .input-section {
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .form-label {
        color: #1e293b;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-control {
        border-radius: 1rem;
        border: 1.5px solid rgba(226, 232, 240, 0.6);
        padding: 0.9rem 1.2rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        background: #ffffff;
    }

    .input-group-text {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 197, 253, 0.08));
        border: 1.5px solid rgba(226, 232, 240, 0.6);
        border-left: none;
        border-radius: 0 1rem 1rem 0;
        color: #1e293b;
        font-weight: 700;
        font-size: 1rem;
    }

    .input-hint {
        color: #64748b;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .input-hint i {
        color: #3b82f6;
        font-size: 0.85rem;
    }

    .preview-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(147, 197, 253, 0.08));
        border: 1px solid rgba(147, 197, 253, 0.3);
        border-left: 4px solid #3b82f6;
        border-radius: 1rem;
        padding: 1.2rem;
        margin-top: 1rem;
        font-size: 0.9rem;
    }

    .preview-box small {
        color: #334155;
        font-weight: 500;
    }

    .preview-box strong {
        color: #1e293b;
        font-weight: 700;
    }

    /* ========== NOTES SECTION ========== */
    .notes-section textarea {
        border-radius: 1rem;
        border: 1.5px solid rgba(226, 232, 240, 0.6);
        padding: 1rem 1.2rem;
        font-size: 0.9rem;
        font-family: inherit;
        resize: vertical;
        transition: all 0.3s ease;
        max-height: 150px;
    }

    .notes-section textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        background: #ffffff;
    }

    /* ========== ACTION BUTTONS ========== */
    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        position: relative;
        z-index: 1;
    }

    .btn-save {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 1rem;
        padding: 0.95rem 2.5rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        cursor: pointer;
        flex: 1;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(59, 130, 246, 0.4);
    }

    .btn-cancel {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1.5px solid rgba(59, 130, 246, 0.2);
        border-radius: 1rem;
        padding: 0.95rem 2.5rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .btn-cancel:hover {
        background: rgba(59, 130, 246, 0.15);
        color: #1d4ed8;
        border-color: rgba(59, 130, 246, 0.3);
    }

    /* ========== ALERTS ========== */
    .alert {
        border-radius: 1.25rem !important;
        border: none !important;
        padding: 1.25rem !important;
        margin-bottom: 2rem !important;
        animation: slideInRight 0.5s ease-out !important;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05)) !important;
        color: #7f1d1d !important;
        border-left: 4px solid #ef4444 !important;
    }

    .alert i {
        font-size: 1.1rem;
        margin-right: 0.75rem;
    }

    /* ========== RESPONSIVE DESIGN ========== */
    
    /* TABLETS (640px - 1024px) */
    @media (max-width: 1024px) {
        .edit-header {
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 1.5rem;
        }

        .edit-header h1 {
            font-size: 1.7rem;
            flex-wrap: wrap;
        }

        .edit-header-desc {
            font-size: 0.95rem;
        }

        .btn-back {
            padding: 0.65rem 1.25rem;
            font-size: 0.9rem;
        }

        .edit-container {
            gap: 1.75rem;
        }

        .product-image {
            height: 300px;
        }

        .product-image-placeholder {
            height: 300px;
            font-size: 3.5rem;
        }

        .edit-form-card {
            padding: 1.75rem;
        }

        .form-group label {
            font-size: 0.95rem;
        }

        .form-control, .form-select {
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
        }

        .btn-submit-stok {
            padding: 0.8rem 1.5rem;
            font-size: 0.95rem;
        }

        .change-type-options {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .change-type-option {
            padding: 1.2rem;
            border-radius: 1rem;
        }
    }

    /* MOBILE (640px - 480px) */
    @media (max-width: 640px) {
        .edit-header {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 1.25rem;
        }

        .edit-header::before {
            width: 200px;
            height: 200px;
            top: -80px;
            right: -80px;
        }

        .edit-header h1 {
            font-size: 1.4rem;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .edit-header-desc {
            font-size: 0.9rem;
        }

        .btn-back {
            width: 100%;
            justify-content: center;
            padding: 0.7rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .edit-container {
            gap: 1.5rem;
        }

        .product-image-card {
            padding: 1.5rem;
            border-radius: 1.25rem;
            animation-delay: 0s;
        }

        .product-image {
            height: 250px;
            border-radius: 1rem;
            margin-bottom: 1.25rem;
        }

        .product-image-placeholder {
            height: 250px;
            border-radius: 1rem;
            font-size: 3rem;
            margin-bottom: 1.25rem;
        }

        .product-quick-info {
            padding: 1.25rem;
            border-radius: 1rem;
        }

        .quick-info-item {
            padding: 0.75rem 0;
        }

        .quick-info-label {
            font-size: 0.75rem;
        }

        .quick-info-value {
            font-size: 1rem;
        }

        .edit-form-card {
            padding: 1.5rem;
            border-radius: 1.25rem;
            animation-delay: 0s;
        }

        .form-title {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .section-label {
            font-size: 0.85rem;
            margin-bottom: 0.9rem;
        }

        .change-type-options {
            grid-template-columns: 1fr;
            gap: 0.9rem;
        }

        .change-type-option {
            padding: 1rem;
            border-radius: 0.95rem;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-size: 0.9rem;
            margin-bottom: 0.6rem;
        }

        .form-control, .form-select {
            font-size: 0.9rem;
            padding: 0.65rem 0.9rem;
            border-radius: 0.85rem;
        }

        .input-section {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
        }

        .input-group {
            gap: 0.75rem;
        }

        .input-group-text {
            font-size: 0.9rem;
            padding: 0.65rem 0.9rem;
        }

        .input-hint {
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            margin-top: 1.5rem;
        }

        .btn-save, .btn-cancel {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            border-radius: 0.85rem;
        }
    }

    /* EXTRA SMALL (< 480px) */
    @media (max-width: 480px) {
        .edit-header {
            padding: 1.25rem 1rem;
            margin-bottom: 1.25rem;
            border-radius: 1rem;
        }

        .edit-header h1 {
            font-size: 1.2rem;
        }

        .edit-header-desc {
            font-size: 0.85rem;
        }

        .btn-back {
            font-size: 0.8rem;
            padding: 0.6rem 0.9rem;
        }

        .edit-container {
            gap: 1.25rem;
        }

        .product-image-card {
            padding: 1.25rem;
            border-radius: 1rem;
        }

        .product-image {
            height: 200px;
            margin-bottom: 1rem;
        }

        .product-image-placeholder {
            height: 200px;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .product-quick-info {
            padding: 1rem;
        }

        .quick-info-item {
            padding: 0.6rem 0;
        }

        .quick-info-label {
            font-size: 0.7rem;
        }

        .quick-info-value {
            font-size: 0.9rem;
        }

        .edit-form-card {
            padding: 1.25rem;
        }

        .form-title {
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
        }

        .section-label {
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }

        .change-type-options {
            gap: 0.75rem;
        }

        .change-type-option {
            padding: 0.9rem;
            border-radius: 0.9rem;
        }

        .change-type-option strong {
            font-size: 0.9rem;
        }

        .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            font-size: 0.85rem;
            padding: 0.6rem 0.8rem;
        }

        .input-section {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-size: 0.8rem;
        }

        .input-hint {
            font-size: 0.75rem;
        }

        .action-buttons {
            gap: 0.6rem;
            margin-top: 1.25rem;
        }

        .btn-save, .btn-cancel {
            padding: 0.65rem 0.9rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- HEADER -->
    <div class="edit-header">
        <div>
            <h1><i class="fas fa-edit"></i>Ubah Stok: {{ $produk->nama }}</h1>
            <p class="edit-header-desc">Kelola stok barang dengan mudah dan pantau perubahan inventori</p>
        </div>
        <div class="mt-3">
            <a href="{{ route('produk.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- ALERTS -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- MAIN CONTENT -->
    <div class="edit-container">
        <!-- LEFT: PRODUCT IMAGE -->
        <div class="product-image-card">
            <div class="product-image-container">
                @if ($produk->foto_url)
                    <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama }}" class="product-image">
                @else
                    <div class="product-image-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                @endif

                <div class="product-quick-info">
                    <div class="quick-info-item">
                        <span class="quick-info-label">Nama Produk</span>
                        <span class="quick-info-value">{{ $produk->nama }}</span>
                    </div>
                    <div class="quick-info-item">
                        <span class="quick-info-label">Harga Jual</span>
                        <span class="quick-info-value price">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                    </div>
                    <div class="quick-info-item">
                        <span class="quick-info-label">Stok Saat Ini</span>
                        <span class="quick-info-value stock">
                            <i class="fas fa-boxes"></i>{{ $produk->stok }} unit
                        </span>
                    </div>
                    <div class="quick-info-item">
                        <span class="quick-info-label">Status Stok</span>
                        <div style="margin-top: 0.35rem;">
                            @if ($produk->stok == 0)
                                <span style="background: rgba(239, 68, 68, 0.15); color: #991b1b; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 0.75rem; padding: 0.45rem 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                    <i class="fas fa-times-circle"></i>Kosong
                                </span>
                            @elseif ($produk->stok <= 10)
                                <span style="background: rgba(251, 146, 60, 0.15); color: #c2410c; border: 1px solid rgba(251, 146, 60, 0.3); border-radius: 0.75rem; padding: 0.45rem 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                    <i class="fas fa-exclamation-circle"></i>Rendah
                                </span>
                            @else
                                <span style="background: rgba(16, 185, 129, 0.15); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 0.75rem; padding: 0.45rem 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                    <i class="fas fa-check-circle"></i>Tersedia
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: FORM -->
        <div class="edit-form-card">
            <h3 class="form-title"><i class="fas fa-sliders-h"></i>Perubahan Stok</h3>

            {{-- Alert: Notifikasi dari stok yang kurang --}}
            <div id="stock-shortage-alert" class="stock-shortage-alert d-none" style="margin-bottom: 1.5rem;">
                <div class="alert-header">
                    <i class="fas fa-info-circle"></i>
                    <span>Info Stok dari Notifikasi</span>
                </div>
                <div class="alert-content">
                    <div class="alert-row">
                        <div class="alert-item">
                            <span class="alert-label">Stok Saat Ini</span>
                            <span class="alert-value available"><span id="alert-current-stock">{{ $produk->stok }}</span> unit</span>
                        </div>
                        <div class="alert-item">
                            <span class="alert-label">Dipesan</span>
                            <span class="alert-value needed"><span id="alert-ordered">0</span> unit</span>
                        </div>
                        <div class="alert-item">
                            <span class="alert-label">Kurang</span>
                            <span class="alert-value shortage"><span id="alert-shortage">0</span> unit</span>
                        </div>
                    </div>
                    <div class="alert-recommendation">
                        <i class="fas fa-lightbulb"></i>
                        <span>Rekomendasi: Tambahkan <strong id="recommended-value">0</strong> unit untuk memenuhi pesanan</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('produk.update', $produk->id) }}">
                @csrf
                @method('PUT')

                <!-- Change Type Selection -->
                <div class="change-type-section">
                    <label class="section-label">Pilih Tipe Perubahan</label>
                    <div class="change-type-options">
                        <div class="change-type-option active">
                            <div class="form-check">
                                <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_tambah" value="tambah" checked>
                                <label class="form-check-label" for="tipe_tambah">
                                    <strong><i class="fas fa-plus me-2" style="color: #10b981;"></i>Tambahkan Stok</strong>
                                </label>
                            </div>
                        </div>
                        <div class="change-type-option">
                            <div class="form-check">
                                <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_kurangi" value="kurangi">
                                <label class="form-check-label" for="tipe_kurangi">
                                    <strong><i class="fas fa-minus me-2" style="color: #ef4444;"></i>Kurangi Stok</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jumlah Perubahan -->
                <div class="input-section" id="jumlah-perubahan-field">
                    <label for="jumlah_perubahan" class="form-label">
                        <span id="label-jumlah">Jumlah Ditambahkan</span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="jumlah_perubahan" name="jumlah_perubahan" min="0" max="999999" 
                            placeholder="Masukkan jumlah" style="border-radius: 1rem 0 0 1rem;">
                        <span class="input-group-text" id="change-operator">+</span>
                    </div>
                    <div class="input-hint" id="jumlah-hint">
                        <i class="fas fa-info-circle"></i>
                        Masukkan jumlah yang akan ditambahkan ke stok saat ini ({{ $produk->stok }})
                    </div>
                </div>

                <!-- Catatan -->
                <div class="input-section notes-section">
                    <label for="keterangan" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                        placeholder="Tambahkan catatan tentang perubahan stok (misal: hasil restock, penyesuaian inventory, dll)">{{ old('keterangan') }}</textarea>
                    <div class="input-hint">
                        <i class="fas fa-lightbulb"></i>
                        Catatan ini akan tersimpan untuk referensi
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="action-buttons">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('produk.index') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
        font-size: 1.5rem;
        font-family: 'Courier New', monospace;
    }

    .change-type-section {
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.12s both;
    }
    .change-type-section label {
        color: #1e293b;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 1rem;
        display: block;
    }
    .change-type-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .change-type-option {
        position: relative;
        padding: 1.25rem;
        background: #ffffff;
        border: 2px solid rgba(219, 234, 254, 0.3);
        border-radius: 1.1rem;
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .change-type-option:hover {
        border-color: rgba(59, 130, 246, 0.5);
        background: rgba(59, 130, 246, 0.02);
    }
    .change-type-option.active {
        background: rgba(59, 130, 246, 0.08);
        border-color: #3b82f6;
    }
    .form-check-input.change-type-radio {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.2rem;
    }
    .change-type-option .form-check-label {
        margin-bottom: 0;
        cursor: pointer;
    }
    .change-type-option strong {
        color: #1e293b;
        font-weight: 700;
        display: block;
        margin-bottom: 0.35rem;
    }
    .change-type-option small {
        color: #64748b;
    }

    .input-section {
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.14s both;
    }
    .input-section label {
        color: #1e293b;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        display: block;
    }
    .input-section .form-control {
        border-radius: 1rem;
        border: 1px solid rgba(219, 234, 254, 0.5);
        padding: 0.85rem 1.1rem;
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }
    .input-section .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.18);
        background: #ffffff;
    }
    .input-section .input-group-text {
        background: #f1f5f9;
        border: 1px solid rgba(219, 234, 254, 0.5);
        border-radius: 0 1rem 1rem 0;
        color: #1e293b;
        font-weight: 700;
        font-size: 1rem;
    }
    .input-hint {
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .input-hint i {
        color: #3b82f6;
    }

    .preview-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 197, 253, 0.08));
        border: 1px solid rgba(147, 197, 253, 0.2);
        border-radius: 1rem;
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.9rem;
    }
    .preview-box small {
        color: #334155;
    }
    .preview-box strong {
        color: #1e293b;
    }

    .notes-section {
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.16s both;
    }
    .notes-section label {
        color: #1e293b;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.75rem;
        display: block;
    }
    .notes-section textarea {
        border-radius: 1rem;
        border: 1px solid rgba(219, 234, 254, 0.5);
        padding: 1rem;
        font-size: 0.9rem;
        font-family: inherit;
        resize: vertical;
        transition: all 0.25s ease;
    }
    .notes-section textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.18);
        background: #ffffff;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        animation: fadeInUp 0.6s ease-out 0.18s both;
    }
    .btn-stok-simpan {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 1rem;
        padding: 0.85rem 2rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }
    .btn-stok-simpan:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    }
    .btn-stok-batal {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 1rem;
        padding: 0.85rem 2rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.25s ease;
    }
    .btn-stok-batal:hover {
        background: rgba(59, 130, 246, 0.15);
        color: #1d4ed8;
        border-color: rgba(59, 130, 246, 0.3);
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

    /* Stock Shortage Alert */
    .stock-shortage-alert {
        background: linear-gradient(135deg, rgba(251, 146, 60, 0.08), rgba(249, 158, 11, 0.06));
        border: 1.5px solid rgba(249, 158, 11, 0.3);
        border-radius: 1.25rem;
        overflow: hidden;
        animation: slideInDown 0.5s ease-out;
        box-shadow: 0 8px 20px rgba(249, 158, 11, 0.1);
    }

    .stock-shortage-alert.d-none {
        display: none;
    }

    .alert-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(135deg, #fb9230 0%, #f59e0b 100%);
        color: white;
        padding: 0.9rem 1.25rem;
        font-weight: 800;
        font-size: 0.95rem;
        letter-spacing: -0.01em;
    }

    .alert-header i {
        font-size: 1.25rem;
        animation: pulse-icon 2s ease-in-out infinite;
    }

    @keyframes pulse-icon {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }

    .alert-content {
        padding: 1.25rem;
    }

    .alert-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .alert-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 0.9rem;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0.875rem;
        border: 1px solid rgba(249, 158, 11, 0.15);
    }

    .alert-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .alert-value {
        font-size: 1.1rem;
        font-weight: 900;
        color: #1e293b;
        letter-spacing: -0.02em;
    }

    .alert-value.available {
        color: #10b981;
    }

    .alert-value.needed {
        color: #0066cc;
    }

    .alert-value.shortage {
        color: #ef4444;
    }

    .alert-recommendation {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: linear-gradient(135deg, rgba(250, 235, 215, 0.8), rgba(255, 244, 224, 0.8));
        padding: 0.9rem 1.1rem;
        border-radius: 0.875rem;
        border-left: 4px solid #f59e0b;
        font-weight: 600;
        font-size: 0.9rem;
        color: #92400e;
    }

    .alert-recommendation i {
        font-size: 1.1rem;
        color: #f59e0b;
        animation: lightbulb-glow 2s ease-in-out infinite;
    }

    @keyframes lightbulb-glow {
        0%, 100% { transform: scale(1); color: #f59e0b; }
        50% { transform: scale(1.1); color: #fbbf24; }
    }

    .alert-recommendation strong {
        color: #b45309;
        font-weight: 900;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .alert-row {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .alert-recommendation {
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }

        .stock-shortage-alert {
            margin-bottom: 1.25rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="edit-stok-header">
        <div>
            <h1>Ubah Stok: {{ $produk->nama }}</h1>
        </div>
        <div class="mt-3">
            <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary" style="border-radius: 1rem; border: 1px solid rgba(148, 163, 184, 0.3); color: #475569;">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Produk
            </a>
        </div>
    </div>

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

    <div class="edit-stok-card">
                <div class="card-header">
                    <h5 class="mb-0">Form Ubah Stok</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('produk.update', $produk->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Product Info -->
                        <div class="product-info-box">
                            <div class="product-info-row">
                                <div class="product-info-item">
                                    <span class="product-info-label">Nama Produk</span>
                                    <div class="product-info-value">{{ $produk->nama }}</div>
                                </div>
                                <div class="product-info-item">
                                    <span class="product-info-label">Harga Jual</span>
                                    <div class="product-info-value">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="product-info-row">
                                <div class="product-info-item">
                                    <span class="product-info-label">Stok Saat Ini</span>
                                    <div class="product-info-value stok">{{ $produk->stok }}</div>
                                </div>
                                <div class="product-info-item">
                                    <span class="product-info-label">Status Stok</span>
                                    <div style="margin-top: 0.35rem;">
                                        @if ($produk->stok == 0)
                                            <span class="badge-status badge-kosong" style="background: rgba(239, 68, 68, 0.12); color: #991b1b; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 999px; padding: 0.55rem 0.95rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas fa-times-circle"></i>Kosong
                                            </span>
                                        @elseif ($produk->stok <= 10)
                                            <span class="badge-status badge-rendah" style="background: rgba(234, 179, 8, 0.12); color: #92400e; border: 1px solid rgba(234, 179, 8, 0.3); border-radius: 999px; padding: 0.55rem 0.95rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas fa-exclamation-triangle"></i>Stok Rendah
                                            </span>
                                        @else
                                            <span class="badge-status badge-tersedia" style="background: rgba(34, 197, 94, 0.12); color: #166534; border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 999px; padding: 0.55rem 0.95rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas fa-check-circle"></i>Tersedia
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Type Selection -->
                        <div class="change-type-section">
                            <label>Pilih Tipe Perubahan</label>
                            <div class="change-type-options">
                                <div class="change-type-option active">
                                    <div class="form-check">
                                        <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_tambah" value="tambah" checked>
                                        <label class="form-check-label" for="tipe_tambah">
                                            <strong>Tambahkan Stok</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="change-type-option">
                                    <div class="form-check">
                                        <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_kurangi" value="kurangi">
                                        <label class="form-check-label" for="tipe_kurangi">
                                            <strong>Kurangi Stok</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input-section" id="jumlah-perubahan-field">
                            <label for="jumlah_perubahan">
                                <span id="label-jumlah">Jumlah Ditambahkan</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jumlah_perubahan" name="jumlah_perubahan" min="0" max="999999" 
                                    placeholder="Masukkan jumlah" style="border-radius: 1rem 0 0 1rem;">
                                <span class="input-group-text" id="change-operator">+</span>
                            </div>
                            <div class="input-hint" id="jumlah-hint">
                                <i class="fas fa-info-circle"></i>
                                Masukkan jumlah yang akan ditambahkan ke stok saat ini ({{ $produk->stok }})
                            </div>
                            <div class="preview-box">
                                <small>
                                    <strong>Preview:</strong> <span id="preview-text">{{ $produk->stok }}</span> 
                                    <span id="preview-operator">+</span> 
                                    <span id="preview-value" style="display:inline-block; width:50px; text-align:center; color: #1e293b; font-weight: 600;">0</span>
                                    = <span id="preview-result" style="font-weight: 700; color: #2563eb;">{{ $produk->stok }}</span>
                                </small>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="notes-section">
                            <label for="keterangan">Catatan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                placeholder="Tambahkan catatan tentang perubahan stok (misal: hasil restock, penyesuaian inventory, dll)">{{ old('keterangan') }}</textarea>
                            <div class="input-hint" style="margin-top: 0.5rem;">
                                <i class="fas fa-lightbulb"></i>
                                Catatan ini akan tersimpan untuk referensi
                            </div>
                        </div>

                        <!-- Upload Foto -->
                        <div class="notes-section">
                            <label for="foto">Upload Foto Produk (Opsional)</label>
                            <div style="display: flex; gap: 2rem; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <div style="border: 2px dashed rgba(59, 130, 246, 0.3); border-radius: 1rem; padding: 1.5rem; text-align: center; background: rgba(248, 250, 255, 0.5); cursor: pointer; transition: all 0.25s ease;" id="dropZone">
                                        <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: #3b82f6; margin-bottom: 0.5rem; display: block;"></i>
                                        <p style="color: #475569; margin: 0.5rem 0; font-weight: 600;">Klik atau drag gambar ke sini</p>
                                        <small style="color: #64748b;">Format: JPG, PNG, GIF | Max: 10MB</small>
                                    </div>
                                </div>
                                <div style="width: 120px;">
                                    @if ($produk->foto)
                                        <img id="preview" src="{{ asset('storage/' . $produk->foto) }}" alt="Preview" style="width: 100%; height: 120px; object-fit: cover; border-radius: 1rem; border: 1px solid rgba(219, 234, 254, 0.5);">
                                    @else
                                        <div id="preview" style="width: 100%; height: 120px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1)); border-radius: 1rem; border: 1px dashed rgba(219, 234, 254, 0.5); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <small>Preview</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="input-hint" style="margin-top: 0.75rem;">
                                <i class="fas fa-info-circle"></i>
                                Upload foto produk untuk ditampilkan saat membuat pesanan
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-stok-simpan" id="btn-submit-form">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('produk.index') }}" class="btn btn-stok-batal" style="text-decoration: none;">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
        </div>
    </div>

    <!-- Success Modal Popup -->
    <div id="success-modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 9999; animation: fadeIn 0.3s ease-out;"></div>
    <div id="success-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10000; animation: popupSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);">
        <div style="background: white; border-radius: 1.5rem; padding: 2.5rem; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); text-align: center; max-width: 450px; width: 90vw;">
            <div style="margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #34d399 100%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; animation: scaleUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);">
                    <i class="fas fa-check" style="font-size: 2.5rem; color: white;"></i>
                </div>
            </div>
            <h2 style="color: #1e293b; font-weight: 900; margin-bottom: 0.75rem; font-size: 1.5rem;">Stok Berhasil Ditambah!</h2>
            <p style="color: #64748b; margin-bottom: 0.5rem; font-size: 0.95rem; line-height: 1.5;">
                Produk <strong id="modal-product-name" style="color: #1e293b;"></strong> telah berhasil ditambahkan.
            </p>
            <p style="color: #94a3b8; margin-bottom: 1.75rem; font-size: 0.85rem;">
                Notifikasi stok yang kurang telah dihapus dari daftar.
            </p>
            <button onclick="closeSuccessModalAndRedirect()" class="btn" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; border: none; padding: 0.75rem 2rem; border-radius: 0.875rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; width: 100%;">
                <i class="fas fa-arrow-right me-2"></i>Lanjutkan ke Daftar Produk
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes popupSlideIn {
        from {
            opacity: 0;
            transform: translate(-50%, -45%);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes scaleUp {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.15);
        }
        100% {
            transform: scale(1);
        }
    }
</style>

<script>
    const jumlahPerubahanField = document.getElementById('jumlah-perubahan-field');
    const jumlahPerubahanInput = document.getElementById('jumlah_perubahan');
    const radioButtons = document.querySelectorAll('.change-type-radio');
    const changeOptionCards = document.querySelectorAll('.change-type-option');
    const previewValue = document.getElementById('preview-value');
    const currentStok = {{ $produk->stok }};

    function updateUI() {
        const selectedType = document.querySelector('input[name="tipe_perubahan"]:checked').value;
        jumlahPerubahanField.style.display = 'block';

        if (selectedType === 'tambah') {
            document.getElementById('change-operator').textContent = '+';
            document.getElementById('preview-operator').textContent = '+';
            document.getElementById('label-jumlah').textContent = 'Jumlah Ditambahkan';
            document.getElementById('jumlah-hint').innerHTML = '<i class="fas fa-info-circle"></i> Masukkan jumlah yang akan ditambahkan ke stok saat ini (' + currentStok + ')';
        } else if (selectedType === 'kurangi') {
            document.getElementById('change-operator').textContent = '-';
            document.getElementById('preview-operator').textContent = '-';
            document.getElementById('label-jumlah').textContent = 'Jumlah Dikurangi';
            document.getElementById('jumlah-hint').innerHTML = '<i class="fas fa-info-circle"></i> Masukkan jumlah yang akan dikurangi dari stok saat ini (' + currentStok + ')';
        }
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateUI);
        radio.addEventListener('change', function() {
            changeOptionCards.forEach(card => card.classList.remove('active'));
            const card = this.closest('.change-type-option');
            if (card) card.classList.add('active');
        });
    });

    jumlahPerubahanInput.addEventListener('input', function() {
        const selectedType = document.querySelector('input[name="tipe_perubahan"]:checked').value;
        const jumlah = parseInt(this.value) || 0;
        previewValue.textContent = jumlah;

        if (selectedType === 'tambah') {
            document.getElementById('preview-result').textContent = currentStok + jumlah;
        } else if (selectedType === 'kurangi') {
            document.getElementById('preview-result').textContent = currentStok - jumlah;
        }
    });

    // Initialize
    updateUI();
    document.getElementById('preview-text').textContent = currentStok;
    document.getElementById('preview-result').textContent = currentStok;
    previewValue.textContent = 0;

    // ===== Handle Query Parameters from Notification =====
    // Auto-select "Tambah Stok" and pre-fill quantity when coming from notification
    setTimeout(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const fromNotification = urlParams.get('from') === 'notification';
        const minStok = urlParams.get('min_stok');
        
        if (fromNotification) {
            // Show stock shortage alert
            const alert = document.getElementById('stock-shortage-alert');
            if (alert) {
                alert.classList.remove('d-none');
                
                // Update alert values
                if (minStok && !isNaN(parseInt(minStok))) {
                    const shortage = parseInt(minStok);
                    const current = currentStok;
                    const ordered = current + shortage;
                    
                    document.getElementById('alert-current-stock').textContent = current;
                    document.getElementById('alert-ordered').textContent = ordered;
                    document.getElementById('alert-shortage').textContent = shortage;
                    document.getElementById('recommended-value').textContent = shortage;
                }
            }
            
            // Auto-select "Tambah Stok"
            document.getElementById('tipe_tambah').checked = true;
            
            // Trigger UI update
            updateUI();
            
            // Update active state
            changeOptionCards.forEach(card => card.classList.remove('active'));
            document.querySelector('[for="tipe_tambah"]').closest('.change-type-option').classList.add('active');
            
            // Pre-fill jumlah if min_stok provided
            if (minStok && !isNaN(parseInt(minStok))) {
                const minValue = parseInt(minStok);
                jumlahPerubahanInput.value = minValue;
                jumlahPerubahanInput.focus();
                
                // Update preview
                previewValue.textContent = minValue;
                document.getElementById('preview-result').textContent = currentStok + minValue;
                
                // Highlight the input to draw attention
                jumlahPerubahanInput.style.animation = 'pulse-highlight 0.6s ease-out';
                jumlahPerubahanInput.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';
                setTimeout(() => {
                    jumlahPerubahanInput.style.boxShadow = '';
                }, 1000);
            }
            
            // Add success message
            const successMsg = document.createElement('div');
            successMsg.className = 'alert alert-info alert-dismissible fade show';
            successMsg.style.marginBottom = '1.5rem';
            successMsg.innerHTML = `
                <i class="fas fa-lightbulb me-2"></i>
                <strong>Dari Notifikasi:</strong> Halaman ini dibuka dari notifikasi stok yang kurang. 
                Anda hanya perlu menambahkan jumlah yang diperlukan di atas.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            const formCard = document.querySelector('.edit-form-card');
            if (formCard && formCard.querySelector('form')) {
                formCard.querySelector('form').parentElement.insertBefore(successMsg, formCard.querySelector('form'));
            }
        }
    }, 100);

    // Add animation keyframe
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes pulse-highlight {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }
    `;
    document.head.appendChild(style);

    // ========== HANDLE FORM SUBMISSION FROM NOTIFICATION ==========
    let isFromNotification = false;

    // Check if we're coming from notification
    setTimeout(function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('from') === 'notification') {
            isFromNotification = true;
        }
    }, 100);

    // Handle form submission via AJAX when from notification
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!isFromNotification) {
                return; // Normal form submission
            }

            e.preventDefault();

            // Show loading state on button
            const submitBtn = document.getElementById('btn-submit-form');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sedang menyimpan...';

            // Prepare form data
            const formData = new FormData(form);

            // Submit via AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Success - show modal
                    const productName = document.querySelector('h1').textContent.replace('Ubah Stok: ', '').trim();
                    document.getElementById('modal-product-name').textContent = productName;
                    showSuccessModal();
                    return;
                }
                throw new Error('Form submission failed');
            })
            .catch(error => {
                console.error('Error:', error);
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                // Show error alert
                alert('Terjadi kesalahan saat menyimpan. Silakan coba lagi.');
            });
        });
    }

    // Show success modal
    function showSuccessModal() {
        const backdrop = document.getElementById('success-modal-backdrop');
        const modal = document.getElementById('success-modal');
        backdrop.style.display = 'block';
        modal.style.display = 'block';
    }

    // Close modal and redirect
    function closeSuccessModalAndRedirect() {
        const backdrop = document.getElementById('success-modal-backdrop');
        const modal = document.getElementById('success-modal');
        
        // Fade out
        backdrop.style.opacity = '0';
        modal.style.opacity = '0';
        backdrop.style.transition = 'opacity 0.3s ease';
        modal.style.transition = 'opacity 0.3s ease';
        
        setTimeout(() => {
            backdrop.style.display = 'none';
            modal.style.display = 'none';
            // Redirect
            window.location.href = '{{ route("produk.index") }}';
        }, 300);
    }
</script>
@endsection
