<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('cleaning.name'))</title>
    <meta name="description" content="@yield('meta', config('cleaning.tagline'))">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0B1F3A;
            --teal: #0D9488;
            --emerald: #059669;
            --bg: #E8EEF5;
            --surface: #ffffff;
            --text: #0F172A;
            --muted: #475569;
            --line: rgba(11,31,58,.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Manrope, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 500px at 10% -10%, rgba(13,148,136,.18), transparent 60%),
                radial-gradient(900px 400px at 100% 0%, rgba(11,31,58,.16), transparent 55%),
                linear-gradient(180deg, #F7FAFC 0%, var(--bg) 100%);
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .wrap { width: min(1120px, calc(100% - 2rem)); margin-inline: auto; }
        .nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.1rem 0; gap: 1rem;
        }
        .brand {
            font-family: Sora, sans-serif; font-weight: 700; font-size: 1.2rem;
            letter-spacing: -.02em; color: var(--navy);
        }
        .nav-links { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; color: var(--muted); font-size: .95rem; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: .7rem; padding: .75rem 1.15rem; font-weight: 700;
            border: 1px solid transparent; transition: .2s ease;
        }
        .btn-primary { background: var(--navy); color: white; }
        .btn-primary:hover { transform: translateY(-1px); background: #123057; }
        .btn-secondary { background: transparent; border-color: rgba(11,31,58,.25); color: var(--navy); }
        .hero {
            display: grid; grid-template-columns: 1.1fr .9fr; gap: 2rem; align-items: center;
            min-height: calc(100vh - 5.5rem); padding: 1.5rem 0 3rem;
        }
        .hero h1 {
            font-family: Sora, sans-serif; font-size: clamp(2.2rem, 5vw, 4rem);
            line-height: 1.05; letter-spacing: -.04em; margin: 0 0 1rem; color: var(--navy);
        }
        .hero p { font-size: 1.1rem; color: var(--muted); max-width: 36rem; margin: 0 0 1.5rem; }
        .cta-row { display: flex; gap: .8rem; flex-wrap: wrap; }
        .preview {
            border-radius: 1.25rem; overflow: hidden; min-height: 420px;
            background:
                linear-gradient(145deg, rgba(11,31,58,.92), rgba(13,148,136,.75)),
                repeating-linear-gradient(90deg, rgba(255,255,255,.05) 0 1px, transparent 1px 28px),
                repeating-linear-gradient(0deg, rgba(255,255,255,.05) 0 1px, transparent 1px 28px);
            box-shadow: 0 30px 80px rgba(11,31,58,.28);
            position: relative; color: white; padding: 1.5rem;
            animation: float 6s ease-in-out infinite;
        }
        .preview::after {
            content: ""; position: absolute; inset: auto 8% 8% 8%; height: 42%;
            border-radius: 1rem; background: rgba(255,255,255,.12); backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,.2);
        }
        .preview h3 { margin: 0 0 .4rem; font-family: Sora, sans-serif; }
        .preview p { margin: 0; opacity: .85; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .section { padding: 4.5rem 0; }
        .section h2 { font-family: Sora, sans-serif; font-size: clamp(1.7rem, 3vw, 2.4rem); letter-spacing: -.03em; margin: 0 0 .6rem; color: var(--navy); }
        .section .lead { color: var(--muted); max-width: 40rem; margin-bottom: 2rem; }
        .grid { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .item { padding: 1.2rem 0; border-top: 1px solid var(--line); }
        .item h3 { margin: 0 0 .4rem; font-size: 1.05rem; color: var(--navy); }
        .item p { margin: 0; color: var(--muted); font-size: .95rem; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .stat { padding: 1rem 0; }
        .stat strong { display: block; font-family: Sora, sans-serif; font-size: 1.8rem; color: var(--teal); }
        .footer { border-top: 1px solid var(--line); padding: 2.5rem 0; margin-top: 2rem; color: var(--muted); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1.5rem; }
        .footer a { display: block; margin: .35rem 0; }
        .form { display: grid; gap: .8rem; max-width: 32rem; }
        input, textarea, select {
            width: 100%; padding: .85rem 1rem; border-radius: .7rem;
            border: 1px solid rgba(11,31,58,.18); background: white; font: inherit;
        }
        .alert { padding: .9rem 1rem; border-radius: .7rem; background: rgba(5,150,105,.12); color: #065f46; margin-bottom: 1rem; }
        @media (max-width: 860px) {
            .hero, .footer-grid, .stats { grid-template-columns: 1fr; }
            .nav-links { display: none; }
            .preview { min-height: 280px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="wrap">
        <nav class="nav">
            <a class="brand" href="{{ route('home') }}">{{ config('cleaning.short_name') }}</a>
            <div class="nav-links">
                <a href="{{ route('features') }}">Features</a>
                <a href="{{ route('modules') }}">Modules</a>
                <a href="{{ route('solutions') }}">Solutions</a>
                <a href="{{ route('pricing') }}">Pricing</a>
                <a href="{{ route('docs') }}">Docs</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a class="btn btn-secondary" href="/admin/login">Login</a>
                <a class="btn btn-primary" href="{{ route('trial') }}">Start Free Trial</a>
            </div>
        </nav>
    </header>
    <main>
        @if(session('success'))
            <div class="wrap"><div class="alert">{{ session('success') }}</div></div>
        @endif
        @yield('content')
    </main>
    <footer class="footer">
        <div class="wrap footer-grid">
            <div>
                <div class="brand">{{ config('cleaning.short_name') }}</div>
                <p>{{ config('cleaning.tagline') }}</p>
            </div>
            <div>
                <strong>Product</strong>
                <a href="{{ route('features') }}">Features</a>
                <a href="{{ route('modules') }}">Modules</a>
                <a href="{{ route('pricing') }}">Pricing</a>
            </div>
            <div>
                <strong>Company</strong>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('security') }}">Security</a>
                <a href="{{ route('status') }}">Status</a>
            </div>
            <div>
                <strong>Legal</strong>
                <a href="{{ route('privacy') }}">Privacy</a>
                <a href="{{ route('terms') }}">Terms</a>
                <a href="{{ route('cookies') }}">Cookies</a>
            </div>
        </div>
    </footer>
</body>
</html>
