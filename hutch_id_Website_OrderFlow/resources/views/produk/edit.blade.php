@extends('layouts.app')

@section('content')
<style>
    .edit-stok-header {
        background: linear-gradient(180deg, rgba(248, 250, 255, 0.95), rgba(241, 245, 255, 0.95));
        border-radius: 1.75rem;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(219, 234, 254, 0.5);
        animation: fadeInUp 0.55s ease-out;
    }
    .edit-stok-header::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.08), transparent 70%);
        border-radius: 50%;
    }
    .edit-stok-header > div {
        position: relative;
        z-index: 1;
    }
    .edit-stok-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .edit-stok-header-desc {
        color: #64748b;
        font-size: 1.05rem;
    }
    .edit-stok-header .btn {
        position: relative;
        z-index: 1;
    }

    .edit-stok-card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(219, 234, 254, 0.3);
        border-radius: 1.5rem;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out 0.15s both;
    }
    .edit-stok-card-header {
        background: linear-gradient(90deg, #f8fbff, #ffffff);
        border-bottom: 1px solid rgba(219, 234, 254, 0.3);
        padding: 1.5rem;
    }
    .edit-stok-card-header h5 {
        color: #1e293b;
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }

    .product-info-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(147, 197, 253, 0.08));
        border: 1px solid rgba(147, 197, 253, 0.2);
        border-left: 4px solid #3b82f6;
        border-radius: 1.25rem;
        padding: 1.75rem;
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out 0.1s both;
    }
    .product-info-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }
    .product-info-row:last-child {
        margin-bottom: 0;
    }
    .product-info-item {
        flex: 1;
    }
    .product-info-label {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
    }
    .product-info-value {
        color: #1e293b;
        font-size: 1.15rem;
        font-weight: 700;
    }
    .product-info-value.stok {
        color: #2d7dd2;
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
                    <form method="POST" action="{{ route('produk.update', $produk->id) }}">
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

                        <!-- Submit Buttons -->
                        <div class="action-buttons">
                            <button type="submit" class="btn btn-stok-simpan">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('produk.index') }}" class="btn btn-stok-batal" style="text-decoration: none;">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
        </div>
    </div>
</div>

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
            document.getElementById('jumlah-hint').textContent = 'Masukkan jumlah yang akan ditambahkan ke stok saat ini (' + currentStok + ')';
        } else if (selectedType === 'kurangi') {
            document.getElementById('change-operator').textContent = '-';
            document.getElementById('preview-operator').textContent = '-';
            document.getElementById('label-jumlah').textContent = 'Jumlah Dikurangi';
            document.getElementById('jumlah-hint').textContent = 'Masukkan jumlah yang akan dikurangi dari stok saat ini (' + currentStok + ')';
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
</script>
@endsection
