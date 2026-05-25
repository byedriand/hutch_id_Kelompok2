@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div>
            <h1 class="h3">Manajemen Stok Barang</h1>
            <p class="mb-0">Kelola stok produk dan pantau ketersediaan barang</p>
        </div>
        <div class="top-actions">
            <div id="stok-summary" style="font-size: 0.9rem;">
                <div class="fw-bold text-primary">Total Stok: {{ $totalStok }} unit</div>
                <small class="text-muted">{{ $jumlahProduk }} produk terdaftar</small>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Produk</h5>
        </div>
        <div class="card-body">
            @if ($produk->count() > 0)
                <div class="table-wrap">
                    <table class="table table-hover">
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
                                        <span class="fw-semibold">{{ $item->nama }}</span>
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="mono fw-bold" style="color: #2d7dd2; font-size: 1rem;">{{ $item->stok }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->stok == 0)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Kosong
                                            </span>
                                        @elseif ($item->stok <= 10)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Rendah
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-sm btn-primary" title="Edit Stok">
                                            <i class="fas fa-edit me-1"></i>Ubah
                                        </a>
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#quickUpdateModal-{{ $item->id }}" title="Update Cepat">
                                            <i class="fas fa-bolt me-1"></i>Cepat
                                        </button>
                                    </td>
                                </tr>

                                <!-- Quick Update Modal -->
                                <div class="modal fade" id="quickUpdateModal-{{ $item->id }}" tabindex="-1" aria-labelledby="quickUpdateLabel-{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="quickUpdateLabel-{{ $item->id }}">Update Cepat: {{ $item->nama }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form class="quick-update-form" data-product-id="{{ $item->id }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 mb-3">
                                                            <label class="form-label">Stok Saat Ini</label>
                                                            <input type="text" class="form-control" value="{{ $item->stok }}" disabled>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Stok Baru</label>
                                                            <input type="number" name="stok" class="form-control" min="0" max="999999" placeholder="Masukkan jumlah stok baru" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                        <p class="text-muted">Tidak ada produk yang tersedia</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($produk->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $produk->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x mb-3" style="color: #d1d5db;"></i>
                    <h5>Tidak Ada Data Produk</h5>
                    <p class="text-muted">Tidak ada produk yang dapat dikelola saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.quick-update-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const productId = form.dataset.productId;
            const formData = new FormData(form);
            
            try {
                const response = await fetch(`/produk/${productId}/quick-update`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Close modal
                    const modalElement = form.closest('.modal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    modal.hide();
                    
                    // Show success message
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    document.querySelector('.container-fluid').insertAdjacentHTML('afterBegin', alertHtml);
                    
                    // Reload page after 1.5 seconds
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + (data.message || 'Gagal mengupdate stok'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });
</script>
@endsection
