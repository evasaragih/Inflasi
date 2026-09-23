<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Inflasi') &middot; {{ setting('site_name', 'Dashboard Inflasi') }}</title>
    <meta name="description" content="Dashboard pemantauan inflasi Kota Padang: angka inflasi, andil kelompok pengeluaran, dan harga pangan strategis.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: { DEFAULT: '#241811', soft: '#6E5F4E' },
                        marun: { DEFAULT: '#D41E43', dark: '#9C1030', light: '#F0466A' },
                        emas: { DEFAULT: '#C0922C', light: '#E3C578', dim: '#8C6A20' },
                        sand: { DEFAULT: '#F1E9D6', dark: '#E6DAB8' },
                        hijau: { DEFAULT: '#12965A', light: '#D6F5E3' },
                        garis: '#E1D3AE',
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        html { scroll-behavior: smooth; }
        body {
            background-color: #f6f2ec;
            background-image:
                radial-gradient(circle at 12% 15%, rgba(192, 146, 44, 0.08), transparent 45%),
                radial-gradient(circle at 88% 85%, rgba(36, 24, 17, 0.06), transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(212, 30, 67, 0.04), transparent 50%);
            background-attachment: fixed;
            color: #241811;
        }
        h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        ::selection { background-color: rgba(192,146,44,0.3); color: #9C1030; }

        /* ========== GLASS & KARTU WARM CHOCOLATE ========== */
        .kartu {
            background: rgba(255, 253, 249, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(225, 211, 174, 0.75);
            border-radius: 1.25rem;
            box-shadow: 0 4px 24px -4px rgba(36, 24, 17, 0.05), 0 2px 8px -2px rgba(36, 24, 17, 0.02);
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .kartu-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -6px rgba(36, 24, 17, 0.1), 0 4px 14px -2px rgba(192, 146, 44, 0.18);
            border-color: rgba(192, 146, 44, 0.55);
        }

        .angka-data {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }

        /* ========== CHIP & BADGE ========== */
        .chip {
            display: inline-flex; align-items: center; gap: 0.375rem;
            border-radius: 9999px; padding: 0.3rem 0.8rem;
            font-size: 0.75rem; font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .chip:hover {
            transform: scale(1.04);
        }
        .chip-naik {
            background: linear-gradient(135deg, #D41E43 0%, #B01535 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(212, 30, 67, 0.3);
        }
        .chip-turun {
            background: linear-gradient(135deg, #12965A 0%, #0E7847 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(18, 150, 90, 0.3);
        }

        /* ========== NAVBAR LINKS ========== */
        .nav-link {
            padding: 0.4rem 0.95rem;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #F1E9D6;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.14);
            transform: translateY(-1px);
        }
        .nav-link.active {
            background: linear-gradient(135deg, #C0922C 0%, #D4A73B 100%);
            color: #241811;
            box-shadow: 0 3px 12px rgba(192, 146, 44, 0.4);
            font-weight: 700;
        }

        /* ========== PULSE LIVE DOT ========== */
        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #12965A;
            display: inline-block;
            position: relative;
        }
        .live-dot::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background-color: rgba(18, 150, 90, 0.5);
            animation: pulse-ring 1.8s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        /* ========== PAGE TRANSITION ========== */
        @keyframes pageIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pageOut {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-10px); }
        }
        .page-transition-wrap {
            animation: pageIn 0.38s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .page-leaving .page-transition-wrap {
            animation: pageOut 0.22s cubic-bezier(0.4, 0, 1, 1) both;
            pointer-events: none;
        }

        /* ========== LOADING BAR ========== */
        #page-progress {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            width: 0%;
            z-index: 9999;
            background: linear-gradient(90deg, #C0922C, #F0466A, #12965A, #C0922C);
            background-size: 300% 100%;
            border-radius: 0 2px 2px 0;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
            animation: shimmer 1.5s linear infinite;
            box-shadow: 0 0 10px rgba(192,146,44,0.7);
        }
        @keyframes shimmer {
            0%   { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
        #page-progress.done {
            width: 100% !important;
            opacity: 0;
            transition: width 0.15s ease, opacity 0.5s ease 0.2s;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden w-full max-w-full">

    <header class="sticky top-0 z-50 overflow-hidden bg-ink text-sand shadow-lg shadow-ink/10">
        <div class="relative mx-auto max-w-[1536px] px-4 sm:px-6 md:px-10 py-3 flex items-center justify-between gap-3 sm:gap-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group min-w-0">
                @if(setting('logo_path'))
                    <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="{{ setting('site_name', 'Logo') }}" class="h-8 w-auto max-w-[120px] sm:max-w-[140px] object-contain shrink-0">
                @endif
                <span class="min-w-0 truncate">
                    <span class="block font-display text-xs sm:text-sm font-bold leading-tight text-white truncate">{{ setting('site_name', 'Dashboard Inflasi') }}</span>
                    <span class="block text-[9px] sm:text-[10px] tracking-wide text-emas-light/80 uppercase truncate">{{ setting('site_subtitle', 'Kota Padang') }}</span>
                </span>
            </a>

            <nav class="hidden md:flex items-center gap-0.5 rounded-full bg-white/5 border border-white/10 p-1">
                <a href="{{ route('dashboard') }}" class="nav-link !text-sand hover:!text-white hover:!bg-white/10 {{ request()->routeIs('dashboard') ? '!bg-emas !text-ink' : '' }}">Ringkasan</a>
                <a href="{{ route('inflasi.index') }}" class="nav-link !text-sand hover:!text-white hover:!bg-white/10 {{ request()->routeIs('inflasi.*') ? '!bg-emas !text-ink' : '' }}">Data Inflasi</a>
                <a href="{{ route('andil.index') }}" class="nav-link !text-sand hover:!text-white hover:!bg-white/10 {{ request()->routeIs('andil.*') ? '!bg-emas !text-ink' : '' }}">Andil Kelompok</a>
                <a href="{{ route('pangan.index') }}" class="nav-link !text-sand hover:!text-white hover:!bg-white/10 {{ request()->routeIs('pangan.*') ? '!bg-emas !text-ink' : '' }}">Harga Pangan</a>
            </nav>

            <button id="menuBtn" class="md:hidden grid h-9 w-9 place-items-center rounded-lg border border-white/15 hover:bg-white/10 transition-colors shrink-0">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
            </button>
        </div>

        <nav id="menuMobile" class="hidden md:hidden relative border-t border-white/10 px-4 py-3 flex flex-col gap-1">
            <a href="{{ route('dashboard') }}" class="nav-link !text-sand {{ request()->routeIs('dashboard') ? '!bg-emas !text-ink' : '' }}">Ringkasan</a>
            <a href="{{ route('inflasi.index') }}" class="nav-link !text-sand {{ request()->routeIs('inflasi.*') ? '!bg-emas !text-ink' : '' }}">Data Inflasi</a>
            <a href="{{ route('andil.index') }}" class="nav-link !text-sand {{ request()->routeIs('andil.*') ? '!bg-emas !text-ink' : '' }}">Andil Kelompok</a>
            <a href="{{ route('pangan.index') }}" class="nav-link !text-sand {{ request()->routeIs('pangan.*') ? '!bg-emas !text-ink' : '' }}">Harga Pangan</a>
        </nav>
    </header>

    <main class="mx-auto max-w-[1536px] px-4 sm:px-6 md:px-10 py-5 md:py-10 min-w-0 w-full overflow-x-hidden">
        @yield('content')
    </main>

    {{-- FOOTER MEWAH SESUAI CMS & REFERENSI --}}
    <footer class="relative mt-16 sm:mt-20 bg-ink text-sand overflow-hidden border-t border-white/10 shadow-2xl w-full max-w-full">

        {{-- Giant Semi-Transparent Background Watermark Text (Sesuai Referensi) --}}
        <div class="pointer-events-none absolute left-0 bottom-10 select-none overflow-hidden max-w-full opacity-[0.035] whitespace-nowrap font-display text-[80px] sm:text-[110px] md:text-[170px] font-black leading-none text-white tracking-widest">
            {{ strtoupper(setting('site_name', 'DASHBOARD INFLASI')) }}
        </div>

        <div class="relative mx-auto max-w-[1536px] px-4 sm:px-6 md:px-10 pt-16 pb-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10">

                {{-- Column 1: Brand Info (5 cols) --}}
                <div class="md:col-span-5 space-y-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        @if(setting('logo_path'))
                            <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="{{ setting('site_name', 'Logo') }}" class="h-10 w-auto max-w-[160px] object-contain">
                        @else
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-emas/20 border border-emas/40 text-emas font-black text-base shadow-sm">
                                PAD
                            </span>
                        @endif
                        <div>
                            <span class="block font-display text-base font-extrabold uppercase tracking-wide text-white leading-tight">
                                {{ setting('site_name', 'Dashboard Inflasi') }}
                            </span>
                            <span class="block text-xs font-semibold tracking-wider text-emas-light/80 uppercase">
                                {{ setting('site_subtitle', 'Kota Padang') }}
                            </span>
                        </div>
                    </a>

                    <p class="text-sm text-sand/75 leading-relaxed pr-4">
                        {{ setting('footer_text', 'Dashboard pemantauan inflasi Kota Padang: angka inflasi, andil kelompok pengeluaran, dan harga pangan strategis.') }}
                    </p>

                    {{-- Social Media Icons --}}
                    <div class="flex items-center gap-3 pt-2">
                        <a href="https://facebook.com" target="_blank" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-sand hover:bg-emas hover:text-ink transition-all duration-200 shadow-sm" title="Facebook">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-sand hover:bg-emas hover:text-ink transition-all duration-200 shadow-sm" title="Instagram">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://youtube.com" target="_blank" class="grid h-9 w-9 place-items-center rounded-full bg-white/10 text-sand hover:bg-emas hover:text-ink transition-all duration-200 shadow-sm" title="YouTube">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Column 2: TAUTAN (3 cols) --}}
                <div class="md:col-span-3 space-y-3">
                    <h4 class="font-display text-xs font-extrabold uppercase tracking-widest text-emas">TAUTAN</h4>
                    <ul class="space-y-2.5 text-sm text-sand/80">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-emas hover:translate-x-1 transition-all inline-block">Ringkasan</a></li>
                        <li><a href="{{ route('inflasi.index') }}" class="hover:text-emas hover:translate-x-1 transition-all inline-block">Data Inflasi</a></li>
                        <li><a href="{{ route('andil.index') }}" class="hover:text-emas hover:translate-x-1 transition-all inline-block">Andil Kelompok</a></li>
                        <li><a href="{{ route('pangan.index') }}" class="hover:text-emas hover:translate-x-1 transition-all inline-block">Harga Pangan</a></li>
                        <li class="pt-2 border-t border-white/10">
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="text-emas hover:underline font-bold">Panel Admin</a>
                            @else
                                <a href="{{ route('login') }}" class="text-emas hover:underline font-bold">Masuk Admin</a>
                            @endauth
                        </li>
                    </ul>
                </div>

                {{-- Column 3: KONTAK (4 cols) --}}
                <div class="md:col-span-4 space-y-3">
                    <h4 class="font-display text-xs font-extrabold uppercase tracking-widest text-emas">KONTAK</h4>
                    <ul class="space-y-3 text-sm text-sand/80">
                        <li class="flex items-start gap-3">
                            <svg viewBox="0 0 20 20" class="h-5 w-5 text-emas shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 2a6 6 0 00-6 6c0 4.5 6 10 6 10s6-5.5 6-10a6 6 0 00-6-6zM10 10a2 2 0 110-4 2 2 0 010 4z" stroke-linecap="round"/></svg>
                            <span>Jl. Bagindo Aziz Chan No. 1, Balai Kota Padang, Sumatera Barat</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg viewBox="0 0 20 20" class="h-5 w-5 text-emas shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" stroke-linecap="round"/></svg>
                            <span>(0751) 123-456</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg viewBox="0 0 20 20" class="h-5 w-5 text-emas shrink-0" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884zM18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" stroke-linecap="round"/></svg>
                            <span>diskominfo@padang.go.id</span>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- Bottom Copyright Bar --}}
            <div class="mt-12 pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-sand/60">
                <p>&copy; {{ date('Y') }} {{ setting('site_name', 'Dashboard Inflasi') }} - {{ setting('site_subtitle', 'Kota Padang') }}. Seluruh hak cipta dilindungi.</p>
                <p>{{ setting('footer_credit', 'Dibangun dengan Laravel & Tailwind CSS') }}</p>
            </div>
        </div>
    </footer>

    {{-- Floating Scroll-To-Top Button (Sesuai Referensi Gambar) --}}
    <button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" aria-label="Kembali ke atas"
        class="fixed bottom-6 right-6 z-40 grid h-11 w-11 place-items-center rounded-full bg-gradient-to-r from-emas via-emas-light to-emas text-ink shadow-xl shadow-emas/30 transition-all duration-300 hover:scale-110 hover:shadow-2xl focus:outline-none opacity-0 pointer-events-none">
        <svg viewBox="0 0 20 20" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 13l5-5 5 5"/>
        </svg>
    </button>

    <script>
        /* ---- Mobile menu toggle ---- */
        document.getElementById('menuBtn')?.addEventListener('click', () => {
            document.getElementById('menuMobile')?.classList.toggle('hidden');
        });

        /* ---- Scroll to Top Button Visibility ---- */
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        if (scrollToTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 250) {
                    scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                    scrollToTopBtn.classList.add('opacity-100', 'pointer-events-auto');
                } else {
                    scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                    scrollToTopBtn.classList.remove('opacity-100', 'pointer-events-auto');
                }
            });
        }

        window.chartDefaultFont = "'Plus Jakarta Sans', system-ui, sans-serif";

        /* ========== SMOOTH PAGE TRANSITION ========== */
        (function () {
            // Buat loading bar element
            const bar = document.createElement('div');
            bar.id = 'page-progress';
            document.body.prepend(bar);

            // Wrap main content untuk animasi
            const mainEl = document.querySelector('main');
            if (mainEl) mainEl.classList.add('page-transition-wrap');

            let barTimer = null;

            function startBar() {
                bar.style.width = '0%';
                bar.style.opacity = '1';
                bar.classList.remove('done');
                // Simulasi progress: 0 → 70% dalam 300ms, lalu tahan
                requestAnimationFrame(() => {
                    bar.style.transition = 'width 0.4s cubic-bezier(0.4,0,0.2,1), opacity 0.4s ease';
                    bar.style.width = '70%';
                });
                barTimer = setTimeout(() => {
                    bar.style.width = '88%';
                }, 600);
            }

            function finishBar() {
                clearTimeout(barTimer);
                bar.classList.add('done');
                setTimeout(() => {
                    bar.style.width = '0%';
                    bar.classList.remove('done');
                    bar.style.opacity = '0';
                }, 700);
            }

            function navigate(url) {
                // Animasi keluar
                document.body.classList.add('page-leaving');
                startBar();
                setTimeout(() => {
                    window.location.href = url;
                }, 200); // sesuai durasi pageOut animation
            }

            // Intercept semua link (kecuali external, hash, download, target)
            document.addEventListener('click', function (e) {
                const link = e.target.closest('a[href]');
                if (!link) return;

                const href = link.getAttribute('href');
                if (!href) return;

                // Lewati: external, hash, mailto, tel, download, new tab
                if (
                    link.hasAttribute('download') ||
                    link.getAttribute('target') === '_blank' ||
                    href.startsWith('#') ||
                    href.startsWith('mailto:') ||
                    href.startsWith('tel:') ||
                    href.startsWith('javascript:')
                ) return;

                // Lewati link ke halaman yang sudah aktif
                try {
                    const dest = new URL(href, window.location.origin);
                    if (dest.href === window.location.href) return;
                } catch (_) { return; }

                e.preventDefault();
                navigate(href);
            });

            // Animasi masuk saat halaman selesai load
            window.addEventListener('pageshow', function (e) {
                document.body.classList.remove('page-leaving');
                finishBar();
            });

            // Handle tombol back/forward
            window.addEventListener('popstate', function () {
                document.body.classList.remove('page-leaving');
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
