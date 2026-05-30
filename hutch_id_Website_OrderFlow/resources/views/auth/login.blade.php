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
            max-width: 1100px;
            padding: 12px;
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
            padding: 1.2rem 1.1rem;
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
            padding: 1.3rem 1.2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            max-height: 95vh;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            color: white;
        }

        .logo-icon {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.8rem;
            box-shadow: 
                0 16px 40px rgba(0, 0, 0, 0.2),
                inset 0 1px 2px rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.15);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        .logo-icon:hover {
            animation: logoPulse 0.6s ease-out;
        }

        @keyframes logoPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
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
            font-size: 1.6rem;
            font-weight: 900;
            color: white;
            margin-bottom: 0.15rem;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.08em;
            background: linear-gradient(135deg, #ffffff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            font-size: 0.75rem;
            margin-bottom: 0.3rem;
            animation: fadeIn 0.8s ease-out 0.3s both;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .divider-text {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.7rem;
            margin-top: 0.3rem;
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
            margin-bottom: 0.8rem;
            font-size: 0.8rem;
            text-align: center;
            letter-spacing: 0.5px;
            animation: fadeIn 0.8s ease-out 0.1s both;
        }

        .role-options {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
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
            padding: 0.65rem 0.85rem;
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 9px;
            text-align: center;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: rgba(255, 255, 255, 0.08);
            color: white;
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.15rem;
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
            border-color: rgba(255, 255, 255, 0.8);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
            box-shadow: 
                0 0 30px rgba(255, 255, 255, 0.3),
                inset 0 1px 2px rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
        }

        .role-card input[type="radio"]:checked + .role-card-content::before {
            left: 100%;
        }

        .role-card:hover .role-card-content {
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .role-icon {
            font-size: 1.1rem;
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
            font-size: 0.7rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .role-desc {
            font-size: 0.55rem;
            color: rgba(255, 255, 255, 0.75);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .role-card:hover .role-desc {
            color: rgba(255, 255, 255, 0.9);
        }

        .form-group {
            margin-bottom: 1rem;
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
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: #1a3a52;
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 0.8rem;
            border: 2px solid #e0e7ff;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-group input:focus {
            outline: none;
            border-color: #2575d7;
            box-shadow: 
                0 0 0 4px rgba(37, 117, 215, 0.1),
                inset 0 1px 3px rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            transform: translateY(-2px);
        }

        .form-group input:hover:not(:focus) {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(37, 117, 215, 0.08);
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
            padding: 0.85rem;
            background: linear-gradient(135deg, #2575d7, #1e5fa5);
            background-size: 200% 200%;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 
                0 12px 28px rgba(37, 117, 215, 0.35),
                0 0 0 1px rgba(37, 117, 215, 0.1);
            position: relative;
            overflow: hidden;
            margin-bottom: 0.6rem;
            letter-spacing: 0.3px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.5s ease;
            z-index: 1;
        }

        .btn-login i {
            transition: all 0.3s ease;
            margin-right: 0.7rem;
        }

        .btn-login:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 16px 40px rgba(37, 117, 215, 0.45),
                0 0 0 1px rgba(37, 117, 215, 0.15);
            background-position: 200% center;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover i {
            transform: scale(1.15);
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
            padding: 0.8rem 0.9rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.8rem;
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

        // Form submission animation
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const btnLogin = this.querySelector('.btn-login');
                if (btnLogin) {
                    btnLogin.style.opacity = '0.7';
                    btnLogin.style.pointerEvents = 'none';
                    
                    // Add loading state
                    const originalText = btnLogin.innerHTML;
                    btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    
                    // Restore after 3 seconds if form doesn't submit
                    setTimeout(() => {
                        btnLogin.innerHTML = originalText;
                        btnLogin.style.opacity = '1';
                        btnLogin.style.pointerEvents = 'auto';
                    }, 3000);
                }
            });
        }

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
</body>
</html>
