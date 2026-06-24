<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hutch.id - Platform Order Flow & AI Chatbot untuk UMKM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --light-bg: rgba(255, 255, 255, 0.05);
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

        .navbar-nav {
            gap: 2rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            display: none;
        }

        .navbar-toggler {
            display: none !important;
        }

        .nav-link:hover {
            color: #00d4ff !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #2d7dd2, #00d4ff);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-login {
            background: linear-gradient(135deg, #2d7dd2, #00d4ff);
            border: none;
            color: #fff;
            padding: 0.7rem 1.8rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            box-shadow: 0 8px 20px rgba(45, 125, 210, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(45, 125, 210, 0.35);
            color: #fff;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            min-height: 65vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px 80px;
            position: relative;
            z-index: 1;
        }

        .hero-content {
            text-align: left;
            animation: fadeInLeft 0.8s ease-out both;
        }

        .hero-row {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 4rem;
            align-items: center;
            max-width: 1300px;
            margin: 0 auto;
            width: 100%;
        }

        @media (max-width: 768px) {
            .hero-row {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-visual {
                order: -1;
            }
        }

        .hero-visual {
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeInRight 0.8s ease-out both;
        }

        .hero-visual-logo {
            width: 100%;
            max-width: 450px;
            height: auto;
            object-fit: contain;
            padding: 30px;
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.15), rgba(0, 212, 255, 0.08));
            border: 2px solid rgba(45, 125, 210, 0.3);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            filter: drop-shadow(0 20px 50px rgba(45, 125, 210, 0.25));
            animation: floatLogo 3s ease-in-out infinite;
            box-shadow: 0 8px 32px rgba(45, 125, 210, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

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

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-badge {
            display: inline-block;
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            color: #00d4ff;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 2.8rem);
            font-weight: 800;
            margin: 0.8rem 0;
            line-height: 1.2;
            background: linear-gradient(135deg, #fff 0%, #00d4ff 50%, #2d7dd2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .hero-desc {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.85);
            margin: 1rem 0 1.5rem 0;
            line-height: 1.7;
            max-width: 100%;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-primary-hero {
            background: linear-gradient(135deg, #2d7dd2, #00d4ff);
            border: none;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 12px 30px rgba(45, 125, 210, 0.3);
            cursor: pointer;
        }

        .btn-primary-hero:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(45, 125, 210, 0.4);
            color: #fff;
            text-decoration: none;
        }

        .btn-secondary-hero {
            background: transparent;
            border: 2px solid rgba(45, 125, 210, 0.5);
            color: #00d4ff;
            padding: 0.95rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-secondary-hero:hover {
            background: rgba(45, 125, 210, 0.1);
            border-color: #2d7dd2;
            transform: translateY(-5px);
            color: #00d4ff;
            text-decoration: none;
        }

        .hero-visual-box {
            display: none;
        }

        .visual-item {
            display: none;
        }

        .visual-icon {
            display: none;
        }

        .visual-content h4 {
            display: none;
        }

        .visual-content p {
            display: none;
        }

        /* ===== DIVIDER ===== */
        .section-divider {
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #00d4ff, transparent);
            margin: 0 auto 2rem;
            border-radius: 2px;
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            padding: 70px 20px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            text-align: center;
            margin-bottom: 0.3rem;
            background: linear-gradient(135deg, #fff 0%, #00d4ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (max-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.8rem;
            }
        }

        @media (max-width: 640px) {
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1.4rem;
            }
        }

        .feature-card {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.08), rgba(0, 212, 255, 0.06));
            border: 1.5px solid rgba(45, 125, 210, 0.25);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.15), transparent);
            transition: left 0.5s ease;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }
        .feature-card:nth-child(4) { animation-delay: 0.1s; }
        .feature-card:nth-child(5) { animation-delay: 0.2s; }
        .feature-card:nth-child(6) { animation-delay: 0.3s; }

        .feature-card:hover {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.18), rgba(0, 212, 255, 0.12));
            border-color: rgba(45, 125, 210, 0.5);
            transform: translateY(-12px);
            box-shadow: 0 24px 60px rgba(45, 125, 210, 0.3);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #2d7dd2, #00d4ff);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.2rem;
            box-shadow: 0 8px 20px rgba(45, 125, 210, 0.28);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.12) rotate(5deg);
            box-shadow: 0 12px 28px rgba(45, 125, 210, 0.4);
        }

        .feature-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: #fff;
            position: relative;
            z-index: 1;
            letter-spacing: -0.3px;
        }

        .feature-desc {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.65;
            font-size: 0.92rem;
            position: relative;
            z-index: 1;
            flex-grow: 1;
        }

        .feature-bullets {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-bullets li {
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .feature-bullets li:before {
            content: "•";
            color: #00d4ff;
            font-weight: bold;
            flex-shrink: 0;
        }

        .feature-bullets li:last-child {
            margin-bottom: 0;
        }

        /* ===== N8N CHATBOT SECTION ===== */
        .chatbot-section {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .chatbot-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .chatbot-content {
            animation: fadeInLeft 0.8s ease-out both;
        }

        .chatbot-visual {
            animation: fadeInRight 0.8s ease-out both;
        }

        .chatbot-badge {
            display: inline-block;
            background: rgba(81, 207, 102, 0.15);
            border: 1px solid rgba(81, 207, 102, 0.3);
            color: #51cf66;
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .chatbot-title {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #51cf66, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .chatbot-features {
            list-style: none;
            margin: 1.5rem 0;
        }

        .chatbot-features li {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            align-items: flex-start;
        }

        .chatbot-features i {
            color: #51cf66;
            font-size: 1.2rem;
            flex-shrink: 0;
            margin-top: 0.2rem;
        }

        .chatbot-features span {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
        }

        .chatbot-box {
            background: linear-gradient(135deg, rgba(81, 207, 102, 0.15), rgba(0, 212, 255, 0.08));
            border: 2px solid rgba(81, 207, 102, 0.3);
            border-radius: 16px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 16px 40px rgba(81, 207, 102, 0.15);
        }

        .chatbot-message {
            background: rgba(45, 125, 210, 0.15);
            border-left: 3px solid #2d7dd2;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .chatbot-message.bot {
            background: rgba(81, 207, 102, 0.15);
            border-left-color: #51cf66;
        }

        .ai-models {
            display: flex;
            gap: 0.8rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .model-badge {
            background: rgba(45, 125, 210, 0.2);
            border: 1px solid rgba(45, 125, 210, 0.4);
            color: #00d4ff;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .model-badge:hover {
            background: rgba(45, 125, 210, 0.4);
            border-color: #2d7dd2;
        }

        /* ===== STATS SECTION ===== */
        .stats-section {
            padding: 60px 20px;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-card {
            text-align: center;
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.1), rgba(0, 212, 255, 0.08));
            border: 1px solid rgba(45, 125, 210, 0.2);
            padding: 2rem;
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            border-color: rgba(45, 125, 210, 0.5);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #00d4ff;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            padding: 60px 20px;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.2), rgba(0, 212, 255, 0.12));
            border: 1px solid rgba(45, 125, 210, 0.3);
            border-radius: 20px;
            padding: 3rem 2rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 60px rgba(45, 125, 210, 0.2);
        }

        .cta-title {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta-desc {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
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

        .footer-grid {
            display: none;
        }

        .footer-col h4 {
            display: none;
        }

        .footer-col ul {
            display: none;
        }

        .footer-col ul li {
            display: none;
        }

        .footer-col ul li a {
            display: none;
        }

        .footer-bottom {
            text-align: center;
            padding: 0;
            border: none;
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.9rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero {
                min-height: auto;
                padding: 50px 20px 60px;
            }

            .hero-row {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-visual {
                order: -1;
                margin-bottom: 2rem;
            }

            .hero-visual-logo {
                max-width: 320px;
                padding: 20px;
                border-radius: 16px;
            }

            .hero-title {
                font-size: 1.6rem;
            }

            .hero-badge {
                padding: 0.4rem 1rem;
                font-size: 0.75rem;
            }

            .hero-desc {
                font-size: 0.9rem;
                margin: 0.8rem 0 1.2rem 0;
            }

            .hero-cta {
                justify-content: center;
            }

            .btn-primary-hero {
                padding: 0.9rem 1.8rem;
                font-size: 0.9rem;
                width: 100%;
            }

            .features {
                padding: 50px 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 1.2rem;
            }

            .section-title {
                font-size: 1.5rem;
                margin-bottom: 0.3rem;
            }

            .section-subtitle {
                font-size: 0.9rem;
                margin-bottom: 2rem;
            }

            .section-divider {
                width: 60px;
                height: 2px;
                margin: 0 auto 1.5rem;
            }

            .feature-card {
                padding: 1.5rem;
            }

            .feature-icon {
                width: 45px;
                height: 45px;
                font-size: 1.3rem;
                margin-bottom: 0.8rem;
            }

            .feature-title {
                font-size: 0.95rem;
            }

            .feature-desc {
                font-size: 0.85rem;
            }

            .navbar {
                padding: 0.8rem 1.5rem;
            }

            .navbar-brand img {
                height: 40px;
            }

            .navbar-brand span {
                font-size: 1.2rem;
            }

            .chatbot-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .chatbot-title {
                font-size: 1.6rem;
            }

            .chatbot-box {
                padding: 1.5rem;
            }

            .hero {
                padding: 40px 20px 60px;
            }
        }

        /* ===== FEATURE MODAL ===== */
        .feature-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }

        .feature-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            animation: fadeInOverlay 0.3s ease-out;
        }

        @keyframes fadeInOverlay {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .feature-modal-content {
            position: relative;
            z-index: 1001;
            background: linear-gradient(135deg, rgba(45, 125, 210, 0.15), rgba(0, 212, 255, 0.08));
            border: 1.5px solid rgba(45, 125, 210, 0.4);
            border-radius: 20px;
            padding: 2.5rem;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 0 0 40px rgba(45, 125, 210, 0.25);
            animation: modalScaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        @keyframes modalScaleIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .feature-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(45, 125, 210, 0.2);
        }

        .feature-modal-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #00d4ff;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .feature-modal-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 2rem;
            cursor: pointer;
            padding: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border-radius: 8px;
        }

        .feature-modal-close:hover {
            color: #00d4ff;
            background: rgba(0, 212, 255, 0.1);
        }

        .feature-modal-body {
            margin-bottom: 2rem;
        }

        .feature-modal-body p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
            line-height: 1.8;
            margin: 0;
        }

        .feature-modal-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(45, 125, 210, 0.2);
        }

        .feature-modal-btn-close {
            background: linear-gradient(135deg, #2d7dd2, #00d4ff);
            border: none;
            color: #fff;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(45, 125, 210, 0.2);
        }

        .feature-modal-btn-close:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(45, 125, 210, 0.35);
        }

        .feature-modal-btn-close:active {
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .feature-modal-content {
                padding: 2rem;
                max-width: 95%;
            }

            .feature-modal-header h2 {
                font-size: 1.4rem;
            }

            .feature-modal-body p {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
    </div>

    <div class="content-wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid px-4">
                <a class="navbar-brand" href="#home">
                    <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id">
                    <span>Hutch.id</span>
                </a>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="container-lg">
                <div class="hero-row">
                    <div class="hero-content" style="text-align: left;">
                        <h1 class="hero-title">Platform Manajemen Pesanan dan Produksi Internal Hutch.id</h1>
                        <p class="hero-desc">
                            Hutch.id merupakan aplikasi berbasis web yang dikembangkan khusus untuk mendukung kebutuhan operasional internal perusahaan. Sistem ini digunakan oleh staf dan pihak yang berwenang untuk mengelola proses bisnis, memantau informasi, serta meningkatkan kolaborasi antar bagian.
                        </p>
                        <div class="hero-cta">
                            <a href="{{ route('login') }}" class="btn btn-primary-hero">
                                <i class="fas fa-arrow-right me-2"></i>Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-secondary-hero">
                                <i class="fas fa-user-plus me-2"></i>Buat Akun
                            </a>
                        </div>
                    </div>
                    <div class="hero-visual">
                        <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id Logo" class="hero-visual-logo">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="section-divider"></div>
            <h2 class="section-title">Fitur Unggulan</h2>
            <p class="section-subtitle">Tools powerful yang dirancang untuk kebutuhan bisnis modern Anda</p>
            
            <div class="features-grid">
                <div class="feature-card" data-modal="0" style="cursor: pointer;">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="feature-title">Manajemen Pesanan</h3>
                    <ul class="feature-desc feature-bullets">
                        <li>Pembuatan PO otomatis</li>
                        <li>Cetak dokumen PDF</li>
                        <li>Pelacakan status pesanan</li>
                    </ul>
                </div>

                <div class="feature-card" data-modal="1" style="cursor: pointer;">
                    <div class="feature-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="feature-title">Inventori Pintar</h3>
                    <ul class="feature-desc feature-bullets">
                        <li>Verifikasi bahan baku otomatis</li>
                        <li>Monitoring stok real-time</li>
                        <li>Notifikasi stok menipis</li>
                    </ul>
                </div>

                <div class="feature-card" data-modal="2" style="cursor: pointer;">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Manajemen Pelanggan</h3>
                    <ul class="feature-desc feature-bullets">
                        <li>CRUD data pelanggan</li>
                        <li>Pencarian otomatis</li>
                        <li>Riwayat pemesanan tersimpan</li>
                    </ul>
                </div>

                <div class="feature-card" data-modal="3" style="cursor: pointer;">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Dashboard Analitik</h3>
                    <ul class="feature-desc feature-bullets">
                        <li>Ringkasan pesanan aktif</li>
                        <li>Monitoring status produksi</li>
                        <li>Data diperbarui real-time</li>
                    </ul>
                </div>

                <div class="feature-card" data-modal="4" style="cursor: pointer;">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3 class="feature-title">Asisten AI N8N</h3>
                    <ul class="feature-desc feature-bullets">
                        <li>Workflow otomatis</li>
                        <li>Pencarian informasi sistem</li>
                        <li>Dukungan notifikasi pintar</li>
                    </ul>
                </div>

                <div class="feature-card" data-modal="5" style="cursor: pointer;">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Keamanan Enterprise</h3>
                    <ul class="feature-desc feature-bullets">
                        <li>RBAC 4 tingkat pengguna</li>
                        <li>Audit trail aktivitas</li>
                        <li>Tautan PDF terbatas waktu</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Feature Modal -->
        <div id="featureModal" class="feature-modal">
            <div class="feature-modal-overlay"></div>
            <div class="feature-modal-content">
                <div class="feature-modal-header">
                    <h2 id="modalTitle"></h2>
                    <button class="feature-modal-close" id="modalCloseBtn">&times;</button>
                </div>
                <div class="feature-modal-body">
                    <p id="modalDesc"></p>
                </div>
                <div class="feature-modal-footer">
                    <button class="feature-modal-btn-close" id="modalFooterCloseBtn">Tutup</button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Tentang Hutch</h4>
                    <ul>
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Produk</h4>
                    <ul>
                        <li><a href="#">Order Management</a></li>
                        <li><a href="#">Inventory System</a></li>
                        <li><a href="#">ChatBot AI</a></li>
                        <li><a href="#">Analytics</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Dukungan</h4>
                    <ul>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Hutch.id.</p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal data array
        const modalData = [
            {
                title: 'Manajemen Pesanan',
                description: 'Modul ini memungkinkan staf penjualan membuat PO baru dengan nomor otomatis, menambahkan beberapa produk dalam satu pesanan, serta melihat dan mencetak dokumen PO dalam format PDF. Harga produk akan dikunci saat pesanan disimpan untuk menjaga konsistensi data.'
            },
            {
                title: 'Inventori Pintar',
                description: 'Sistem secara otomatis memverifikasi stok bahan baku berdasarkan BOM produk ketika PO dibuat. Pengguna dapat melihat jumlah stok tersedia, kebutuhan produksi, dan selisih kekurangan, serta menerima notifikasi saat persediaan menipis.'
            },
            {
                title: 'Manajemen Pelanggan',
                description: 'Menyediakan fitur CRUD data pelanggan lengkap dengan pencarian otomatis saat membuat PO. Riwayat data pelanggan tersimpan dengan baik sehingga memudahkan pengelolaan pesanan berulang dan menjaga akurasi informasi pelanggan.'
            },
            {
                title: 'Dashboard Analitik',
                description: 'Dashboard menampilkan ringkasan jumlah PO aktif, pesanan yang menunggu konfirmasi, status produksi, serta pesanan yang siap dikirim. Informasi diperbarui secara real-time untuk membantu pengambilan keputusan operasional.'
            },
            {
                title: 'Asisten AI N8N',
                description: 'Terintegrasi dengan workflow N8N untuk menjalankan proses otomatis, membantu pencarian informasi sistem, serta mendukung pengembangan fitur notifikasi dan automasi operasional agar pekerjaan menjadi lebih efisien.'
            },
            {
                title: 'Keamanan Enterprise',
                description: 'Sistem menerapkan Role-Based Access Control (RBAC) dengan empat tingkat pengguna, audit trail perubahan status pesanan, autentikasi berbasis sesi, serta tautan berbagi dokumen PDF yang memiliki masa berlaku terbatas untuk menjaga keamanan informasi.'
            }
        ];

        // Modal functionality
        const featureModal = document.getElementById('featureModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalDesc = document.getElementById('modalDesc');
        const modalCloseBtn = document.getElementById('modalCloseBtn');
        const modalFooterCloseBtn = document.getElementById('modalFooterCloseBtn');
        const modalOverlay = document.querySelector('.feature-modal-overlay');

        // Open modal
        function openModal(index) {
            if (modalData[index]) {
                modalTitle.textContent = modalData[index].title;
                modalDesc.textContent = modalData[index].description;
                featureModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        // Close modal
        function closeModal() {
            featureModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Add click event to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('click', () => {
                const modalIndex = card.getAttribute('data-modal');
                openModal(parseInt(modalIndex));
            });
        });

        // Close button click
        modalCloseBtn.addEventListener('click', closeModal);
        modalFooterCloseBtn.addEventListener('click', closeModal);

        // Close on overlay click
        modalOverlay.addEventListener('click', closeModal);

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && featureModal.classList.contains('active')) {
                closeModal();
            }
        });

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll animation for cards
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .stat-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
            observer.observe(el);
        });

        // Add active state to navbar links on scroll
        window.addEventListener('scroll', () => {
            let current = '';
            document.querySelectorAll('section').forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').slice(1) === current) {
                    link.classList.add('active');
                }
            });
        });

        // Navbar collapse on link click (mobile)
        const navLinks = document.querySelectorAll('.navbar-collapse .nav-link');
        const navToggle = document.querySelector('.navbar-toggler');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    navToggle.click();
                }
            });
        });
    </script>
</body>
</html>