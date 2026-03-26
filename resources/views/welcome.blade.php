<!DOCTYPE html>
<html lang="fr" style="scroll-behavior: smooth;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Groupe Scolaire Les Étoiles — Portail Étudiant</title>
    <meta name="description" content="Le portail numérique du Groupe Scolaire Les Étoiles. Gérez inscriptions et paiements de frais scolaires en toute simplicité.">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,900i|dm-sans:300,400,500,600&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        :root {
            --navy:       #0C1F3F;
            --navy-mid:   #162D56;
            --blue:       #2563EB;
            --blue-hover: #1D4ED8;
            --gold:       #C9973B;
            --gold-light: #F0C060;
            --sky:        #EFF6FF;
            --cream:      #FAFAF8;
            --text:       #1E293B;
            --muted:      #64748B;
            --border:     #E2E8F0;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; color: var(--text); background: var(--cream); overflow-x: hidden; }
        .display { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; }
        img, svg { display: block; }
        a { text-decoration: none; }

        /* ── Layout ─────────────────────────────── */
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ── Buttons ─────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 13px 28px; border-radius: 10px;
            font-weight: 600; font-size: 0.9rem;
            transition: transform .2s, box-shadow .2s, background .2s, border-color .2s;
            white-space: nowrap;
        }
        .btn-primary { background: var(--blue); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,.28); }
        .btn-primary:hover { background: var(--blue-hover); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(37,99,235,.4); }
        .btn-glass { background: rgba(255,255,255,.09); color: #fff; border: 1px solid rgba(255,255,255,.18); }
        .btn-glass:hover { background: rgba(255,255,255,.16); border-color: rgba(255,255,255,.32); }
        .btn-white { background: #fff; color: var(--blue); box-shadow: 0 4px 20px rgba(0,0,0,.18); }
        .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.24); }

        /* ── Gold accent line ───────────────────── */
        .gold-line { display: block; width: 44px; height: 3px; border-radius: 2px; background: linear-gradient(90deg, var(--gold), var(--gold-light)); }

        /* ── Badge ──────────────────────────────── */
        .badge-gold {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(201,151,59,.13); border: 1px solid rgba(201,151,59,.3);
            border-radius: 100px; padding: 5px 14px;
            color: var(--gold-light); font-size: 0.75rem; font-weight: 500;
            letter-spacing: .07em; text-transform: uppercase;
        }

        /* ── NAV ────────────────────────────────── */
        .site-nav {
            position: fixed; inset: 0 0 auto; z-index: 200;
            background: rgba(12,31,63,.82);
            backdrop-filter: blur(18px); -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255,255,255,.07);
            transition: background .35s;
        }
        .site-nav.solid { background: rgba(12,31,63,.97); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 66px; }
        .nav-logo { display: flex; align-items: center; gap: 11px; }
        .nav-logo-badge {
            width: 38px; height: 38px; background: var(--blue); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 10px rgba(37,99,235,.4);
        }
        .nav-logo-badge span { color: #fff; font-weight: 700; font-size: 14px; letter-spacing: .03em; }
        .nav-logo-text { line-height: 1.25; }
        .nav-logo-name { color: #fff; font-weight: 600; font-size: .9rem; }
        .nav-logo-sub  { color: rgba(255,255,255,.38); font-size: .68rem; }
        .nav-links { display: flex; gap: 28px; }
        .nav-link { color: rgba(255,255,255,.65); font-size: .875rem; transition: color .2s; }
        .nav-link:hover { color: #fff; }
        .nav-actions { display: flex; align-items: center; gap: 12px; }
        .nav-login { color: rgba(255,255,255,.7); font-size: .875rem; font-weight: 500; transition: color .2s; }
        .nav-login:hover { color: #fff; }

        /* Mobile nav */
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; color: rgba(255,255,255,.8); }
        .mobile-nav {
            display: none; flex-direction: column; gap: 4px;
            border-top: 1px solid rgba(255,255,255,.08);
            padding: 14px 0 20px;
        }
        .mobile-nav.open { display: flex; }
        .mobile-nav-link { color: rgba(255,255,255,.65); font-size: .9rem; padding: 10px 0; transition: color .2s; }
        .mobile-nav-link:hover { color: #fff; }
        .mobile-nav-actions { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; }

        /* ── HERO ───────────────────────────────── */
        .hero {
            min-height: 100vh; display: flex; align-items: center;
            padding: 120px 24px 72px;
            background: var(--navy);
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.07) 1px, transparent 0);
            background-size: 44px 44px;
        }
        .hero-orb-blue {
            position: absolute; width: 640px; height: 640px; border-radius: 50%; pointer-events: none;
            background: radial-gradient(circle, rgba(37,99,235,.16) 0%, transparent 68%);
            top: -220px; right: -60px;
        }
        .hero-orb-gold {
            position: absolute; width: 420px; height: 420px; border-radius: 50%; pointer-events: none;
            background: radial-gradient(circle, rgba(201,151,59,.11) 0%, transparent 70%);
            bottom: -100px; left: -80px;
        }
        .hero-content { position: relative; z-index: 1; max-width: 720px; }
        .hero-headline {
            color: #fff; line-height: 1.08; letter-spacing: -.025em;
            font-size: clamp(2.8rem, 6vw, 5.2rem); font-weight: 700;
            margin-bottom: 22px;
        }
        .hero-headline em { color: var(--gold-light); font-style: italic; }
        .hero-sub { color: rgba(255,255,255,.6); font-size: 1.1rem; line-height: 1.75; max-width: 520px; margin-bottom: 38px; font-weight: 300; }
        .hero-ctas { display: flex; gap: 12px; flex-wrap: wrap; }

        /* Hero float card */
        .hero-card {
            position: absolute; right: 0; top: 50%; transform: translateY(-50%);
            z-index: 1;
        }
        .card-glass {
            background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.11);
            border-radius: 18px; backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            padding: 26px 30px;
        }
        .card-pill {
            display: flex; align-items: center; gap: 8px;
            background: rgba(34,197,94,.15); border-radius: 100px; padding: 5px 12px;
            color: #4ADE80; font-size: .72rem; font-weight: 600; margin-bottom: 16px;
        }
        .pulse-dot { width: 7px; height: 7px; background: #22C55E; border-radius: 50%; animation: pulse-anim 2s ease-in-out infinite; }
        @keyframes pulse-anim { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.55;transform:scale(.75)} }
        .card-label { color: rgba(255,255,255,.45); font-size: .7rem; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .05em; }
        .card-amount { color: #fff; font-size: 1.55rem; font-weight: 700; font-family: 'Playfair Display', serif; }
        .card-sub-pill {
            background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px; padding: 14px 18px; margin-top: 14px;
        }
        .card-sub-label { color: rgba(255,255,255,.42); font-size: .68rem; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 3px; }
        .card-sub-value { color: #fff; font-weight: 600; font-size: .85rem; }
        .card-sub-tag { color: var(--gold-light); font-size: .75rem; margin-top: 2px; }

        /* ── STATS ──────────────────────────────── */
        .stats-bar { background: #fff; border-bottom: 1px solid var(--border); }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); border-left: 1px solid var(--border); }
        .stat-cell { padding: 32px 24px; text-align: center; border-right: 1px solid var(--border); }
        .stat-num { font-family: 'Playfair Display', serif; font-size: 2.8rem; font-weight: 700; color: var(--blue); line-height: 1; }
        .stat-lbl { color: var(--muted); font-size: .8rem; margin-top: 6px; }

        /* ── FEATURES ───────────────────────────── */
        .features { background: var(--sky); padding: 96px 0; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-title { font-size: clamp(1.9rem, 4vw, 2.6rem); font-weight: 700; color: var(--navy); letter-spacing: -.022em; margin-bottom: 14px; line-height: 1.15; }
        .section-desc { color: var(--muted); font-size: 1rem; max-width: 480px; margin: 0 auto; line-height: 1.7; }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .feat-card {
            background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 26px 24px;
            transition: transform .3s, box-shadow .3s, border-color .3s;
        }
        .feat-card:hover { transform: translateY(-4px); border-color: #BFDBFE; box-shadow: 0 18px 38px rgba(37,99,235,.08), 0 4px 12px rgba(0,0,0,.05); }
        .feat-icon { width: 50px; height: 50px; border-radius: 13px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; flex-shrink: 0; }
        .feat-title { font-weight: 600; font-size: .95rem; color: var(--navy); margin-bottom: 7px; }
        .feat-desc { color: var(--muted); font-size: .835rem; line-height: 1.65; }

        /* ── HOW IT WORKS ───────────────────────── */
        .how { background: #fff; padding: 96px 0; }
        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; position: relative; }
        .steps-line {
            position: absolute; top: 27px;
            height: 2px; background: linear-gradient(90deg, #BFDBFE, #93C5FD, #60A5FA);
            z-index: 0; left: calc(16.67% + 4px); right: calc(16.67% + 4px);
        }
        .step { text-align: center; padding: 0 12px; }
        .step-num {
            width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 26px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.2rem;
            color: #fff; position: relative; z-index: 1;
        }
        .step-title { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 600; color: var(--navy); margin-bottom: 10px; }
        .step-desc { color: var(--muted); font-size: .84rem; line-height: 1.7; }

        /* ── ABOUT ──────────────────────────────── */
        .about-section { background: var(--navy); padding: 96px 0; position: relative; overflow: hidden; }
        .about-section::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0);
            background-size: 38px 38px;
        }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; position: relative; z-index: 1; }
        .about-headline { font-size: clamp(1.8rem, 3.5vw, 2.4rem); font-weight: 700; color: #fff; letter-spacing: -.022em; line-height: 1.2; margin-bottom: 18px; }
        .about-headline em { color: var(--gold-light); font-style: normal; }
        .about-body { color: rgba(255,255,255,.58); font-size: .94rem; line-height: 1.8; font-weight: 300; margin-bottom: 14px; }
        .highlight-row {
            display: flex; align-items: center; gap: 18px;
            background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.09);
            border-radius: 14px; padding: 20px 22px; margin-bottom: 12px;
        }
        .highlight-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .highlight-title { color: #fff; font-weight: 600; font-size: .875rem; margin-bottom: 3px; }
        .highlight-sub   { color: rgba(255,255,255,.45); font-size: .775rem; }

        /* ── CTA ────────────────────────────────── */
        .cta-section {
            padding: 96px 0; text-align: center;
            background: linear-gradient(140deg, var(--navy) 0%, #173571 48%, var(--blue) 100%);
            position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: ''; position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.055) 1px, transparent 0);
            background-size: 30px 30px;
        }
        .cta-inner { position: relative; z-index: 1; max-width: 600px; margin: 0 auto; }
        .cta-title { font-size: clamp(1.9rem, 4vw, 2.6rem); font-weight: 700; color: #fff; letter-spacing: -.02em; line-height: 1.2; margin-bottom: 16px; }
        .cta-sub { color: rgba(255,255,255,.6); font-size: 1rem; line-height: 1.7; margin-bottom: 38px; font-weight: 300; }
        .cta-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        /* ── FOOTER ─────────────────────────────── */
        .site-footer { background: var(--navy); border-top: 1px solid rgba(255,255,255,.07); padding: 56px 0 28px; }
        .footer-grid { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 48px; margin-bottom: 44px; }
        .footer-brand-desc { color: rgba(255,255,255,.4); font-size: .8rem; line-height: 1.7; margin-top: 14px; }
        .footer-heading { color: rgba(255,255,255,.4); font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 14px; }
        .footer-links { display: flex; flex-direction: column; gap: 9px; }
        .footer-link { color: rgba(255,255,255,.55); font-size: .825rem; transition: color .2s; }
        .footer-link:hover { color: rgba(255,255,255,.9); }
        .footer-bar { border-top: 1px solid rgba(255,255,255,.07); padding-top: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .footer-copy { color: rgba(255,255,255,.28); font-size: .775rem; }

        /* ── Scroll reveal ──────────────────────── */
        .reveal { opacity: 0; transform: translateY(22px); transition: opacity .75s ease, transform .75s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        .delay-1 { transition-delay: .1s; } .delay-2 { transition-delay: .2s; }
        .delay-3 { transition-delay: .3s; } .delay-4 { transition-delay: .4s; }
        .delay-5 { transition-delay: .5s; } .delay-6 { transition-delay: .6s; }

        /* ── Responsive ─────────────────────────── */
        @media (max-width: 1024px) {
            .hero-card { display: none !important; }
            .about-grid { grid-template-columns: 1fr; gap: 48px; }
        }
        @media (max-width: 768px) {
            .nav-links, .nav-actions { display: none !important; }
            .hamburger { display: block; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .steps-line { display: none; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid > :first-child { grid-column: 1 / -1; }
        }
        @media (max-width: 540px) {
            .features-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; border-left: none; }
            .stat-cell { border-right: none; border-bottom: 1px solid var(--border); }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- ══════════════════════════════════════
     NAVIGATION
══════════════════════════════════════ --}}
<nav class="site-nav" id="site-nav">
    <div class="container">
        <div class="nav-inner">
            <a href="/" class="nav-logo">
                <div class="nav-logo-badge">
                    <span>PE</span>
                </div>
                <div class="nav-logo-text">
                    <div class="nav-logo-name">Portail Étudiant</div>
                    <div class="nav-logo-sub">Les Étoiles</div>
                </div>
            </a>

            <div class="nav-links">
                <a href="#features"    class="nav-link">Fonctionnalités</a>
                <a href="#how-it-works" class="nav-link">Comment ça marche</a>
                <a href="#about"       class="nav-link">À propos</a>
            </div>

            <div class="nav-actions">
                @auth
                    <a href="/portal" class="btn btn-primary" style="padding: 9px 20px; font-size: .84rem;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.2"/><rect x="14" y="3" width="7" height="7" rx="1.2"/><rect x="3" y="14" width="7" height="7" rx="1.2"/><rect x="14" y="14" width="7" height="7" rx="1.2"/></svg>
                        Mon Tableau de bord
                    </a>
                @else
                    <a href="/portal/login"    class="nav-login">Se connecter</a>
                    <a href="/portal/register" class="btn btn-primary" style="padding: 9px 20px; font-size: .84rem;">S'inscrire</a>
                @endauth
            </div>

            <button class="hamburger" id="hamburger" aria-label="Menu" onclick="document.getElementById('mobile-nav').classList.toggle('open')">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div class="mobile-nav" id="mobile-nav">
            <a href="#features"     class="mobile-nav-link" onclick="this.closest('.mobile-nav').classList.remove('open')">Fonctionnalités</a>
            <a href="#how-it-works" class="mobile-nav-link" onclick="this.closest('.mobile-nav').classList.remove('open')">Comment ça marche</a>
            <a href="#about"        class="mobile-nav-link" onclick="this.closest('.mobile-nav').classList.remove('open')">À propos</a>
            <div class="mobile-nav-actions">
                @auth
                    <a href="/portal" class="btn btn-primary" style="justify-content: center;">Mon Tableau de bord</a>
                @else
                    <a href="/portal/login"    class="mobile-nav-link" style="text-align: center; padding: 11px 0;">Se connecter</a>
                    <a href="/portal/register" class="btn btn-primary" style="justify-content: center;">S'inscrire</a>
                @endauth
            </div>
        </div>
    </div>
</nav>


{{-- ══════════════════════════════════════
     HERO
══════════════════════════════════════ --}}
<section class="hero" id="hero">
    <div class="hero-orb-blue"></div>
    <div class="hero-orb-gold"></div>

    <div class="container" style="width: 100%; position: relative; z-index: 1;">
        <div class="hero-content">
            <div style="margin-bottom: 26px;">
                <span class="badge-gold">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    Groupe Scolaire Les Étoiles — Depuis 1998
                </span>
            </div>

            <h1 class="display hero-headline">
                L'excellence<br>
                <em>académique</em><br>
                au bout des doigts
            </h1>

            <p class="hero-sub">
                Gérez vos inscriptions, réglez vos frais scolaires et suivez vos paiements en toute simplicité depuis notre portail numérique sécurisé.
            </p>

            <div class="hero-ctas">
                @auth
                    <a href="/portal" class="btn btn-primary" style="padding: 14px 32px; font-size: .95rem;">
                        Mon Tableau de bord
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @else
                    <a href="/portal/login" class="btn btn-primary" style="padding: 14px 32px; font-size: .95rem;">
                        Accéder au portail
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @endauth
                <a href="#features" class="btn btn-glass" style="padding: 14px 26px; font-size: .95rem;">
                    Découvrir
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </div>
        </div>

        {{-- Floating payment card (desktop only) --}}
        <div class="hero-card" style="max-width: 286px;">
            <div class="card-glass">
                <div class="card-pill">
                    <span class="pulse-dot"></span>
                    Paiement confirmé
                </div>
                <div>
                    <div class="card-label">Montant réglé</div>
                    <div class="card-amount">125 000 F CFA</div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 12px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.4)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span style="color: rgba(255,255,255,.38); font-size: .72rem;">Reçu envoyé par email</span>
                </div>
                <div class="card-sub-pill">
                    <div class="card-sub-label">Prochaine échéance</div>
                    <div class="card-sub-value">2ème versement</div>
                    <div class="card-sub-tag">Dans 14 jours</div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     STATS
══════════════════════════════════════ --}}
<div class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-cell reveal">
                <div class="stat-num">1 200+</div>
                <div class="stat-lbl">Élèves inscrits</div>
            </div>
            <div class="stat-cell reveal delay-2">
                <div class="stat-num">15</div>
                <div class="stat-lbl">Classes disponibles</div>
            </div>
            <div class="stat-cell reveal delay-4">
                <div class="stat-num">98%</div>
                <div class="stat-lbl">Taux de satisfaction</div>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     FEATURES
══════════════════════════════════════ --}}
<section class="features" id="features">
    <div class="container">
        <div class="section-header reveal">
            <span class="gold-line" style="margin: 0 auto 20px;"></span>
            <h2 class="display section-title">Tout ce dont vous avez besoin</h2>
            <p class="section-desc">Une plateforme complète pour gérer chaque aspect de votre vie scolaire en ligne.</p>
        </div>

        <div class="features-grid">

            <div class="feat-card reveal delay-1">
                <div class="feat-icon" style="background: #EFF6FF;">
                    <svg width="23" height="23" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div class="feat-title">Inscription en ligne</div>
                <div class="feat-desc">Soumettez votre demande d'inscription à une classe en quelques clics, sans vous déplacer à l'école.</div>
            </div>

            <div class="feat-card reveal delay-2">
                <div class="feat-icon" style="background: #F0FDF4;">
                    <svg width="23" height="23" fill="none" viewBox="0 0 24 24" stroke="#16A34A" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="feat-title">Paiements sécurisés</div>
                <div class="feat-desc">Réglez vos frais scolaires via KKiaPay, la solution de paiement mobile de confiance en Afrique de l'Ouest.</div>
            </div>

            <div class="feat-card reveal delay-3">
                <div class="feat-icon" style="background: #FFF7ED;">
                    <svg width="23" height="23" fill="none" viewBox="0 0 24 24" stroke="#EA580C" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="feat-title">Suivi en temps réel</div>
                <div class="feat-desc">Consultez l'état de vos paiements, inscriptions et soldes à tout moment depuis votre espace personnel.</div>
            </div>

            <div class="feat-card reveal delay-4">
                <div class="feat-icon" style="background: #EFF6FF;">
                    <svg width="23" height="23" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
                <div class="feat-title">Remboursements simplifiés</div>
                <div class="feat-desc">Faites une demande de remboursement directement depuis le portail et suivez son traitement en temps réel.</div>
            </div>

            <div class="feat-card reveal delay-5">
                <div class="feat-icon" style="background: #FDF4FF;">
                    <svg width="23" height="23" fill="none" viewBox="0 0 24 24" stroke="#9333EA" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="feat-title">Reçus PDF</div>
                <div class="feat-desc">Téléchargez instantanément vos reçus de paiement au format PDF pour chaque transaction effectuée.</div>
            </div>

            <div class="feat-card reveal delay-6">
                <div class="feat-icon" style="background: #FFFBEB;">
                    <svg width="23" height="23" fill="none" viewBox="0 0 24 24" stroke="#D97706" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="feat-title">Rappels automatiques</div>
                <div class="feat-desc">Recevez des notifications par email avant les dates limites de paiement pour ne jamais manquer une échéance.</div>
            </div>

        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════ --}}
