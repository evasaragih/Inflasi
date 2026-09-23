<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin &middot; {{ setting('site_name', 'Dashboard Inflasi') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: { DEFAULT: '#241811', soft: '#6E5F4E' },
                        marun: { DEFAULT: '#D41E43', dark: '#9C1030' },
                        emas: { DEFAULT: '#C0922C', light: '#E3C578', dim: '#8C6A20' },
                        sand: { DEFAULT: '#F1E9D6', dark: '#E6DAB8' },
                        garis: '#E1D3AE',
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        .grid-bg {
            background-color: #241811;
            background-image:
                linear-gradient(rgba(227, 197, 120, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(227, 197, 120, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="min-h-screen font-sans bg-white antialiased">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        {{-- LEFT PANEL: Hero & Branding (5 cols) --}}
        <div class="lg:col-span-5 grid-bg text-sand p-8 lg:p-14 flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-bold text-emas tracking-widest uppercase border border-emas/30 backdrop-blur-md">
                    <span class="h-2 w-2 rounded-full bg-emas animate-pulse"></span>
                    PORTAL INTERNAL
                </span>
            </div>

            <div class="my-auto py-12 text-center relative z-10 space-y-6">
                {{-- Logo Emblem --}}
                <div class="inline-block p-4 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-md shadow-2xl">
                    @if(setting('logo_path'))
                        <img src="{{ asset('storage/' . setting('logo_path')) }}" alt="{{ setting('site_name', 'Logo') }}" class="h-24 w-auto max-w-[200px] object-contain mx-auto">
                    @else
                        <span class="grid h-24 w-24 place-items-center rounded-2xl bg-emas/20 border border-emas/40 text-emas font-black text-3xl shadow-inner">
                            PAD
                        </span>
                    @endif
                </div>

                <div class="space-y-2">
                    <h1 class="font-display text-3xl lg:text-4xl font-extrabold uppercase tracking-tight text-white leading-tight">
                        {{ setting('site_name', 'Dashboard Inflasi') }}
                    </h1>
                    <p class="font-display text-lg lg:text-xl font-bold text-emas-light tracking-wide uppercase">
                        {{ setting('site_subtitle', 'Kota Padang') }}
                    </p>
                </div>

                <p class="text-sand/70 text-sm max-w-sm mx-auto leading-relaxed">
                    Sistem informasi terpadu pemantauan inflasi, andil kelompok pengeluaran, dan harga pangan strategis Kota Padang. Kelola konten dan data dengan aman dan cepat.
                </p>
            </div>

            <div class="relative z-10 text-xs text-sand/40 text-center lg:text-left">
                &copy; {{ date('Y') }} {{ setting('site_name', 'Dashboard Inflasi') }} &middot; Pemerintah Kota Padang
            </div>
        </div>

        {{-- RIGHT PANEL: Form Login (7 cols) --}}
        <div class="lg:col-span-7 bg-white text-ink min-h-screen flex items-center justify-center p-8 lg:p-14">
            <div class="w-full max-w-md space-y-8">

                <div>
                    <h2 class="font-display text-3xl md:text-4xl font-extrabold text-ink tracking-tight">
                        Selamat Datang!
                    </h2>
                    <p class="text-ink-soft text-sm mt-2">
                        Silakan masuk menggunakan kredensial akun Anda untuk mengakses sistem.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm p-4 flex items-center gap-3">
                        <svg viewBox="0 0 20 20" class="h-5 w-5 text-rose-500 shrink-0" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                    @csrf

                    {{-- Field Email --}}
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-ink mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-ink-soft/60 font-semibold">
                                @
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="nama@padang.go.id"
                                class="w-full rounded-2xl border border-garis/80 bg-sand/20 pl-11 pr-4 py-3.5 text-sm text-ink placeholder-ink-soft/40 focus:outline-none focus:ring-2 focus:ring-emas focus:border-emas transition-all duration-200 font-medium">
                        </div>
                    </div>

                    {{-- Field Kata Sandi --}}
                    <div>
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-ink mb-2">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-ink-soft/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="5" y="11" width="14" height="10" rx="2"/>
                                    <path d="M8 11V7a4 4 0 018 0v4"/>
                                </svg>
                            </div>
                            <input type="password" name="password" required
                                placeholder="••••••••"
                                class="w-full rounded-2xl border border-garis/80 bg-sand/20 pl-11 pr-4 py-3.5 text-sm text-ink placeholder-ink-soft/40 focus:outline-none focus:ring-2 focus:ring-emas focus:border-emas transition-all duration-200 font-medium">
                        </div>
                    </div>

                    {{-- Checkbox Remember --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2.5 text-sm text-ink-soft font-semibold cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-garis text-marun focus:ring-emas">
                            Ingat Saya
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="w-full rounded-2xl bg-ink hover:bg-ink/90 text-white font-extrabold py-4 px-6 text-sm shadow-xl shadow-ink/20 hover:shadow-2xl transition-all duration-200 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Masuk Sistem</span>
                        <svg viewBox="0 0 20 20" class="w-5 h-5" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </form>

                {{-- Back Link --}}
                <div class="pt-4 text-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-ink-soft hover:text-marun transition-colors">
                        <span>&larr; Kembali ke Halaman Utama</span>
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>
