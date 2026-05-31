@extends('layouts.app')

@section('content')
<style>
    .create-header {
        background: linear-gradient(180deg, rgba(248, 250, 255, 0.95), rgba(241, 245, 255, 0.95));
        border-radius: 1.75rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(219, 234, 254, 0.5);
        animation: fadeInUp 0.55s ease-out;
    }
    .create-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .create-form-card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(219, 234, 254, 0.3);
        border-radius: 1.5rem;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out 0.15s both;
    }
    .form-card-header {
        background: linear-gradient(90deg, #f8fbff, #ffffff);
        border-bottom: 1px solid rgba(219, 234, 254, 0.3);
        padding: 1.5rem;
    }
    .form-card-header h5 {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }
    .form-group-wrapper {
        margin-bottom: 1.5rem;
    }
    .form-group-wrapper label {
        color: #334155;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-control {
        border-radius: 1rem;
        border: 1px solid #d8e2ef;
        padding: 0.85rem 1rem;
        font-size: 0.95rem;
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.18);
        border-color: #93c5fd;
    }
    .drop-zone {
        border: 2px dashed rgba(59, 130, 246, 0.3);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        background: rgba(248, 250, 255, 0.5);
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .drop-zone.highlight {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.05);
    }
    .btn-create {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 1rem;
        padding: 0.85rem 2rem;
        font-weight: 700;
        transition: all 0.25s ease;
    }
    .btn-create:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    }
    .btn-cancel {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 1rem;
        padding: 0.85rem 2rem;
        font-weight: 700;
        transition: all 0.25s ease;
    }
    .btn-cancel:hover {
        background: rgba(59, 130, 246, 0.15);
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
</style>

<div class="container-fluid">
    <div class="create-header">
        <h1>Tambah Produk Baru</h1>
        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary mt-3" style="border-radius: 1rem; border: 1px solid rgba(148, 163, 184, 0.3); color: #475569;">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 1.25rem; border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.08);">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="create-form-card">
        <div class="form-card-header">
            <h5 class="mb-0">Informasi Produk</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group-wrapper">
                            <label for="nama">Nama Produk *</label>
                            <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" 
                                placeholder="Masukkan nama produk" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group-wrapper">
                            <label for="harga_jual">Harga Jual (Rp) *</label>
                            <input type="number" id="harga_jual" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror" 
                                placeholder="0" min="0" value="{{ old('harga_jual') }}" required>
                            @error('harga_jual')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group-wrapper">
                            <label for="stok">Stok Awal *</label>
                            <input type="number" id="stok" name="stok" class="form-control @error('stok') is-invalid @enderror" 
                                placeholder="0" min="0" value="{{ old('stok') }}" required>
                            @error('stok')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group-wrapper">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" id="keterangan" name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                                placeholder="Informasi tambahan tentang produk" value="{{ old('keterangan') }}">
                            @error('keterangan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group-wrapper">
                            <label for="foto">Foto Produk</label>
                            <div style="display: flex; gap: 2rem; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <div class="drop-zone @error('foto') is-invalid @enderror" id="dropZone">
                                        <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 2.5rem; color: #3b82f6; margin-bottom: 0.5rem; display: block;"></i>
                                        <p style="color: #475569; margin: 0.5rem 0; font-weight: 600;">Klik atau drag gambar ke sini</p>
                                        <small style="color: #64748b;">Format: JPG, PNG, GIF | Max: 5MB</small>
                                    </div>
                                    @error('foto')
                                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div style="width: 120px;">
                                    <div id="preview" style="width: 100%; height: 120px; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1)); border-radius: 1rem; border: 1px dashed rgba(219, 234, 254, 0.5); display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                        <small>Preview</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-create">
                        <i class="fas fa-save me-2"></i>Tambah Produk
                    </button>
                    <a href="{{ route('produk.index') }}" class="btn btn-cancel" style="text-decoration: none;">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const dropZone = document.getElementById('dropZone');
    const fotoInput = document.getElementById('foto');
    const preview = document.getElementById('preview');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('highlight'));
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('highlight'));
    });

    dropZone.addEventListener('drop', handleDrop);
    dropZone.addEventListener('click', () => fotoInput.click());
    fotoInput.addEventListener('change', handleFileSelect);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fotoInput.files = files;
        handleFileSelect({ target: { files: files } });
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        if (files.length > 0) {
            const file = files[0];
            if (!file.type.startsWith('image/')) {
                alert('Silakan pilih file gambar');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar (max 5MB)');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                preview.innerHTML = '';
                preview.style.background = 'transparent';
                preview.style.border = 'none';
                const img = document.createElement('img');
                img.src = event.target.result;
                img.style.width = '100%';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '1rem';
                img.style.border = '1px solid rgba(219, 234, 254, 0.5)';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
