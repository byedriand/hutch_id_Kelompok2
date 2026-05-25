@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div>
            <h1 class="h3">Ubah Stok: {{ $produk->nama }}</h1>
            <p class="mb-0">Update jumlah stok barang dengan berbagai pilihan</p>
        </div>
        <div class="top-actions">
            <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Ubah Stok</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('produk.update', $produk->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Product Info -->
                        <div class="mb-4 p-3" style="background-color: #f8fbff; border-radius: 12px; border: 1px solid #dbe5f1;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted" style="font-size: 0.85rem;">Nama Produk</label>
                                    <div class="fw-bold" style="font-size: 1.1rem; color: #0f3d7f;">{{ $produk->nama }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted" style="font-size: 0.85rem;">Harga Jual</label>
                                    <div class="fw-bold" style="font-size: 1.1rem; color: #0f3d7f;">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.85rem;">Stok Saat Ini</label>
                                    <div class="fw-bold mono" style="font-size: 1.5rem; color: #2d7dd2;">{{ $produk->stok }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted" style="font-size: 0.85rem;">Status Stok</label>
                                    <div>
                                        @if ($produk->stok == 0)
                                            <span class="badge bg-danger p-2">
                                                <i class="fas fa-times-circle me-1"></i>Kosong
                                            </span>
                                        @elseif ($produk->stok <= 10)
                                            <span class="badge bg-warning p-2">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Stok Rendah
                                            </span>
                                        @else
                                            <span class="badge bg-success p-2">
                                                <i class="fas fa-check-circle me-1"></i>Tersedia
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Pilih Tipe Perubahan</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_set" value="set" checked>
                                        <label class="form-check-label" for="tipe_set">
                                            <strong>Set Ke Nilai Baru</strong>
                                            <br>
                                            <small class="text-muted">Atur stok ke jumlah tertentu</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_tambah" value="tambah">
                                        <label class="form-check-label" for="tipe_tambah">
                                            <strong>Tambahkan Stok</strong>
                                            <br>
                                            <small class="text-muted">Tambah ke stok yang ada</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input change-type-radio" type="radio" name="tipe_perubahan" id="tipe_kurangi" value="kurangi">
                                        <label class="form-check-label" for="tipe_kurangi">
                                            <strong>Kurangi Stok</strong>
                                            <br>
                                            <small class="text-muted">Kurang dari stok yang ada</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Input Fields -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="stok">
                                <span id="label-stok">Stok Baru</span>
                            </label>
                            <input type="number" class="form-control" id="stok" name="stok" min="0" max="999999" 
                                placeholder="Masukkan jumlah stok" value="{{ old('stok', $produk->stok) }}" required>
                            <small class="text-muted" id="stok-hint">
                                <i class="fas fa-info-circle me-1"></i>Masukkan angka yang akan menjadi stok baru
                            </small>
                        </div>

                        <div class="mb-4" id="jumlah-perubahan-field" style="display: none;">
                            <label class="form-label fw-semibold" for="jumlah_perubahan">
                                <span id="label-jumlah">Jumlah Perubahan</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jumlah_perubahan" name="jumlah_perubahan" min="0" max="999999" 
                                    placeholder="Masukkan jumlah">
                                <span class="input-group-text" id="change-operator">+</span>
                            </div>
                            <small class="text-muted" id="jumlah-hint"></small>
                            <div class="mt-2 p-2" style="background-color: #eef4fb; border-radius: 8px;">
                                <small>
                                    <strong>Preview:</strong> <span id="preview-text">{{ $produk->stok }}</span> 
                                    <span id="preview-operator">+</span> 
                                    <input type="text" value="0" style="width: 50px; border: none; background: transparent; text-align: center;" id="preview-value" readonly>
                                    = <span id="preview-result">{{ $produk->stok }}</span>
                                </small>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" for="keterangan">Catatan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                placeholder="Tambahkan catatan tentang perubahan stok (misal: hasil restock, penyesuaian inventory, dll)">{{ old('keterangan') }}</textarea>
                            <small class="text-muted">Catatan ini akan tersimpan untuk referensi</small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('produk.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Panduan Perubahan Stok</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-cog" style="color: #2d7dd2;"></i> Set Ke Nilai Baru
                        </h6>
                        <p class="small text-muted mb-0">Gunakan untuk menetapkan stok ke jumlah tertentu. Contoh: jika stok saat ini 50 dan Anda ingin menjadi 100, masukkan 100.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-plus-circle" style="color: #16a34a;"></i> Tambahkan Stok
                        </h6>
                        <p class="small text-muted mb-0">Gunakan untuk menambah stok yang ada. Contoh: stok saat ini 50, tambahkan 30 menjadi 80.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-minus-circle" style="color: #dc2626;"></i> Kurangi Stok
                        </h6>
                        <p class="small text-muted mb-0">Gunakan untuk mengurangi stok yang ada. Contoh: stok saat ini 50, kurangi 15 menjadi 35.</p>
                    </div>

                    <hr>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <small><strong>Tips:</strong> Selalu tambahkan catatan untuk mencatat alasan perubahan stok agar mudah dilacak nanti.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const stokInput = document.getElementById('stok');
    const jumlahPerubahanField = document.getElementById('jumlah-perubahan-field');
    const jumlahPerubahanInput = document.getElementById('jumlah_perubahan');
    const radioButtons = document.querySelectorAll('.change-type-radio');
    const currentStok = {{ $produk->stok }};

    function updateUI() {
        const selectedType = document.querySelector('input[name="tipe_perubahan"]:checked').value;
        
        if (selectedType === 'set') {
            stokInput.style.display = 'block';
            stokInput.parentElement.style.display = 'block';
            jumlahPerubahanField.style.display = 'none';
            document.getElementById('label-stok').textContent = 'Stok Baru';
            document.getElementById('stok-hint').textContent = 'Masukkan angka yang akan menjadi stok baru';
        } else if (selectedType === 'tambah') {
            stokInput.style.display = 'none';
            stokInput.parentElement.style.display = 'none';
            jumlahPerubahanField.style.display = 'block';
            document.getElementById('change-operator').textContent = '+';
            document.getElementById('preview-operator').textContent = '+';
            document.getElementById('label-jumlah').textContent = 'Jumlah Ditambahkan';
            document.getElementById('jumlah-hint').textContent = 'Masukkan jumlah yang akan ditambahkan ke stok saat ini (' + currentStok + ')';
        } else if (selectedType === 'kurangi') {
            stokInput.style.display = 'none';
            stokInput.parentElement.style.display = 'none';
            jumlahPerubahanField.style.display = 'block';
            document.getElementById('change-operator').textContent = '-';
            document.getElementById('preview-operator').textContent = '-';
            document.getElementById('label-jumlah').textContent = 'Jumlah Dikurangi';
            document.getElementById('jumlah-hint').textContent = 'Masukkan jumlah yang akan dikurangi dari stok saat ini (' + currentStok + ')';
        }
    }

    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateUI);
    });

    jumlahPerubahanInput.addEventListener('input', function() {
        const selectedType = document.querySelector('input[name="tipe_perubahan"]:checked').value;
        const jumlah = parseInt(this.value) || 0;
        document.getElementById('preview-value').value = jumlah;
        
        if (selectedType === 'tambah') {
            document.getElementById('preview-result').textContent = currentStok + jumlah;
        } else if (selectedType === 'kurangi') {
            document.getElementById('preview-result').textContent = currentStok - jumlah;
        }
    });

    // Initialize
    updateUI();
</script>
@endsection
