<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Hutch.id</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2d7dd2;
            --primary-dark: #1a4a8a;
            --secondary: #00d4ff;
            --accent: #ff6b6b;
            --success: #51cf66;
            --dark: #0f1419;
            --darker: #0a0e14;
        }

        html, body {
            width: 100%;
            overflow-x: hidden;
            background: var(--dark);
            color: #fff;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ===== ANIMATED BACKGROUND ===== */
        .animated-bg {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #0a0e14 0%, #0f1f3c 25%, #1a3a5c 50%, #0d1f3a 75%, #050a12 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
            z-index: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: rgba(15, 20, 30, 0.9) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(45, 125, 210, 0.15);
            padding: 1rem 2.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand {
            font-size: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-brand img {
            height: 45px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(45, 125, 210, 0.2));
        }

        .navbar-brand span {
            font-size: 1.4rem;
            font-weight: 800;
            background: linear-gradient(135deg, #00d4ff, #2d7dd2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        /* ===== PAGE WRAP ===== */
        .feature-page {
            padding: 50px 20px 80px;
            max-width: 1300px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1.6rem;
            text-decoration: none;
            transition: color 0.25s ease;
        }

        .back-link:hover {
            color: #00d4ff;
        }

        /* ===== HERO ===== */
        .feature-hero {
            padding: 10px 0 36px;
            border-bottom: 1px solid rgba(45, 125, 210, 0.15);
            margin-bottom: 40px;
        }

        .feature-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            color: #00d4ff;
            padding: 0.5rem 1.1rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            margin-bottom: 1.4rem;
        }

        .feature-hero-badge i {
            font-size: 0.9rem;
        }

        .feature-hero h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.5px;
            margin-bottom: 1.1rem;
            background: linear-gradient(135deg, #fff 0%, #00d4ff 55%, #2d7dd2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-hero p {
            font-size: 1.02rem;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.75;
            max-width: 780px;
        }

        /* ===== MAIN GRID ===== */
        .feature-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 2.2rem;
            align-items: start;
        }

        @media (max-width: 991px) {
            .feature-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ===== IMAGE ===== */
        .feature-image-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            border: 1.5px solid rgba(45, 125, 210, 0.25);
            background: var(--darker);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            margin-bottom: 1.8rem;
        }

        .feature-image-wrapper .feature-image-inner {
            position: relative;
            min-height: 300px;
            max-height: 440px;
            display: flex;
            overflow: hidden;
        }

        .feature-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .feature-image-wrapper .img-fallback {
            width: 100%;
            min-height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.4);
            gap: 0.8rem;
        }

        .feature-image-wrapper::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15, 20, 25, 0) 60%, rgba(10, 14, 20, 0.55) 100%);
            pointer-events: none;
        }

        /* ===== CONTENT CARDS (matches .feature-card glass style) ===== */
        .feature-card-block {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.08), rgba(0, 212, 255, 0.06));
            border: 1.5px solid rgba(45, 125, 210, 0.25);
            border-radius: 16px;
            padding: 1.8rem 2rem;
            backdrop-filter: blur(10px);
            margin-bottom: 1.6rem;
        }

        .feature-card-block h3 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .feature-card-block p {
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.75;
            font-size: 0.96rem;
        }

        .feature-benefit-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }

        .feature-benefit {
            display: grid;
            gap: 0.9rem;
        }

        .feature-benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            padding: 1rem 1.2rem;
            background: rgba(45, 125, 210, 0.07);
            border: 1px solid rgba(45, 125, 210, 0.2);
            border-radius: 12px;
            transition: all 0.25s ease;
        }

        .feature-benefit-item:hover {
            background: rgba(45, 125, 210, 0.14);
            border-color: rgba(45, 125, 210, 0.4);
            transform: translateX(4px);
        }

        .feature-benefit-item .icon-circle {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            border-radius: 10px;
            background: linear-gradient(135deg, #2d7dd2, #00d4ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            color: #fff;
            box-shadow: 0 6px 16px rgba(45, 125, 210, 0.3);
        }

        .feature-benefit-item span {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
            padding-top: 0.15rem;
        }

        /* ===== SIDEBAR ===== */
        .feature-sidebar {
            position: sticky;
            top: 100px;
        }

        .feature-sidebar-card {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.2), rgba(0, 212, 255, 0.12));
            border: 1px solid rgba(45, 125, 210, 0.3);
            border-radius: 18px;
            padding: 1.8rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(45, 125, 210, 0.18);
        }

        .feature-sidebar-card h5 {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            background: linear-gradient(135deg, #fff, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-sidebar-card p {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.92rem;
            line-height: 1.65;
            margin-bottom: 1.4rem;
        }

        .btn-feature-cta {
            display: block;
            width: 100%;
            text-align: center;
            background: linear-gradient(135deg, #2d7dd2, #00d4ff);
            border: none;
            color: #fff;
            padding: 0.9rem 1.2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            box-shadow: 0 12px 30px rgba(45, 125, 210, 0.3);
            transition: all 0.3s ease;
        }

        .btn-feature-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(45, 125, 210, 0.4);
            color: #fff;
        }

        .feature-sidebar-other {
            margin-top: 0;
        }

        .feature-sidebar-other-label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: rgba(255, 255, 255, 0.45);
            margin-bottom: 0.8rem;
            padding-left: 0.2rem;
        }

        .other-feature-link {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 0.3rem;
        }

        .other-feature-link i {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(45, 125, 210, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: #00d4ff;
            flex-shrink: 0;
        }

        .other-feature-link:hover {
            background: rgba(45, 125, 210, 0.12);
            color: #fff;
        }

        /* ===== FOOTER ===== */
        .footer {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            text-align: center;
            border-top: 1px solid rgba(45, 125, 210, 0.15);
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        @media (max-width: 767px) {
            .feature-page {
                padding: 30px 16px 60px;
            }

            .feature-card-block {
                padding: 1.4rem 1.4rem;
            }

            .feature-image-wrapper .feature-image-inner {
                min-height: 220px;
                max-height: 280px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg"></div>

    <div class="content-wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="{{ route('landing') }}">
                    <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id">
                    <span>Hutch.id</span>
                </a>
            </div>
        </nav>

        <div class="feature-page">
            <a href="{{ route('landing') }}#features" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>

            <div class="feature-hero">
                <span class="feature-hero-badge"><i class="fas fa-bolt"></i> Fitur Unggulan Hutch.id</span>
                <h1>{{ $title }}</h1>
                <p>{{ $description }}</p>
            </div>

            <div class="feature-grid">
                <div>
                    <div class="feature-image-wrapper">
                        <div class="feature-image-inner">
                            @if(!empty($image))
                                <img src="{{ asset(ltrim($image, '/')) }}" alt="{{ $title }}">
                            @else
                                <div class="img-fallback">
                                    <i class="fas fa-image fa-2x"></i>
                                    <span>Gambar belum tersedia</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="feature-card-block">
                        <h3>Tentang {{ $title }}</h3>
                        <p>{{ $description }}</p>
                    </div>

                    <div class="feature-card-block">
                        <div class="feature-benefit-title">Yang bisa Anda lakukan</div>
                        <div class="feature-benefit">
                            @foreach($bullets as $b)
                                <div class="feature-benefit-item">
                                    <span class="icon-circle"><i class="fas fa-check"></i></span>
                                    <span>{{ $b }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="feature-sidebar">
                    <div class="feature-sidebar-card">
                        @php
                            $otherFeatures = [
                                'manajemen-pesanan' => ['label' => 'Manajemen Pesanan', 'icon' => 'fa-shopping-cart'],
                                'inventori-pintar' => ['label' => 'Inventori Pintar', 'icon' => 'fa-boxes'],
                                'manajemen-pelanggan' => ['label' => 'Manajemen Pelanggan', 'icon' => 'fa-users'],
                                'dashboard-analitik' => ['label' => 'Dashboard Analitik', 'icon' => 'fa-chart-line'],
                                'asisten-ai' => ['label' => 'Asisten AI', 'icon' => 'fa-robot'],
                                'keamanan-enterprise' => ['label' => 'Keamanan Enterprise', 'icon' => 'fa-shield-alt'],
                            ];
                        @endphp

                        <div class="feature-sidebar-other">
                            <div class="feature-sidebar-other-label">Fitur Lainnya</div>
                            @foreach($otherFeatures as $otherSlug => $info)
                                @if($otherSlug !== $slug)
                                    <a href="{{ route('feature.show', $otherSlug) }}" class="other-feature-link">
                                        <i class="fas {{ $info['icon'] }}"></i> {{ $info['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-bottom">
                <p>&copy; 2026 Hutch.id.</p>
            </div>
        </footer>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