<section class="how" id="how-it-works">
    <div class="container">
        <div class="section-header reveal">
            <span class="gold-line" style="margin: 0 auto 20px;"></span>
            <h2 class="display section-title">Comment ça marche</h2>
            <p class="section-desc">En seulement trois étapes, accédez à tous les services du portail scolaire.</p>
        </div>

        <div class="steps-grid">
            <div class="steps-line"></div>

            <div class="step reveal delay-1">
                <div class="step-num" style="background: var(--blue); box-shadow: 0 4px 18px rgba(37,99,235,.32);">1</div>
                <div class="display step-title">Créez votre compte</div>
                <div class="step-desc">Inscrivez-vous en quelques minutes avec vos informations personnelles. Votre adresse email est vérifiée instantanément.</div>
            </div>

            <div class="step reveal delay-2">
                <div class="step-num" style="background: var(--gold); box-shadow: 0 4px 18px rgba(201,151,59,.36);">2</div>
                <div class="display step-title">Soumettez votre dossier</div>
                <div class="step-desc">Choisissez la classe souhaitée et soumettez votre demande d'inscription. L'équipe administrative traite votre dossier sous 48h.</div>
            </div>

            <div class="step reveal delay-3">
                <div class="step-num" style="background: var(--navy); box-shadow: 0 4px 18px rgba(12,31,63,.28);">3</div>
                <div class="display step-title">Payez en ligne</div>
                <div class="step-desc">Réglez vos frais scolaires de manière sécurisée via Mobile Money. Un reçu PDF vous est envoyé par email instantanément.</div>
            </div>

        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     ABOUT
