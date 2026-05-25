@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Notifikasi</h1>
                    <p class="text-muted mb-0">Kelola semua notifikasi pesanan dan stok</p>
                </div>
                @if(request()->query('filter') !== 'read')
                    <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">Tandai Semua Sudah Dibaca</button>
                    </form>
                @endif
            </div>

            <!-- Filter Tabs -->
            <div class="nav-tabs mb-4">
                <a href="{{ route('notifikasi.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Semua
                </a>
                <a href="{{ route('notifikasi.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ request('filter') === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Belum Dibaca
                </a>
                <a href="{{ route('notifikasi.index', ['filter' => 'read']) }}" class="btn btn-sm {{ request('filter') === 'read' ? 'btn-primary' : 'btn-outline-secondary' }}">
                    Sudah Dibaca
                </a>
            </div>

            <!-- Notifications List -->
            @if($notifikasis->count() > 0)
                <div class="space-y-3">
                    @foreach($notifikasis as $notif)
                        <div class="card border-0 shadow-sm mb-3 {{ is_null($notif->dibaca_at) ? 'border-start border-4 border-primary' : '' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $notif->judul }}</h6>
                                        <p class="mb-2 text-muted">{{ $notif->pesan }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $notif->created_at->diffForHumans() }}
                                        </small>
                                        @if($notif->pesanan)
                                            <small class="text-muted ms-3">
                                                <i class="fas fa-file me-1"></i>
                                                <a href="{{ route('pesanan.show', $notif->pesanan) }}" class="text-decoration-none">
                                                    {{ $notif->pesanan->nomor_po }}
                                                </a>
                                            </small>
                                        @endif
                                    </div>
                                    <div class="ms-3">
                                        @if(is_null($notif->dibaca_at))
                                            <form action="{{ route('notifikasi.markAsRead', $notif) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-check"></i> Baca
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('notifikasi.destroy', $notif) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus notifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($notif->data)
                                    <div class="alert alert-light p-2 mt-3 small">
                                        @if(isset($notif->data['nomor_po']))
                                            <strong>Pesanan:</strong> {{ $notif->data['nomor_po'] }}<br>
                                        @endif
                                        @if(isset($notif->data['nama_produk']))
                                            <strong>Produk:</strong> {{ $notif->data['nama_produk'] }}<br>
                                            <strong>Stok Lama:</strong> {{ $notif->data['stok_lama'] }} unit<br>
                                            <strong>Stok Baru:</strong> {{ $notif->data['stok_baru'] }} unit<br>
                                            <strong>Perubahan:</strong> 
                                            @if($notif->data['perubahan'] > 0)
                                                <span class="badge bg-success">+{{ $notif->data['perubahan'] }}</span>
                                            @elseif($notif->data['perubahan'] < 0)
                                                <span class="badge bg-danger">{{ $notif->data['perubahan'] }}</span>
                                            @else
                                                <span class="badge bg-secondary">0</span>
                                            @endif
                                            <br>
                                            @if(isset($notif->data['keterangan']) && $notif->data['keterangan'])
                                                <strong>Catatan:</strong> {{ $notif->data['keterangan'] }}<br>
                                            @endif
                                        @endif
                                        @if(isset($notif->data['detail_kurang']) && !empty($notif->data['detail_kurang']))
                                            <div class="border-top mt-3 pt-3">
                                                <div class="row g-2 small mb-3">
                                                    <div class="col-6">
                                                        <strong>Nomor PO:</strong> {{ $notif->data['nomor_po'] ?? 'Pesanan (Draft)' }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Dibuat:</strong> {{ $notif->created_at->format('d M Y H:i') }}
                                                    </div>
                                                </div>
                                                @if($notif->pesanan)
                                                    <div class="row g-2 small mb-3">
                                                        <div class="col-6">
                                                            <strong>Pelanggan:</strong> {{ $notif->pesanan->pelanggan->nama ?? 'N/A' }}
                                                        </div>
                                                        <div class="col-6">
                                                            <strong>Target Pengiriman:</strong> {{ $notif->pesanan->tanggal_pengiriman?->format('d M Y') ?? 'Belum ditentukan' }}
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="table-responsive mt-3">
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-center" style="width: 5%">#</th>
                                                                <th>Produk</th>
                                                                <th class="text-center" style="width: 15%">Dipesan</th>
                                                                <th class="text-center" style="width: 15%">Stok Ada</th>
                                                                <th class="text-center" style="width: 15%">Kurang</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($notif->data['detail_kurang'] as $idx => $detail)
                                                                <tr>
                                                                    <td class="text-center">{{ $idx + 1 }}</td>
                                                                    <td>
                                                                        <strong>{{ $detail['nama_produk'] }}</strong>
                                                                    </td>
                                                                    <td class="text-center fw-semibold">{{ $detail['jumlah_dipesan'] ?? $detail['kebutuhan'] ?? '-' }} unit</td>
                                                                    <td class="text-center">{{ $detail['stok_tersedia'] }} unit</td>
                                                                    <td class="text-center">
                                                                        <span class="badge bg-danger">{{ $detail['kurang'] }} unit</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                @if(auth()->user()->role === 'operator_gudang')
                                                    <div class="mt-3 pt-2 border-top">
                                                        <strong class="text-muted small d-block mb-2">Aksi Cepat:</strong>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach($notif->data['detail_kurang'] as $detail)
                                                                @php
                                                                    $resolvedId = $detail['produk_id'] ?? null;
                                                                    if (!$resolvedId) {
                                                                        $resolvedId = \App\Models\Produk::where('nama', $detail['nama_produk'])
                                                                            ->orWhere('nama', 'like', '%' . $detail['nama_produk'] . '%')
                                                                            ->value('id');
                                                                    }
                                                                @endphp
                                                                <button type="button" class="btn btn-sm btn-success btn-add-stock"
                                                                    data-produk-id="{{ $resolvedId ?? '' }}"
                                                                    data-nama="{{ $detail['nama_produk'] }}"
                                                                    data-stok="{{ $detail['stok_tersedia'] }}"
                                                                    data-kurang="{{ $detail['kurang'] }}">
                                                                    <i class="fas fa-plus me-1"></i>{{ substr($detail['nama_produk'], 0, 20) }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $notifikasis->links() }}
                </div>
            @else
                <div class="alert alert-secondary text-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block text-muted"></i>
                    <h5>Tidak ada notifikasi</h5>
                    <p class="text-muted mb-0">Semua notifikasi sudah dibaca atau belum ada notifikasi baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .nav-tabs {
        display: flex;
        gap: 0.5rem;
    }

    .space-y-3 > * + * {
        margin-top: 1rem;
    }
