<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Hutch.id')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(180deg, #081f47 0%, #0d2d6a 35%, #0f3c87 70%, #1350a5 100%); color: #f8fafc; background-image: radial-gradient(circle at 20% 20%, rgba(0,212,255,0.15), transparent 30%), radial-gradient(circle at 80% 15%, rgba(45,125,210,0.12), transparent 27%), radial-gradient(circle at 50% 95%, rgba(255,255,255,0.05), transparent 25%); }
        header.landing-header { padding: 18px 0; background: rgba(8,31,71,0.68); border-bottom: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px); }
        header .brand { font-weight:800; color:#ffffff; font-size:1.05rem; letter-spacing:0.2px }
        .container-hero { padding: 64px 0; }
        .features-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:18px; }
        .feature-card { background:linear-gradient(180deg, rgba(255,255,255,0.12), rgba(255,255,255,0.07)); border-radius:14px; padding:20px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 16px 40px rgba(0,0,0,0.12); transition:transform .14s ease, box-shadow .14s ease; }
        .feature-card:hover{ transform:translateY(-8px); box-shadow:0 24px 60px rgba(0,0,0,0.16); }
        .feature-icon { width:64px;height:64px;border-radius:12px;background:linear-gradient(135deg, rgba(0,212,255,0.18), rgba(45,125,210,0.12));display:flex;align-items:center;justify-content:center;font-size:22px;color:#e0f2fe;margin-bottom:10px }
        .hero-wrap { background: rgba(255,255,255,0.08); border-radius:18px; padding:36px 28px; margin-bottom:28px; box-shadow:0 12px 40px rgba(0,0,0,0.14); backdrop-filter: blur(16px); border:1px solid rgba(255,255,255,0.12); }
        .btn-gradient{ background: linear-gradient(90deg,#2d7dd2,#00d4ff); border: none; color: #fff; font-weight:700; padding:10px 14px; border-radius:10px }
        .btn-gradient:hover{ filter:brightness(.96) }
        footer.landing-footer{ padding:30px 0; text-align:center; color:#cbd5e1; font-size:14px }
        @media (max-width: 768px){ .container-hero{ padding:40px 0 } .hero-wrap{ padding:20px } }
    </style>
    @stack('styles')
    @yield('head')
</head>
<body>
    <header class="landing-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id" style="width:42px;height:42px;object-fit:contain;margin-right:12px">
                <div class="brand">Hutch.id</div>
            </div>
            <div aria-hidden="true"></div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="landing-footer mt-5">
        &copy; {{ date('Y') }} Hutch.id — Semua hak dilindungi.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
