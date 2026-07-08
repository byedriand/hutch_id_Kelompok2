@extends('layouts.app')

@section('content')
<style>
    /* ========== MODERN ANIMATIONS ========== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.6;
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-8px);
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

    @keyframes scaleIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ========== HEADER SECTION ========== */
    .stok-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
        border-radius: 2rem;
        padding: 3.5rem;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stok-header::before {
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

    .stok-header::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(34, 197, 94, 0.2), transparent 70%);
        border-radius: 50%;
    }

    .stok-header > div {
        position: relative;
        z-index: 1;
    }

    .stok-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }

    .stok-header h1 {
        font-size: 2.5rem;
        font-weight: 900;
        color: #ffffff;
        margin-bottom: 0.75rem;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stok-header h1 i {
        font-size: 2.8rem;
        opacity: 0.9;
        animation: float 4s ease-in-out infinite;
    }

    .stok-header-desc {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
        font-weight: 500;
    }

    .btn-add-stok {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white;
        border: none;
        border-radius: 1.2rem;
        padding: 1rem 2rem;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        white-space: nowrap;
    }

    .btn-add-stok:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
    }

    .btn-add-stok:active {
        transform: translateY(-2px);
    }

    /* ========== SUMMARY CARDS ========== */
    .stok-summary-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .stok-stat-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 1.5rem;
        padding: 1.75rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(15, 23, 42, 0.1);
        animation: slideInRight 0.6s ease-out;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stok-stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stok-stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stok-stat-card:nth-child(3) { animation-delay: 0.3s; }

    .stok-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 50px rgba(15, 23, 42, 0.15);
    }

    .stok-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: -50%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1), transparent 70%);
        border-radius: 50%;
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
        color: #2563eb;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        color: #1e293b;
        font-family: 'Courier New', monospace;
        animation: scaleIn 0.6s ease-out;
    }

    .stat-subtext {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-top: 0.5rem;
    }

    @keyframes cardHover {
        0% { transform: translateY(0); }
        100% { transform: translateY(-8px); }
    }

    @keyframes shimmerLoad {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* ========== PRODUK GRID ========== */
    .produk-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
        margin-bottom: 3rem;
    }

    .produk-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 1.5rem;
        border: 1px solid rgba(59, 130, 246, 0.1);
        padding: 0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08), 0 2px 8px rgba(59, 130, 246, 0.05);
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        position: relative;
        overflow: hidden;
        animation: shimmerLoad 0.6s ease-out forwards;
        display: flex;
        flex-direction: column;
    }

    .produk-card:nth-child(1) { animation-delay: 0.1s; }
    .produk-card:nth-child(2) { animation-delay: 0.15s; }
    .produk-card:nth-child(3) { animation-delay: 0.2s; }
    .produk-card:nth-child(4) { animation-delay: 0.25s; }
    .produk-card:nth-child(5) { animation-delay: 0.3s; }

    .produk-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1), transparent 70%);
        border-radius: 50%;
        transition: all 0.6s ease;
        z-index: 0;
    }

    .produk-card::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -50%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(34, 197, 94, 0.08), transparent 70%);
        border-radius: 50%;
        transition: all 0.6s ease;
        z-index: 0;
    }

    .produk-card:hover {
        transform: translateY(-16px) scale(1.02);
        box-shadow: 0 24px 56px rgba(15, 23, 42, 0.18), 0 12px 24px rgba(59, 130, 246, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.5);
        border-color: rgba(59, 130, 246, 0.4);
    }

    .produk-card:hover::before {
        top: -30%;
        right: -30%;
    }

    .produk-card:hover::after {
        bottom: -30%;
        left: -30%;
    }

    .produk-image-container {
        width: 100%;
        height: 240px;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        overflow: hidden;
        position: relative;
        border-bottom: 1px solid rgba(59, 130, 246, 0.1);
    }

    .produk-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.4s ease;
    }

    .produk-card:hover .produk-image {
        transform: scale(1.1) rotate(1deg);
    }

    .produk-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.1));
        color: #cbd5e1;
        font-size: 3.5rem;
        position: relative;
    }

    .produk-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.15) 100%);
        pointer-events: none;
    }

    .produk-content {
        padding: 2rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
    }

    .produk-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.2rem;
        position: relative;
        z-index: 1;
    }

    .produk-number {
        width: 44px;
        height: 44px;
        border-radius: 1rem;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
        transition: all 0.3s ease;
    }

    .produk-card:hover .produk-number {
        transform: scale(1.1) rotateZ(-5deg);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
    }

    .produk-status-badge {
        border-radius: 0.75rem;
        padding: 0.4rem 0.9rem;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        backdrop-filter: blur(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .produk-card:hover .produk-status-badge {
        transform: translateY(-2px);
    }

    .badge-tersedia {
        background: rgba(16, 185, 129, 0.15);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-rendah {
        background: rgba(251, 146, 60, 0.15);
        color: #c2410c;
        border: 1px solid rgba(251, 146, 60, 0.3);
    }

    .badge-kosong {
        background: rgba(239, 68, 68, 0.15);
        color: #991b1b;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .produk-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1.2rem;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.3s ease;
    }

    .produk-card:hover .produk-name {
        color: #2563eb;
    }

    .produk-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.95rem 0;
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .produk-card:hover .produk-info-row {
        border-bottom-color: rgba(59, 130, 246, 0.2);
    }

    .produk-info-row:last-of-type {
        border-bottom: none;
    }

    .produk-info-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .produk-info-value {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
    }

    .produk-price {
        font-size: 1.35rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        transition: all 0.3s ease;
    }

    .produk-card:hover .produk-price {
        filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.3));
    }

    .produk-stock {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.95rem;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(147, 197, 253, 0.08));
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 0.85rem;
        font-weight: 700;
        color: #2563eb;
        transition: all 0.3s ease;
    }

    .produk-card:hover .produk-stock {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(147, 197, 253, 0.12));
        border-color: rgba(59, 130, 246, 0.25);
        transform: translateY(-2px);
    }

    .produk-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .btn-produk-edit {
        flex: 1;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
        border-radius: 0.9rem;
        padding: 0.8rem 1.2rem;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        position: relative;
        overflow: hidden;
    }

    .btn-produk-edit::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-produk-edit:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(59, 130, 246, 0.4), 0 0 20px rgba(59, 130, 246, 0.2);
        color: white;
    }

    .btn-produk-edit:hover::before {
        left: 100%;
    }

    /* ========== TABLE STYLES (untuk compatibility) ==========
        overflow: hidden;
        animation: fadeInUp 0.7s ease-out 0.2s both;
        background: #ffffff;
    }

    .stok-card-header {
        background: linear-gradient(90deg, #f8fbff, #ffffff);
        border-bottom: 2px solid rgba(219, 234, 254, 0.5);
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stok-card-header i {
        font-size: 1.5rem;
        color: #2563eb;
    }

    .stok-card-header h5 {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.3rem;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .stok-card-body {
        padding: 0;
    }

    /* ========== TABLE STYLES ========== */
    .stok-table {
        margin-bottom: 0;
    }

    .stok-table thead {
        background: linear-gradient(90deg, #f0f9ff, #f8fbff);
    }

    .stok-table thead th {
        border: none;
        color: #475569;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 1.25rem 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: linear-gradient(90deg, #f0f9ff, #f8fbff);
    }

    .stok-table tbody tr {
        border: none;
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        background: #ffffff;
        transition: background-color 0.25s ease;
        position: relative;
    }

    .stok-table tbody tr::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0;
        background: rgba(59, 130, 246, 0.05);
        transition: width 0.25s ease;
    }

    .stok-table tbody tr:nth-child(odd) {
        background: #f8fafc;
    }

    .stok-table tbody tr:hover {
        background: #f0f9ff;
    }

    .stok-table tbody tr:hover::before {
        width: 100%;
    }

    .stok-table tbody tr:hover td {
        color: #1e293b;
    }

    .stok-table tbody td {
        padding: 1.25rem 1.5rem;
        color: #475569;
        border: none;
        vertical-align: middle;
    }

    .stok-table .product-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .stok-table .price {
        font-weight: 700;
        color: #2563eb;
        font-size: 0.95rem;
    }

    /* ========== STATUS BADGES ========== */
    .badge-status {
        border-radius: 0.5rem;
        padding: 0.5rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.25px;
        box-shadow: none;
        transition: all 0.25s ease;
    }

    .badge-status i {
        font-size: 0.8rem;
    }

    .badge-tersedia {
        background: #d1fae5;
        color: #065f46;
        border: none;
    }

    .badge-tersedia:hover {
        background: #a7f3d0;
    }

    .badge-rendah {
        background: #fef3c7;
        color: #78350f;
        border: none;
    }

    .badge-rendah:hover {
        background: #fde68a;
    }

    .badge-kosong {
        background: #fee2e2;
        color: #7f1d1d;
        border: none;
    }

    .badge-kosong:hover {
        background: #fecaca;
    }

    /* ========== ACTION BUTTONS ========== */
    .btn-stok-aksi {
        border-radius: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.25s ease;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        box-shadow: none;
        position: relative;
        overflow: hidden;
    }

    .btn-stok-ubah {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }

    .btn-stok-ubah:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        color: white;
    }

    .btn-stok-ubah i {
        font-size: 0.75rem;
    }

    .btn-stok-aksi::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-stok-aksi:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-stok-ubah {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        position: relative;
        z-index: 1;
    }

    .btn-stok-ubah:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    }

    .btn-stok-ubah i {
        transition: transform 0.3s ease;
    }

    .btn-stok-ubah:hover i {
        transform: scale(1.2);
    }

    /* ========== EMPTY STATE ========== */
    .empty-state {
        text-align: center;
        padding: 5rem 2rem;
        animation: fadeInUp 0.6s ease-out 0.3s both;
    }

    .empty-state i {
        color: #dbeafe;
        opacity: 0.4;
        margin-bottom: 2rem;
        display: block;
        font-size: 5rem;
        animation: float 4s ease-in-out infinite;
    }

    .empty-state h5 {
        color: #1e293b;
        font-weight: 800;
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
    }

    .empty-state p {
        color: #94a3b8;
        font-size: 1rem;
    }

    /* ========== PAGINATION ========== */
    .pagination-wrap {
        display: flex;
        justify-content: center;
        margin-top: 2.5rem;
        padding-bottom: 1rem;
        animation: fadeInUp 0.6s ease-out 0.35s both;
    }

    .pagination .page-link {
        border-radius: 0.75rem;
        margin: 0 0.35rem;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .pagination .page-link:hover {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
        transform: translateY(-2px);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-color: #2563eb;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    /* ========== ALERTS ========== */
    .alert {
        border-radius: 1.5rem !important;
        border: none !important;
        padding: 1.5rem !important;
        animation: slideInRight 0.5s ease-out !important;
        margin-bottom: 1.5rem !important;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05)) !important;
        color: #065f46 !important;
        border-left: 4px solid #10b981 !important;
    }

    .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05)) !important;
        color: #7f1d1d !important;
        border-left: 4px solid #ef4444 !important;
    }

    .alert-success i,
    .alert-danger i {
        font-size: 1.25rem;
        margin-right: 0.75rem;
    }

    .btn-close {
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }

    .btn-close:hover {
        opacity: 1;
    }

    /* ========== RESPONSIVE DESIGN ========== */
    
    /* TABLETS (640px - 1024px) */
    @media (max-width: 1024px) {
        .stok-header {
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            border-radius: 1.5rem;
        }

        .stok-header h1 {
            font-size: 1.8rem;
        }

        .stok-header-actions {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-add-stok {
            padding: 0.7rem 1.5rem;
            font-size: 0.85rem;
        }

        .stok-summary-container {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
        }

        .produk-grid-container {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .produk-image-container {
            height: 200px;
        }

        .produk-content {
            padding: 1.5rem;
        }

        .produk-name {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .btn-produk-edit {
            padding: 0.7rem 1rem;
            font-size: 0.8rem;
        }
    }

    /* MOBILE (< 640px) */
    @media (max-width: 640px) {
        /* Header */
        .stok-header {
            padding: 2rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 1.25rem;
        }

        .stok-header::before {
            width: 200px;
            height: 200px;
            top: -80px;
            right: -80px;
        }

        .stok-header::after {
            width: 150px;
            height: 150px;
            bottom: -40px;
            left: -40px;
        }

        .stok-header h1 {
            font-size: 1.5rem;
            gap: 0.5rem;
        }

        .stok-header-actions {
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }

        .btn-add-stok {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        /* Summary Cards */
        .stok-summary-container {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stok-stat-card {
            padding: 1.5rem;
            border-radius: 1.25rem;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 1.25rem;
        }

        .stat-label {
            font-size: 0.75rem;
        }

        .stat-value {
            font-size: 1.8rem;
        }

        /* Product Grid */
        .produk-grid-container {
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-top: 1.5rem;
            margin-bottom: 2rem;
        }

        .produk-card {
            border-radius: 1.25rem;
        }

        .produk-card:hover {
            transform: translateY(-12px) scale(1.01);
        }

        .produk-image-container {
            height: 180px;
            border-radius: 1.25rem 1.25rem 0 0;
        }

        .produk-image-placeholder {
            font-size: 3rem;
        }

        .produk-content {
            padding: 1.25rem;
        }

        .produk-card-header {
            margin-bottom: 1rem;
        }

        .produk-number {
            width: 38px;
            height: 38px;
            font-size: 0.85rem;
        }

        .produk-name {
            font-size: 1.05rem;
            margin-bottom: 0.9rem;
        }

        .produk-info-row {
            padding: 0.75rem 0;
        }

        .produk-info-label {
            font-size: 0.75rem;
        }

        .produk-info-value {
            font-size: 0.95rem;
        }

        .produk-price {
            font-size: 1.2rem;
        }

        .produk-stock {
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
        }

        .produk-actions {
            gap: 0.5rem;
            margin-top: 1.25rem;
        }

        .btn-produk-edit {
            padding: 0.65rem 0.85rem;
            font-size: 0.75rem;
            border-radius: 0.75rem;
        }

        /* Status Badge */
        .produk-status-badge {
            padding: 0.35rem 0.75rem;
            font-size: 0.65rem;
        }
    }

    /* EXTRA SMALL (< 480px) */
    @media (max-width: 480px) {
        .stok-header {
            padding: 1.5rem 1rem;
            margin-bottom: 1.5rem;
            border-radius: 1rem;
        }

        .stok-header h1 {
            font-size: 1.3rem;
            flex-wrap: wrap;
        }

        .stok-header-desc {
            font-size: 0.9rem;
        }

        .btn-add-stok {
            font-size: 0.8rem;
            padding: 0.65rem 0.9rem;
        }

        .stok-summary-container {
            gap: 0.75rem;
        }

        .stok-stat-card {
            padding: 1.25rem;
            border-radius: 1rem;
        }

        .stat-header {
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 1.1rem;
        }

        .stat-label {
            font-size: 0.7rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-subtext {
            font-size: 0.75rem;
        }

        .produk-grid-container {
            gap: 1rem;
            margin-top: 1.25rem;
        }

        .produk-image-container {
            height: 150px;
        }

        .produk-content {
            padding: 1rem;
        }

        .produk-name {
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        .produk-info-row {
            padding: 0.6rem 0;
        }

        .produk-number {
            width: 34px;
            height: 34px;
            font-size: 0.8rem;
        }

        .btn-produk-edit {
            padding: 0.6rem 0.7rem;
            font-size: 0.7rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- HEADER SECTION -->
    <div class="stok-header">
        <div>
            <div class="stok-header-content">
                <div>
                    <h1><i class="fas fa-warehouse"></i>Manajemen Stok Barang</h1>
                    <p class="stok-header-desc mb-0">Kelola stok produk dan pantau ketersediaan barang dengan mudah</p>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="stok-summary-container">
                <div class="stok-stat-card">
                    <div class="stat-header">
                        <div class="stat-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-label">Total Stok</div>
                    </div>
                    <div class="stat-value" id="totalStokDisplay">{{ $totalStok }}</div>
                    <div class="stat-subtext">Unit tersedia di gudang</div>
                </div>

                <div class="stok-stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1)); color: #10b981;">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div class="stat-label">Produk Terdaftar</div>
                    </div>
                    <div class="stat-value" id="jumlahProdukDisplay">{{ $jumlahProduk }}</div>
                    <div class="stat-subtext">Jenis produk aktif</div>
                </div>

                <div class="stok-stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1)); color: #ef4444;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-label">Stok Rendah</div>
                    </div>
                    <div class="stat-value" id="stokRendahDisplay">{{ $stokRendah }}</div>
                    <div class="stat-subtext">Produk memerlukan pengisian</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERTS -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Terjadi Kesalahan!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- MAIN CARD -->
    <div style="margin-top: 3rem;">
        @if ($produk->count() > 0)
            <div class="produk-grid-container">
                @forelse ($produk as $index => $item)
                    <div class="produk-card">
                        <!-- Image Section -->
                        <div class="produk-image-container">
                            @if ($item->foto_url)
                                <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}" class="produk-image">
                            @else
                                <div class="produk-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            <div class="produk-image-overlay"></div>
                        </div>

                        <!-- Content Section -->
                        <div class="produk-content">
                            <div class="produk-card-header">
                                <div class="produk-number">
                                    {{ $produk->firstItem() + $index }}
                                </div>
                                @if ($item->stok == 0)
                                    <span class="produk-status-badge badge-kosong">
                                        <i class="fas fa-times-circle"></i>Kosong
                                    </span>
                                @elseif ($item->stok <= 10)
                                    <span class="produk-status-badge badge-rendah">
                                        <i class="fas fa-exclamation-circle"></i>Rendah
                                    </span>
                                @else
                                    <span class="produk-status-badge badge-tersedia">
                                        <i class="fas fa-check-circle"></i>Tersedia
                                    </span>
                                @endif
                            </div>

                            <div class="produk-name">{{ $item->nama }}</div>

                            <div class="produk-info-row">
                                <span class="produk-info-label">Harga Jual</span>
                                <span class="produk-price">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                            </div>

                            <div class="produk-info-row">
                                <span class="produk-info-label">Stok Saat Ini</span>
                                <span class="produk-stock">
                                    <i class="fas fa-boxes"></i>{{ $item->stok }} unit
                                </span>
                            </div>

                            <div class="produk-actions">
                                <a href="{{ route('produk.edit', $item->id) }}" class="btn-produk-edit">
                                    <i class="fas fa-edit me-2"></i>Ubah Stok
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                        <h5 style="color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">Tidak Ada Produk</h5>
                        <p style="color: #94a3b8; margin-bottom: 0;">Mulai dengan menambahkan produk baru ke dalam sistem</p>
                    </div>
                @endforelse
            </div>

            @if ($produk->hasPages())
                <div style="display: flex; justify-content: center; margin-top: 3rem;">
                    {{ $produk->links() }}
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 197, 253, 0.05)); border-radius: 1.5rem; border: 1px dashed rgba(59, 130, 246, 0.2);">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <h5 style="color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">Tidak Ada Data Produk</h5>
                <p style="color: #94a3b8; margin-bottom: 1.5rem;">Tidak ada produk yang dapat dikelola saat ini</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Produk Baru -->
<div class="modal fade" id="addStokModal" tabindex="-1" aria-labelledby="addStokModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 2rem; box-shadow: 0 25px 80px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); border: none; padding: 2.5rem; position: relative; overflow: hidden;">
                <div style="position: relative; z-index: 1;">
                    <h5 class="modal-title" id="addStokModalLabel" style="color: white; font-weight: 900; font-size: 1.5rem; letter-spacing: -0.3px;">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Produk Baru
                    </h5>
                    <p style="color: rgba(255, 255, 255, 0.7); margin: 0.5rem 0 0 3.5rem; font-size: 0.9rem;">Daftarkan produk baru ke sistem stok</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="position: relative; z-index: 10;"></button>
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent 70%); border-radius: 50%;"></div>
            </div>
            <div class="modal-body" style="padding: 2.5rem;">
                <form id="addStokForm" method="POST" action="{{ route('produk.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="nama" class="form-label" style="font-weight: 700; color: #1e293b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.3px;">Nama Produk *</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama produk" required 
                            style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.9rem 1.2rem; font-weight: 500; transition: all 0.3s ease; font-size: 0.95rem;">
                        <small style="color: #94a3b8; display: block; margin-top: 0.5rem;"><i class="fas fa-info-circle me-1"></i>Nama produk harus unik dan deskriptif</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="harga_jual" class="form-label" style="font-weight: 700; color: #1e293b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.3px;">Harga Jual (Rp) *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 1.2rem; top: 50%; transform: translateY(-50%); color: #2563eb; font-weight: 700;">Rp</span>
                                <input type="number" class="form-control" id="harga_jual" name="harga_jual" placeholder="0" required 
                                    style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.9rem 1.2rem 0.9rem 3.5rem; font-weight: 500; transition: all 0.3s ease; font-size: 0.95rem;">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="stok" class="form-label" style="font-weight: 700; color: #1e293b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.3px;">Stok Awal *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; right: 1.2rem; top: 50%; transform: translateY(-50%); color: #10b981; font-weight: 700;">unit</span>
                                <input type="number" class="form-control" id="stok" name="stok" placeholder="0" value="0" required min="0"
                                    style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.9rem 4rem 0.9rem 1.2rem; font-weight: 500; transition: all 0.3s ease; font-size: 0.95rem;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label" style="font-weight: 700; color: #1e293b; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.3px;">Keterangan Produk</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Deskripsi produk, spesifikasi, atau catatan tambahan..."
                            style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.9rem 1.2rem; font-weight: 500; transition: all 0.3s ease; resize: vertical; font-size: 0.95rem;"></textarea>
                        <small style="color: #94a3b8; display: block; margin-top: 0.5rem;"><i class="fas fa-lightbulb me-1"></i>Informasi ini membantu identifikasi produk di kemudian hari</small>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="border-top: 2px solid #f0f9ff; padding: 1.75rem 2.5rem; background: linear-gradient(90deg, #f8fbff, #fafbff);">
                <button type="button" class="btn" data-bs-dismiss="modal" 
                    style="background: #e8eef7; color: #2d7dd2; border: none; border-radius: 1rem; padding: 0.8rem 1.75rem; font-weight: 700; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.3px; font-size: 0.9rem;">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="submit" form="addStokForm" class="btn" 
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 1rem; padding: 0.8rem 1.75rem; font-weight: 700; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); text-transform: uppercase; letter-spacing: 0.3px; font-size: 0.9rem; box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);">
                    <i class="fas fa-check-circle me-2"></i>Simpan Produk
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAddStokModal() {
        const modal = new bootstrap.Modal(document.getElementById('addStokModal'), {
            keyboard: false
        });
        modal.show();
    }

    // Add real-time formatting for price input
    document.getElementById('harga_jual').addEventListener('input', function(e) {
        let value = this.value;
        if (value && !isNaN(value)) {
            this.value = parseInt(value);
        }
    });

    // Enhance input field focus states
    document.querySelectorAll('input.form-control, textarea.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#2563eb';
            this.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.1)';
        });
        
        input.addEventListener('blur', function() {
            this.style.borderColor = '#e2e8f0';
            this.style.boxShadow = 'none';
        });
    });

    // Add subtle animations to stat cards
    document.querySelectorAll('.stok-stat-card').forEach((card, index) => {
        card.style.animationDelay = (0.1 * (index + 1)) + 's';
    });
</script>

@endsection