</style>
<!-- Modal for quick add stock -->
<div class="modal fade" id="addStockModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog modal-sm modal-dialog-centered" style="z-index: 10000;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Stok Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Produk:</strong></p>
                <p id="modalProduk" class="mb-3 p-2 bg-light rounded">-</p>
                
                <p><strong>Stok Saat Ini:</strong></p>
                <p id="modalStok" class="mb-3 p-2 bg-light rounded">0</p>
                
                <label for="jumlahTambah" class="form-label"><strong>Jumlah Tambah:</strong></label>
                <input type="number" class="form-control form-control-lg mb-3" id="jumlahTambah" value="1" min="1">
                
                <div id="candidateContainer" class="d-none mt-3 pt-3 border-top">
                    <p class="text-danger mb-2"><strong>Produk tidak ditemukan. Pilih dari daftar:</strong></p>
                    <div id="candidateList"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success btn-lg" id="btnTambahStok">✓ Tambah Stok</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-backdrop {
        z-index: 9998 !important;
    }
    #addStockModal {
        z-index: 9999 !important;
    }
    .modal-backdrop.show {
        opacity: 0.5;
    }
</style>

@push('scripts')
<script>
    // Initialize after small delay to ensure DOM is ready
    setTimeout(function() {
        console.log('✅ Starting initialization');
        
        const modalEl = document.getElementById('addStockModal');
        console.log('Modal element:', modalEl ? 'FOUND' : 'NOT FOUND');
        
        if (!modalEl) {
            console.error('❌ Modal element not found');
            return;
        }
        
        let bsModal = new bootstrap.Modal(modalEl);
        console.log('✅ Bootstrap modal created');
        
        // Setup event delegation for quick add buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-add-stock')) {
                console.log('✅ Quick add button clicked');
                e.preventDefault();
                
                const btn = e.target.closest('.btn-add-stock');
                const produkId = btn.getAttribute('data-produk-id') || '';
                const nama = btn.getAttribute('data-nama') || 'Unknown';
                const stok = parseInt(btn.getAttribute('data-stok')) || 0;
                const kurang = parseInt(btn.getAttribute('data-kurang')) || 1;
                
                console.log(`Data: ID='${produkId}', Nama='${nama}', Stok=${stok}, Kurang=${kurang}`);
                
                // Set modal content
                document.getElementById('modalProduk').textContent = nama;
                document.getElementById('modalStok').textContent = stok;
                document.getElementById('jumlahTambah').value = kurang;
                modalEl.dataset.produkId = produkId;
                document.getElementById('candidateContainer').classList.add('d-none');
                
                // Show modal
                console.log('Showing modal...');
                bsModal.show();
            }
        });
        
        // Handle Tambah Stok button
        document.getElementById('btnTambahStok').addEventListener('click', async function() {
            const produkId = modalEl.dataset.produkId || '';
            const stokLama = parseInt(document.getElementById('modalStok').textContent) || 0;
            const jumlahTambah = parseInt(document.getElementById('jumlahTambah').value) || 1;
            const newStok = stokLama + jumlahTambah;
            
            console.log(`Submitting: ID='${produkId}', NewStok=${newStok}`);
            
            if (!produkId) {
                alert('❌ Pilih produk terlebih dahulu');
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '⏳ Processing...';
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                const response = await fetch(`/produk/${produkId}/quick-update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ stok: newStok })
                });
                
                const data = await response.json();
                console.log('Response:', data);
                
                if (response.ok && data.success) {
                    alert('✅ ' + (data.message || 'Stok berhasil diperbarui!'));
                    bsModal.hide();
                    setTimeout(() => location.reload(), 300);
                } else if (data.candidates && data.candidates.length > 0) {
                    const candidateList = document.getElementById('candidateList');
                    candidateList.innerHTML = '';
                    
                    data.candidates.forEach(candidate => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-outline-primary btn-sm d-block w-100 text-start mb-2';
                        btn.innerHTML = `<strong>${candidate.nama}</strong> <small class="text-muted">(Stok: ${candidate.stok})</small>`;
                        
                        btn.addEventListener('click', function() {
                            document.getElementById('addStockModal').dataset.produkId = candidate.id;
                            document.getElementById('modalProduk').textContent = candidate.nama;
                            document.getElementById('modalStok').textContent = candidate.stok;
                            document.getElementById('candidateContainer').classList.add('d-none');
                        });
                        
                        candidateList.appendChild(btn);
                    });
                    
                    document.getElementById('candidateContainer').classList.remove('d-none');
                    this.disabled = false;
                    this.innerHTML = '✓ Tambah Stok';
                } else {
                    alert('❌ ' + (data.error || 'Gagal memperbarui stok'));
                    this.disabled = false;
                    this.innerHTML = '✓ Tambah Stok';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error: ' + error.message);
                this.disabled = false;
                this.innerHTML = '✓ Tambah Stok';
            }
        });
        
        console.log('✅ Initialization complete');
    }, 500);
</script>
@endpush
@endsection
