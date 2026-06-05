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
            background: linear-gradient(135deg, #1a3f6d 0%, #0d2a52 50%, #051f3f 100%);
            color: #f7fbff;
            border-right: 1px solid rgba(255,255,255,0.08);
            min-height: 100vh;
            height: auto;
            box-shadow: 0 0 60px rgba(0, 0, 0, 0.15), inset 0 0 40px rgba(255,255,255,0.04);
            position: relative;
            overflow: hidden;
        }

        #sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.12) 0%, transparent 25%),
                radial-gradient(circle at 80% 80%, rgba(45,125,210,0.08) 0%, transparent 30%),
                linear-gradient(135deg, rgba(255,255,255,0.02) 0%, transparent 50%);
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
            gap: 1rem;
            padding: 1.4rem 1.1rem;
            margin-bottom: 0.5rem;
            margin: 0.9rem 0.75rem 0.8rem;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(37, 99, 235, 0.08) 100%);
            border: 1.5px solid rgba(112, 183, 255, 0.25);
            border-radius: 1.5rem;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15),
                        0 0 20px rgba(59, 130, 246, 0.1),
                        inset 0 1px 2px rgba(255,255,255,0.2);
            backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            animation: brandFadeIn 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .sidebar-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 50%, rgba(112, 183, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .sidebar-brand:hover {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.22) 0%, rgba(37, 99, 235, 0.15) 100%);
            border-color: rgba(112, 183, 255, 0.4);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2),
                        0 0 30px rgba(59, 130, 246, 0.2),
                        inset 0 1px 2px rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }

        @keyframes brandFadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px) scale(0.95);
                filter: blur(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        .sidebar-brand .logo-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(45, 125, 210, 0.2));
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.25),
                        inset 0 1px 2px rgba(255,255,255,0.3),
                        0 0 16px rgba(59, 130, 246, 0.15);
            overflow: hidden;
            border: 1.5px solid rgba(112, 183, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(12px);
            flex-shrink: 0;
            animation: iconSlideIn 0.8s ease-out;
        }

        @keyframes iconSlideIn {
            from {
                opacity: 0;
                transform: scale(0.8) rotateZ(-15deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotateZ(0deg);
            }
        }

        .sidebar-brand .logo-icon:hover {
            transform: scale(1.12) rotateZ(5deg);
            box-shadow: 0 12px 32px rgba(59, 130, 246, 0.35),
                        inset 0 1px 2px rgba(255,255,255,0.4),
                        0 0 20px rgba(59, 130, 246, 0.25);
            border-color: rgba(112, 183, 255, 0.5);
        }

        .sidebar-brand .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .sidebar-brand .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            flex: 1;
            animation: textSlideIn 0.8s ease-out 0.1s both;
        }

        @keyframes textSlideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar-brand .logo-text .brand-name {
            font-size: 0.9rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            filter: drop-shadow(0 1px 2px rgba(112, 183, 255, 0.3));
        }

        .sidebar-brand .logo-text .brand-subtitle {
            color: rgba(255,255,255,0.75);
            font-size: 0.68rem;
            letter-spacing: 0.04em;
            font-weight: 600;
            background: linear-gradient(135deg, rgba(255,255,255,0.8) 0%, rgba(226, 232, 240, 0.6) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            padding: 0.8rem 0 1rem 0;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
            border-radius: 999px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 999px;
            transition: all 0.3s ease;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem 0.85rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            background: rgba(255,255,255,0.04);
            border-radius: 1.25rem;
            margin: 0 0.75rem 0.85rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), inset 0 1px 1px rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }

        .sidebar-footer .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-shrink: 0;
            padding: 0.75rem 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            background: rgba(255,255,255,0.04);
            transition: all 0.3s ease;
        }

        .sidebar-footer .sidebar-user:hover {
            background: rgba(255,255,255,0.08);
        }

        .sidebar-footer .sidebar-user .user-info {
            min-width: 0;
            flex: 1;
        }

        .sidebar-footer .sidebar-user .user-info .fw-bold {
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 700;
        }

        .sidebar-footer .sidebar-user .user-info .text-muted {
            color: rgba(255, 255, 255, 0.65) !important;
            font-size: 0.72rem;
            line-height: 1.2;
            font-weight: 500;
        }

        .sidebar-footer .logout-btn {
            border-radius: 12px;
            padding: 0.7rem 1rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.12), rgba(255,255,255,0.06));
            border: 1px solid rgba(255,255,255,0.15);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15), inset 0 1px 1px rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            position: relative;
            overflow: hidden;
        }

        .sidebar-footer .logout-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-footer .logout-btn:hover::before {
            opacity: 0.1;
        }

        .sidebar-footer .logout-btn:hover,
        .sidebar-footer .logout-btn:focus {
            background: linear-gradient(135deg, rgba(255,255,255,0.18), rgba(255,255,255,0.1));
            transform: translateY(-2px);
            color: #ffffff;
            border-color: rgba(255,255,255,0.25);
            box-shadow: 0 6px 20px rgba(112, 183, 255, 0.2), inset 0 1px 1px rgba(255,255,255,0.25);
        }

        .sidebar-footer .logout-btn:active {
            transform: translateY(-1px);
        }

        #sidebar .border-bottom,
        #sidebar .border-top {
            border-color: rgba(255,255,255,0.12) !important;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.92);
            padding: 0.85rem 1.1rem;
            border-radius: 14px;
            margin: 0 0.75rem 0.95rem;
            min-height: 50px;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(4px);
            animation: slideInNavLink 0.5s ease both;
            position: relative;
            overflow: hidden;
        }

        #sidebar .nav-link:nth-child(1) { animation-delay: 0.1s; }
        #sidebar .nav-link:nth-child(2) { animation-delay: 0.2s; }
        #sidebar .nav-link:nth-child(3) { animation-delay: 0.3s; }
        #sidebar .nav-link:nth-child(4) { animation-delay: 0.4s; }
        #sidebar .nav-link:nth-child(5) { animation-delay: 0.5s; }

        #sidebar .nav-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #sidebar .nav-link:hover::before {
            opacity: 0.1;
        }

        #sidebar .nav-link i {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }

        #sidebar .nav-link:hover i {
            background: rgba(255,255,255,0.2);
            transform: rotate(6deg) scale(1.1);
        }

        #sidebar .nav-link.active i {
            background: rgba(112, 183, 255, 0.4);
            color: #70b7ff;
        }

        #sidebar .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
            transform: translateX(4px);
            box-shadow: 0 8px 24px rgba(112, 183, 255, 0.15);
            border-color: rgba(255,255,255,0.2);
        }

        #sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(112, 183, 255, 0.25), rgba(112, 183, 255, 0.1));
            color: #fff;
            border-color: rgba(112, 183, 255, 0.3);
            box-shadow: 0 8px 32px rgba(112, 183, 255, 0.2), inset 0 1px 1px rgba(255,255,255,0.2);
            position: relative;
        }

        #sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 50%;
            background: linear-gradient(180deg, #70b7ff, #2d7dd2);
            border-radius: 999px;
            box-shadow: 0 0 12px rgba(112, 183, 255, 0.6);
            animation: activePulse 2s ease-in-out infinite;
        }

        #sidebar .nav-link.active .badge {
            background: rgba(220, 38, 38, 0.9);
            color: #fff;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            animation: badgePulse 2s ease-in-out infinite;
        }

        #sidebar .badge {
            background: rgba(220, 38, 38, 0.85);
            color: #fff;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
            font-weight: 700;
        }

        #sidebar .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: 1.4rem;
            border: 2.5px solid rgba(255,255,255,0.4);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.25), inset 0 1px 2px rgba(255,255,255,0.35);
            flex-shrink: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(12px);
            position: relative;
            overflow: visible;
        }

        /* Enhanced online status indicator */
        #sidebar .avatar-circle::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.2),
                        0 0 0 2px rgba(16, 185, 129, 0.12),
                        0 0 6px rgba(16, 185, 129, 0.85),
                        0 0 12px rgba(16, 185, 129, 0.6),
                        0 0 18px rgba(16, 185, 129, 0.4),
                        inset -1px -1px 2px rgba(0, 0, 0, 0.2),
                        inset 1px 1px 2px rgba(255, 255, 255, 0.35);
            animation: onlineStatusPulse 2.2s ease-in-out infinite, onlineStatusGlow 1.4s ease-in-out infinite;
        }

        @keyframes onlineStatusPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.2),
                           0 0 0 2px rgba(16, 185, 129, 0.12),
                           0 0 6px rgba(16, 185, 129, 0.85),
                           0 0 12px rgba(16, 185, 129, 0.6),
                           0 0 18px rgba(16, 185, 129, 0.4),
                           inset -1px -1px 2px rgba(0, 0, 0, 0.2),
                           inset 1px 1px 2px rgba(255, 255, 255, 0.35);
            }
            50% {
                transform: scale(1.2);
                box-shadow: 0 0 0 1.5px rgba(16, 185, 129, 0.3),
                           0 0 0 3px rgba(16, 185, 129, 0.15),
                           0 0 8px rgba(16, 185, 129, 0.95),
                           0 0 16px rgba(16, 185, 129, 0.7),
                           0 0 24px rgba(16, 185, 129, 0.5),
                           inset -1px -1px 2px rgba(0, 0, 0, 0.2),
                           inset 1px 1px 2px rgba(255, 255, 255, 0.35);
            }
        }

        @keyframes onlineStatusGlow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }

        .sidebar-footer .sidebar-user:hover .avatar-circle {
            transform: scale(1.12);
            box-shadow: 0 14px 36px rgba(59, 130, 246, 0.4), inset 0 1px 2px rgba(255,255,255,0.4);
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
            letter-spacing: 0.12em;
            font-size: 0.65rem;
            margin-top: 1rem;
            margin-bottom: 0.7rem;
            font-weight: 800;
            padding: 0.3rem 1.1rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.6);
            position: relative;
            transition: all 0.3s ease;
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

        @keyframes slideInNavLink {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes activePulse {
            0%, 100% {
                box-shadow: 0 0 8px rgba(112, 183, 255, 0.4);
            }
            50% {
                box-shadow: 0 0 16px rgba(112, 183, 255, 0.8);
            }
        }

        @keyframes badgePulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
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

        /* Success Modal Styles */
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
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 10px 30px rgba(16, 185, 129, 0.3),
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
            background: radial-gradient(circle, rgba(16, 185, 129, 0.4) 0%, transparent 70%);
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
                                    <span id="notif-badge" class="badge bg-danger ms-auto" style="font-size: 0.65rem; padding: 0.2rem 0.5rem; display: none;">0</span>
                                </a>
                        @if(auth()->user()->role !== 'operator_gudang')
                            <a class="nav-link {{ request()->routeIs('pesanan.index') ? 'active' : '' }}" href="{{ route('pesanan.index') }}">
                                <i class="fas fa-list"></i>Daftar Pesanan
                                @if($jumlahMenunggu > 0)
                                    <span class="badge bg-danger ms-auto" style="font-size: 0.65rem; padding: 0.2rem 0.5rem;">{{ $jumlahMenunggu }}</span>
                                @endif
                            </a>
                        @endif
                        @if(auth()->user()->role !== 'operator_gudang')
                            <a class="nav-link {{ request()->routeIs('pesanan.create') ? 'active' : '' }}" href="{{ route('pesanan.create') }}">
                                <i class="fas fa-plus"></i>Buat PO
                            </a>
                            <a class="nav-link {{ request()->routeIs('pelanggan.index') ? 'active' : '' }}" href="{{ route('pelanggan.index') }}">
                                <i class="fas fa-users"></i>Pelanggan
                            </a>
                        @endif
                        @if(auth()->user()->role === 'operator_gudang')
                            <a class="nav-link {{ request()->routeIs('produk.index') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                                <i class="fas fa-boxes"></i>Manajemen Stok
                            </a>
                        @endif
                        @if(auth()->user()->role === 'staf_penjualan')
                            <div class="px-3 mt-3 mb-1 text-uppercase text-white-50 fw-semibold small sidebar-section">Staf</div>
                            <nav class="nav flex-column py-1">
                                <a class="nav-link {{ request()->routeIs('produk.staff') ? 'active' : '' }}" href="{{ route('produk.staff') }}">
                                    <i class="fas fa-cube"></i>Tambah Produk
                                </a>
                            </nav>
                        @endif
                    </nav>

                            @if(auth()->user()->role === 'administrator')
                                <div class="px-3 mt-2 mb-1 text-uppercase text-white-50 fw-semibold small sidebar-section">Admin</div>
                                <nav class="nav flex-column py-1">
                                    <a class="nav-link {{ request()->routeIs('arsip.index') ? 'active' : '' }}" href="{{ route('arsip.index') }}">
                                        <i class="fas fa-archive"></i>Arsip PDF
                                    </a>
                                </nav>
                            @endif
                        </div>

                        <div class="sidebar-footer">
                            <div class="sidebar-user">
                                <div class="avatar-circle">
                                    <i class="fas fa-user"></i>
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
                            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                                @csrf
                                <button type="submit" class="btn logout-btn w-100" id="logoutBtn">
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

    <!-- Success Modal for Login -->
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

            <button class="success-continue-btn" onclick="closeSuccessModal()">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @auth
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

        // Function to close success modal
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            modal.classList.remove('show');
            // Clear the session storage flag
            sessionStorage.removeItem('pendingLoginRole');
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

        // Check if login just completed
        window.addEventListener('load', function() {
            const pendingRole = sessionStorage.getItem('pendingLoginRole');
            if (pendingRole) {
                showSuccessModal(pendingRole);
                // Auto-close after 4 seconds
                setTimeout(() => {
                    closeSuccessModal();
                }, 4000);
            }

            // Check if logout just completed
            const pendingLogout = sessionStorage.getItem('pendingLogout');
            if (pendingLogout) {
                showLogoutModal();
                setTimeout(() => {
                    redirectToLogin();
                }, 6000);
            }
        });

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

        // Intercept logout form submission
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForm = document.querySelector('.logout-form');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    // Set the logout flag in sessionStorage
                    sessionStorage.setItem('pendingLogout', 'true');
                    // Let the form submit normally
                });
            }
        });
    </script>
    @endauth
    @stack('scripts')
</body>
</html>