══════════════════════════════════════ --}}
<section class="about-section" id="about">
    <div class="container">
        <div class="about-grid">

            <div class="reveal">
                <span class="gold-line" style="margin-bottom: 24px;"></span>
                <h2 class="display about-headline">
                    Le Groupe Scolaire<br>
                    <em>Les Étoiles</em>
                </h2>
                <p class="about-body">
                    Fondé en 1998 à Cotonou, le Groupe Scolaire Les Étoiles s'est imposé comme une référence de l'éducation privée au Bénin. Notre mission : offrir un enseignement d'excellence dans un environnement épanouissant.
                </p>
                <p class="about-body">
                    Notre portail numérique incarne notre engagement envers la modernisation des démarches administratives — pour plus de simplicité au service de nos élèves et de leurs familles.
                </p>
                <div style="margin-top: 30px;">
                    @auth
                        <a href="/portal" class="btn btn-primary">Accéder au portail</a>
                    @else
                        <a href="/portal/register" class="btn btn-primary">Rejoindre l'école</a>
                    @endauth
                </div>
            </div>

            <div class="reveal delay-2">
                <div class="highlight-row">
                    <div class="highlight-icon" style="background: rgba(201,151,59,.14);">
                        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="#D4A843" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div>
                        <div class="highlight-title">Enseignement de qualité</div>
                        <div class="highlight-sub">Programmes conformes aux standards nationaux et régionaux</div>
                    </div>
                </div>

                <div class="highlight-row">
                    <div class="highlight-icon" style="background: rgba(37,99,235,.18);">
                        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="#60A5FA" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="highlight-title">Communauté scolaire</div>
                        <div class="highlight-sub">Plus de 1 200 élèves et 80 enseignants passionnés</div>
                    </div>
                </div>

                <div class="highlight-row">
                    <div class="highlight-icon" style="background: rgba(34,197,94,.14);">
                        <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="#22C55E" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <div class="highlight-title">Paiements 100% sécurisés</div>
                        <div class="highlight-sub">Technologie KKiaPay certifiée, transactions chiffrées</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     CTA
