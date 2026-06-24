@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header dashboard-header align-items-center justify-content-between mb-5">
        <div class="dashboard-header-left">
            <div class="dashboard-title-wrapper">
                <div class="dashboard-icon" style="background: linear-gradient(135deg, #2d7dd2 0%, #1e56a0 100%); border-radius: 14px; box-shadow: 0 8px 20px rgba(45, 125, 210, 0.3);">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h1 class="dashboard-title">Edit Produk</h1>
                    <p class="dashboard-subtitle">Perbarui informasi produk: nama, harga, deskripsi, dan foto.</p>
                </div>
            </div>
        </div>
        <a href="{{ route('produk.staff') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
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

    <div class="row g-4">
        <!-- Form Edit -->
        <div class="col-lg-8">
            <div class="card staff-form-card shadow-lg">
                <div class="card-header staff-form-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Edit Informasi Produk</h5>
                            <small class="text-white-50">Perbarui detail produk di bawah</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('produk.staff.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- ID Produk (Read-only) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted">
                                <i class="fas fa-hashtag me-2"></i>ID Produk
                            </label>
                            <input type="text" class="form-control form-control-lg" value="#{{ $produk->id }}" disabled>
                        </div>

                        <!-- Nama Produk -->
                        <div class="mb-4">
                            <label for="nama" class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-2"></i>Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama', $produk->nama) }}" required>
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
                                       id="harga_jual" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" min="0" step="1000" required>
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
                                      id="keterangan" name="keterangan" rows="5">{{ old('keterangan', $produk->keterangan) }}</textarea>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle me-1"></i>Deskripsi lengkap produk (Max 1000 karakter)
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
                                <input type="file" class="form-control photo-input @error('foto') is-invalid @enderror" 
                                       id="foto" name="foto" accept="image/*">
                                <div class="photo-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="mb-1">Klik atau drag foto di sini</p>
                                    <small>JPG, PNG, GIF (Max 10MB)</small>
                                </div>
                                <div id="foto-preview" class="mt-3 p-3 bg-light rounded" style="display: none; border: 2px solid #3b82f6;">
                                    <div class="mb-3">
                                        <img id="preview-image" src="" alt="Preview" class="preview-img" style="width: 100%; max-height: 300px; object-fit: cover; display: block;">
                                    </div>
                                    <p class="text-success mb-2 font-weight-bold">
                                        <i class="fas fa-check-circle me-2"></i>Foto baru siap untuk di-upload
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="remove-foto">
                                        <i class="fas fa-trash-alt me-1"></i>Hapus Foto Baru
                                    </button>
                                </div>
                            </div>
                            @error('foto')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Button Group -->
                        <div class="button-group mt-5">
                            <a href="{{ route('produk.staff') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg submit-btn">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash-alt me-2"></i>Hapus Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <!-- Current Foto -->
            <div class="card staff-info-card shadow-lg mb-4">
                <div class="card-header staff-form-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-image"></i>
                        <span>Foto Produk Saat Ini</span>
                    </div>
                </div>
                <div class="card-body p-4 text-center">
                    @if($produk->foto_url)
                        <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama }}" class="current-photo">
                    @else
                        <div class="no-photo-placeholder">
                            <i class="fas fa-image"></i>
                            <p class="mt-3 text-muted">Tidak ada foto</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Card -->
            <div class="card staff-info-card shadow-lg">
                <div class="card-header staff-form-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        <span>Informasi Produk</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="info-item mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase fw-semibold">Stok Tersedia</small>
                        <p class="h5 text-success mt-2 mb-0">
                            <i class="fas fa-boxes me-2"></i>{{ $produk->stok }} Unit
                        </p>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-lock me-1"></i>Stok hanya bisa diubah oleh Operator Gudang
                        </small>
                    </div>
                    <div class="info-item mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase fw-semibold">Tanggal Dibuat</small>
                        <p class="mt-2 mb-0">{{ $produk->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="info-item">
                        <small class="text-muted text-uppercase fw-semibold">Terakhir Diubah</small>
                        <p class="mt-2 mb-0">{{ $produk->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

    .staff-info-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        background: white;
        transition: all 0.4s ease;
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

    .form-control-lg:disabled {
        background: #f1f5f9;
        color: #6c7a93;
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

    .photo-upload-area {
        position: relative;
        border-radius: 12px;
        overflow: visible;
        background: linear-gradient(135deg, #f0f7ff 0%, #f7fbff 100%);
        border: 2px dashed #3b82f6;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-upload-area:hover {
        background: linear-gradient(135deg, #e6f2ff 0%, #f0f7ff 100%);
        border-color: #2563eb;
    }

    .photo-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -webkit-tap-highlight-color: transparent;
        margin: 0;
        padding: 0;
        border: none;
    }

    @media (max-width: 768px) {
        .photo-input {
            display: block !important;
            position: static;
            opacity: 0.001 !important;
            width: 100% !important;
            height: 100% !important;
            padding: 8rem 1rem !important;
            z-index: 100 !important;
        }
    }

    .photo-placeholder {
        padding: 2rem 1rem;
        text-align: center;
        color: #3b82f6;
        width: 100%;
        pointer-events: none;
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

    .current-photo {
        max-width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .no-photo-placeholder {
        padding: 4rem 1rem;
        text-align: center;
        background: linear-gradient(135deg, #f0f7ff 0%, #f7fbff 100%);
        border-radius: 10px;
        color: #cbd5e1;
    }

    .no-photo-placeholder i {
        font-size: 3rem;
        display: block;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn-lg {
        padding: 0.95rem 2rem;
        font-weight: 700;
        border-radius: 10px;
        font-size: 1rem;
    }

    .submit-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
        color: white;
    }

    .info-item small {
        font-size: 0.75rem;
    }

    .text-danger {
        color: #dc2626;
        font-weight: 600;
    }

    .custom-alert-danger {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(185, 28, 28, 0.05) 100%);
        color: #7f1d1d;
        border: 1px solid rgba(220, 38, 38, 0.3);
        border-left: 4px solid #dc2626;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .button-group {
            flex-direction: column;
        }

        .button-group .btn {
            width: 100%;
        }
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
                if (photoPlaceholder) photoPlaceholder.style.display = 'none';
                console.log('✅ Photo preview loaded');
            };
            reader.onerror = () => {
                console.error('❌ Failed to read photo file');
            };
            reader.readAsDataURL(photoInput.files[0]);
        }
    }

    // Event listeners for desktop
    photoArea.addEventListener('click', function(e) {
        if (e.target !== photoInput) {
            photoInput.click();
        }
    });

    // Touch support for mobile
    photoArea.addEventListener('touchstart', function(e) {
        photoInput.click();
    });

    photoArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        photoArea.style.borderColor = '#2563eb';
        photoArea.style.background = 'linear-gradient(135deg, #e6f2ff 0%, #f0f7ff 100%)';
    });

    photoArea.addEventListener('dragleave', () => {
        photoArea.style.borderColor = '#3b82f6';
        photoArea.style.background = 'linear-gradient(135deg, #f0f7ff 0%, #f7fbff 100%)';
    });

    photoArea.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        photoArea.style.borderColor = '#3b82f6';
        photoInput.files = e.dataTransfer.files;
        handlePhotoSelect();
    });

    photoInput.addEventListener('change', handlePhotoSelect);
    photoInput.addEventListener('touchend', function(e) {
        e.preventDefault();
    });

    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', () => {
            photoInput.value = '';
            photoPreview.classList.remove('show');
            if (photoPlaceholder) photoPlaceholder.style.display = 'block';
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

    console.log('✅ Photo upload handler initialized for mobile and desktop');
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hapus Produk
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">Apakah Anda yakin ingin menghapus produk <strong>{{ $produk->nama }}</strong>?</p>
                <div class="alert alert-warning mb-0" role="alert">
                    <i class="fas fa-warning me-2"></i>
                    <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan. Produk akan dihapus secara permanen dari sistem.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form action="{{ route('produk.staff.destroy', $produk->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Ya, Hapus Produk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
