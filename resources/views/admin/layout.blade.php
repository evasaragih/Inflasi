<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Admin') &middot; {{ setting('site_name', 'Dashboard Inflasi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
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
        } } }
    </script>
    <style>
        body { background-color: #f6f2ec; color: #241811; }
        .angka-data { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }
        .side-link { display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem 0.875rem; border-radius: 0.625rem; font-size: 0.875rem; font-weight: 600; color: rgba(241,233,214,0.65); transition: all .2s cubic-bezier(0.4, 0, 0.2, 1); }
        .side-link:hover { color: #fff; background: rgba(255,255,255,0.08); transform: translateX(3px); }
        .side-link.active { background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.2); font-weight: 700; backdrop-filter: blur(8px); }
        .form-input { width: 100%; border-radius: 0.5rem; border: 1px solid #E1D3AE; background: #fff; padding: 0.55rem 0.85rem; font-size: 0.875rem; }
        .form-input:focus { outline: none; box-shadow: 0 0 0 2px #C0922C; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: #241811; margin-bottom: 0.35rem; }

        /* Scrollbar sidebar (gelap) */
        aside { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent; }
        aside::-webkit-scrollbar { width: 6px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.18); border-radius: 999px; }
        aside::-webkit-scrollbar-thumb:hover { background-color: rgba(255,255,255,0.3); }

        /* Scrollbar konten utama (terang) */
        html { scrollbar-width: thin; scrollbar-color: #C0922C55 transparent; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #C0922C66; border-radius: 999px; border: 2px solid transparent; background-clip: content-box; }
        ::-webkit-scrollbar-thumb:hover { background-color: #C0922C99; background-clip: content-box; }

        /* Tabel dengan banyak kolom: scroll horizontal halus tanpa geser layout utama */
        .overflow-x-auto { scrollbar-width: thin; scrollbar-color: #C0922C55 transparent; }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden w-full max-w-full">
    {{-- Mobile Top Bar --}}
    <div class="lg:hidden bg-ink text-white px-4 py-3 flex items-center justify-between border-b border-white/10 sticky top-0 z-50">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
            @if(setting('logo_path'))
                <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="Logo" class="h-7 w-auto max-w-[28px] object-contain shrink-0">
            @endif
            <span class="font-display text-xs font-bold leading-tight">Panel Admin <span class="text-emas-light/70 text-[10px] font-normal block">{{ setting('site_name', 'Dashboard Inflasi') }}</span></span>
        </a>
        <button id="adminMenuBtn" class="grid h-8 w-8 place-items-center rounded-lg border border-white/20 text-white hover:bg-white/10">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/></svg>
        </button>
    </div>

    <div class="flex min-h-screen relative min-w-0 w-full">
        <aside id="adminSidebar" class="hidden lg:flex w-64 shrink-0 bg-ink text-white flex-col h-screen sticky top-0 overflow-y-auto z-40 max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:w-64 max-lg:shadow-2xl">
            <div class="p-5 border-b border-white/10 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    @if(setting('logo_path'))
                        <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="Logo" class="h-9 w-auto max-w-[36px] object-contain shrink-0">
                    @else
                        <span class="grid h-9 w-9 place-items-center rounded-lg bg-emas/15 border border-emas/40 shrink-0">
                            <svg viewBox="0 0 40 40" class="h-5 w-5" fill="none"><path d="M4 32 L4 22 C 9 8, 13 8, 16 22 C 19 8, 21 8, 24 22 C 27 8, 31 8, 36 22 L36 32 Z" fill="#E3C578"/></svg>
                        </span>
                    @endif
                    <span class="font-display text-sm leading-tight">Panel Admin<br><span class="text-emas-light/70 text-xs font-sans">{{ setting('site_name', 'Dashboard Inflasi') }}</span></span>
                </a>
                <button id="adminCloseBtn" class="lg:hidden text-sand/70 hover:text-white p-1">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round"/></svg>
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    <span>Ringkasan</span>
                </a>
                <a href="{{ route('admin.inflasi.index') }}" class="side-link {{ request()->routeIs('admin.inflasi.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Data Inflasi</span>
                </a>
                <a href="{{ route('admin.andil.index') }}" class="side-link {{ request()->routeIs('admin.andil.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Andil Inflasi</span>
                </a>
                <a href="{{ route('admin.pangan.index') }}" class="side-link {{ request()->routeIs('admin.pangan.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span>Harga Pangan</span>
                </a>
                <a href="{{ route('admin.stok.index') }}" class="side-link {{ request()->routeIs('admin.stok.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Stok Pangan</span>
                </a>
                <div class="pt-3 mt-3 border-t border-white/10">
                    <a href="{{ route('admin.settings.edit') }}" class="side-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Pengaturan Situs</span>
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-white/10 space-y-2">
                <a href="{{ route('dashboard') }}" target="_blank" class="side-link">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Lihat Situs</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="side-link w-full text-left !text-rose-300 hover:!bg-rose-500/20">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile Overlay --}}
        <div id="adminOverlay" class="hidden fixed inset-0 bg-ink/60 backdrop-blur-xs z-30 lg:hidden"></div>

        <main class="flex-1 min-w-0 w-full overflow-x-hidden">
            <div class="max-w-[1536px] mx-auto px-4 sm:px-6 md:px-10 py-6 md:py-8 min-w-0">
                @if (session('sukses'))
                    <div class="mb-6 rounded-xl bg-hijau/15 border border-hijau/30 text-hijau px-4 py-3 text-sm font-medium">
                        {{ session('sukses') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-marun/10 border border-marun/30 text-marun-dark px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        window.chartDefaultFont = "'Plus Jakarta Sans', system-ui, sans-serif";
        const adminMenuBtn = document.getElementById('adminMenuBtn');
        const adminCloseBtn = document.getElementById('adminCloseBtn');
        const adminSidebar = document.getElementById('adminSidebar');
        const adminOverlay = document.getElementById('adminOverlay');

        function toggleAdminSidebar() {
            if (adminSidebar.classList.contains('hidden')) {
                adminSidebar.classList.remove('hidden');
                adminOverlay.classList.remove('hidden');
            } else {
                adminSidebar.classList.add('hidden');
                adminOverlay.classList.add('hidden');
            }
        }

        adminMenuBtn?.addEventListener('click', toggleAdminSidebar);
        adminCloseBtn?.addEventListener('click', toggleAdminSidebar);
        adminOverlay?.addEventListener('click', toggleAdminSidebar);
    </script>
    @stack('scripts')
</body>
</html>
