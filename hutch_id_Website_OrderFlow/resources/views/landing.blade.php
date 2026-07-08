<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0a0e14">
    <title>Hutch.id - Platform Order Flow & AI Chatbot untuk UMKM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        :root{--primary:#2d7dd2;--secondary:#00d4ff;--dark:#0f1419;--darker:#0a0e14}
        html,body{width:100%;overflow-x:hidden;background:var(--dark);color:#fff;scroll-behavior:smooth}
        body{font-family:'Plus Jakarta Sans',sans-serif}

        /* BG */
        .animated-bg{position:fixed;width:100%;height:100%;top:0;left:0;background:linear-gradient(135deg,#0a0e14 0%,#0f1f3c 25%,#1a3a5c 50%,#0d1f3a 75%,#050a12 100%);background-size:400% 400%;animation:gradientShift 20s ease infinite;z-index:0}
        @keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        @media(max-width:768px){.animated-bg{animation:none;background-position:0% 50%}}
        .content-wrapper{position:relative;z-index:1}

        /* NAVBAR — hanya logo + nama + nav links, tanpa tombol di kanan */
        .navbar{background:rgba(15,20,30,.9)!important;backdrop-filter:blur(20px);border-bottom:1px solid rgba(45,125,210,.15);padding:1rem 2.5rem;padding-top:calc(1rem + env(safe-area-inset-top));position:sticky;top:0;z-index:100;box-shadow:0 8px 32px rgba(0,0,0,.3)}
        .navbar-brand{display:flex;align-items:center;gap:1rem}
        .navbar-brand img{height:42px;width:auto;object-fit:contain}
        .navbar-brand span{font-size:1.35rem;font-weight:800;background:linear-gradient(135deg,#00d4ff,#2d7dd2);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-.5px}
        .nav-links-desk{display:flex;align-items:center;gap:1.8rem;margin-left:auto}
        .nav-links-desk a{color:rgba(255,255,255,.8);font-weight:500;font-size:.9rem;text-decoration:none;transition:color .25s}
        .nav-links-desk a:hover{color:#00d4ff}
        @media(max-width:768px){.nav-links-desk{display:none}}

        /* HERO */
        .hero{min-height:65vh;display:flex;align-items:center;justify-content:center;padding:60px 20px 80px;position:relative;z-index:1}
        .hero-row{display:grid;grid-template-columns:1fr 1.1fr;gap:4rem;align-items:center;max-width:1300px;margin:0 auto;width:100%}
        @media(max-width:768px){.hero-row{grid-template-columns:1fr;gap:2rem}.hero-visual{order:-1}}
        .hero-visual{display:flex;justify-content:center;align-items:center}
        .hero-visual-logo{width:100%;max-width:380px;height:auto;object-fit:contain;padding:28px;background:linear-gradient(135deg,rgba(45,125,210,.15),rgba(0,212,255,.08));border:2px solid rgba(45,125,210,.3);border-radius:20px;backdrop-filter:blur(10px);filter:drop-shadow(0 20px 50px rgba(45,125,210,.25));animation:floatLogo 3s ease-in-out infinite;box-shadow:0 8px 32px rgba(45,125,210,.2)}
        @keyframes floatLogo{0%,100%{transform:translateY(0)}50%{transform:translateY(-18px)}}
        @media(max-width:768px){.hero-visual-logo{max-width:260px;padding:20px;animation:none}}
        @keyframes fadeInLeft{from{opacity:0;transform:translateX(-50px)}to{opacity:1;transform:translateX(0)}}
        @keyframes fadeInRight{from{opacity:0;transform:translateX(50px)}to{opacity:1;transform:translateX(0)}}
        .hero-content{animation:fadeInLeft .8s ease-out both}
        .hero-visual{animation:fadeInRight .8s ease-out both}
        .hero-badge{display:inline-block;background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.3);color:#00d4ff;padding:.5rem 1.2rem;border-radius:50px;font-size:.8rem;font-weight:700;margin-bottom:1.2rem}
        .hero-title{font-size:clamp(1.8rem,4.5vw,2.8rem);font-weight:800;margin:.8rem 0;line-height:1.2;background:linear-gradient(135deg,#fff 0%,#00d4ff 50%,#2d7dd2 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-.5px}
        .hero-desc{font-size:.95rem;color:rgba(255,255,255,.85);margin:1rem 0 1.5rem;line-height:1.7}
        .hero-cta{display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.5rem}
        @media(max-width:768px){.hero-cta{flex-direction:column}.hero-cta .btn-primary-hero,.hero-cta .btn-download-apk{width:100%;justify-content:center}}
        .btn-primary-hero{background:linear-gradient(135deg,#2d7dd2,#00d4ff);border:none;color:#fff;padding:1rem 2rem;border-radius:12px;font-weight:700;font-size:1rem;box-shadow:0 12px 30px rgba(45,125,210,.3);display:inline-flex;align-items:center;gap:.5rem;text-decoration:none;transition:all .3s}
        .btn-primary-hero:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(45,125,210,.4);color:#fff;text-decoration:none}
        .btn-download-apk{display:inline-flex;align-items:center;gap:.6rem;background:transparent;border:1.5px solid rgba(81,207,102,.55);color:#51cf66;padding:.95rem 2rem;border-radius:12px;font-weight:700;font-size:1rem;text-decoration:none;transition:all .3s;white-space:nowrap}
        .btn-download-apk:hover{border-color:#51cf66;color:#51cf66;transform:translateY(-4px);box-shadow:0 12px 28px rgba(81,207,102,.25);text-decoration:none}
        .hero-apk-hint{margin-top:1rem;font-size:.8rem;color:rgba(255,255,255,.45);display:flex;align-items:center;gap:.4rem}
        .hero-apk-hint i{color:#51cf66}

        /* DIVIDER */
        .section-divider{width:80px;height:3px;background:linear-gradient(90deg,transparent,#00d4ff,transparent);margin:0 auto 2rem;border-radius:2px;box-shadow:0 0 20px rgba(0,212,255,.3)}
        .section-separator{width:100%;max-width:1200px;margin:0 auto;height:1px;background:linear-gradient(90deg,transparent,rgba(45,125,210,.2),rgba(0,212,255,.15),rgba(45,125,210,.2),transparent);position:relative;z-index:1}

        /* COMMON SECTION */
        .section-title{font-size:clamp(1.8rem,4vw,2.5rem);font-weight:800;text-align:center;margin-bottom:.3rem;background:linear-gradient(135deg,#fff 0%,#00d4ff 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;letter-spacing:-.5px}
        .section-subtitle{text-align:center;color:rgba(255,255,255,.7);font-size:.95rem;margin-bottom:2.5rem;max-width:600px;margin-left:auto;margin-right:auto;line-height:1.6}
        .section-badge{display:inline-flex;align-items:center;gap:.5rem;background:rgba(45,125,210,.12);border:1px solid rgba(45,125,210,.3);color:#00d4ff;padding:.45rem 1.1rem;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.4px;text-transform:uppercase;margin-bottom:1.2rem}

        /* FEATURES */
        .features{padding:70px 20px;max-width:1400px;margin:0 auto;position:relative;z-index:1}
        .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;max-width:1400px;margin:0 auto}
        @media(max-width:1024px){.features-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:640px){.features-grid{grid-template-columns:1fr;gap:1.2rem}}
        .feature-card{background:linear-gradient(135deg,rgba(45,125,210,.08),rgba(0,212,255,.06));border:1.5px solid rgba(45,125,210,.25);border-radius:16px;padding:2rem;transition:all .4s cubic-bezier(.34,1.56,.64,1);backdrop-filter:blur(10px);position:relative;overflow:hidden;height:100%;display:flex;flex-direction:column}
        .feature-card:hover{background:linear-gradient(135deg,rgba(45,125,210,.18),rgba(0,212,255,.12));border-color:rgba(45,125,210,.5);transform:translateY(-10px);box-shadow:0 24px 60px rgba(45,125,210,.3)}
        .feature-icon{width:56px;height:56px;background:linear-gradient(135deg,#2d7dd2,#00d4ff);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.6rem;margin-bottom:1.2rem;box-shadow:0 8px 20px rgba(45,125,210,.28);transition:all .3s;position:relative;z-index:1}
        .feature-card:hover .feature-icon{transform:scale(1.1) rotate(5deg)}
        .feature-title{font-size:1.05rem;font-weight:700;margin-bottom:.8rem;color:#fff;position:relative;z-index:1}
        .feature-desc{color:rgba(255,255,255,.8);line-height:1.65;font-size:.92rem;position:relative;z-index:1;flex-grow:1}
        .feature-detail-link{display:inline-flex;align-items:center;gap:.5rem;color:#00d4ff;font-weight:700;font-size:.92rem;margin-top:auto;position:relative;z-index:1;text-decoration:none;transition:gap .25s}
        .feature-card:hover .feature-detail-link{gap:.75rem}
        @media(max-width:768px){.feature-card{padding:1.5rem;backdrop-filter:none}.feature-card:hover{transform:none}}

        /* INFO SECTIONS */
        .info-section{padding:80px 20px;max-width:1200px;margin:0 auto;position:relative;z-index:1}
        @media(max-width:768px){.info-section{padding:50px 20px}}

        /* TUJUAN */
        .tujuan-grid{display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:center}
        @media(max-width:768px){.tujuan-grid{grid-template-columns:1fr;gap:2rem}}
        .tujuan-icon-block{background:linear-gradient(135deg,rgba(45,125,210,.15),rgba(0,212,255,.08));border:1.5px solid rgba(45,125,210,.28);border-radius:24px;padding:2.5rem;display:flex;flex-direction:column;gap:1.2rem}
        .tujuan-icon-row{display:flex;align-items:center;gap:1.2rem}
        .tujuan-dot{width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#2d7dd2,#00d4ff);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;box-shadow:0 6px 16px rgba(45,125,210,.3)}
        .tujuan-dot-text{color:rgba(255,255,255,.85);font-size:.9rem;line-height:1.6;font-weight:500}
        .tujuan-text p{color:rgba(255,255,255,.78);line-height:1.85;font-size:.97rem;margin-top:1rem}

        /* TARGET */
        .target-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.8rem;margin-top:2.5rem}
        @media(max-width:768px){.target-grid{grid-template-columns:1fr;gap:1.2rem}}
        .target-card{background:linear-gradient(145deg,rgba(45,125,210,.1),rgba(0,212,255,.06));border:1.5px solid rgba(45,125,210,.22);border-radius:18px;padding:2rem 1.5rem;text-align:center;transition:all .35s;position:relative;overflow:hidden}
        .target-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,#2d7dd2,#00d4ff)}
        .target-card:hover{transform:translateY(-8px);border-color:rgba(45,125,210,.5);box-shadow:0 20px 50px rgba(45,125,210,.25)}
        .target-icon{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,rgba(45,125,210,.2),rgba(0,212,255,.12));border:1.5px solid rgba(0,212,255,.25);display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin:0 auto 1.2rem}
        .target-title{font-size:1.05rem;font-weight:700;color:#fff;margin-bottom:.7rem}
        .target-desc{font-size:.88rem;color:rgba(255,255,255,.72);line-height:1.65}

        /* STUDI KASUS */
        .studikasus-box{background:linear-gradient(135deg,rgba(45,125,210,.1),rgba(0,212,255,.06));border:1.5px solid rgba(45,125,210,.25);border-radius:24px;padding:3rem;margin-top:2.5rem;position:relative;overflow:hidden}
        .studikasus-box::before{content:'';position:absolute;top:0;left:0;width:4px;height:100%;background:linear-gradient(180deg,#2d7dd2,#00d4ff);border-radius:4px 0 0 4px}
        @media(max-width:768px){.studikasus-box{padding:1.8rem 1.4rem}}
        .studikasus-inner{display:grid;grid-template-columns:1fr 1fr;gap:2.5rem}
        @media(max-width:768px){.studikasus-inner{grid-template-columns:1fr;gap:1.8rem}}
        .studikasus-col h4{font-size:.78rem;font-weight:700;color:#00d4ff;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:.8rem}
        .studikasus-col p{color:rgba(255,255,255,.8);font-size:.95rem;line-height:1.8}
        .studikasus-quote{margin-top:2rem;padding-top:2rem;border-top:1px solid rgba(45,125,210,.2)}
        .studikasus-quote p{color:rgba(255,255,255,.85);font-size:.97rem;line-height:1.85;font-style:italic}
        .studikasus-meta{display:flex;gap:1rem;margin-top:1.8rem;flex-wrap:wrap}
        .studikasus-tag{background:rgba(0,212,255,.08);border:1px solid rgba(0,212,255,.2);color:#00d4ff;padding:.4rem 1rem;border-radius:50px;font-size:.8rem;font-weight:600}

        /* KEUNGGULAN */
        .keunggulan-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1.8rem;margin-top:2.5rem}
        @media(max-width:640px){.keunggulan-grid{grid-template-columns:1fr}}
        .keunggulan-card{display:flex;gap:1.4rem;align-items:flex-start;background:linear-gradient(135deg,rgba(45,125,210,.09),rgba(0,212,255,.05));border:1.5px solid rgba(45,125,210,.2);border-radius:18px;padding:1.8rem;transition:all .3s}
        .keunggulan-card:hover{border-color:rgba(45,125,210,.45);transform:translateY(-5px);box-shadow:0 16px 40px rgba(45,125,210,.2)}
        .keunggulan-num{width:50px;height:50px;border-radius:14px;background:linear-gradient(135deg,#2d7dd2,#00d4ff);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
        .keunggulan-body h4{font-size:1.05rem;font-weight:700;color:#fff;margin-bottom:.5rem}
        .keunggulan-body p{font-size:.9rem;color:rgba(255,255,255,.75);line-height:1.65}
        @media(max-width:768px){.keunggulan-card:hover{transform:none}}

        /* ABOUT US */
        .aboutus-box{max-width:820px;margin:2rem auto 0;text-align:center}
        .aboutus-box p{color:rgba(255,255,255,.8);font-size:1rem;line-height:1.9;margin-bottom:1.2rem}
        .aboutus-pills{display:flex;flex-wrap:wrap;gap:.8rem;justify-content:center;margin-top:2rem}
        .aboutus-pill{background:rgba(45,125,210,.12);border:1px solid rgba(45,125,210,.28);color:rgba(255,255,255,.85);padding:.5rem 1.2rem;border-radius:50px;font-size:.85rem;font-weight:600}

        /* TEAM */
        .about-section{padding:70px 20px 90px;max-width:1300px;margin:0 auto;position:relative;z-index:1}
        .about-intro{max-width:760px;margin:0 auto 4rem;text-align:center}
        .about-intro p{color:rgba(255,255,255,.78);font-size:1rem;line-height:1.8;margin-top:.6rem}

        .team-grid{display:flex;flex-wrap:wrap;justify-content:center;gap:2.6rem 2rem;max-width:1320px;margin:0 auto}
        .team-card{display:flex;flex-direction:column;align-items:center;text-align:center;flex:0 1 220px;max-width:220px}
        /* Zig-zag stagger */
        .team-card:nth-child(odd){margin-top:0}
        .team-card:nth-child(even){margin-top:3rem}
        @media(max-width:768px){
            .team-grid{gap:2rem 1.2rem}
            .team-card{flex:0 1 calc(50% - .6rem);max-width:calc(50% - .6rem)}
            .team-card:nth-child(even){margin-top:2.4rem}
        }
        @media(max-width:400px){
            .team-card{flex:0 1 calc(50% - .5rem);max-width:calc(50% - .5rem)}
        }

        /* Pill frame */
        .team-pill{position:relative;width:100%;aspect-ratio:3/4;border-radius:120px;overflow:hidden;background:linear-gradient(160deg,rgba(45,125,210,.3),rgba(0,212,255,.12));border:1px solid rgba(0,212,255,.2);box-shadow:0 16px 36px rgba(0,0,0,.35),0 0 0 1px rgba(255,255,255,.03) inset;transition:all .4s cubic-bezier(.34,1.56,.64,1)}
        .team-card:hover .team-pill{transform:translateY(-8px);border-color:rgba(0,212,255,.55);box-shadow:0 26px 50px rgba(45,125,210,.35)}
        .team-pill img{width:100%;height:100%;object-fit:cover;display:block;filter:saturate(1.05) contrast(1.02);transition:transform .5s}
        .team-card:hover .team-pill img{transform:scale(1.06)}
        .team-pill::after{content:'';position:absolute;inset:0;border-radius:120px;background:linear-gradient(180deg,rgba(8,16,30,0) 62%,rgba(6,13,26,.88) 100%);pointer-events:none}
        /* Fallback avatar */
        .team-pill .photo-fallback{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(160deg,rgba(45,125,210,.35),rgba(0,212,255,.15))}
        .team-pill .photo-fallback span{font-size:2.2rem;font-weight:900;color:rgba(255,255,255,.6)}
        @media(max-width:768px){.team-card:hover .team-pill{transform:none}.team-card:hover .team-pill img{transform:none}}

        .team-info{margin-top:1.1rem}
        .team-name{font-size:1rem;font-weight:700;color:#fff;margin-bottom:.4rem;line-height:1.3}
        @media(max-width:768px){.team-name{font-size:.9rem}}
        .team-role{display:inline-block;font-size:.75rem;color:#00d4ff;font-weight:700;text-transform:uppercase;letter-spacing:.4px;background:rgba(0,212,255,.1);border:1px solid rgba(0,212,255,.22);border-radius:50px;padding:.28rem .85rem;margin-bottom:.9rem;line-height:1.4}
        .team-socials{display:flex;justify-content:center;gap:.6rem}
        .team-socials a{width:36px;height:36px;border-radius:10px;background:rgba(45,125,210,.1);border:1px solid rgba(45,125,210,.25);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.7);font-size:.9rem;transition:all .25s;text-decoration:none}
        .team-socials a:hover{background:linear-gradient(135deg,#2d7dd2,#00d4ff);color:#fff;border-color:transparent;transform:translateY(-3px);box-shadow:0 8px 18px rgba(0,212,255,.35)}
        @media(max-width:768px){.team-socials a:hover{transform:none}}

        /* FOOTER */
        .footer{padding:36px 20px;padding-bottom:calc(36px + env(safe-area-inset-bottom));max-width:1400px;margin:0 auto;position:relative;z-index:1;text-align:center;border-top:1px solid rgba(45,125,210,.15)}
        .footer-bottom{color:rgba(255,255,255,.55);font-size:.88rem}

        /* SPLASH */
        #splash{position:fixed;inset:0;z-index:9999;background:#050a12;display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden;will-change:opacity,transform}
        #splash::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(0,212,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(0,212,255,.045) 1px,transparent 1px);background-size:60px 60px;animation:gridDrift 8s linear infinite;pointer-events:none}
        @keyframes gridDrift{from{background-position:0 0,0 0}to{background-position:0 60px,60px 0}}
        #splash::after{content:'';position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(45,125,210,.22) 0%,transparent 70%);animation:glowPulse 3s ease-in-out infinite;pointer-events:none}
        @keyframes glowPulse{0%,100%{transform:scale(1);opacity:.7}50%{transform:scale(1.2);opacity:1}}
        .splash-ring{position:absolute;border-radius:50%;border:1px solid rgba(0,212,255,.18);animation:ringExpand 3.2s ease-out infinite;pointer-events:none}
        .splash-ring:nth-child(1){width:200px;height:200px;animation-delay:0s}
        .splash-ring:nth-child(2){width:340px;height:340px;animation-delay:.6s}
        .splash-ring:nth-child(3){width:500px;height:500px;animation-delay:1.2s}
        @keyframes ringExpand{0%{opacity:.7;transform:scale(.6)}100%{opacity:0;transform:scale(1.1)}}
        .splash-logo-wrap{position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;gap:0;animation:splashLogoIn .9s cubic-bezier(.34,1.56,.64,1) both;animation-delay:.3s}
        @keyframes splashLogoIn{from{opacity:0;transform:scale(.6) translateY(30px)}to{opacity:1;transform:scale(1) translateY(0)}}
        .splash-logo-ring{position:relative;width:130px;height:130px;display:flex;align-items:center;justify-content:center}
        .splash-logo-ring::before{content:'';position:absolute;inset:-4px;border-radius:50%;border:2px solid transparent;border-top-color:#00d4ff;border-right-color:rgba(0,212,255,.4);animation:spinArc 1.4s linear infinite}
        .splash-logo-ring::after{content:'';position:absolute;inset:4px;border-radius:50%;border:1px solid transparent;border-bottom-color:rgba(45,125,210,.5);border-left-color:rgba(45,125,210,.25);animation:spinArc 2.1s linear infinite reverse}
        @keyframes spinArc{to{transform:rotate(360deg)}}
        .splash-logo-inner{width:110px;height:110px;border-radius:50%;background:radial-gradient(circle at 40% 35%,rgba(45,125,210,.35),rgba(0,0,0,.6));border:1px solid rgba(0,212,255,.2);display:flex;align-items:center;justify-content:center;box-shadow:0 0 40px rgba(0,212,255,.2),inset 0 0 30px rgba(45,125,210,.15)}
        .splash-logo-inner img{width:64px;height:64px;object-fit:contain;filter:drop-shadow(0 0 12px rgba(0,212,255,.6));animation:logoBreathe 3s ease-in-out infinite;animation-delay:1.2s}
        @keyframes logoBreathe{0%,100%{filter:drop-shadow(0 0 12px rgba(0,212,255,.6))}50%{filter:drop-shadow(0 0 24px rgba(0,212,255,1))}}
        .splash-brand{margin-top:28px;text-align:center;animation:splashTextIn .8s ease-out both;animation-delay:.7s}
        @keyframes splashTextIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .splash-brand-name{font-size:clamp(1.6rem,5vw,2.2rem);font-weight:800;letter-spacing:-.5px;background:linear-gradient(135deg,#fff 0%,#00d4ff 55%,#2d7dd2 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1.1}
        .splash-brand-sub{margin-top:6px;font-size:.8rem;font-weight:600;color:rgba(0,212,255,.7);letter-spacing:2.5px;text-transform:uppercase}
        .splash-progress-wrap{position:absolute;bottom:14%;left:50%;transform:translateX(-50%);width:min(320px,70vw);z-index:2;animation:splashTextIn .8s ease-out both;animation-delay:1s}
        .splash-progress-label{font-size:.72rem;font-weight:600;color:rgba(255,255,255,.4);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:10px;text-align:center}
        .splash-progress-track{height:2px;background:rgba(255,255,255,.08);border-radius:2px;overflow:hidden}
        .splash-progress-bar{height:100%;width:0%;border-radius:2px;background:linear-gradient(90deg,#2d7dd2,#00d4ff);box-shadow:0 0 8px rgba(0,212,255,.6);transition:width .1s linear}
        .splash-dots{position:absolute;bottom:calc(14% - 28px);left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:2}
        .splash-dot{width:5px;height:5px;border-radius:50%;background:rgba(0,212,255,.25);animation:dotPop 1.2s ease-in-out infinite}
        .splash-dot:nth-child(1){animation-delay:0s}.splash-dot:nth-child(2){animation-delay:.2s}.splash-dot:nth-child(3){animation-delay:.4s}
        @keyframes dotPop{0%,100%{background:rgba(0,212,255,.25);transform:scale(1)}50%{background:#00d4ff;transform:scale(1.6)}}
        #splash.splash-exit{animation:splashExit .9s cubic-bezier(.4,0,.2,1) forwards}
        @keyframes splashExit{0%{opacity:1;transform:scale(1)}40%{opacity:1;transform:scale(1.04)}100%{opacity:0;transform:scale(1.08);pointer-events:none}}
        body.splash-active{overflow:hidden}
        .content-wrapper,.animated-bg{opacity:0;transition:opacity .7s ease .1s}
        .content-wrapper.splash-done,.animated-bg.splash-done{opacity:1}

        @media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important;scroll-behavior:auto!important}}
    </style>
</head>
<body class="splash-active">

<!-- SPLASH -->
<div id="splash" role="status" aria-label="Memuat Hutch.id…">
    <div class="splash-ring"></div>
    <div class="splash-ring"></div>
    <div class="splash-ring"></div>
    <div class="splash-logo-wrap">
        <div class="splash-logo-ring">
            <div class="splash-logo-inner">
                <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id" width="64" height="64" loading="eager">
            </div>
        </div>
        <div class="splash-brand">
            <div class="splash-brand-name">Hutch.id</div>
            <div class="splash-brand-sub">Order Flow Platform</div>
        </div>
    </div>
    <div class="splash-progress-wrap">
        <div class="splash-progress-label">Memuat sistem</div>
        <div class="splash-progress-track"><div class="splash-progress-bar" id="splashBar"></div></div>
    </div>
    <div class="splash-dots">
        <div class="splash-dot"></div>
        <div class="splash-dot"></div>
        <div class="splash-dot"></div>
    </div>
</div>

<div class="animated-bg"></div>

<div class="content-wrapper">

    <!-- ═══ NAVBAR — tanpa tombol Login/APK di kanan ═══ -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-3 px-md-4">
            <a class="navbar-brand" href="#home">
                <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id" loading="eager">
                <span>Hutch.id</span>
            </a>
            <!-- Nav links desktop only -->
            <div class="nav-links-desk">
                <a href="#features">Fitur</a>
                <a href="#tujuan">Tujuan</a>
                <a href="#about-us">Tentang</a>
                <a href="#about">Tim</a>
            </div>
        </div>
    </nav>

    <!-- ═══ HERO ═══ -->
    <section class="hero" id="home">
        <div class="container-lg px-3">
            <div class="hero-row">
                <div class="hero-content">
                    <span class="hero-badge"> Platform Manajemen Pesanan</span>
                    <h1 class="hero-title">Platform Manajemen Pesanan dan Produksi Internal Hutch.id</h1>
                    <p class="hero-desc">
                        Hutch.id merupakan aplikasi berbasis web yang dikembangkan khusus untuk mendukung kebutuhan operasional internal perusahaan. Sistem ini digunakan oleh staf dan pihak yang berwenang untuk mengelola proses bisnis, memantau informasi, serta meningkatkan kolaborasi antar bagian.
                    </p>
                    <div class="hero-cta">
                        <a href="{{ route('login') }}" class="btn-primary-hero">
                            <i class="fas fa-arrow-right"></i> Login
                        </a>
                        <a href="{{ asset('downloads/Hutch-mobile-v1.0.2.apk') }}" download="Hutch-mobile.apk" class="btn-download-apk">
                            <i class="fas fa-mobile-alt"></i> Unduh APK
                            <span style="background:rgba(81,207,102,.18);border:1px solid rgba(81,207,102,.35);border-radius:4px;font-size:.65rem;font-weight:800;padding:1px 5px;">APK</span>
                        </a>
                    </div>
                    <div class="hero-apk-hint">
                        <i class="fab fa-android"></i> Tersedia untuk Android &nbsp;·&nbsp; Gratis
                    </div>
                </div>
                <div class="hero-visual">
                    <img src="{{ asset('images/hutch-logo.png') }}" alt="Hutch.id Logo" class="hero-visual-logo" loading="eager">
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ FEATURES ═══ -->
    <section class="features" id="features">
        <div class="section-divider"></div>
        <h2 class="section-title">Fitur Unggulan</h2>
        <p class="section-subtitle">Tools powerful yang dirancang untuk kebutuhan bisnis modern Anda</p>
        <div class="features-grid">
            <a href="{{ route('feature.show','manajemen-pesanan') }}" class="feature-card text-decoration-none text-reset">
                <div class="feature-icon"><i class="fas fa-shopping-cart"></i></div>
                <h3 class="feature-title">Manajemen Pesanan</h3>
                <span class="feature-detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="{{ route('feature.show','inventori-pintar') }}" class="feature-card text-decoration-none text-reset">
                <div class="feature-icon"><i class="fas fa-boxes"></i></div>
                <h3 class="feature-title">Inventori Pintar</h3>
                <span class="feature-detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="{{ route('feature.show','manajemen-pelanggan') }}" class="feature-card text-decoration-none text-reset">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3 class="feature-title">Manajemen Pelanggan</h3>
                <span class="feature-detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="{{ route('feature.show','dashboard-analitik') }}" class="feature-card text-decoration-none text-reset">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3 class="feature-title">Dashboard Analitik</h3>
                <span class="feature-detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="{{ route('feature.show','asisten-ai') }}" class="feature-card text-decoration-none text-reset">
                <div class="feature-icon"><i class="fas fa-robot"></i></div>
                <h3 class="feature-title">Asisten AI</h3>
                <span class="feature-detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="{{ route('feature.show','keamanan-enterprise') }}" class="feature-card text-decoration-none text-reset">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3 class="feature-title">Keamanan Enterprise</h3>
                <span class="feature-detail-link">Lihat Detail <i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </section>

    <!-- ═══ TUJUAN ═══ -->
    <div class="section-separator"></div>
    <section class="info-section" id="tujuan">
        <div class="section-divider"></div>
        <div class="tujuan-grid">
            <div class="tujuan-text">
                <span class="section-badge"><i class="fas fa-bullseye"></i> Tujuan Dibangun</span>
                <h2 class="section-title" style="text-align:left;margin-bottom:0">Mengapa Sistem Ini Dibuat?</h2>
                <p>Sistem ini hadir untuk <strong>mengatasi proses operasional yang masih dilakukan secara manual</strong>. Dengan menghadirkan platform terpusat, seluruh aktivitas pengelolaan data, pembagian tugas, pemantauan pekerjaan, dan penyimpanan informasi dapat berjalan lebih efisien dan terintegrasi.</p>
                <p>Tidak lagi ada fragmentasi antar divisi, tidak ada redundansi data, dan tidak ada hambatan komunikasi — semua terhubung, semua real-time.</p>
            </div>
            <div class="tujuan-visual">
                <div class="tujuan-icon-block">
                    <div class="tujuan-icon-row">
                        <div class="tujuan-dot"><i class="fas fa-ban" style="color:#fff;font-size:1rem"></i></div>
                        <p class="tujuan-dot-text">Proses manual yang lambat &amp; rawan kesalahan</p>
                    </div>
                    <div style="text-align:center;font-size:1.5rem;color:rgba(0,212,255,.5);padding:.3rem 0">↓</div>
                    <div class="tujuan-icon-row">
                        <div class="tujuan-dot"><i class="fas fa-layer-group" style="color:#fff;font-size:1rem"></i></div>
                        <p class="tujuan-dot-text">Sistem terpusat &amp; terintegrasi untuk semua divisi</p>
                    </div>
                    <div style="text-align:center;font-size:1.5rem;color:rgba(0,212,255,.5);padding:.3rem 0">↓</div>
                    <div class="tujuan-icon-row">
                        <div class="tujuan-dot"><i class="fas fa-bolt" style="color:#fff;font-size:1rem"></i></div>
                        <p class="tujuan-dot-text">Operasional lebih cepat, efisien &amp; akurat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ TARGET ═══ -->
    <div class="section-separator"></div>
    <section class="info-section" id="target">
        <div class="section-divider"></div>
        <span class="section-badge" style="display:flex;justify-content:center;width:fit-content;margin:0 auto .5rem"><i class="fas fa-users"></i> Target Pengguna</span>
        <h2 class="section-title">Siapa yang Menggunakan Sistem Ini?</h2>
        <p class="section-subtitle">Tiga peran utama yang mendapatkan akses data real-time untuk mendukung aktivitas operasional perusahaan.</p>
        <div class="target-grid">
            <div class="target-card">
                <div class="target-icon"><i class="fas fa-user-shield" style="color:#00d4ff"></i></div>
                <div class="target-title">Admin</div>
                <p class="target-desc">Mengelola seluruh konfigurasi sistem, data pengguna, akses hak peran, dan pemantauan aktivitas lintas divisi.</p>
            </div>
            <div class="target-card">
                <div class="target-icon"><i class="fas fa-chart-bar" style="color:#00d4ff"></i></div>
                <div class="target-title">Staff Penjualan</div>
                <p class="target-desc">Memproses pesanan masuk, memantau status pelanggan, dan mengelola data transaksi penjualan secara terstruktur.</p>
            </div>
            <div class="target-card">
                <div class="target-icon"><i class="fas fa-warehouse" style="color:#00d4ff"></i></div>
                <div class="target-title">Operator Gudang</div>
                <p class="target-desc">Mengakses informasi stok, memverifikasi pengiriman, dan memperbarui status inventori secara langsung.</p>
            </div>
        </div>
    </section>

    <!-- ═══ STUDI KASUS ═══ -->
    <div class="section-separator"></div>
    <section class="info-section" id="studikasus">
        <div class="section-divider"></div>
        <span class="section-badge" style="display:flex;justify-content:center;width:fit-content;margin:0 auto .5rem"><i class="fas fa-flask"></i> Studi Kasus</span>
        <h2 class="section-title">Latar Belakang &amp; Konteks</h2>
        <div class="studikasus-box">
            <div class="studikasus-inner">
                <div class="studikasus-col">
                    <h4><i class="fas fa-exclamation-circle me-1"></i> Permasalahan</h4>
                    <p>Aktivitas administrasi, penjualan, dan gudang di Hutch.id masih berjalan pada platform yang terpisah — menciptakan silo informasi, duplikasi kerja, dan hambatan koordinasi.</p>
                </div>
                <div class="studikasus-col">
                    <h4><i class="fas fa-lightbulb me-1"></i> Solusi</h4>
                    <p>Diperlukan sebuah sistem informasi yang mampu mengintegrasikan seluruh proses kerja dalam satu platform — dari administrasi, pemantauan penjualan, hingga pengelolaan gudang.</p>
                </div>
            </div>
            <div class="studikasus-quote">
                <p>Studi kasus ini diangkat dari kebutuhan nyata digitalisasi sistem operasional <strong style="color:#00d4ff">Hutch.id</strong>, di mana seluruh proses bisnis membutuhkan satu platform yang mampu menyatukan data, orang, dan alur kerja.</p>
                <div class="studikasus-meta">
                    <span class="studikasus-tag"><i class="fas fa-building me-1"></i> Hutch.id</span>
                    <span class="studikasus-tag"><i class="fas fa-graduation-cap me-1"></i> Proyek UAS 2026</span>
                    <span class="studikasus-tag"><i class="fas fa-university me-1"></i> Universitas Kebangsaan RI</span>
                    <span class="studikasus-tag"><i class="fas fa-code me-1"></i> Sistem Informasi</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ KEUNGGULAN ═══ -->
    <div class="section-separator"></div>
    <section class="info-section" id="keunggulan">
        <div class="section-divider"></div>
        <span class="section-badge" style="display:flex;justify-content:center;width:fit-content;margin:0 auto .5rem"><i class="fas fa-star"></i> Keunggulan</span>
        <h2 class="section-title">Apa yang Membuat Sistem Ini Berbeda?</h2>
        <p class="section-subtitle">Empat pilar utama yang menjadi fondasi sistem informasi Hutch.id</p>
        <div class="keunggulan-grid">
            <div class="keunggulan-card">
                <div class="keunggulan-num"><i class="fas fa-database" style="color:#fff;font-size:1.2rem"></i></div>
                <div class="keunggulan-body"><h4>Terpusat</h4><p>Semua data perusahaan berada dalam satu sistem yang dapat diakses kapan saja oleh pihak yang berwenang.</p></div>
            </div>
            <div class="keunggulan-card">
                <div class="keunggulan-num"><i class="fas fa-tachometer-alt" style="color:#fff;font-size:1.2rem"></i></div>
                <div class="keunggulan-body"><h4>Efisien</h4><p>Mengurangi proses manual dan duplikasi pekerjaan secara signifikan sehingga tim fokus pada pekerjaan bernilai tinggi.</p></div>
            </div>
            <div class="keunggulan-card">
                <div class="keunggulan-num"><i class="fas fa-handshake" style="color:#fff;font-size:1.2rem"></i></div>
                <div class="keunggulan-body"><h4>Kolaboratif</h4><p>Memudahkan koordinasi antara admin, penjualan, dan gudang — tidak ada lagi informasi yang tidak sinkron.</p></div>
            </div>
            <div class="keunggulan-card">
                <div class="keunggulan-num"><i class="fas fa-lock" style="color:#fff;font-size:1.2rem"></i></div>
                <div class="keunggulan-body"><h4>Aman</h4><p>Hak akses disesuaikan dengan peran masing-masing — setiap orang hanya melihat data yang relevan dengan tanggung jawabnya.</p></div>
            </div>
        </div>
    </section>

    <!-- ═══ ABOUT US ═══ -->
    <div class="section-separator"></div>
    <section class="info-section" id="about-us">
        <div class="section-divider"></div>
        <span class="section-badge" style="display:flex;justify-content:center;width:fit-content;margin:0 auto .5rem"><i class="fas fa-info-circle"></i> About Us</span>
        <h2 class="section-title">Tentang Sistem Ini</h2>
        <div class="aboutus-box">
            <p>Sistem informasi ini dikembangkan untuk membantu proses operasional <strong style="color:#00d4ff">Hutch.id</strong> agar lebih terstruktur, efisien, dan terintegrasi. Menjadi solusi digital bagi berbagai aktivitas administrasi perusahaan, mulai dari pengelolaan data, monitoring pekerjaan, hingga koordinasi antar divisi.</p>
            <p>Website ini dikembangkan pada tahun <strong>2026</strong> sebagai proyek Ujian Akhir Semester Program Studi Sistem Informasi <strong>Universitas Kebangsaan Republik Indonesia</strong>, oleh tim yang terdiri dari 7 mahasiswa.</p>
            <div class="aboutus-pills">
                <span class="aboutus-pill"><i class="fas fa-calendar me-1"></i> Tahun 2026</span>
                <span class="aboutus-pill"><i class="fas fa-university me-1"></i> Universitas Kebangsaan RI</span>
                <span class="aboutus-pill"><i class="fas fa-laptop-code me-1"></i> Sistem Informasi</span>
                <span class="aboutus-pill"><i class="fas fa-users me-1"></i> 7 Mahasiswa</span>
                <span class="aboutus-pill"><i class="fas fa-file-alt me-1"></i> Proyek UAS</span>
            </div>
        </div>
    </section>

    <!-- ═══ TEAM ═══ -->
    <div class="section-separator"></div>
    <section class="about-section" id="about">
        <div class="section-divider"></div>
        <h2 class="section-title">Tim Pengembang</h2>
        <div class="about-intro">
            <p>Website ini dikembangkan oleh tim 7 mahasiswa Program Studi Sistem Informasi Universitas Kebangsaan Republik Indonesia sebagai bagian dari proyek akademik Tahun 2026.</p>
        </div>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/nayla.jpeg') }}" alt="Nayla Rabia Gustari"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>NR</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Nayla Rabia Gustari</div>
                    <span class="team-role">Project Manager</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/naylagstr" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/nayyut" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/adrian.jpeg') }}" alt="Adrian Ronald Daga"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>AR</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Adrian Ronald Daga</div>
                    <span class="team-role">Backend</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/byedriand" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/byedriand" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/alvin.jpeg') }}" alt="Muhamad Alvin Ramadhan"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>MA</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Muhamad Alvin Ramadhan</div>
                    <span class="team-role">Frontend</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/muhamadalvinrmdhn" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/alvinzyz" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/sopyan.jpeg') }}" alt="Sopyan Rinaldhi"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>SR</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Sopyan Rinaldhi</div>
                    <span class="team-role">QA Tester Mobile</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/sopyanrnldhi" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/Sopyanrnldhi" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/eka.jpeg') }}" alt="Eka Febryanto"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>EF</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Eka Febryanto</div>
                    <span class="team-role">QA Tester Website</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/eka_febryanto" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/EkaFebryanto" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/julia.jpeg') }}" alt="Julia Habibah"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>JH</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Julia Habibah</div>
                    <span class="team-role">Sistem Analyst</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/juliahabibahh_" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/bibajulia40-eng" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            <div class="team-card">
                <div class="team-pill">
                    <img src="{{ asset('images/team/akbar.jpeg') }}" alt="Akbar"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="photo-fallback" style="display:none"><span>AK</span></div>
                </div>
                <div class="team-info">
                    <div class="team-name">Akbar</div>
                    <span class="team-role">QA Tester Mobile</span>
                    <div class="team-socials">
                        <a href="https://www.instagram.com/hunters_00000" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://github.com/namaakbar44-collab" target="_blank" rel="noopener" title="GitHub"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; 2026 Hutch.id.</p>
        </div>
    </footer>

</div><!-- end content-wrapper -->

<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    'use strict';
    const splash=document.getElementById('splash'),bar=document.getElementById('splashBar'),DURATION=2600,EXIT_MS=900;
    const reduced=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if(reduced){splash.style.display='none';document.body.classList.remove('splash-active');document.querySelectorAll('.content-wrapper,.animated-bg').forEach(el=>el.classList.add('splash-done'));return;}
    const COLORS=['rgba(0,212,255,','rgba(45,125,210,','rgba(255,255,255,'];
    for(let i=0;i<22;i++){const p=document.createElement('div');const sz=Math.random()*4+2;const col=COLORS[Math.floor(Math.random()*COLORS.length)];const dur=Math.random()*8+6;const del=Math.random()*DURATION/1000;Object.assign(p.style,{position:'absolute',borderRadius:'50%',pointerEvents:'none',width:sz+'px',height:sz+'px',left:Math.random()*100+'vw',bottom:'-20px',background:col+(Math.random()*.5+.3)+')',animation:`floatParticle ${dur}s ${del}s linear infinite`});splash.appendChild(p);}
    let startTime=null;
    function tickBar(ts){if(!startTime)startTime=ts;const pct=Math.min(((ts-startTime)/DURATION)*100,100);bar.style.width=pct+'%';if(pct<100)requestAnimationFrame(tickBar);}
    requestAnimationFrame(tickBar);
    function dismissSplash(){splash.classList.add('splash-exit');setTimeout(()=>{splash.style.display='none';document.body.classList.remove('splash-active');document.querySelectorAll('.content-wrapper,.animated-bg').forEach(el=>el.classList.add('splash-done'));},EXIT_MS);}
    let timerDone=false,pageDone=false;
    setTimeout(()=>{timerDone=true;if(pageDone)dismissSplash();},DURATION);
    if(document.readyState==='complete'){pageDone=true;}
    else{window.addEventListener('load',()=>{pageDone=true;if(timerDone)dismissSplash();},{once:true});}
})();
</script>
</body>
</html>