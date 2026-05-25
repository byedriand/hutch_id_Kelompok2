<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>hutch.id OrderFlow</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar: #0f3d7f;
            --sidebar-dark: #0b315f;
            --primary: #2d7dd2;
            --primary-light: #d7e8fd;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f59e0b;
            --muted: #6c7a93;
            --bg: #eef4fb;
            --surface: #ffffff;
            --surface-soft: #f7fbff;
            --border: #dbe5f1;
            --shadow: 0 18px 50px rgba(15, 64, 124, 0.08);
        }

        html,
        body {
            min-height: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .container-fluid {
            min-height: 100%;
        }

        .row {
            min-height: 100%;
            align-items: stretch;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
            color: #17233d;
        }

        #sidebar {
            background: linear-gradient(180deg, #1d457c 0%, #0c2f5d 100%);
            color: #f7fbff;
            border-right: 1px solid rgba(255,255,255,0.12);
            min-height: 100vh;
            height: auto;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.12);
            position: relative;
            overflow: hidden;
        }

        #sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(255,255,255,0.15), transparent 30%), radial-gradient(circle at bottom right, rgba(255,255,255,0.08), transparent 25%);
            pointer-events: none;
        }

        .bg-navy {
            background-color: var(--sidebar) !important;
        }

        #sidebar .sidebar-inner {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            justify-content: space-between;
            position: relative;
            z-index: 1;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 1.4rem 1.25rem;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.08);
            border-radius: 1.5rem;
            margin: 1rem 1rem 0.75rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .sidebar-brand .logo-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(255,255,255,0.22), rgba(255,255,255,0.08));
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .sidebar-brand .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .sidebar-brand .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .sidebar-brand .logo-text .brand-name {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #fff;
        }

        .sidebar-brand .logo-text .brand-subtitle {
            color: rgba(255,255,255,0.75);
            font-size: 0.78rem;
        }

        .sidebar-section {
            letter-spacing: 0.15em;
            text-shadow: 0 0 10px rgba(255,255,255,0.08);
        }

        .sidebar-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-bottom: 1rem;
            overflow: hidden;
        }

        .sidebar-menu {
            overflow-y: auto;
            max-height: calc(100vh - 280px);
            padding-bottom: 1rem;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.08);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.24);
            border-radius: 999px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 1.25rem 1.35rem;
            border-top: 1px solid rgba(255,255,255,0.12);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: rgba(255,255,255,0.06);
            border-radius: 1.5rem;
            margin: 0 1rem 1rem;
            box-shadow: inset 0 0 0 rgba(255,255,255,0.08);
        }

        .sidebar-footer .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar-footer .sidebar-user .user-info {
            min-width: 0;
        }

        .sidebar-footer .sidebar-user .user-info .fw-bold {
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer .sidebar-user .user-info .text-muted {
            color: rgba(255, 255, 255, 0.75) !important;
            font-size: 0.8rem;
            line-height: 1.35;
        }

        .sidebar-footer .logout-btn {
            border-radius: 999px;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.16);
            border: none;
            color: #ffffff;
            font-weight: 700;
            transition: background-color 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .sidebar-footer .logout-btn:hover,
        .sidebar-footer .logout-btn:focus {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
            color: #ffffff;
        }

        #sidebar .border-bottom,
        #sidebar .border-top {
            border-color: rgba(255,255,255,0.12) !important;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.92);
            padding: 1rem 1.25rem;
            border-radius: 18px;
            margin: 0 0.9rem 1rem;
            min-height: 56px;
            display: flex;
            align-items: center;
            gap: 0.95rem;
            transition: transform 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease;
            font-size: 0.95rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(6px);
            animation: fadeInLeft 0.5s ease both;
        }

        #sidebar .nav-link i {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(255,255,255,0.16);
            color: #ffffff;
            font-size: 0.95rem;
        }

        #sidebar .nav-link:hover {
            background: rgba(255,255,255,0.18);
            color: #fff;
            transform: translateX(3px);
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.12);
        }

        #sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border-color: rgba(255,255,255,0.20);
            box-shadow: 0 20px 36px rgba(0, 0, 0, 0.14);
            position: relative;
        }

        #sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 56%;
            background: #70b7ff;
            border-radius: 999px;
        }

        #sidebar .nav-link.active .badge {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        #sidebar .badge {
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        #sidebar .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.26), rgba(255,255,255,0.14));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: 0.95rem;
            border: 1px solid rgba(255,255,255,0.18);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        #sidebar h5 {
            font-size: 1rem;
            letter-spacing: 0.02em;
            margin-bottom: 0.2rem;
        }

        #sidebar small {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
        }

        .main-content {
            padding: 0.5rem 0.85rem 1rem;
            min-height: auto;
            background-color: transparent;
        }

        .container-fluid {
            min-height: 100vh;
        }

        .row.g-0 {
            min-height: 100vh;
            align-items: stretch;
        }

        #sidebar {
            min-height: 100vh;
            height: auto;
        }

        #sidebar .sidebar-inner {
            min-height: 100vh;
        }

        .sidebar-content {
            min-height: 0;
        }

        .main-content > .d-flex:first-child,
        .page-header {
            margin-bottom: 0.75rem;
        }

        .page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.9rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(15, 64, 124, 0.08);
        }

        .page-header h1,
        .page-header h2,
        .page-header h3 {
            margin-bottom: 0.25rem;
            font-weight: 700;
        }

        .page-header p,
        .page-header small {
            margin-bottom: 0;
            color: #5f6d85;
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }

        .container-fluid > .row {
            min-height: auto;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            background: var(--surface);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 18px 38px rgba(15, 64, 124, 0.08);
            border-color: var(--primary-light);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border);
            padding: 0.95rem 1rem 0.75rem;
        }

        .card-header h5 {
            margin-bottom: 0;
            font-size: 0.98rem;
            font-weight: 700;
            color: #17233d;
        }

        .card-body {
            padding: 0.95rem 1rem 1.1rem;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.5rem 0.95rem;
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #1f6bb8;
            border-color: #1f6bb8;
        }

        .btn-secondary {
            background-color: #e6eefb;
            color: #243a5c;
            border-color: transparent;
        }

        .btn-secondary:hover {
            background-color: #d0deef;
        }

        .btn-outline-light {
            color: #fff;
            border-color: rgba(255,255,255,0.25);
        }

        .btn-outline-light:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.14);
        }

        .alert {
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(24, 43, 89, 0.08);
        }

        .table-wrap {
            overflow-x: auto;
            padding-bottom: 0.25rem;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
        }

        .table th,
        .table td {
            border-top: none;
            vertical-align: middle;
            padding: 0.7rem 0.85rem;
        }
            padding: 1rem 0.75rem;
        }

        .table thead th {
            background: #f8fbff;
            border-bottom: 1px solid #e8edf7;
            color: #4f5d7a;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 0.78rem;
        }

        .table tbody tr:hover {
            background-color: #f4f8fe;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            border: 1px solid #dbe5f1;
            background: #fbfdff;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.15rem rgba(45,125,210,0.16);
        }

        .form-control,
        .form-select {
            min-height: 42px;
        }

        .form-control-sm,
        .form-select-sm {
            min-height: 38px;
        }

        .form-label {
            font-weight: 600;
            color: #344767;
        }

        .btn-group .btn {
            border-radius: 12px;
        }

        .btn-outline-secondary {
            color: #344767;
            border-color: #dbe5f1;
        }

        .btn-outline-secondary:hover {
            background-color: #f8faff;
            border-color: #cfdbea;
        }

        .sidebar-section {
            letter-spacing: 0.1em;
            font-size: 0.68rem;
            margin-top: 0.6rem;
            margin-bottom: 0.4rem;
            font-weight: 700;
        }

        .nav-link {
            color: #334155 !important;
        }

        .nav-link.active {
            color: #0b3d7f !important;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.88) !important;
        }

        #sidebar .nav-link.active {
            color: #fff !important;
        }

        .b-done {
            background-color: #059669;
            color: #fff;
        }

        .b-cancel {
            background-color: #dc2626;
            color: #fff;
        }

        .b-ok {
            background-color: #0d6efd;
            color: #fff;
        }

        .b-warn {
            background-color: #f59e0b;
            color: #fff;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #fff 0%, #f8fbff 100%);
            padding: 1.8rem;
            border-radius: 18px;
            border: 1px solid var(--border);
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(15, 64, 124, 0.06);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(45, 125, 210, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(15, 64, 124, 0.12);
            border-color: var(--primary);
        }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-card .stat-desc {
            color: #6c7a93;
            font-weight: 600;
            position: relative;
            z-index: 1;
            font-size: 0.95rem;
        }

        .stat-card small {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
            position: relative;
            z-index: 1;
        }

        .detail-grid,
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .detail-grid .card,
        .form-grid .card {
            border-radius: 18px;
        }

        /* Dashboard Cards Animation */
        .stat-grid .stat-card {
            animation: fadeInUp 0.5s ease-out;
        }

        .stat-grid .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-grid .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-grid .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-grid .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .row > .col-lg-6 .card {
            animation: fadeInUp 0.6s ease-out;
        }

        .row > .col-lg-6:nth-child(1) .card { animation-delay: 0.5s; }
        .row > .col-lg-6:nth-child(2) .card { animation-delay: 0.6s; }

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

        /* Card items animation */
        .p-3.border-bottom {
            animation: slideIn 0.3s ease-out;
            transition: all 0.2s ease;
        }

        .p-3.border-bottom:hover {
            background-color: #f9fbfd;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-18px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Badge styling */
        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .b-wait {
            background-color: #fef3c7;
            color: #92400e;
        }

        .b-prod {
            background-color: #dbeafe;
            color: #0c4a6e;
        }

        /* Mono text styling */
        .mono {
            font-family: 'Fira Code', monospace;
            font-size: 0.9rem;
        }

        @media (max-width: 991px) {
            #sidebar {
                min-height: auto;
                position: relative;
            }

            #sidebar .nav-link {
                margin: 0 1rem 0.3rem;
            }

            .main-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .container-fluid > .row {
                display: block;
            }

            #sidebar {
                width: 100%;
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }

            .main-content {
                padding: 1.25rem;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .detail-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <div class="container-fluid px-0">
        <div class="row g-0">
            <div class="col-md-3 col-lg-2 px-0 bg-navy text-white" id="sidebar">
                <div class="sidebar-inner">
                    <div class="sidebar-brand">
                        <div class="logo-icon">
                            <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch Prestige Logo">
                        </div>
                        <div class="logo-text">
                            <span class="brand-name">HUTCH PRESTIGE</span>
                            <span class="brand-subtitle">Modul Manajemen</span>
                        </div>
                    </div>

                    <div class="sidebar-content">
                        <div class="sidebar-menu">
                            <div class="px-3 mt-2 mb-1 text-uppercase text-white-50 fw-semibold small sidebar-section">Menu</div>
                            <nav class="nav flex-column py-1">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>Dashboard
                                </a>
                                <a class="nav-link {{ request()->routeIs('notifikasi.index') ? 'active' : '' }}" href="{{ route('notifikasi.index') }}">
                                    <i class="fas fa-bell"></i>Notifikasi
                                    <span id="notif-badge" class="badge bg-danger ms-auto" style="font-size: 0.7rem; padding: 0.25rem 0.6rem; display: none;">0</span>
                                </a>
                        <a class="nav-link {{ request()->routeIs('pesanan.index') ? 'active' : '' }}" href="{{ route('pesanan.index') }}">
                            <i class="fas fa-list"></i>Daftar Pesanan
                            @if($jumlahMenunggu > 0)
                                <span class="badge bg-danger ms-auto" style="font-size: 0.7rem; padding: 0.25rem 0.6rem;">{{ $jumlahMenunggu }}</span>
                            @endif
                        </a>
                        @if(auth()->user()->role !== 'operator_gudang')
                            <a class="nav-link {{ request()->routeIs('pesanan.create') ? 'active' : '' }}" href="{{ route('pesanan.create') }}">
                                <i class="fas fa-plus"></i>Buat PO
                            </a>
                        @endif
                        <a class="nav-link {{ request()->routeIs('pelanggan.index') ? 'active' : '' }}" href="{{ route('pelanggan.index') }}">
                            <i class="fas fa-users"></i>Pelanggan
                        </a>
                        @if(auth()->user()->role === 'operator_gudang')
                            <a class="nav-link {{ request()->routeIs('produk.index') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                                <i class="fas fa-boxes"></i>Manajemen Stok
                            </a>
                        @endif
                    </nav>

                            <div class="px-3 mt-2 mb-1 text-uppercase text-white-50 fw-semibold small sidebar-section">Lain</div>
                            <nav class="nav flex-column py-1">
                                <a class="nav-link {{ request()->routeIs('arsip.index') ? 'active' : '' }}" href="{{ route('arsip.index') }}">
                                    <i class="fas fa-archive"></i>Arsip PDF
                                </a>
                            </nav>
                        </div>

                        <div class="sidebar-footer">
                            <div class="sidebar-user">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="user-info">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        @switch(auth()->user()->role)
                                            @case('staf_penjualan')
                                                Staf Penjualan
                                                @break
                                            @case('pemilik_umkm')
                                                Pemilik UMKM
                                                @break
                                            @case('operator_gudang')
                                                Operator Gudang
                                                @break
                                            @case('administrator')
                                                Admin
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn logout-btn w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-3">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                @yield('content')
            </div>
        </div>
    </div>
    @endauth

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050;" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050;" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
    <script>
        // Update notification count on page load and every 30 seconds
        function updateNotificationCount() {
            fetch('{{ route("api.notifikasi.countUnread") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notif-badge');
                    if (data && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => console.log('Error fetching notifications:', error));
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', updateNotificationCount);

        // Update every 30 seconds
        setInterval(updateNotificationCount, 30000);
    </script>
    @endauth
    @stack('scripts')
</body>
</html>
