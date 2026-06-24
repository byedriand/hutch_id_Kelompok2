<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar - hutch.id OrderFlow</title>

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
            background: linear-gradient(135deg, #0a2463 0%, #247ba0 25%, #1b4965 50%, #0f3460 75%, #16213e 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 20px 0;
            margin: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

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
            opacity: 0.08;
            animation: float 8s ease-in-out infinite;
            filter: blur(40px);
        }

        .shape1 { 
            width: 400px; 
            height: 400px; 
            background: #2d7dd2; 
            top: -150px; 
            left: -100px; 
            animation-delay: 0s;
            animation: float 10s ease-in-out infinite;
        }
        .shape2 { 
            width: 300px; 
            height: 300px; 
            background: #00d4ff; 
            bottom: -100px; 
            right: -50px; 
            animation-delay: 2s;
            animation: float 12s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px) scale(1); }
            50% { transform: translateY(-50px) translateX(30px) scale(1.1); }
        }

        .register-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
            padding: 15px;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.18),
                inset 0 1px 2px rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 2.2rem 1.8rem;
            animation: slideUp 0.7s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 1.8rem;
            color: #1a3a52;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2575d7, #1e5fa5);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 20px 50px rgba(37, 117, 215, 0.3);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
        }

        .register-title {
            font-size: 1.8rem;
            font-weight: 900;
            color: #1a3a52;
            margin-bottom: 0.3rem;
            letter-spacing: -0.5px;
        }

        .register-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.2rem;
            position: relative;
            animation: inputIn 0.6s ease-out backwards;
        }

        .form-group:nth-of-type(1) { animation-delay: 0.1s; }
        .form-group:nth-of-type(2) { animation-delay: 0.2s; }
        .form-group:nth-of-type(3) { animation-delay: 0.3s; }

        @keyframes inputIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.6rem;
            color: #1a3a52;
            font-size: 0.9rem;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: rgba(248, 250, 252, 0.8);
            color: #1a3a52;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .form-group input::placeholder,
        .form-group select {
            color: #94a3b8;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2575d7;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 3px rgba(37, 117, 215, 0.1);
        }

        .form-group input:invalid:not(:placeholder-shown),
        .form-group.has-error input {
            border-color: #dc2626;
        }

        .form-group input:valid:not(:placeholder-shown) {
            border-color: #16a34a;
        }

        .error-text {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .error-text i {
            font-size: 0.7rem;
        }

        .error-message {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #dc2626;
            padding: 1rem;
            border-radius: 9px;
            margin-bottom: 1.2rem;
            font-size: 0.9rem;
            border-left: 4px solid #dc2626;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideInDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-message {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #16a34a;
            border-left-color: #22c55e;
            border-color: rgba(34, 197, 94, 0.2);
        }

        .form-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-register {
            flex: 1;
            padding: 1rem;
            background: linear-gradient(135deg, #2575d7, #1e5fa5);
            border: none;
            color: white;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 12px 30px rgba(37, 117, 215, 0.3);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
            z-index: 1;
        }

        .btn-register i {
            margin-right: 0.5rem;
            transition: all 0.4s ease;
        }

        .btn-register:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 48px rgba(37, 117, 215, 0.5);
            background-position: 200% center;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover i {
            transform: scale(1.2) rotate(5deg);
        }

        .btn-register:active {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 117, 215, 0.3);
        }

        .btn-back {
            flex: 1;
            padding: 1rem;
            background: transparent;
            border: 2px solid #e2e8f0;
            color: #2575d7;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-back:hover {
            border-color: #2575d7;
            background: rgba(37, 117, 215, 0.05);
            transform: translateY(-2px);
            color: #2575d7;
            text-decoration: none;
        }

        .register-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
        }

        .register-footer a {
            color: #2575d7;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .register-footer a:hover {
            color: #1e5fa5;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-wrapper {
                padding: 10px;
                max-width: 100%;
            }

            .register-card {
                padding: 1.5rem;
                border-radius: 20px;
            }

            .register-title {
                font-size: 1.5rem;
            }

            .form-buttons {
                flex-direction: column;
                gap: 0.8rem;
            }

            .form-group input,
            .form-group select {
                padding: 0.95rem;
                font-size: 16px;
            }

            .btn-register,
            .btn-back {
                padding: 0.9rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-elements">
        <div class="shape shape1"></div>
        <div class="shape shape2"></div>
    </div>

    <div class="register-wrapper">
        <div class="register-card">
            <div class="register-header">
                <div class="logo-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="register-title">Buat Akun</h1>
                <p class="register-subtitle">Daftar akun baru untuk mengakses Hutch.id</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="error-message success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope" style="color: #2575d7; margin-right: 0.5rem;"></i>Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    @error('email')
                        <div class="error-text">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock" style="color: #2575d7; margin-right: 0.5rem;"></i>Password</label>
                    <input id="password" type="password" name="password" placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <div class="error-text">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role"><i class="fas fa-briefcase" style="color: #2575d7; margin-right: 0.5rem;"></i>Role</label>
                    <select id="role" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="administrator" {{ old('role') === 'administrator' ? 'selected' : '' }}>Administrator</option>
                        <option value="staf_penjualan" {{ old('role') === 'staf_penjualan' ? 'selected' : '' }}>Staf Penjualan</option>
                        <option value="operator_gudang" {{ old('role') === 'operator_gudang' ? 'selected' : '' }}>Operator Gudang</option>
                    </select>
                    @error('role')
                        <div class="error-text">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-check"></i>Daftar
                    </button>
                    <a href="{{ route('login') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>Kembali
                    </a>
                </div>

                <div class="register-footer">
                    Sudah memiliki akun? <a href="{{ route('login') }}">Login di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

