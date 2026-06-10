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
            background: linear-gradient(135deg, #0a2463 0%, #247ba0 25%, #1b4965 50%, #0f3460 75%, #16213e 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 0;
            margin: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
        .shape3 { 
            width: 250px; 
            height: 250px; 
            background: #1e88e5; 
            top: 50%; 
            left: 10%; 
            animation-delay: 4s;
            animation: float 14s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px) scale(1); }
            50% { transform: translateY(-50px) translateX(30px) scale(1.1); }
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
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

        .login-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.18),
                inset 0 1px 2px rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.25);
            animation: slideUp 0.7s ease-out;
            display: flex;
            overflow: hidden;
            height: auto;
            max-height: 95vh;
            transition: all 0.4s ease;
        }

        .login-card:hover {
            box-shadow: 
                0 16px 48px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.25),
                inset 0 1px 2px rgba(255, 255, 255, 0.6);
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

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0a3068 0%, #1e5fa5 50%, #2575d7 100%);
            padding: 2.2rem 1.8rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
            max-height: 95vh;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: orbiting 15s linear infinite;
        }

        @keyframes orbiting {
            0% {
                transform: translateX(0) translateY(0);
            }
            50% {
                transform: translateX(-30px) translateY(30px);
            }
            100% {
                transform: translateX(0) translateY(0);
            }
        }

        .login-left > * {
            position: relative;
            z-index: 1;
        }

        .login-right {
            flex: 1;
            padding: 2.2rem 1.8rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            max-height: 95vh;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.2rem;
            color: white;
        }

        .logo-icon {
            width: 105px;
            height: 105px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.08));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.3),
                inset 0 1px 2px rgba(255, 255, 255, 0.4),
                0 0 40px rgba(37, 117, 215, 0.2);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            animation: logoFloat 4s cubic-bezier(0.34, 1.56, 0.64, 1) infinite, logoPulseGlow 3s ease-in-out infinite;
            backdrop-filter: blur(15px);
            position: relative;
            overflow: hidden;
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmerEffect 3s infinite;
            pointer-events: none;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px) rotateZ(0deg); }
            50% { transform: translateY(-20px) rotateZ(2deg); }
        }

        @keyframes logoPulseGlow {
            0%, 100% { box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.4), 0 0 40px rgba(37, 117, 215, 0.2); }
            50% { box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.4), 0 0 60px rgba(37, 117, 215, 0.4); }
        }

        @keyframes shimmerEffect {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .logo-icon:hover {
            animation: logoPulse 0.6s ease-out, logoFloat 4s cubic-bezier(0.34, 1.56, 0.64, 1) infinite, logoPulseGlow 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
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
            font-size: 1.95rem;
            font-weight: 900;
            color: white;
            margin-bottom: 0.3rem;
            text-shadow: 0 4px 16px rgba(0, 0, 0, 0.3), 0 0 20px rgba(37, 117, 215, 0.3);
            letter-spacing: 0.08em;
            background: linear-gradient(135deg, #ffffff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleGlow 3s ease-in-out infinite;
        }

        @keyframes titleGlow {
            0%, 100% { text-shadow: 0 4px 16px rgba(0, 0, 0, 0.3), 0 0 20px rgba(37, 117, 215, 0.3); }
            50% { text-shadow: 0 4px 16px rgba(0, 0, 0, 0.3), 0 0 30px rgba(59, 130, 246, 0.5); }
        }

        .logo-subtext {
            font-size: 0.65rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: 0.4em;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            animation: fadeIn 0.8s ease-out 0.2s both;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            animation: fadeIn 0.8s ease-out 0.3s both;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .divider-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
            margin-top: 0.5rem;
            animation: fadeIn 0.8s ease-out 0.4s both;
        }

        /* Role Selection */
        .role-selection {
            margin-bottom: 0;
            width: 100%;
        }

        .role-label {
            display: block;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            text-align: center;
            letter-spacing: 0.5px;
            animation: fadeIn 0.8s ease-out 0.1s both;
        }

        .role-options {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            margin-bottom: 0;
            width: 100%;
        }

        .role-card {
            position: relative;
            cursor: pointer;
            animation: cardIn 0.5s ease-out backwards;
        }

        .role-card:nth-child(1) { animation-delay: 0.1s; }
        .role-card:nth-child(2) { animation-delay: 0.2s; }
        .role-card:nth-child(3) { animation-delay: 0.3s; }
        .role-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .role-card input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .role-card-content {
            padding: 0.9rem 1rem;
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            text-align: center;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: rgba(255, 255, 255, 0.08);
            color: white;
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            position: relative;
            overflow: hidden;
        }

        .role-card-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .role-card input[type="radio"]:checked + .role-card-content {
            border-color: rgba(255, 255, 255, 0.9);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.15));
            box-shadow: 
                0 0 40px rgba(59, 130, 246, 0.4),
                inset 0 1px 2px rgba(255, 255, 255, 0.5);
            transform: scale(1.06) translateY(-2px);
            animation: selectedCardGlow 0.6s ease-out;
        }

        @keyframes selectedCardGlow {
            0% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.2), inset 0 1px 2px rgba(255, 255, 255, 0.5);
            }
            100% {
                box-shadow: 0 0 40px rgba(59, 130, 246, 0.4), inset 0 1px 2px rgba(255, 255, 255, 0.5);
            }
        }

        .role-card input[type="radio"]:checked + .role-card-content::before {
            left: 100%;
        }

        .role-card:hover .role-card-content {
            border-color: rgba(255, 255, 255, 0.5);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.08));
            transform: translateX(10px) translateY(-3px);
            box-shadow: 0 12px 30px rgba(37, 117, 215, 0.2), inset 0 1px 2px rgba(255, 255, 255, 0.3);
        }

        .role-icon {
            font-size: 1.3rem;
            color: white;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .role-card input[type="radio"]:checked + .role-card-content .role-icon {
            animation: iconPulse 0.5s ease-out;
        }

        @keyframes iconPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.25); }
            100% { transform: scale(1.15); }
        }

        .role-name {
            font-weight: 700;
            color: white;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .role-desc {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.75);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .role-card:hover .role-desc {
            color: rgba(255, 255, 255, 0.9);
        }

        .form-group {
            margin-bottom: 1.4rem;
            position: relative;
            animation: inputIn 0.6s ease-out backwards;
        }

        .form-group:nth-of-type(1) { animation-delay: 0s; }
        .form-group:nth-of-type(2) { animation-delay: 0.1s; }

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

        .form-group:last-of-type {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            font-weight: 700;
            margin-bottom: 0.6rem;
            color: #1a3a52;
            font-size: 0.9rem;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            animation: labelFloat 0.4s ease-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes labelFloat {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group label i {
            color: #2575d7;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus ~ label i,
        .form-group:has(input:focus) label i {
            transform: scale(1.2) rotate(10deg);
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1.1rem;
            border: 2px solid #e0e7ff;
            border-radius: 11px;
            font-size: 0.9rem;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05), 0 4px 16px rgba(37, 117, 215, 0.05);
            position: relative;
            overflow: hidden;
        }

        .form-group input::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: -100%;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #2575d7, transparent);
            animation: none;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2575d7;
            box-shadow: 
                0 0 0 5px rgba(37, 117, 215, 0.15),
                inset 0 1px 3px rgba(0, 0, 0, 0.05),
                0 8px 20px rgba(37, 117, 215, 0.12);
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            transform: translateY(-3px);
        }

        .form-group input:focus::before {
            left: 0;
            animation: lineSlide 0.6s ease-out;
        }

        @keyframes lineSlide {
            from { left: -100%; }
            to { left: 100%; }
        }

        .form-group input:hover:not(:focus) {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(37, 117, 215, 0.12), inset 0 1px 3px rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, #f5f9ff 0%, #ffffff 100%);
        }

        .form-group input::placeholder {
            color: #cbd5e1;
            transition: all 0.3s ease;
        }

        .form-group input:focus::placeholder {
            color: #a0aec0;
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 1.1rem 1.2rem;
            background: linear-gradient(135deg, #2575d7, #1e5fa5);
            background-size: 200% 200%;
            color: white;
            border: none;
            border-radius: 11px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 
                0 14px 32px rgba(37, 117, 215, 0.4),
                0 0 0 1px rgba(37, 117, 215, 0.15),
                inset 0 1px 2px rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            margin-bottom: 0.8rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .btn-login::before {
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

        .btn-login::after {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .btn-login i {
            transition: all 0.4s ease;
            margin-right: 0.9rem;
            font-size: 1.05rem;
        }

        .btn-login:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 20px 48px rgba(37, 117, 215, 0.5),
                0 0 0 1px rgba(37, 117, 215, 0.2),
                inset 0 1px 2px rgba(255, 255, 255, 0.4);
            background-position: 200% center;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover i {
            transform: scale(1.2) rotate(5deg);
        }

        .btn-login:active {
            transform: translateY(-1px);
            box-shadow: 
                0 6px 20px rgba(37, 117, 215, 0.3),
                0 0 0 1px rgba(37, 117, 215, 0.1);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Error Message */
        .error-message {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #dc2626;
            padding: 1rem 1.1rem;
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

        .error-message i {
            flex-shrink: 0;
            font-size: 1.1rem;
            animation: errorPulse 0.6s ease-out;
        }

        @keyframes errorPulse {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .warning-message {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            color: #b45309;
            border-left-color: #f59e0b;
            border-color: rgba(245, 158, 11, 0.2);
        }

        /* Footer */
        .login-footer {
            margin-top: 0.8rem;
            padding-top: 0.6rem;
            font-size: 0.65rem;
            color: #94a3b8;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .footer-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f0f4f8, #e8eef7);
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            margin: 0 0.3rem;
            color: #2575d7;
            font-weight: 600;
            font-size: 0.75rem;
            border: 1px solid rgba(37, 117, 215, 0.1);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-badge:hover {
            background: linear-gradient(135deg, #dbeafe, #e0e7ff);
            transform: scale(1.05);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .login-wrapper {
                max-width: 100%;
                padding: 10px;
            }

            .login-card {
                border-radius: 20px;
            }

            .login-left,
            .login-right {
                padding: 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .login-wrapper {
                padding: 8px;
            }

            .login-left,
            .login-right {
                padding: 1.3rem 1.2rem;
            }

            .logo-icon {
                width: 70px;
                height: 70px;
            }

            .logo-text {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                min-height: auto;
                border-radius: 16px;
                max-height: none;
            }

            .login-left {
                padding: 2rem 1.5rem;
                border-radius: 16px 16px 0 0;
                max-height: none;
                min-height: auto;
            }

            .login-right {
                padding: 2rem 1.5rem;
                max-height: none;
                overflow-y: visible;
            }

            .login-wrapper {
                padding: 15px;
            }

            .login-header {
                margin-bottom: 1.8rem;
            }

            .logo-icon {
                width: 65px;
                height: 65px;
            }

            .logo-text {
                font-size: 1.6rem;
            }

            .logo-subtext {
                font-size: 0.7rem;
            }

            .subtitle {
                font-size: 0.85rem;
            }

            .divider-text {
                font-size: 0.75rem;
            }

            .role-label {
                font-size: 0.85rem;
                margin-bottom: 1rem;
            }

            .role-options {
                gap: 0.8rem;
                grid-template-columns: 1fr 1fr;
            }

            .role-card-content {
                padding: 1rem;
            }

            .role-name {
                font-size: 0.9rem;
            }

            .role-desc {
                font-size: 0.75rem;
            }

            .role-icon {
                font-size: 1.5rem;
            }

            .form-group {
                margin-bottom: 1.3rem;
            }

            .form-group label {
                font-size: 0.95rem;
            }

            .form-group input {
                padding: 1rem;
                font-size: 16px;
            }

            .btn-login {
                padding: 1rem;
                font-size: 1rem;
            }

            .login-footer {
                margin-top: 1rem;
                padding-top: 0.8rem;
            }
        }

        @media (max-width: 640px) {
            .login-wrapper {
                padding: 12px;
                max-width: 100%;
            }

            .login-card {
                border-radius: 14px;
            }

            .login-left {
                padding: 1.5rem 1.2rem;
                border-radius: 14px 14px 0 0;
            }

            .login-right {
                padding: 1.5rem 1.2rem;
            }

            .login-header {
                margin-bottom: 1.5rem;
            }

            .logo-icon {
                width: 55px;
                height: 55px;
                font-size: 28px;
            }

            .logo-text {
                font-size: 1.4rem;
            }

            .subtitle {
                font-size: 0.8rem;
            }

            .role-label {
                font-size: 0.8rem;
            }

            .role-options {
                grid-template-columns: 1fr;
                gap: 0.7rem;
            }

            .role-card-content {
                padding: 0.9rem;
            }

            .role-name {
                font-size: 0.85rem;
            }

            .role-icon {
                font-size: 1.3rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-group input {
                padding: 0.95rem;
                font-size: 16px;
            }

            .btn-login {
                padding: 0.95rem;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 576px) {
            .login-wrapper {
                padding: 10px;
                margin: auto;
            }

            .login-card {
                flex-direction: column;
                min-height: auto;
                max-height: none;
                border-radius: 12px;
            }

            .login-left {
                padding: 1.3rem 1rem;
                border-radius: 12px 12px 0 0;
                max-height: none;
                overflow: visible;
            }

            .login-right {
                padding: 1.3rem 1rem;
                max-height: none;
                overflow: visible;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .login-header {
                margin-bottom: 1.3rem;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .logo-text {
                font-size: 1.3rem;
            }

            .logo-subtext {
                font-size: 0.65rem;
                margin-bottom: 0.3rem;
            }

            .subtitle {
                font-size: 0.75rem;
            }

            .divider-text {
                font-size: 0.7rem;
            }

            .role-label {
                font-size: 0.75rem;
                margin-bottom: 0.9rem;
            }

            .role-options {
                gap: 0.6rem;
                grid-template-columns: 1fr;
            }

            .role-card {
                margin-bottom: 0.3rem;
            }

            .role-card-content {
                padding: 0.8rem;
            }

            .role-name {
                font-size: 0.8rem;
            }

            .role-desc {
                font-size: 0.65rem;
            }

            .role-icon {
                font-size: 1.2rem;
            }

            .form-group {
                margin-bottom: 1.1rem;
            }

            .form-group label {
                font-size: 0.85rem;
            }

            .form-group label i {
                margin-right: 0.3rem;
            }

            .form-group input {
                padding: 0.9rem;
                font-size: 16px;
                border-radius: 8px;
            }

            .btn-login {
                padding: 0.9rem;
                font-size: 0.9rem;
                border-radius: 8px;
            }

            .login-footer {
                margin-top: 0.8rem;
                padding-top: 0.5rem;
                font-size: 0.6rem;
            }

            .footer-badge {
                padding: 0.3rem 0.7rem;
                margin: 0 0.2rem;
                font-size: 0.65rem;
                border-radius: 16px;
            }

            .shape {
                display: none;
            }

            .bg-elements {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-wrapper {
                padding: 8px;
            }

            .login-card {
                border-radius: 10px;
            }

            .login-left {
                padding: 1.2rem 0.9rem;
                border-radius: 10px 10px 0 0;
            }

            .login-right {
                padding: 1.2rem 0.9rem;
            }

            .logo-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .logo-text {
                font-size: 1.1rem;
            }

            .subtitle {
                font-size: 0.7rem;
            }

            .role-options {
                gap: 0.5rem;
            }

            .role-card-content {
                padding: 0.7rem;
            }

            .form-group input {
                padding: 0.8rem;
                font-size: 16px;
            }

            .btn-login {
                padding: 0.85rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 360px) {
            .login-wrapper {
                padding: 5px;
            }

            .login-left {
                padding: 1rem 0.8rem;
            }

            .login-right {
                padding: 1rem 0.8rem;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .logo-text {
                font-size: 1rem;
            }

            .logo-subtext,
            .divider-text {
                display: none;
            }

            .subtitle {
                font-size: 0.65rem;
            }

            .role-label {
                font-size: 0.7rem;
            }

            .form-group label {
                font-size: 0.8rem;
            }

            .form-group input {
                padding: 0.75rem;
                font-size: 16px;
            }

            .btn-login {
                padding: 0.8rem;
                font-size: 0.8rem;
            }
        }

        /* Success Modal */
        .success-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .success-modal-overlay.show {
            display: flex;
            opacity: 1;
        }

        .success-modal {
            background: white;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            max-width: 450px;
            width: 90%;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 40px rgba(37, 117, 215, 0.15);
            animation: modalPopIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .success-modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2575d7, #1e5fa5, #0a3068);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes modalPopIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(30px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }

        .success-icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 10px 30px rgba(59, 130, 246, 0.3),
                inset 0 1px 2px rgba(255, 255, 255, 0.3);
            animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .success-icon-container::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.4) 0%, transparent 70%);
            animation: iconPulse 2s ease-in-out infinite;
        }

        .success-icon-container i {
            font-size: 2.5rem;
            color: white;
            position: relative;
            z-index: 1;
            animation: checkmarkDraw 0.6s ease-out;
        }

        @keyframes iconBounce {
            0% {
                transform: scale(0) rotateZ(-45deg);
            }
            50% {
                transform: scale(1.1) rotateZ(5deg);
            }
            100% {
                transform: scale(1) rotateZ(0);
            }
        }

        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        @keyframes checkmarkDraw {
            from {
                transform: scale(0) rotate(-45deg);
            }
            to {
                transform: scale(1) rotate(0);
            }
        }

        .success-title {
            font-size: 1.8rem;
            font-weight: 900;
            color: #1a3a52;
            margin-bottom: 0.8rem;
            letter-spacing: 0.5px;
            animation: textFadeIn 0.6s ease-out 0.2s both;
            background: linear-gradient(135deg, #1a3a52, #2575d7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .success-message {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            animation: textFadeIn 0.6s ease-out 0.3s both;
        }

        .success-role-info {
            background: linear-gradient(135deg, #f0f7ff, #f5f9ff);
            border: 2px solid #e0e7ff;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            animation: slideUpIn 0.6s ease-out 0.4s both;
            position: relative;
            overflow: hidden;
        }

        .success-role-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(37, 117, 215, 0.08), transparent);
            pointer-events: none;
        }

        .role-info-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .role-info-value {
            font-size: 1.3rem;
            font-weight: 800;
            color: #2575d7;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            position: relative;
            z-index: 1;
        }

        .role-info-value i {
            font-size: 1.5rem;
            animation: iconRotate 0.8s ease-out;
        }

        @keyframes slideUpIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes textFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes iconRotate {
            from {
                transform: rotateY(-90deg) scale(0);
            }
            to {
                transform: rotateY(0) scale(1);
            }
        }

        .success-continue-btn {
            width: 100%;
            padding: 1rem 1.2rem;
            background: linear-gradient(135deg, #2575d7, #1e5fa5);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 25px rgba(37, 117, 215, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: slideUpIn 0.6s ease-out 0.5s both;
            position: relative;
            overflow: hidden;
        }

        .success-continue-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .success-continue-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(37, 117, 215, 0.4);
        }

        .success-continue-btn:hover::before {
            left: 100%;
        }

        .success-continue-btn:active {
            transform: translateY(-1px);
        }

        /* Logout Modal Styles */
        .logout-icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 10px 30px rgba(239, 68, 68, 0.3),
                inset 0 1px 2px rgba(255, 255, 255, 0.3);
            animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        .logout-icon-container::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.4) 0%, transparent 70%);
            animation: iconPulse 2s ease-in-out infinite;
        }

        .logout-icon-container i {
            font-size: 2.5rem;
            color: white;
            position: relative;
            z-index: 1;
            animation: checkmarkDraw 0.6s ease-out;
        }

        .logout-title {
            font-size: 1.8rem;
            font-weight: 900;
            color: #1a3a52;
            margin-bottom: 0.8rem;
            letter-spacing: 0.5px;
            animation: textFadeIn 0.6s ease-out 0.2s both;
            background: linear-gradient(135deg, #1a3a52, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logout-message {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            animation: textFadeIn 0.6s ease-out 0.3s both;
        }

        .logout-info {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 2px solid #fca5a5;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            animation: slideUpIn 0.6s ease-out 0.4s both;
            position: relative;
            overflow: hidden;
        }

        .logout-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.08), transparent);
            pointer-events: none;
        }

        .logout-info-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: #991b1b;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-info-label::before {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .logout-continue-btn {
            width: 100%;
            padding: 1rem 1.2rem;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: slideUpIn 0.6s ease-out 0.5s both;
            position: relative;
            overflow: hidden;
        }

        .logout-continue-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .logout-continue-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(239, 68, 68, 0.4);
        }

        .logout-continue-btn:hover::before {
            left: 100%;
        }

        .logout-continue-btn:active {
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .success-modal {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }

            .success-icon-container {
                width: 70px;
                height: 70px;
            }

            .success-icon-container i {
                font-size: 2rem;
            }

            .success-title {
                font-size: 1.5rem;
            }

            .success-message {
                font-size: 0.9rem;
                margin-bottom: 1.2rem;
            }

            .success-role-info {
                padding: 1rem;
                margin-bottom: 1.2rem;
            }

            .role-info-value {
                font-size: 1.1rem;
                gap: 0.6rem;
            }

            .role-info-value i {
                font-size: 1.3rem;
            }

            .success-continue-btn {
                padding: 0.9rem;
                font-size: 0.9rem;
            }

            .logout-icon-container {
                width: 70px;
                height: 70px;
            }

            .logout-icon-container i {
                font-size: 2rem;
            }

            .logout-title {
                font-size: 1.5rem;
            }

            .logout-message {
                font-size: 0.9rem;
                margin-bottom: 1.2rem;
            }

            .logout-info {
                padding: 1rem;
                margin-bottom: 1.2rem;
            }

            .logout-info-label {
                font-size: 0.85rem;
            }

            .logout-continue-btn {
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
                @if(session('warning'))
                    <div class="error-message warning-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <!-- Hidden input untuk role -->
                    <input type="hidden" name="role" id="roleInput" value="administrator">

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope" style="color: #2575d7; margin-right: 0.5rem;"></i>Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock" style="color: #2575d7; margin-right: 0.5rem;"></i>Password</label>
                        <input id="password" type="password" name="password" placeholder="Masukkan password Anda" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>Masuk Sekarang
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

    <!-- Success Modal -->
    <div class="success-modal-overlay" id="successModal">
        <div class="success-modal">
            <div class="success-icon-container">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="success-title">Login Berhasil!</h2>
            <p class="success-message">Anda berhasil masuk ke sistem. Selamat datang di Hutch Prestige!</p>
            
            <div class="success-role-info">
                <div class="role-info-label">Login Sebagai</div>
                <div class="role-info-value">
                    <i id="roleIcon" class="fas fa-crown"></i>
                    <span id="roleName">Administrator</span>
                </div>
            </div>

            <button class="success-continue-btn" onclick="continuePage()">
                <i class="fas fa-arrow-right"></i> Lanjutkan
            </button>
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="success-modal-overlay" id="logoutModal">
        <div class="success-modal">
            <div class="logout-icon-container">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h2 class="logout-title">Logout Berhasil!</h2>
            <p class="logout-message">Anda telah berhasil keluar dari sistem.</p>

            <button class="logout-continue-btn" onclick="redirectToLogin()">
                <i class="fas fa-sign-in-alt"></i> Kembali ke Login
            </button>
        </div>
    </div>

    <script>
        // Role icons mapping
        const roleIcons = {
            'administrator': 'fas fa-crown',
            'staf_penjualan': 'fas fa-user',
            'operator_gudang': 'fas fa-warehouse'
        };

        const roleLabels = {
            'administrator': 'Administrator',
            'staf_penjualan': 'Staf Penjualan',
            'operator_gudang': 'Operator Gudang'
        };

        // Function to show success modal
        function showSuccessModal(role) {
            const modal = document.getElementById('successModal');
            const roleName = document.getElementById('roleName');
            const roleIcon = document.getElementById('roleIcon');

            roleName.textContent = roleLabels[role] || 'Administrator';
            roleIcon.className = roleIcons[role] || 'fas fa-crown';

            modal.classList.add('show');
        }

        // Function to continue to dashboard
        function continuePage() {
            // Clear the session storage flag
            sessionStorage.removeItem('pendingLoginRole');
            window.location.href = '{{ route("dashboard") }}';
        }

        // Function to show logout modal
        function showLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.classList.add('show');
        }

        // Function to redirect to login
        function redirectToLogin() {
            // Clear the session storage flag
            sessionStorage.removeItem('pendingLogout');
            window.location.href = '{{ route("login") }}';
        }

        // Intercept form submission to store role and show loading state
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const btnLogin = this.querySelector('.btn-login');
                const role = document.getElementById('roleInput').value;
                
                // Store role in sessionStorage before submit
                sessionStorage.setItem('pendingLoginRole', role);
                
                // Show loading state
                if (btnLogin) {
                    btnLogin.style.opacity = '0.7';
                    btnLogin.style.pointerEvents = 'none';
                    const originalText = btnLogin.innerHTML;
                    btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    btnLogin._originalText = originalText;
                    btnLogin._role = role;
                }
                
                // Submit the form using native submit
                const formElement = this;
                setTimeout(() => {
                    formElement.submit();
                }, 100);
            });
        }

        // Check on page load to handle login response
        window.addEventListener('load', function() {
            // Check if there are error messages (login failed)
            const errorMessages = document.querySelector('.error-message');
            
            if (errorMessages) {
                // Login failed - show errors and restore button
                const loginBtn = document.querySelector('.btn-login');
                if (loginBtn && loginBtn._originalText) {
                    loginBtn.innerHTML = loginBtn._originalText;
                    loginBtn.style.opacity = '1';
                    loginBtn.style.pointerEvents = 'auto';
                }
                // Clear pending login role
                sessionStorage.removeItem('pendingLoginRole');
                console.log('Login failed with errors');
            } else {
                // Check if pending login role exists
                const pendingRole = sessionStorage.getItem('pendingLoginRole');
                if (pendingRole) {
                    // Login successful - show modal and redirect
                    showSuccessModal(pendingRole);
                    
                    // Redirect after showing modal
                    setTimeout(() => {
                        sessionStorage.removeItem('pendingLoginRole');
                        window.location.href = '{{ route("dashboard") }}';
                    }, 2500);
                }
            }
        });

        // Check on page load for logout completion
        window.addEventListener('load', function() {
            // Check if logout just completed
            const pendingLogout = sessionStorage.getItem('pendingLogout');
            if (pendingLogout) {
                showLogoutModal();
                setTimeout(() => {
                    redirectToLogin();
                }, 3000);
            }
        });

        // Smooth role selection animation
        document.querySelectorAll('input[name="role-select"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('roleInput').value = this.value;
                
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s ease-out';
                ripple.style.pointerEvents = 'none';
                
                const rect = this.parentElement.getBoundingClientRect();
                ripple.style.width = ripple.style.height = '100px';
                ripple.style.left = (rect.width / 2 - 50) + 'px';
                ripple.style.top = (rect.height / 2 - 50) + 'px';
            });
        });

        // Set initial value
        document.getElementById('roleInput').value = document.querySelector('input[name="role-select"]:checked').value;

        // Add ripple animation keyframe
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Add focus animation to inputs
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.animation = 'inputFocus 0.3s ease-out';
            });
        });

        // Add input focus animation
        const focusStyle = document.createElement('style');
        focusStyle.textContent = `
            @keyframes inputFocus {
                from {
                    transform: scale(0.98);
                }
                to {
                    transform: scale(1);
                }
            }
        `;
        document.head.appendChild(focusStyle);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