══════════════════════════════════════ --}}
<section class="cta-section">
    <div class="container">
        <div class="cta-inner reveal">
            <span class="gold-line" style="margin: 0 auto 24px;"></span>
            <h2 class="display cta-title">Prêt à rejoindre la communauté numérique&nbsp;?</h2>
            <p class="cta-sub">Rejoignez les élèves qui gèrent déjà leur parcours scolaire en ligne depuis notre portail.</p>
            <div class="cta-actions">
                @auth
                    <a href="/portal" class="btn btn-white" style="padding: 14px 36px; font-size: .95rem;">
                        Accéder à mon espace
                    </a>
                @else
                    <a href="/portal/register" class="btn btn-white" style="padding: 14px 36px; font-size: .95rem;">
                        Créer mon compte
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="/portal/login" class="btn btn-glass" style="padding: 14px 26px; font-size: .95rem;">
                        Se connecter
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════
     FOOTER
══════════════════════════════════════ --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">

            <div>
                <a href="/" class="nav-logo" style="margin-bottom: 0;">
                    <div class="nav-logo-badge">
                        <span>PE</span>
                    </div>
                    <div class="nav-logo-text">
                        <div class="nav-logo-name">Portail Étudiant</div>
                        <div class="nav-logo-sub">Les Étoiles</div>
                    </div>
                </a>
                <p class="footer-brand-desc">
                    Le portail numérique du Groupe Scolaire Les Étoiles, pour gérer simplement inscriptions et paiements scolaires.
                </p>
            </div>

            <div>
                <div class="footer-heading">Navigation</div>
                <div class="footer-links">
                    <a href="#features"     class="footer-link">Fonctionnalités</a>
                    <a href="#how-it-works" class="footer-link">Comment ça marche</a>
                    <a href="#about"        class="footer-link">À propos</a>
                </div>
            </div>

            <div>
                <div class="footer-heading">Portail</div>
                <div class="footer-links">
                    <a href="/portal/login"    class="footer-link">Se connecter</a>
                    <a href="/portal/register" class="footer-link">Créer un compte</a>
                    <a href="/portal"          class="footer-link">Tableau de bord</a>
                </div>
            </div>

        </div>

        <div class="footer-bar">
            <span class="footer-copy">&copy; {{ date('Y') }} Groupe Scolaire Les Étoiles. Tous droits réservés.</span>
            <span class="footer-copy">Portail Étudiant — Gestion scolaire numérique</span>
        </div>
    </div>
</footer>


<script>
(function () {
    // Nav scroll solidify
    var nav = document.getElementById('site-nav');
    window.addEventListener('scroll', function () {
        nav.classList.toggle('solid', window.scrollY > 24);
    }, { passive: true });

    // Smooth anchor scroll
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            var offset = target.getBoundingClientRect().top + window.scrollY - 74;
            window.scrollTo({ top: offset, behavior: 'smooth' });
            var mobileNav = document.getElementById('mobile-nav');
            if (mobileNav) mobileNav.classList.remove('open');
        });
    });

    // Scroll reveal
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -36px 0px' });

    document.querySelectorAll('.reveal').forEach(function (el) {
        observer.observe(el);
    });
})();
</script>

</body>
</html>
