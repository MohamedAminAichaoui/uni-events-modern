<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'UniEvents')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: {
                    sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    serif: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                },
                colors: {
                    navy: { DEFAULT: '#1b2a4a', 800: '#16223c', 900: '#0f1a30' },
                    gold: { 400: '#dcb44a', 500: '#c9a227' },
                    // Admin and manager pages use "indigo" classes: map them to the navy palette.
                    indigo: { 50: '#eef1f8', 100: '#dde3f1', 200: '#bcc7e2', 300: '#8d9fc9', 400: '#5c74a9', 500: '#3a5287',
                              600: '#1b2a4a', 700: '#13203b', 800: '#0f1a30', 900: '#0a1222' },
                },
            } },
        };
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { background: #f6f7fb; }
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 3px solid #c9a227; outline-offset: 2px;
        }
        h1, h2, h3 { letter-spacing: -0.02em; }
        /* Global modernisation of existing pages (forms, panels, buttons) */
        main .bg-white { border-radius: 1rem; }
        .bg-white.shadow-sm { border: 1px solid #e6e9f2; box-shadow: 0 1px 2px rgba(15,26,48,.04), 0 8px 24px -12px rgba(15,26,48,.12); }
        main input:not([type=checkbox]):not([type=radio]), main select, main textarea {
            border-radius: .75rem; border: 1px solid #d7dce8; background: #fff; transition: box-shadow .2s, border-color .2s;
        }
        main input:focus, main select:focus, main textarea:focus { border-color: #c9a227; box-shadow: 0 0 0 4px rgba(201,162,39,.18); outline: none; }
        main button, main a.bg-navy, main a.bg-indigo-600, main button.bg-indigo-600 { border-radius: .75rem; transition: transform .15s, box-shadow .2s, background-color .2s; }
        main button:active { transform: scale(.97); }
        main table { border-collapse: separate; border-spacing: 0; }
        main tbody tr { transition: background-color .15s; }
        main tbody tr:hover { background: #f1f4fb; }
        /* Cards */
        .card { background:#fff; border:1px solid #e6e9f2; border-radius:1.25rem; transition: transform .25s ease, box-shadow .25s ease, border-color .25s; }
        .card:hover { transform: translateY(-4px); box-shadow: 0 18px 40px -18px rgba(15,26,48,.28); border-color: #dcb44a; }
        /* Scroll reveal */
        .reveal { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.in { opacity: 1; transform: none; }
        .hero-bg { background: radial-gradient(1200px 500px at 85% -10%, rgba(201,162,39,.35), transparent 60%), radial-gradient(800px 400px at -10% 110%, rgba(92,116,169,.45), transparent 60%), linear-gradient(135deg,#0f1a30,#1b2a4a); }
        .chip { transition: all .2s; }
        .chip.active { background:#1b2a4a; color:#fff; border-color:#1b2a4a; }
        @keyframes toastin { from { opacity:0; transform: translateY(-10px) } to { opacity:1; transform:none } }
        .toast { animation: toastin .4s ease; }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } .reveal { opacity:1; transform:none; } }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col font-sans antialiased">

<header id="topbar" class="sticky top-0 z-40 bg-navy-900 border-b border-white/10 transition-shadow">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center gap-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-gold-400 to-gold-500 text-navy font-extrabold text-xl flex items-center justify-center shadow-lg shadow-black/30">U</span>
            <span class="font-extrabold text-xl text-white tracking-tight">Uni<span class="text-gold-400">Events</span></span>
        </a>

        <nav class="hidden md:flex items-center gap-1 text-sm font-semibold">
            @php($link = fn($active) => 'px-4 py-2 rounded-full transition '.($active ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white'))
            <a href="{{ route('home') }}" class="{{ $link(request()->routeIs('home','events.*')) }}">Events</a>
            <a href="{{ route('clubs.index') }}" class="{{ $link(request()->routeIs('clubs.*')) }}">Clubs</a>
            @auth <a href="{{ route('registrations.index') }}" class="{{ $link(request()->routeIs('registrations.*')) }}">My registrations</a> @endauth
        </nav>

        <div class="ml-auto hidden md:flex items-center gap-3 text-sm">
            @auth
                <span class="flex items-center gap-2 text-white font-semibold">
                    <span class="w-8 h-8 rounded-full bg-gold-400 text-navy flex items-center justify-center font-bold">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-slate-300 font-semibold px-3 py-2 rounded-full hover:bg-white/10 hover:text-white">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white font-semibold px-4 py-2 rounded-full hover:bg-white/10">Log in</a>
                <a href="{{ route('register') }}" class="bg-gold-500 text-navy font-bold px-5 py-2.5 rounded-full hover:bg-gold-400 hover:-translate-y-0.5 transition shadow-lg shadow-black/30">Sign up</a>
            @endauth
        </div>

        <button id="menuBtn" class="md:hidden ml-auto w-10 h-10 rounded-xl hover:bg-white/10 flex items-center justify-center" aria-label="Menu" aria-expanded="false">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
    </div>
    <div id="mobileMenu" class="hidden md:hidden border-t border-white/10 bg-navy-900 px-4 py-3 space-y-1 text-sm font-semibold text-slate-200">
        <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-xl hover:bg-white/10">Events</a>
        <a href="{{ route('clubs.index') }}" class="block px-3 py-2.5 rounded-xl hover:bg-white/10">Clubs</a>
        @auth
            <a href="{{ route('registrations.index') }}" class="block px-3 py-2.5 rounded-xl hover:bg-white/10">My registrations</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left px-3 py-2.5 rounded-xl hover:bg-white/10">Log out ({{ auth()->user()->name }})</button></form>
        @else
            <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-xl hover:bg-white/10">Log in</a>
            <a href="{{ route('register') }}" class="block px-3 py-2.5 rounded-xl bg-gold-500 text-navy text-center">Sign up</a>
        @endauth
    </div>
</header>

@auth
    @if(auth()->user()->isAdmin() || auth()->user()->isClubManager())
        <div class="bg-navy-900 text-white text-sm">
            <div class="max-w-6xl mx-auto px-4 py-2 flex flex-wrap gap-2">
                @php($adm = 'px-3 py-1 rounded-full bg-white/10 hover:bg-gold-500 hover:text-navy transition')
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="{{ $adm }}">Overview</a>
                    <a href="{{ route('admin.analytics.events') }}" class="{{ $adm }}">Events dashboard</a>
                    <a href="{{ route('admin.analytics.clubs') }}" class="{{ $adm }}">Clubs dashboard</a>
                @endif
                <a href="{{ route('manage.events.index') }}" class="{{ $adm }}">Manage events</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.clubs.index') }}" class="{{ $adm }}">Manage clubs</a>
                    <a href="{{ route('admin.categories.index') }}" class="{{ $adm }}">Categories</a>
                    <a href="{{ route('admin.users.index') }}" class="{{ $adm }}">Users and roles</a>
                @endif
            </div>
        </div>
    @endif
@endauth

@if(session('success') || session('error') || $errors->any())
    <div class="max-w-6xl mx-auto px-4 pt-6 w-full space-y-3">
        @if(session('success'))
            <div role="status" data-dismiss class="toast flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-900 px-5 py-3.5 shadow-sm">
                <span>{{ session('success') }}</span><button type="button" class="text-xl leading-none opacity-60 hover:opacity-100" aria-label="Dismiss">&times;</button></div>
        @endif
        @if(session('error'))
            <div role="alert" data-dismiss class="toast flex items-center justify-between gap-4 rounded-2xl border border-red-200 bg-red-50 text-red-900 px-5 py-3.5 shadow-sm">
                <span>{{ session('error') }}</span><button type="button" class="text-xl leading-none opacity-60 hover:opacity-100" aria-label="Dismiss">&times;</button></div>
        @endif
        @if($errors->any())
            <div role="alert" class="toast rounded-2xl border border-red-200 bg-red-50 text-red-900 px-5 py-3.5 shadow-sm">
                <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
@endif

@yield('hero')

<main class="max-w-6xl mx-auto px-4 py-10 w-full flex-1">
    @yield('content')
</main>

<footer class="hero-bg text-slate-300 mt-10">
    <div class="max-w-6xl mx-auto px-4 py-12 grid gap-8 md:grid-cols-3">
        <div>
            <p class="font-extrabold text-xl text-white">Uni<span class="text-gold-400">Events</span></p>
            <p class="mt-2 text-sm max-w-xs text-slate-400">The events calendar for the university and its student clubs.</p>
        </div>
        <div>
            <p class="font-semibold text-white">Browse</p>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-gold-400 transition">Upcoming events</a></li>
                <li><a href="{{ route('clubs.index') }}" class="hover:text-gold-400 transition">Clubs and societies</a></li>
            </ul>
        </div>
        <div>
            <p class="font-semibold text-white">Your account</p>
            <ul class="mt-3 space-y-2 text-sm">
                @auth
                    <li><a href="{{ route('registrations.index') }}" class="hover:text-gold-400 transition">My registrations</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-gold-400 transition">Log in</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-gold-400 transition">Create a student account</a></li>
                @endauth
            </ul>
        </div>
    </div>
</footer>

<script>
    const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];
    $('#menuBtn').addEventListener('click', e => {
        const m = $('#mobileMenu'); m.classList.toggle('hidden');
        e.currentTarget.setAttribute('aria-expanded', !m.classList.contains('hidden'));
    });
    addEventListener('scroll', () => $('#topbar').classList.toggle('shadow-lg', scrollY > 8), { passive: true });
    $$('[data-dismiss] button').forEach(b => b.addEventListener('click', () => b.parentElement.remove()));
    setTimeout(() => $$('[data-dismiss]').forEach(t => { t.style.transition = 'opacity .5s'; t.style.opacity = 0; setTimeout(() => t.remove(), 500); }), 6000);
    const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .08 });
    $$('.reveal').forEach((el, i) => { el.style.transitionDelay = (i % 6) * 60 + 'ms'; io.observe(el); });
</script>
@stack('scripts')
</body>
</html>
