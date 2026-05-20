<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - hutch.id OrderFlow</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f3d7f 0%, #0b315f 50%, #1a1a2e 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
        .bg-elements {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape1 { width: 300px; height: 300px; background: #2d7dd2; top: -150px; left: -100px; animation-delay: 0s; }
        .shape2 { width: 200px; height: 200px; background: #16a34a; bottom: -100px; right: -50px; animation-delay: 2s; }
        .shape3 { width: 150px; height: 150px; background: #f59e0b; top: 50%; left: 10%; animation-delay: 4s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px); }
            50% { transform: translateY(-30px) translateX(20px); }
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1000px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
            display: flex;
            overflow: hidden;
            min-height: 500px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0f3d7f 0%, #1a5f9f 100%);
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-right {
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            color: white;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }

        .logo-icon svg,
        .logo-icon img {
            width: 100%;
            height: 100%;
        }

        .logo-icon img {
            object-fit: contain;
            border-radius: 18px;
        }

        .logo-text {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.15em;
        }

        .logo-subtext {
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.3em;
            margin-bottom: 0.8rem;
            text-transform: uppercase;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .divider-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        /* Role Selection */
        .role-selection {
            margin-bottom: 0;
            width: 100%;
        }

        .role-label {
            display: block;
            font-weight: 600;
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            text-align: center;
        }

        .role-options {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
            margin-bottom: 0;
        }

        .role-card {
            position: relative;
            cursor: pointer;
        }

        .role-card input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .role-card-content {
            padding: 1rem 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .role-card input[type="radio"]:checked + .role-card-content {
            border-color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }

        .role-card:hover .role-card-content {
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .role-icon {
            font-size: 1.5rem;
            margin-bottom: 0.4rem;
            color: white;
        }

        .role-card input[type="radio"]:checked ~ .role-card-content .role-icon {
            animation: scaleUp 0.3s ease;
        }

        @keyframes scaleUp {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1.1); }
        }

        .role-name {
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .role-desc {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Form Group */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group:last-of-type {
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.6rem;
            color: #1f2937;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2d7dd2;
            box-shadow: 0 0 0 4px rgba(45, 125, 210, 0.1);
            background: #f8fbff;
        }

        .form-group input::placeholder {
            color: #b0b9c3;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #2d7dd2, #1f6bb8);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(45, 125, 210, 0.3);
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(45, 125, 210, 0.4);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Error Message */
        .error-message {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #dc2626;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border-left: 4px solid #dc2626;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message i {
            flex-shrink: 0;
        }

        /* Footer */
        .login-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.75rem;
            color: #94a3b8;
            text-align: center;
        }

        .footer-badge {
            display: inline-block;
            background: #f0f4f8;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            margin: 0 0.3rem;
            color: #2d7dd2;
            font-weight: 500;
            font-size: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                min-height: auto;
            }

            .login-left {
                padding: 2rem;
                border-radius: 24px 24px 0 0;
            }

            .login-right {
                padding: 2rem;
            }

            .login-header {
                margin-bottom: 1.5rem;
            }

            .role-options {
                gap: 0.7rem;
            }

            .logo-text {
                font-size: 1.8rem;
            }

            .subtitle {
                font-size: 0.9rem;
            }

            .form-group {
                margin-bottom: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .login-wrapper {
                padding: 10px;
            }

            .login-left {
                padding: 1.5rem;
            }

            .login-right {
                padding: 1.5rem;
            }

            .login-header {
                margin-bottom: 1rem;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .logo-text {
                font-size: 1.5rem;
            }

            .subtitle {
                font-size: 0.85rem;
            }

            .divider-text {
                font-size: 0.8rem;
            }

            .role-label {
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }

            .role-options {
                gap: 0.6rem;
            }

            .role-card-content {
                padding: 0.8rem 1rem;
            }

            .role-name {
                font-size: 0.85rem;
            }

            .role-desc {
                font-size: 0.7rem;
            }

            .form-group input {
                padding: 0.9rem;
                font-size: 16px; /* Prevent zoom on iOS */
            }

            .btn-login {
                padding: 0.95rem;
                font-size: 0.95rem;
            }

            .shape {
                display: none;
            }
        }

        @media (max-width: 400px) {
            .login-wrapper {
                padding: 5px;
            }

            .login-left {
                padding: 1rem;
            }

            .login-right {
                padding: 1rem;
            }

            .login-card {
                min-height: auto;
            }

            .logo-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .logo-text {
                font-size: 1.3rem;
            }

            .subtitle {
                font-size: 0.8rem;
            }

            .role-card-content {
                padding: 0.7rem 0.8rem;
                font-size: 0.9rem;
            }

            .role-name {
                font-size: 0.8rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-group input {
                padding: 0.8rem;
                font-size: 16px;
            }

            .btn-login {
                padding: 0.9rem;
                font-size: 0.9rem;
            }

            .login-footer {
                font-size: 0.7rem;
            }

            .footer-badge {
                padding: 0.2rem 0.6rem;
                margin: 0 0.2rem;
                font-size: 0.65rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-elements">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
        <div class="shape shape3"></div>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- LEFT SECTION - Role Selection -->
            <div class="login-left">
                <div class="login-header">
                    <div class="logo-icon">
                        <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch Prestige Logo">
                    </div>
                    <div class="logo-text">HUTCH PRESTIGE</div>
                    <div class="subtitle">Bag Manufacturing & In-House Brand</div>
                    <div class="divider-text">Sistem Manajemen Pesanan</div>
                </div>

                <!-- Role Selection -->
                <div class="role-selection">
                    <label class="role-label">
                        <i class="fas fa-user-tie"></i> Pilih Role Anda
                    </label>
                    <div class="role-options">
                        <label class="role-card">
                            <input type="radio" name="role-select" value="administrator" checked>
                            <div class="role-card-content">
                                <div class="role-icon"><i class="fas fa-crown"></i></div>
                                <div class="role-name">Administrator</div>
                                <div class="role-desc">Akses Penuh</div>
                            </div>
                        </label>
                        <label class="role-card">
                            <input type="radio" name="role-select" value="pemilik_umkm">
                            <div class="role-card-content">
                                <div class="role-icon"><i class="fas fa-user-circle"></i></div>
                                <div class="role-name">Pemilik UMKM</div>
                                <div class="role-desc">Owner</div>
                            </div>
                        </label>
                        <label class="role-card">
                            <input type="radio" name="role-select" value="staf_penjualan">
                            <div class="role-card-content">
                                <div class="role-icon"><i class="fas fa-user"></i></div>
                                <div class="role-name">Staf Penjualan</div>
                                <div class="role-desc">Sales</div>
                            </div>
                        </label>
                        <label class="role-card">
                            <input type="radio" name="role-select" value="operator_gudang">
                            <div class="role-card-content">
                                <div class="role-icon"><i class="fas fa-warehouse"></i></div>
                                <div class="role-name">Operator Gudang</div>
                                <div class="role-desc">Warehouse</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION - Login Form -->
            <div class="login-right">
                @if($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Hidden input untuk role -->
                    <input type="hidden" name="role" id="roleInput" value="administrator">

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope" style="color: #2d7dd2; margin-right: 0.5rem;"></i>Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock" style="color: #2d7dd2; margin-right: 0.5rem;"></i>Password</label>
                        <input id="password" type="password" name="password" placeholder="Masukkan password Anda" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>Masuk Sekarang
                    </button>

                    <div class="login-footer">
                        <!-- <span class="footer-badge"><i class="fas fa-lock-open"></i> Aman</span> -->
                        <!-- <span class="footer-badge"><i class="fas fa-cookie"></i> Cookies</span> -->
                        <!-- <span class="footer-badge"><i class="fas fa-shield-alt"></i> HTTPS</span> -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sync role selection dengan hidden input
        document.querySelectorAll('input[name="role-select"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('roleInput').value = this.value;
            });
        });

        // Set initial value
        document.getElementById('roleInput').value = document.querySelector('input[name="role-select"]:checked').value;
    </script>
</body>
</html>
