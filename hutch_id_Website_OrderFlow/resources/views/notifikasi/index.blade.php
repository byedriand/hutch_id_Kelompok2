@extends('layouts.app')

@section('content')
<style>
    * {
        transition: color 0.2s ease, background-color 0.2s ease;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2.5rem;
        gap: 1.5rem;
        flex-wrap: wrap;
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08), rgba(59, 130, 246, 0.05));
        padding: 2.5rem;
        border-radius: 1.75rem;
        border: 1.5px solid rgba(45, 125, 210, 0.15);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(45, 125, 210, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .page-header > div:first-child {
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 900;
        background: linear-gradient(135deg, #1e293b 0%, #2d7dd2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        letter-spacing: -0.03em;
    }

    .page-header p {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0;
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .header-actions button {
        padding: 0.7rem 1.5rem;
        border-radius: 1rem;
        border: 2px solid transparent;
        background: linear-gradient(135deg, #2d7dd2 0%, #1e5aa8 100%);
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(45, 125, 210, 0.3);
        letter-spacing: 0.3px;
    }

    .header-actions button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
        z-index: 0;
    }

    .header-actions button:hover::before {
        left: 100%;
    }

    .header-actions button:hover {
        background: linear-gradient(135deg, #1e5aa8 0%, #1e40af 100%);
        transform: translateY(-2px) scale(1.01);
        box-shadow: 0 8px 20px rgba(45, 125, 210, 0.35);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .header-actions button:active {
        transform: translateY(-1px) scale(0.99);
    }

    .header-actions button i {
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .header-actions button:hover i {
        transform: scale(1.1);
    }

    .filter-tabs {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.6));
        padding: 1rem;
        border-radius: 1.5rem;
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .filter-tab {
        padding: 0.75rem 1.5rem;
        border-radius: 1rem;
        border: 2px solid transparent;
        background: rgba(255, 255, 255, 0.7);
        color: #64748b;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .filter-tab::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .filter-tab:hover::before {
        left: 100%;
    }

    .filter-tab:hover {
        border-color: rgba(45, 125, 210, 0.3);
        color: #2d7dd2;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(45, 125, 210, 0.15);
        background: rgba(45, 125, 210, 0.08);
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #2d7dd2 0%, #1e5aa8 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 8px 24px rgba(45, 125, 210, 0.35);
        transform: translateY(-3px);
    }

    .filter-tab.active i {
        animation: iconRotate 0.5s ease;
    }

    @keyframes iconRotate {
        0% { transform: rotate(-10deg); }
        100% { transform: rotate(0deg); }
    }

    .notif-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .notif-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
        border-radius: 1.5rem;
        border: 2px solid rgba(219, 229, 241, 0.6);
        padding: 1.75rem;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease-out;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    .notif-card.unread {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08), rgba(59, 130, 246, 0.05));
        border-color: rgba(45, 125, 210, 0.35);
        animation: slideInGlow 0.6s ease-out, pulseNotif 3s ease-in-out infinite;
        box-shadow: 0 6px 24px rgba(45, 125, 210, 0.12);
    }

    .notif-card.read {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
        border-color: rgba(219, 229, 241, 0.5);
    }

    .notif-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(180deg, #2d7dd2 0%, #1e5aa8 50%, #1e40af 100%);
        opacity: 0;
        transition: all 0.4s ease;
    }

    .notif-card.unread::before {
        opacity: 1;
        animation: slideInLeft 0.5s ease-out;
    }

    .notif-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(45, 125, 210, 0.18);
        border-color: rgba(45, 125, 210, 0.4);
    }

    .notif-card.unread:hover {
        box-shadow: 0 16px 48px rgba(45, 125, 210, 0.22);
    }

    .notif-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.15), rgba(59, 130, 246, 0.1));
        color: #2d7dd2;
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-right: 1rem;
        box-shadow: 0 4px 12px rgba(45, 125, 210, 0.1);
    }

    .notif-card.unread .notif-icon {
        animation: iconFloat 2s ease-in-out infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }

    .notif-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        gap: 1.25rem;
    }

    .notif-header-content {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.35rem;
        flex: 1;
    }

    .notif-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        line-height: 1.3;
        letter-spacing: -0.01em;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .notif-card.unread .notif-title {
        color: #0f3d7f;
        font-weight: 900;
    }

    .notif-status {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .status-badge {
        background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.9rem;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 16px rgba(45, 125, 210, 0.3);
        animation: badgePulse 2s ease-in-out infinite;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge i {
        font-size: 0.9rem;
        animation: iconBounce 1.5s ease-in-out infinite;
    }

    .notif-message {
        color: #475569;
        font-size: 0.95rem;
        margin: 0;
        line-height: 1.6;
        font-weight: 500;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .notif-card.unread .notif-message {
        color: #1e293b;
        font-weight: 600;
    }

    .notif-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-top: 1.25rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        font-size: 0.88rem;
        color: #64748b;
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-top: 1.5px solid rgba(219, 229, 241, 0.5);
        border-bottom: 1.5px solid rgba(219, 229, 241, 0.5);
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .meta-item i {
        color: #2d7dd2;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .meta-item:hover i {
        transform: scale(1.1);
        color: #1e5aa8;
    }

    .notif-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 0.5rem;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .action-btn {
        padding: 0.65rem 1.25rem;
        border-radius: 0.95rem;
        border: none;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.3px;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        transition: left 0.4s ease;
        z-index: 0;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .action-btn i {
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .action-btn:hover i {
        transform: scale(1.2);
    }

    .action-btn-read {
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.15), rgba(45, 125, 210, 0.08));
        color: #2d7dd2;
        border: 2px solid rgba(45, 125, 210, 0.35);
        box-shadow: 0 2px 8px rgba(45, 125, 210, 0.1);
    }

    .action-btn-read:hover {
        background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(45, 125, 210, 0.3);
    }

    .action-btn-delete {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.15), rgba(220, 38, 38, 0.08));
        color: #dc2626;
        border: 2px solid rgba(220, 38, 38, 0.35);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1);
    }

    .action-btn-delete:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        background: linear-gradient(135deg, rgba(45, 125, 210, 0.08), rgba(59, 130, 246, 0.05));
        border-radius: 1.75rem;
        border: 2px dashed rgba(45, 125, 210, 0.25);
        animation: fadeInUp 0.6s ease-out;
        position: relative;
        overflow: hidden;
    }

    .empty-state::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(45, 125, 210, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .empty-state-icon {
        font-size: 5rem;
        background: linear-gradient(135deg, #2d7dd2, #1e5aa8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1.5rem;
        animation: float 3s ease-in-out infinite;
        position: relative;
        z-index: 1;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.75rem;
        position: relative;
        z-index: 1;
        letter-spacing: -0.02em;
    }

    .empty-state-text {
        color: #64748b;
        font-size: 1.05rem;
        position: relative;
        z-index: 1;
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

    @keyframes slideInLeft {
        from {
            transform: translateX(-5px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideInGlow {
        from {
            opacity: 0;
            transform: translateY(10px);
            filter: brightness(1.2);
        }
        to {
            opacity: 1;
            transform: translateY(0);
            filter: brightness(1);
        }
    }

    @keyframes pulseNotif {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(45, 125, 210, 0.2);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(45, 125, 210, 0);
        }
    }

    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.08);
        }
    }

    @keyframes iconBounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-4px);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-12px);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @media (max-width: 1024px) {
        .page-header {
            padding: 2rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
        }

        .filter-tab {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        .notif-card {
            padding: 1.25rem;
            border-radius: 1.1rem;
        }

        .notif-icon {
            width: 3.2rem;
            height: 3.2rem;
            font-size: 1.35rem;
        }

        .status-badge {
            padding: 0.4rem 0.75rem;
            font-size: 0.72rem;
        }

        .action-btn {
            padding: 0.5rem 0.9rem;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            gap: 1rem;
            padding: 1.75rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .header-actions {
            width: 100%;
        }

        .header-actions button {
            font-size: 0.85rem;
            padding: 0.65rem 1.25rem;
            width: 100%;
            gap: 0.5rem;
        }

        .filter-tab {
            padding: 0.5rem 0.8rem;
            font-size: 0.8rem;
        }

        .notif-card {
            padding: 1rem;
            border-radius: 1rem;
            border-width: 1.5px;
        }

        .notif-card::before {
            width: 4px;
        }

        .notif-header {
            flex-direction: row;
            align-items: center;
            margin-bottom: 1rem;
            gap: 0.75rem;
        }

        .notif-header-content {
            flex-direction: column;
            gap: 0.25rem;
        }

        .notif-icon {
            width: 2.8rem;
            height: 2.8rem;
            margin-right: 0;
            margin-bottom: 0;
            font-size: 1.1rem;
        }

        .notif-title {
            font-size: 0.9rem;
        }

        .notif-message {
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .status-badge {
            padding: 0.35rem 0.65rem;
            font-size: 0.7rem;
        }

        .notif-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .action-btn {
            width: 100%;
            padding: 0.45rem 0.8rem;
            font-size: 0.8rem;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 {
            font-size: 1.25rem;
        }

        .header-actions button {
            font-size: 0.8rem;
            padding: 0.6rem 1rem;
            width: 100%;
            gap: 0.4rem;
            font-weight: 600;
        }

        .filter-tab {
            padding: 0.4rem 0.6rem;
            font-size: 0.7rem;
            border-radius: 0.6rem;
        }

        .filter-tab i {
            font-size: 0.8rem;
        }

        .notif-card {
            padding: 0.85rem;
            border-radius: 0.9rem;
            border-width: 1px;
        }

        .notif-card::before {
            width: 3px;
        }

        .notif-header {
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .notif-icon {
            width: 2.4rem;
            height: 2.4rem;
            font-size: 0.95rem;
        }

        .notif-header-content {
            gap: 0.2rem;
        }

        .notif-title {
            font-size: 0.85rem;
            word-break: break-word;
            line-height: 1.2;
        }

        .notif-message {
            font-size: 0.78rem;
            line-height: 1.4;
        }

        .status-badge {
            padding: 0.3rem 0.55rem;
            font-size: 0.65rem;
            border-radius: 0.5rem;
            white-space: nowrap;
        }

        .notif-meta {
            font-size: 0.72rem;
            gap: 0.75rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            margin-top: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .notif-actions {
            gap: 0.4rem;
            padding-top: 0.75rem;
            margin-top: 0.75rem;
        }

        .action-btn {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
        }

        .empty-state {
            padding: 1.5rem 1rem;
            border-radius: 1rem;
        }

        .empty-state-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .empty-state-title {
            font-size: 1.05rem;
        }

        .empty-state-text {
            font-size: 0.8rem;
        }
    }
</style>

<div class="page-header">
    <div>
        <h1>Notifikasi</h1>
        <p>Kelola semua notifikasi pesanan dan stok Anda</p>
    </div>
    @if(request()->query('filter') !== 'read')
    <div class="header-actions">
        <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" class="action-btn action-btn-read" style="margin: 0;">
                <i class="fas fa-check-double"></i>Tandai Semua Sudah Dibaca
            </button>
        </form>
    </div>
    @endif
</div>

<div class="filter-tabs">
    <a href="{{ route('notifikasi.index') }}" class="filter-tab {{ !request('filter') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        Semua
    </a>
    <a href="{{ route('notifikasi.index', ['filter' => 'unread']) }}" class="filter-tab {{ request('filter') === 'unread' ? 'active' : '' }}">
        <i class="fas fa-circle"></i>
        Belum Dibaca
    </a>
    <a href="{{ route('notifikasi.index', ['filter' => 'read']) }}" class="filter-tab {{ request('filter') === 'read' ? 'active' : '' }}">
        <i class="fas fa-check-circle"></i>
        Sudah Dibaca
    </a>
</div>

<div class="notif-list">
    @forelse($notifikasis as $notif)
        <div class="notif-card {{ is_null($notif->dibaca_at) ? 'unread' : 'read' }}">
            <div class="notif-header">
                <div class="notif-icon">
                    @if(strpos($notif->pesan, 'Stok') !== false)
                        <i class="fas fa-box-open"></i>
                    @elseif(strpos($notif->pesan, 'Pesanan') !== false || strpos($notif->pesan, 'Pengiriman') !== false)
                        <i class="fas fa-shopping-cart"></i>
                    @else
                        <i class="fas fa-bell"></i>
                    @endif
                </div>
                <div class="notif-header-content">
                    <h5 class="notif-title">{{ $notif->judul }}</h5>
                    <p class="notif-message">{{ $notif->pesan }}</p>
                </div>
                <div class="notif-status">
                    @if(is_null($notif->dibaca_at))
                        <span class="status-badge">
                            <i class="fas fa-circle"></i>
                            Baru
                        </span>
                    @endif
                </div>
            </div>

            <div class="notif-meta">
                <span class="meta-item">
                    <i class="fas fa-clock"></i>
                    {{ $notif->created_at->diffForHumans() }}
                </span>
                @if($notif->pesanan)
                    <span class="meta-item">
                        <i class="fas fa-file"></i>
                        <a href="{{ route('pesanan.show', $notif->pesanan) }}" class="text-decoration-none" style="color: #2d7dd2; font-weight: 600;">
                            {{ $notif->pesanan->nomor_po }}
                        </a>
                    </span>
                @endif
            </div>

            {{-- Detail Produk yang Kurang Stok --}}
            @if($notif->tipe === 'stok_kurang' && isset($notif->data['detail_kurang']) && !empty($notif->data['detail_kurang']) && auth()->user()->role === 'operator_gudang')
                <div class="stock-shortage-section">
                    <div class="stock-shortage-header">
                        <div class="header-content">
                            <i class="fas fa-boxes"></i>
                            <span>Produk yang Kurang Stok</span>
                        </div>
                        <span class="item-count">{{ count($notif->data['detail_kurang']) }} item</span>
                    </div>
                    
                    <div class="shortage-items-grid">
                        @foreach($notif->data['detail_kurang'] as $detail)
                            @php
                                // Try to resolve product ID
                                $resolvedId = $detail['produk_id'] ?? null;
                                if (!$resolvedId && isset($detail['nama_produk'])) {
                                    $resolvedId = \App\Models\Produk::where('nama', $detail['nama_produk'])
                                        ->orWhere('nama', 'like', '%' . $detail['nama_produk'] . '%')
                                        ->value('id');
                                }
                                
                                // Calculate percentage for progress
                                $totalNeeded = ($detail['stok_tersedia'] ?? 0) + ($detail['kurang'] ?? 0);
                                $percentage = $totalNeeded > 0 ? (($detail['stok_tersedia'] ?? 0) / $totalNeeded) * 100 : 0;
                            @endphp
                            
                            <div class="shortage-item-card">
                                <div class="card-header">
                                    <h4 class="product-name">{{ $detail['nama_produk'] }}</h4>
                                    <div class="shortage-badge" title="Stok saat ini: {{ $detail['stok_tersedia'] }} unit, Dipesan: {{ $detail['jumlah_dipesan'] ?? $detail['kebutuhan'] ?? '-' }} unit, Kurang: {{ $detail['kurang'] }} unit" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-triangle-exclamation"></i>
                                        <span>{{ $detail['kurang'] }} unit</span>
                                    </div>
                                </div>

                                <div class="stock-info">
                                    <div class="info-row">
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-cube"></i>
                                                Stok Tersedia
                                            </span>
                                            <span class="info-value available">{{ $detail['stok_tersedia'] }} unit</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="fas fa-shopping-cart"></i>
                                                Dipesan
                                            </span>
                                            <span class="info-value needed">{{ $detail['jumlah_dipesan'] ?? $detail['kebutuhan'] ?? '-' }} unit</span>
                                        </div>
                                    </div>

                                    <div class="stock-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <div class="progress-labels">
                                            <span class="progress-label">Tersedia</span>
                                            <span class="progress-label">Kurang</span>
                                        </div>
                                    </div>
                                </div>

                                @if($resolvedId)
                                    <a href="{{ route('produk.edit', $resolvedId) }}?from=notification&type=tambah_stok&min_stok={{ $detail['kurang'] }}"
                                       class="add-stock-btn">
                                        <span class="btn-icon">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <div class="btn-content">
                                            <span class="btn-text">Tambah Stok Sekarang</span>
                                            <span class="btn-subtitle">Tambahkan {{ $detail['kurang'] }} unit untuk memenuhi pesanan</span>
                                        </div>
                                        <span class="btn-arrow">
                                            <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </a>
                                @else
                                    <div class="product-not-found">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Produk tidak ditemukan dalam sistem</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <style>
                    .stock-shortage-section {
                        margin-top: 1.5rem;
                        padding-top: 1.5rem;
                        border-top: 2px solid rgba(219, 229, 241, 0.6);
                    }

                    .stock-shortage-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 1.25rem;
                        padding-bottom: 1rem;
                    }

                    .header-content {
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        font-size: 1.05rem;
                        font-weight: 800;
                        color: #1e293b;
                        letter-spacing: -0.01em;
                    }

                    .header-content i {
                        font-size: 1.35rem;
                        color: #f59e0b;
                        animation: box-float 3s ease-in-out infinite;
                    }

                    @keyframes box-float {
                        0%, 100% { transform: translateY(0px); }
                        50% { transform: translateY(-6px); }
                    }

                    .item-count {
                        background: linear-gradient(135deg, rgba(249, 158, 11, 0.15), rgba(245, 158, 11, 0.08));
                        color: #b45309;
                        padding: 0.4rem 0.9rem;
                        border-radius: 0.75rem;
                        font-size: 0.85rem;
                        font-weight: 700;
                        border: 1px solid rgba(249, 158, 11, 0.25);
                    }

                    .shortage-items-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                        gap: 1rem;
                        animation: fadeInUp 0.6s ease-out 0.2s both;
                    }

                    .shortage-item-card {
                        background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.95) 100%);
                        border: 1.5px solid rgba(249, 158, 11, 0.2);
                        border-radius: 1.25rem;
                        padding: 1.25rem;
                        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        position: relative;
                        overflow: hidden;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    }

                    .shortage-item-card::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        height: 3px;
                        background: linear-gradient(90deg, #f59e0b, #d97706, #b45309);
                    }

                    .shortage-item-card::after {
                        content: '';
                        position: absolute;
                        top: -50%;
                        right: -50%;
                        width: 300px;
                        height: 300px;
                        background: radial-gradient(circle, rgba(249, 158, 11, 0.08) 0%, transparent 70%);
                        border-radius: 50%;
                        pointer-events: none;
                    }

                    .shortage-item-card:hover {
                        transform: translateY(-8px);
                        border-color: rgba(249, 158, 11, 0.4);
                        box-shadow: 0 16px 32px rgba(249, 158, 11, 0.15);
                    }

                    .card-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        gap: 1rem;
                        margin-bottom: 1rem;
                        position: relative;
                        z-index: 1;
                    }

                    .product-name {
                        margin: 0;
                        font-size: 1.05rem;
                        font-weight: 800;
                        color: #0f172a;
                        letter-spacing: -0.01em;
                        flex: 1;
                        word-break: break-word;
                        line-height: 1.3;
                    }

                    .shortage-badge {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.4rem;
                        background: linear-gradient(135deg, #f59e0b, #d97706);
                        color: white;
                        padding: 0.45rem 0.85rem;
                        border-radius: 0.75rem;
                        font-weight: 700;
                        font-size: 0.8rem;
                        white-space: nowrap;
                        box-shadow: 0 4px 12px rgba(249, 158, 11, 0.3);
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                    }

                    .shortage-badge i {
                        font-size: 0.75rem;
                    }

                    .stock-info {
                        margin-bottom: 1.25rem;
                        position: relative;
                        z-index: 1;
                    }

                    .info-row {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 0.75rem;
                        margin-bottom: 1rem;
                    }

                    .info-item {
                        display: flex;
                        flex-direction: column;
                        gap: 0.35rem;
                    }

                    .info-label {
                        display: flex;
                        align-items: center;
                        gap: 0.4rem;
                        font-size: 0.75rem;
                        font-weight: 700;
                        color: #64748b;
                        text-transform: uppercase;
                        letter-spacing: 0.3px;
                    }

                    .info-label i {
                        font-size: 0.85rem;
                        color: #0066cc;
                    }

                    .info-value {
                        font-size: 0.95rem;
                        font-weight: 800;
                        color: #1e293b;
                    }

                    .info-value.available {
                        color: #10b981;
                    }

                    .info-value.needed {
                        color: #0066cc;
                    }

                    .stock-progress {
                        background: rgba(248, 250, 255, 0.8);
                        border-radius: 0.875rem;
                        padding: 0.75rem;
                        border: 1px solid rgba(219, 229, 241, 0.5);
                    }

                    .progress-bar {
                        width: 100%;
                        height: 8px;
                        background: rgba(219, 229, 241, 0.5);
                        border-radius: 0.5rem;
                        overflow: hidden;
                        margin-bottom: 0.5rem;
                    }

                    .progress-fill {
                        height: 100%;
                        background: linear-gradient(90deg, #10b981, #059669);
                        border-radius: 0.5rem;
                        transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
                    }

                    .progress-labels {
                        display: flex;
                        justify-content: space-between;
                        font-size: 0.7rem;
                        font-weight: 700;
                        color: #64748b;
                        text-transform: uppercase;
                        letter-spacing: 0.2px;
                    }

                    .add-stock-btn {
                        width: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 0.75rem;
                        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                        color: white;
                        padding: 0.85rem 1.25rem;
                        border-radius: 0.95rem;
                        font-weight: 800;
                        font-size: 0.95rem;
                        text-decoration: none;
                        border: none;
                        cursor: pointer;
                        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                        position: relative;
                        z-index: 2;
                        overflow: hidden;
                        box-shadow: 0 6px 16px rgba(249, 158, 11, 0.3);
                        letter-spacing: 0.3px;
                    }

                    .add-stock-btn::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: -100%;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
                        transition: left 0.6s ease;
                        z-index: 0;
                    }

                    .add-stock-btn:hover::before {
                        left: 100%;
                    }

                    .add-stock-btn:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 12px 28px rgba(249, 158, 11, 0.4);
                        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                    }

                    .add-stock-btn:active {
                        transform: translateY(-1px);
                    }

                    .btn-icon,
                    .btn-text,
                    .btn-arrow {
                        position: relative;
                        z-index: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .btn-content {
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 0.25rem;
                    }

                    .btn-text {
                        font-size: 0.95rem;
                        font-weight: 800;
                        letter-spacing: 0.3px;
                    }

                    .btn-subtitle {
                        font-size: 0.75rem;
                        font-weight: 600;
                        opacity: 0.9;
                        letter-spacing: 0.2px;
                    }

                    .btn-icon {
                        font-size: 1.1rem;
                        animation: icon-bounce 2s ease-in-out infinite;
                    }

                    @keyframes icon-bounce {
                        0%, 100% { transform: translateX(0); }
                        50% { transform: translateX(3px); }
                    }

                    .btn-arrow {
                        font-size: 0.85rem;
                        opacity: 0;
                        transform: translateX(-8px);
                        transition: all 0.3s ease;
                    }

                    .add-stock-btn:hover .btn-arrow {
                        opacity: 1;
                        transform: translateX(0);
                    }

                    .add-stock-btn:hover .btn-icon {
                        transform: scale(1.15);
                    }

                    .product-not-found {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        padding: 1rem;
                        background: rgba(239, 68, 68, 0.08);
                        border: 1.5px solid rgba(239, 68, 68, 0.2);
                        border-radius: 0.875rem;
                        color: #991b1b;
                        font-weight: 700;
                        font-size: 0.85rem;
                        position: relative;
                        z-index: 1;
                    }

                    .product-not-found i {
                        font-size: 1rem;
                    }

                    @media (max-width: 768px) {
                        .shortage-items-grid {
                            grid-template-columns: 1fr;
                            gap: 0.875rem;
                        }

                        .card-header {
                            flex-direction: column;
                            align-items: flex-start;
                            gap: 0.75rem;
                        }

                        .shortage-item-card {
                            padding: 1rem;
                            border-radius: 1rem;
                        }

                        .product-name {
                            font-size: 0.95rem;
                        }

                        .shortage-badge {
                            align-self: flex-start;
                        }

                        .add-stock-btn {
                            font-size: 0.9rem;
                            padding: 0.75rem 1rem;
                            gap: 0.5rem;
                        }

                        .btn-content {
                            gap: 0.15rem;
                        }

                        .btn-text {
                            font-size: 0.85rem;
                        }

                        .btn-subtitle {
                            font-size: 0.7rem;
                        }

                        .btn-icon {
                            font-size: 0.95rem;
                        }

                        .btn-arrow {
                            font-size: 0.75rem;
                        }

                        .header-content {
                            font-size: 0.95rem;
                        }

                        .item-count {
                            font-size: 0.8rem;
                            padding: 0.35rem 0.75rem;
                        }
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
            @endif

            <div class="notif-actions">
                @if(is_null($notif->dibaca_at))
                    <form action="{{ route('notifikasi.markAsRead', $notif) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="action-btn action-btn-read">
                            <i class="fas fa-check"></i>Tandai Sudah Dibaca
                        </button>
                    </form>
                @endif
                <form action="{{ route('notifikasi.destroy', $notif) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus notifikasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn action-btn-delete">
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
            <div class="empty-state-title">Tidak Ada Notifikasi</div>
            <div class="empty-state-text">Semua notifikasi sudah ditandai atau belum ada notifikasi baru saat ini</div>
        </div>
    @endforelse
</div>
@endsection
