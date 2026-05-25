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
                                        @if(isset($notif->data['detail_kurang']))
                                            @if(auth()->user()->role === 'operator_gudang')
                                                <div class="alert alert-warning small mb-2">
                                                    <strong>Untuk Operator Gudang:</strong> Mohon tambahkan stok sesuai kebutuhan di bawah.
                                                </div>
                                            @endif
                                            <strong>Detail Kekurangan:</strong>
                                            <ul class="mb-0 mt-1 ps-3">
                                                @foreach($notif->data['detail_kurang'] as $detail)
                                                    <li class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            {{ $detail['nama_produk'] }}: {{ $detail['kurang'] }} unit (Stok: {{ $detail['stok_tersedia'] }})
                                                        </div>
                                                        @if(auth()->user()->role === 'operator_gudang')
                                                            <div>
                                                                @php
                                                                    $resolvedId = $detail['produk_id'] ?? null;
                                                                    if (!$resolvedId) {
                                                                        $resolvedId = \App\Models\Produk::where('nama', $detail['nama_produk'])
                                                                            ->orWhere('nama', 'like', '%' . $detail['nama_produk'] . '%')
                                                                            ->value('id');
                                                                    }
                                                                @endphp
                                                                <button type="button" class="btn btn-sm btn-outline-success btn-add-stock"
                                                                    data-produk-id="{{ $resolvedId ?? '' }}"
                                                                    data-nama="{{ $detail['nama_produk'] }}"
                                                                    data-stok="{{ $detail['stok_tersedia'] }}"
                                                                    data-kurang="{{ $detail['kurang'] }}">
                                                                    <i class="fas fa-plus me-1"></i>Tambah Stok
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
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
<div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-add-stock">
                    <input type="hidden" id="modal-produk-id">
                    <div class="mb-2">
                        <label class="form-label">Produk</label>
                        <div id="modal-produk-nama" class="fw-semibold"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Stok Saat Ini</label>
                        <div id="modal-stok-lama"></div>
                    </div>
                    <div class="mb-3">
                        <label for="modal-jumlah-tambah" class="form-label">Jumlah yang ditambahkan</label>
                        <input type="number" id="modal-jumlah-tambah" class="form-control" min="1" value="1" required>
                    </div>
                    <div id="modal-candidates" class="mb-3 d-none">
                        <label class="form-label">Pilih Produk (cocok)</label>
                        <div id="modal-candidates-list" class="list-group"></div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
        const addStockButtons = document.querySelectorAll('.btn-add-stock');
        const modal = new bootstrap.Modal(document.getElementById('addStockModal'));

        addStockButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                        const produkId = this.dataset.produkId;
                        const nama = this.dataset.nama;
                        const stok = parseInt(this.dataset.stok) || 0;
                        const kurang = parseInt(this.dataset.kurang) || 1;

                        document.getElementById('modal-produk-id').value = produkId;
                        document.getElementById('modal-produk-nama').textContent = nama;
                        document.getElementById('modal-stok-lama').textContent = stok;
                        document.getElementById('modal-jumlah-tambah').value = kurang;

                        modal.show();
                });
        });

        const form = document.getElementById('form-add-stock');
        form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const produkId = document.getElementById('modal-produk-id').value;
                const jumlahTambah = parseInt(document.getElementById('modal-jumlah-tambah').value) || 0;
                const stokLama = parseInt(document.getElementById('modal-stok-lama').textContent) || 0;
                const newStok = stokLama + jumlahTambah;
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

                if (!produkId) {
                        alert('Produk tidak ditemukan');
                        return;
                }

                try {
                    let res, json;
                    if (produkId) {
                        res = await fetch(`/produk/${produkId}/quick-update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ stok: newStok })
                        });
                    } else {
                        // Fallback: try update by product name
                        const nama = document.getElementById('modal-produk-nama').textContent || '';
                        res = await fetch(`/produk/quick-update-by-name`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ nama: nama.trim(), stok: newStok })
                        });
                    }

                    json = await res.json();
                    if (res.ok && json.success) {
                        alert(json.message || 'Stok berhasil diperbarui');
                        modal.hide();
                        location.reload();
                    } else {
                        // If server returned candidate list, show them
                        if (json && Array.isArray(json.candidates) && json.candidates.length > 0) {
                            const cWrap = document.getElementById('modal-candidates');
                            const cList = document.getElementById('modal-candidates-list');
                            cList.innerHTML = '';
                            json.candidates.forEach(c => {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = `${c.nama} (Stok: ${c.stok})`;
                                item.dataset.id = c.id;
                                item.addEventListener('click', function() {
                                    document.getElementById('modal-produk-id').value = this.dataset.id;
                                    document.getElementById('modal-produk-nama').textContent = c.nama;
                                    document.getElementById('modal-stok-lama').textContent = c.stok;
                                    cWrap.classList.add('d-none');
                                    // After selecting candidate, re-submit form programmatically
                                    form.dispatchEvent(new Event('submit'));
                                });
                                cList.appendChild(item);
                            });
                            cWrap.classList.remove('d-none');
                        } else {
                            console.error(json);
                            alert(json.error || 'Gagal memperbarui stok. Lihat konsol untuk detail.');
                        }
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan saat mengirim permintaan.');
                }
        });
});
</script>
@endpush
@endsection
