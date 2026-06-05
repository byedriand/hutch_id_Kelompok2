@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        --primary-light: #e6f2ff;
        --primary-color: #0052a3;
        --accent-blue: #1e88e5;
        --success-color: #10b981;
        --error-color: #ef4444;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        gap: 1.5rem;
        flex-wrap: wrap;
        animation: slideDown 0.6s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .page-header > div:first-child {
        flex: 1;
    }

    .page-header h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.25rem;
        letter-spacing: -0.01em;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .page-header h2::before {
        content: '';
        display: inline-flex;
        width: 4px;
        height: 20px;
        background: linear-gradient(135deg, #0066cc, #0052a3);
        border-radius: 2px;
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
        from {
            width: 0;
            opacity: 0;
        }
        to {
            width: 4px;
            opacity: 1;
        }
    }

    .page-header > div:first-child > small {
        color: #64748b;
        font-size: 0.9rem;
        font-weight: 500;
        letter-spacing: 0.02em;
    }

    .top-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-primary {
        background: var(--primary-gradient);
        border: none;
        border-radius: 0.9rem;
        padding: 0.75rem 1.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 26px rgba(0, 82, 163, 0.28);
        color: white;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        transition: left 0.3s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 36px rgba(0, 82, 163, 0.38);
    }

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .btn-primary i {
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .btn-primary:hover i {
        transform: rotate(90deg) scale(1.1);
    }

    .search-wrapper {
        background: white;
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 2.5rem;
        border: 1.5px solid rgba(0, 82, 163, 0.1);
        box-shadow: 0 6px 18px rgba(0, 82, 163, 0.06);
        animation: fadeInUp 0.6s ease-out 0.1s backwards;
    }

    .search-form {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.9rem;
        align-items: flex-end;
    }

    .form-control {
        border: 1.5px solid #dbe5f1;
        border-radius: 0.9rem;
        padding: 0.8rem 1.1rem;
        font-size: 0.9rem;
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        transition: all 0.3s ease;
        font-weight: 500;
        color: #1e293b;
    }

    .form-control:focus {
        border-color: #0066cc;
        background: white;
        box-shadow: 0 0 0 3.5px rgba(0, 102, 204, 0.1);
        outline: none;
    }

    .btn-search {
        background: var(--primary-gradient);
        border: none;
        border-radius: 0.9rem;
        padding: 0.8rem 1.5rem;
        color: white;
        font-weight: 700;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(0, 82, 163, 0.2);
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .btn-search:hover {
        background: linear-gradient(135deg, #003d82, #004399);
        transform: translateY(-1px);
        box-shadow: 0 9px 24px rgba(0, 82, 163, 0.3);
    }

    .btn-search i {
        font-size: 0.85rem;
    }

    .pelanggan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
        gap: 1.75rem;
        margin-bottom: 2.5rem;
    }

    .pelanggan-card {
        background: white;
        border-radius: 1.5rem;
        border: 1.5px solid rgba(0, 82, 163, 0.08);
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 10px 30px rgba(0, 82, 163, 0.08);
        animation: fadeInUp 0.6s ease-out;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .pelanggan-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pelanggan-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 82, 163, 0.15);
        border-color: rgba(0, 102, 204, 0.3);
    }

    .pelanggan-card:hover::before {
        opacity: 1;
    }

    .card-header-gradient {
        background: linear-gradient(135deg, rgba(0, 82, 163, 0.03), rgba(0, 102, 204, 0.02));
        padding: 1.5rem;
        border-bottom: 1.5px solid rgba(0, 102, 204, 0.1);
    }

    .card-title-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        gap: 1rem;
    }

    .card-title-section h5 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0052a3;
        flex: 1;
    }

    .badge {
        background: linear-gradient(135deg, #0066cc, #0052a3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.8rem;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.3);
        display: flex;
        align-items: center;
        gap: 0.4rem;
        animation: pulse 2s ease-in-out infinite;
    }

    .badge::before {
        content: '';
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 6px 16px rgba(0, 102, 204, 0.3);
        }
        50% {
            box-shadow: 0 8px 20px rgba(0, 102, 204, 0.45);
        }
    }

    .card-phone {
        color: #64748b;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    }

    .card-phone i {
        color: #0066cc;
    }

    .card-body {
        padding: 1.5rem;
        flex: 1;
    }

    .info-item {
        margin-bottom: 1rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-label {
        font-weight: 700;
        color: #0066cc;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-label i {
        font-size: 0.9rem;
    }

    .info-value {
        color: #1e293b;
        font-size: 0.95rem;
        word-break: break-word;
        font-weight: 600;
    }

    .card-actions {
        display: flex;
        gap: 0.75rem;
        padding: 1.25rem;
        border-top: 1.5px solid rgba(0, 102, 204, 0.1);
        background: linear-gradient(135deg, rgba(248, 251, 255, 0.8), rgba(255, 255, 255, 0.5));
    }

    .btn-action {
        flex: 1;
        padding: 0.75rem 1rem;
        border-radius: 0.9rem;
        font-size: 0.9rem;
        font-weight: 700;
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .btn-edit {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.12), rgba(0, 82, 163, 0.08));
        color: #0052a3;
        border: 2px solid rgba(0, 102, 204, 0.3);
    }

    .btn-edit::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(0, 102, 204, 0.15);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-edit:hover::after {
        width: 100%;
        height: 100%;
    }

    .btn-edit:hover {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.15), rgba(0, 82, 163, 0.12));
        border-color: #0066cc;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.2);
    }

    .btn-delete {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(220, 38, 38, 0.08));
        color: #dc2626;
        border: 2px solid rgba(239, 68, 68, 0.3);
    }

    .btn-delete::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(239, 68, 68, 0.15);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-delete:hover::after {
        width: 100%;
        height: 100%;
    }

    .btn-delete:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.12));
        border-color: #ef4444;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 5rem 2rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .empty-state-icon {
        font-size: 5rem;
        background: linear-gradient(135deg, #0066cc, #0052a3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.5rem;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0052a3;
        margin-bottom: 0.75rem;
    }

    .empty-state-text {
        color: #64748b;
        margin-bottom: 2rem;
        font-weight: 500;
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

    @media (max-width: 1024px) {
        .page-header h2 {
            font-size: 1.3rem;
        }

        .pelanggan-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .btn-primary {
            padding: 0.7rem 1.5rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            font-size: 1.2rem;
        }

        .page-header h2::before {
            height: 18px;
        }

        .page-header > div:first-child > small {
            font-size: 0.8rem;
        }

        .top-actions {
            width: 100%;
        }

        .top-actions .btn-primary {
            flex: 1;
            font-size: 0.8rem;
            padding: 0.65rem 1.2rem;
            justify-content: center;
        }

        .search-wrapper {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .search-form {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .form-control {
            padding: 0.7rem 0.95rem;
            font-size: 0.85rem;
        }

        .btn-search {
            width: 100%;
            padding: 0.7rem 1rem;
            font-size: 0.85rem;
        }

        .pelanggan-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1rem;
        }

        .card-header-gradient {
            padding: 1.25rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .card-actions {
            padding: 1rem;
        }

        .card-actions .btn {
            flex: 1;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .page-header h2 {
            font-size: 1rem;
        }

        .page-header h2::before {
            width: 3px;
            height: 16px;
        }

        .page-header > div:first-child > small {
            font-size: 0.75rem;
        }

        .top-actions {
            width: 100%;
        }

        .top-actions .btn-primary {
            width: 100%;
            padding: 0.6rem 1rem;
            font-size: 0.75rem;
        }

        .search-wrapper {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .search-form {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }

        .form-control {
            padding: 0.65rem 0.9rem;
            font-size: 0.8rem;
        }

        .btn-search {
            width: 100%;
            padding: 0.65rem 0.9rem;
            font-size: 0.75rem;
        }

        .pelanggan-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .pelanggan-card {
            border-radius: 1rem;
        }

        .card-header-gradient {
            padding: 1rem;
        }

        .card-title-section {
            flex-direction: column;
            gap: 0.5rem;
        }

        .card-title-section h5 {
            font-size: 0.9rem;
        }

        .badge {
            font-size: 0.7rem;
            padding: 0.35rem 0.75rem;
        }

        .card-phone {
            font-size: 0.8rem;
        }

        .card-body {
            padding: 1rem;
        }

        .info-label {
            font-size: 0.7rem;
        }

        .info-value {
            font-size: 0.8rem;
        }

        .card-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 1rem;
        }

        .card-actions .btn {
            width: 100%;
            padding: 0.55rem 0.8rem;
            font-size: 0.75rem;
        }
    }
</style>

<div class="page-header">
    <div>
        <h2 class="mb-0">Daftar Pelanggan</h2>
        <small>Kelola data pelanggan yang dapat dipilih saat membuat PO.</small>
    </div>
    <div class="top-actions">
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Pelanggan
        </a>
    </div>
</div>

<div class="search-wrapper">
    <form class="search-form" action="{{ route('pelanggan.index') }}" method="GET">
        <div>
            <input type="text" name="cari" class="form-control" value="{{ request('cari') }}" placeholder="Cari nama atau nomor telepon pelanggan...">
        </div>
        <button type="submit" class="btn-search">
            <i class="fas fa-search me-2"></i>Cari
        </button>
    </form>
</div>

<div class="pelanggan-grid">
    @forelse($pelanggan as $item)
        <div class="pelanggan-card">
            <div class="card-header-gradient">
                <div class="card-title-section">
                    <h5>{{ $item->nama }}</h5>
                    <span class="badge">{{ $item->pesanan_count ?? 0 }} PO</span>
                </div>
                <div class="card-phone">
                    <i class="fas fa-phone fa-xs"></i>
                    {{ $item->telepon }}
                </div>
            </div>

            <div class="card-body">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-map-marker-alt me-1"></i>Alamat</div>
                    <div class="info-value">{{ $item->alamat }}</div>
                </div>

                @if($item->email)
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-envelope me-1"></i>Email</div>
                    <div class="info-value">{{ $item->email }}</div>
                </div>
                @endif
            </div>

            <div class="card-actions">
                <a href="{{ route('pelanggan.edit', $item) }}" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i>Edit
                </a>
                <form action="{{ route('pelanggan.destroy', $item) }}" method="POST" class="flex-grow-1" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete w-100" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                        <i class="fas fa-trash"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="empty-state-title">Belum Ada Data Pelanggan</div>
            <div class="empty-state-text">Mulai dengan menambahkan pelanggan baru untuk memulai mengelola data pesanan Anda.</div>
            <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pelanggan Pertama
            </a>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $pelanggan->withQueryString()->links() }}
</div>
@endsection
