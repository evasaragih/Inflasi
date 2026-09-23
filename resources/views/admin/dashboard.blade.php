@extends('admin.layout')

@section('title', 'Ringkasan Analytics')

@section('content')

    {{-- HERO HEADER --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 min-w-0">
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-ink break-words">Ringkasan &amp; Analitik Backend</h1>
            <p class="text-ink-soft text-xs sm:text-sm mt-1">Setiap grafik dan bagian data memiliki filter Tahun &amp; Bulan tersendiri yang dapat disesuaikan.</p>
        </div>

        {{-- Filter Ringkasan Inflasi --}}
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2 shrink-0">
            <input type="hidden" name="tahun_andil" value="{{ $tahunAndil }}">
            <input type="hidden" name="bulan_andil" value="{{ $bulanAndil }}">
            <input type="hidden" name="tahun_pangan" value="{{ $tahunPangan }}">
            <input type="hidden" name="komoditas_pangan" value="{{ $komoditasPangan }}">

            <div class="relative">
                <select name="tahun_inflasi" onchange="this.form.submit()"
                    class="appearance-none rounded-xl border border-garis bg-white/90 pl-3 pr-8 py-2 text-xs font-semibold text-ink shadow-sm hover:border-emas/50 focus:border-emas focus:outline-none transition-all cursor-pointer">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" @selected($tahunInflasi == $t)>Tahun {{ $t }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 text-ink-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <div class="relative">
                <select name="bulan_inflasi" onchange="this.form.submit()"
                    class="appearance-none rounded-xl border border-garis bg-white/90 pl-3 pr-8 py-2 text-xs font-semibold text-ink shadow-sm hover:border-emas/50 focus:border-emas focus:outline-none transition-all cursor-pointer">
                    @foreach($daftarBulan as $u => $namaBln)
                        <option value="{{ $u }}" @selected($bulanInflasi == $u)>{{ $namaBln }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 text-ink-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </form>
    </div>

    {{-- INFOGRAPHIC METRIC CARDS --}}
    @php
        $padangData = $terkini['padang'] ?? null;
        $yoyVal = $padangData->yoy ?? 0;
        $mtmVal = $padangData->mtm ?? 0;
        $sumbarYoy = $terkini['sumbar']->yoy ?? 0;
        $panganTop = $panganNaik->first();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5 mb-6 sm:mb-8 min-w-0">
        {{-- Card 1: YoY Padang --}}
        <div class="rounded-2xl border border-garis bg-white/80 backdrop-blur-md p-4 sm:p-5 shadow-xs hover:border-emas/60 transition-all">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft">YoY Padang</span>
                <span class="inline-flex items-center gap-0.5 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full {{ $yoyVal < 0 ? 'bg-hijau/10 text-hijau border border-hijau/20' : 'bg-marun/10 text-marun border border-marun/20' }}">
                    {{ $yoyVal < 0 ? '▼ Deflasi' : '▲ Inflasi' }}
                </span>
            </div>
            <p class="angka-data text-2xl sm:text-3xl font-extrabold tracking-tight {{ $yoyVal < 0 ? 'text-hijau' : 'text-marun' }}">
                {{ fsign($yoyVal) }}<span class="text-base font-semibold">%</span>
            </p>
            <p class="text-[11px] text-ink-soft mt-2 border-t border-garis/40 pt-2 flex items-center justify-between">
                <span>{{ $namaBulanTerkini }} {{ $tahunInflasi }}</span>
                <span class="font-semibold text-ink">IHK: {{ $padangData?->ihk ? fnum($padangData->ihk) : '-' }}</span>
            </p>
        </div>

        {{-- Card 2: MTM Padang --}}
        <div class="rounded-2xl border border-garis bg-white/80 backdrop-blur-md p-4 sm:p-5 shadow-xs hover:border-emas/60 transition-all">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft">MTM Padang</span>
                <span class="inline-flex items-center gap-0.5 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full {{ $mtmVal < 0 ? 'bg-hijau/10 text-hijau border border-hijau/20' : 'bg-marun/10 text-marun border border-marun/20' }}">
                    {{ $mtmVal < 0 ? '▼ Bulanan' : '▲ Bulanan' }}
                </span>
            </div>
            <p class="angka-data text-2xl sm:text-3xl font-extrabold tracking-tight {{ $mtmVal < 0 ? 'text-hijau' : 'text-marun' }}">
                {{ fsign($mtmVal) }}<span class="text-base font-semibold">%</span>
            </p>
            <p class="text-[11px] text-ink-soft mt-2 border-t border-garis/40 pt-2 flex items-center justify-between">
                <span>vs Bln Lalu</span>
                <span class="font-semibold text-ink">YoY Sumbar: {{ fsign($sumbarYoy) }}%</span>
            </p>
        </div>

        {{-- Card 3: IHK Kota Padang (Diganti dari DB Record & Ditukar Posisi) --}}
        <div class="rounded-2xl border border-garis bg-white/80 backdrop-blur-md p-4 sm:p-5 shadow-xs hover:border-emas/60 transition-all">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft">IHK Kota Padang</span>
                <span class="inline-flex items-center gap-0.5 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full bg-emas/15 text-emas-dim border border-emas/30">
                    Indeks Harga
                </span>
            </div>
            <p class="angka-data text-2xl sm:text-3xl font-extrabold tracking-tight text-ink">
                {{ $padangData?->ihk ? fnum($padangData->ihk) : '-' }}
            </p>
            <p class="text-[11px] text-ink-soft mt-2 border-t border-garis/40 pt-2 flex items-center justify-between">
                <span>BPS Kota Padang</span>
                <span class="font-semibold text-ink">{{ $namaBulanTerkini }} {{ $tahunInflasi }}</span>
            </p>
        </div>

        {{-- Card 4: Lonjakan Pangan (Ditukar Posisi) --}}
        <div class="rounded-2xl border border-garis bg-white/80 backdrop-blur-md p-4 sm:p-5 shadow-xs hover:border-emas/60 transition-all">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft">Lonjakan Pangan</span>
                <span class="inline-flex items-center gap-0.5 text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-700 border border-amber-300">
                    Paling Tinggi
                </span>
            </div>
            <p class="text-base sm:text-lg font-bold text-ink truncate">
                {{ $panganTop->komoditas ?? 'Pangan' }}
            </p>
            <p class="text-[11px] text-ink-soft mt-1.5 border-t border-garis/40 pt-1.5 flex items-center justify-between">
                <span class="angka-data font-bold text-marun">+{{ fnum($panganTop?->persen ?? 0) }}%</span>
                <span class="angka-data text-ink-soft text-[10px]">Rp {{ fnum($panganTop?->harga_ini ?? 0, 0) }}</span>
            </p>
        </div>
    </div>

    {{-- BAGIAN 1 & 2: GRAFIK INFLASI & GRAFIK ANDIL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 mb-6 sm:mb-8 min-w-0">
        
        {{-- GRAFIK 1: Tren Inflasi YoY (Filter Mandiri Tahun Inflasi) --}}
        <div class="lg:col-span-2 rounded-2xl border border-garis bg-white p-4 sm:p-6 shadow-xs overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 border-b border-garis/40 pb-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-marun"></span>
                        <h3 class="font-display text-base sm:text-lg font-bold text-ink">Grafik 1: Tren Inflasi YoY</h3>
                    </div>
                    <p class="text-xs text-ink-soft mt-0.5">Perbandingan Kota Padang, Sumbar, dan Nasional</p>
                </div>

                {{-- Filter Khusus Grafik Inflasi --}}
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2 shrink-0">
                    <input type="hidden" name="tahun_andil" value="{{ $tahunAndil }}">
                    <input type="hidden" name="bulan_andil" value="{{ $bulanAndil }}">
                    <input type="hidden" name="tahun_pangan" value="{{ $tahunPangan }}">
                    <input type="hidden" name="komoditas_pangan" value="{{ $komoditasPangan }}">
                    <input type="hidden" name="bulan_inflasi" value="{{ $bulanInflasi }}">

                    <div class="relative">
                        <select name="tahun_inflasi" onchange="this.form.submit()"
                            class="appearance-none rounded-lg border border-garis bg-sand-dark/20 pl-2.5 pr-7 py-1 text-xs font-semibold text-ink shadow-2xs hover:border-emas focus:outline-none transition-all cursor-pointer">
                            @foreach($daftarTahun as $t)
                                <option value="{{ $t }}" @selected($tahunInflasi == $t)>Tahun {{ $t }}</option>
                            @endforeach
                        </select>
                        <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </form>
            </div>

            <div class="h-64 sm:h-72 w-full">
                <canvas id="chartAdminTren"></canvas>
            </div>
        </div>

        {{-- GRAFIK 2: Andil Inflasi per Kelompok (Filter Mandiri Tahun & Bulan Andil) --}}
        <div class="rounded-2xl border border-garis bg-white p-4 sm:p-6 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3 border-b border-garis/40 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-emas"></span>
                            <h3 class="font-display text-base font-bold text-ink">Grafik 2: Andil Kelompok</h3>
                        </div>
                        <p class="text-xs text-ink-soft mt-0.5">Sumbangan kelompok pengeluaran</p>
                    </div>
                </div>

                {{-- Filter Khusus Grafik Andil --}}
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2 mb-4 bg-sand-dark/20 p-2 rounded-xl border border-garis/50">
                    <input type="hidden" name="tahun_inflasi" value="{{ $tahunInflasi }}">
                    <input type="hidden" name="bulan_inflasi" value="{{ $bulanInflasi }}">
                    <input type="hidden" name="tahun_pangan" value="{{ $tahunPangan }}">
                    <input type="hidden" name="komoditas_pangan" value="{{ $komoditasPangan }}">

                    <span class="text-[10px] font-bold uppercase text-ink-soft shrink-0">Filter Andil:</span>
                    <div class="relative flex-1 min-w-[90px]">
                        <select name="tahun_andil" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-lg border border-garis bg-white pl-2 pr-6 py-1 text-[11px] font-semibold text-ink focus:outline-none transition-all cursor-pointer">
                            @foreach($daftarTahun as $t)
                                <option value="{{ $t }}" @selected($tahunAndil == $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-1.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>

                    <div class="relative flex-1 min-w-[100px]">
                        <select name="bulan_andil" onchange="this.form.submit()"
                            class="w-full appearance-none rounded-lg border border-garis bg-white pl-2 pr-6 py-1 text-[11px] font-semibold text-ink focus:outline-none transition-all cursor-pointer">
                            @foreach($daftarBulan as $u => $namaBln)
                                <option value="{{ $u }}" @selected($bulanAndil == $u)>{{ $namaBln }}</option>
                            @endforeach
                        </select>
                        <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-1.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </form>
            </div>

            <div class="h-56 w-full">
                <canvas id="chartAdminAndil"></canvas>
            </div>
        </div>
    </div>

    {{-- BAGIAN 3: GRAFIK HARGA PANGAN & AKSI CEPAT --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 mb-6 sm:mb-8 min-w-0">
        
        {{-- GRAFIK 3: Harga Pangan Strategis vs HET per Komoditas (Filter Tahun & Komoditas) --}}
        <div class="lg:col-span-2 rounded-2xl border border-garis bg-white p-4 sm:p-6 shadow-xs overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 border-b border-garis/40 pb-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-hijau"></span>
                        <h3 class="font-display text-base sm:text-lg font-bold text-ink">Grafik 3: Tren Harga Komoditas vs HET/HA</h3>
                    </div>
                    <p class="text-xs text-ink-soft mt-0.5">Komoditas: <span class="font-bold text-ink">{{ $komoditasPangan }}</span> (Rp / {{ $satuanPangan }}) &middot; Tahun {{ $tahunPangan }}</p>
                </div>

                {{-- Filter Khusus Grafik Harga Pangan (Hanya Tahun & Komoditas) --}}
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2 shrink-0">
                    <input type="hidden" name="tahun_inflasi" value="{{ $tahunInflasi }}">
                    <input type="hidden" name="bulan_inflasi" value="{{ $bulanInflasi }}">
                    <input type="hidden" name="tahun_andil" value="{{ $tahunAndil }}">
                    <input type="hidden" name="bulan_andil" value="{{ $bulanAndil }}">

                    <div class="relative">
                        <select name="tahun_pangan" onchange="this.form.submit()"
                            class="appearance-none rounded-lg border border-garis bg-sand-dark/20 pl-2.5 pr-7 py-1 text-xs font-semibold text-ink shadow-2xs hover:border-emas focus:outline-none transition-all cursor-pointer">
                            @foreach($daftarTahun as $t)
                                <option value="{{ $t }}" @selected($tahunPangan == $t)>Tahun {{ $t }}</option>
                            @endforeach
                        </select>
                        <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>

                    <div class="relative">
                        <select name="komoditas_pangan" onchange="this.form.submit()"
                            class="appearance-none rounded-lg border border-garis bg-white pl-2.5 pr-7 py-1 text-xs font-bold text-marun shadow-2xs hover:border-emas focus:outline-none transition-all cursor-pointer max-w-[160px] truncate">
                            @foreach($daftarKomoditas as $k)
                                <option value="{{ $k }}" @selected($komoditasPangan == $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                        <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </form>
            </div>

            <div class="h-64 sm:h-72 w-full">
                <canvas id="chartAdminPangan"></canvas>
            </div>
        </div>

        {{-- Quick Actions Panel --}}
        <div class="rounded-2xl border border-garis bg-white/90 p-4 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="font-display text-base sm:text-lg font-bold text-ink mb-1">Aksi Cepat Admin</h3>
                <p class="text-xs text-ink-soft mb-4">Akses cepat kelola data &amp; identitas situs.</p>

                <div class="space-y-2.5">
                    <a href="{{ route('admin.inflasi.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-garis bg-sand-dark/20 hover:border-emas hover:bg-white transition-all text-xs font-semibold text-ink">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-marun"></span>
                            + Tambah Data Inflasi
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('admin.andil.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-garis bg-sand-dark/20 hover:border-emas hover:bg-white transition-all text-xs font-semibold text-ink">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emas"></span>
                            + Tambah Andil Kelompok
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('admin.pangan.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-garis bg-sand-dark/20 hover:border-emas hover:bg-white transition-all text-xs font-semibold text-ink">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-hijau"></span>
                            + Tambah Harga Pangan
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('admin.settings.edit') }}" class="flex items-center justify-between p-3 rounded-xl border border-garis bg-sand-dark/20 hover:border-emas hover:bg-white transition-all text-xs font-semibold text-ink">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-ink"></span>
                            ⚙ Pengaturan Logo &amp; Teks
                        </span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="mt-5 border-t border-garis/50 pt-4 flex items-center justify-between">
                <span class="text-[11px] text-ink-soft font-medium">Status Server &amp; DB</span>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-hijau px-2 py-0.5 rounded-full bg-hijau/10 border border-hijau/20">
                    <span class="h-1.5 w-1.5 rounded-full bg-hijau animate-pulse"></span>
                    Aktif (SQLite)
                </span>
            </div>
        </div>
    </div>

    {{-- BAGIAN 4: RINGKASAN STOK PANGAN PER KOMODITI (FILTER TAHUN & BULAN) --}}
    <div class="rounded-2xl border border-garis bg-white p-4 sm:p-6 shadow-xs overflow-hidden mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 border-b border-garis/40 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="h-2.5 w-2.5 rounded-full bg-emas"></span>
                    <h3 class="font-display text-base sm:text-lg font-bold text-ink">Ringkasan Stok Pangan Kota Padang</h3>
                </div>
                <p class="text-xs text-ink-soft mt-0.5">Estimasi pasokan stok, kebutuhan bulanan, dan status ketahanan pangan &middot; {{ $daftarBulan[$bulanStok] ?? '' }} {{ $tahunStok }}</p>
            </div>

            {{-- Filter Khusus Stok Pangan (Tahun & Bulan) --}}
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2 shrink-0">
                <input type="hidden" name="tahun_inflasi" value="{{ $tahunInflasi }}">
                <input type="hidden" name="bulan_inflasi" value="{{ $bulanInflasi }}">
                <input type="hidden" name="tahun_andil" value="{{ $tahunAndil }}">
                <input type="hidden" name="bulan_andil" value="{{ $bulanAndil }}">
                <input type="hidden" name="tahun_pangan" value="{{ $tahunPangan }}">
                <input type="hidden" name="komoditas_pangan" value="{{ $komoditasPangan }}">

                <div class="relative">
                    <select name="tahun_stok" onchange="this.form.submit()"
                        class="appearance-none rounded-lg border border-garis bg-sand-dark/20 pl-2.5 pr-7 py-1 text-xs font-semibold text-ink shadow-2xs hover:border-emas focus:outline-none transition-all cursor-pointer">
                        @foreach($daftarTahun as $t)
                            <option value="{{ $t }}" @selected($tahunStok == $t)>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                    <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>

                <div class="relative">
                    <select name="bulan_stok" onchange="this.form.submit()"
                        class="appearance-none rounded-lg border border-garis bg-sand-dark/20 pl-2.5 pr-7 py-1 text-xs font-semibold text-ink shadow-2xs hover:border-emas focus:outline-none transition-all cursor-pointer">
                        @foreach($daftarBulan as $u => $namaBln)
                            <option value="{{ $u }}" @selected($bulanStok == $u)>{{ $namaBln }}</option>
                        @endforeach
                    </select>
                    <svg viewBox="0 0 16 16" class="h-3 w-3 text-ink-soft absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </form>
        </div>

        {{-- Table / Grid Stok Pangan --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-ink border-collapse">
                <thead>
                    <tr class="border-b border-garis/60 bg-sand-dark/20 text-ink-soft uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-2.5 px-3">Komoditas Pangan</th>
                        <th class="py-2.5 px-3 text-right">Ketersediaan Stok</th>
                        <th class="py-2.5 px-3 text-right">Kebutuhan Bulanan</th>
                        <th class="py-2.5 px-3 text-center">Ketahanan Stok</th>
                        <th class="py-2.5 px-3 text-center">Status Pasokan</th>
                        <th class="py-2.5 px-3 text-right">Harga Pasar / HET/HA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-garis/30">
                    @foreach($stokPanganList as $stok)
                        <tr class="hover:bg-sand-dark/10 transition-colors">
                            <td class="py-2.5 px-3 font-bold text-ink">{{ $stok->komoditas }}</td>
                            <td class="py-2.5 px-3 text-right angka-data font-extrabold text-ink">{{ fnum($stok->stok, 0) }} <span class="text-[10px] font-normal text-ink-soft">{{ $stok->satuan }}</span></td>
                            <td class="py-2.5 px-3 text-right angka-data font-semibold text-ink-soft">{{ fnum($stok->kebutuhan, 0) }} <span class="text-[10px] font-normal">{{ $stok->satuan }}</span></td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="w-24 mx-auto bg-garis/40 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full {{ $stok->rasio >= 115 ? 'bg-hijau' : ($stok->rasio >= 98 ? 'bg-blue-600' : 'bg-marun') }}" style="width: {{ min(100, $stok->rasio) }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold angka-data mt-0.5 block text-ink-soft">{{ $stok->rasio }}%</span>
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full border {{ $stok->badgeClass }}">
                                    {{ $stok->status }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right angka-data">
                                <span class="font-bold text-marun">Rp {{ fnum($stok->harga_ini, 0) }}</span>
                                <span class="text-[10px] text-ink-soft block">HET/HA: Rp {{ fnum($stok->het, 0) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- DETAIL TABLES SUMMARY --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 min-w-0">
        {{-- Top Andil --}}
        <div class="rounded-2xl border border-garis bg-white p-4 sm:p-6 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between gap-2 mb-3">
                <div>
                    <h3 class="font-display text-base font-bold text-ink">Penyumbang Inflasi Dominan</h3>
                    <p class="text-xs text-ink-soft">Andil tertinggi pada {{ $daftarBulan[$bulanAndil] ?? '' }} {{ $tahunAndil }}</p>
                </div>
                <a href="{{ route('admin.andil.index') }}" class="text-xs font-bold text-marun hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @foreach($andilTertinggi as $a)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-garis/60 bg-sand-dark/10">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-ink truncate">{{ $a->kelompok }}</p>
                            <p class="text-[10px] text-ink-soft">Inflasi YoY {{ fsign($a->yoy) }}%</p>
                        </div>
                        <span class="chip chip-naik text-[10px] px-2.5 py-1 font-bold shrink-0">+{{ fnum($a->andil_yoy) }}%</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Pangan --}}
        <div class="rounded-2xl border border-garis bg-white p-4 sm:p-6 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between gap-2 mb-3">
                <div>
                    <h3 class="font-display text-base font-bold text-ink">Pergerakan Harga Komoditas</h3>
                    <p class="text-xs text-ink-soft">Kenaikan harga tertinggi pada Tahun {{ $tahunPangan }}</p>
                </div>
                <a href="{{ route('admin.pangan.index') }}" class="text-xs font-bold text-marun hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @foreach($panganNaik as $p)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-garis/60 bg-sand-dark/10">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-ink truncate">{{ $p->komoditas }}</p>
                            <p class="text-[10px] text-ink-soft angka-data">Rp {{ fnum($p->harga_ini, 0) }} / {{ $p->satuan }}</p>
                        </div>
                        <span class="chip {{ $p->persen >= 0 ? 'chip-naik' : 'chip-turun' }} text-[10px] px-2.5 py-1 font-bold shrink-0">{{ fsign($p->persen) }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // 1. Chart Tren Inflasi YoY (Bar Chart dengan Gradasi 3 Warna Soft & Light)
    const ctxBar = document.getElementById('chartAdminTren').getContext('2d');

    // Gradasi 3 Warna Padang (Soft Crimson Maroon)
    const gradPadang = ctxBar.createLinearGradient(0, 0, 0, 280);
    gradPadang.addColorStop(0, 'rgba(244, 63, 94, 0.85)');   // 1. Top: Soft Rose Crimson
    gradPadang.addColorStop(0.5, 'rgba(225, 29, 72, 0.72)'); // 2. Middle: Light Maroon
    gradPadang.addColorStop(1, 'rgba(156, 16, 48, 0.60)');   // 3. Bottom: Soft Wine

    // Gradasi 3 Warna Sumbar (Soft Amber Gold)
    const gradSumbar = ctxBar.createLinearGradient(0, 0, 0, 280);
    gradSumbar.addColorStop(0, 'rgba(251, 191, 36, 0.85)');   // 1. Top: Soft Gold
    gradSumbar.addColorStop(0.5, 'rgba(217, 119, 6, 0.72)');  // 2. Middle: Light Amber
    gradSumbar.addColorStop(1, 'rgba(146, 64, 14, 0.60)');   // 3. Bottom: Soft Bronze

    // Gradasi 3 Warna Nasional (Soft Warm Espresso)
    const gradNasional = ctxBar.createLinearGradient(0, 0, 0, 280);
    gradNasional.addColorStop(0, 'rgba(180, 83, 9, 0.75)');   // 1. Top: Soft Warm Tan
    gradNasional.addColorStop(0.5, 'rgba(120, 53, 15, 0.62)');  // 2. Middle: Soft Chocolate
    gradNasional.addColorStop(1, 'rgba(50, 25, 10, 0.50)');   // 3. Bottom: Soft Espresso

    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: @json($labelBulanInflasi),
            datasets: [
                {
                    label: 'Kota Padang',
                    data: @json($seriPadangYoy),
                    backgroundColor: gradPadang,
                    borderColor: 'rgba(225, 29, 72, 0.9)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.75,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Sumatera Barat',
                    data: @json($seriSumbarYoy),
                    backgroundColor: gradSumbar,
                    borderColor: 'rgba(217, 119, 6, 0.9)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.75,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Nasional',
                    data: @json($seriNasionalYoy),
                    backgroundColor: gradNasional,
                    borderColor: 'rgba(120, 53, 15, 0.9)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.75,
                    categoryPercentage: 0.7
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: window.chartDefaultFont, size: 11, weight: '600' }, usePointStyle: true } },
                tooltip: { callbacks: { label: (c) => ` ${c.dataset.label}: ${c.formattedValue}%` } }
            },
            scales: {
                y: { ticks: { callback: (v) => v + '%', font: { family: window.chartDefaultFont, size: 10 } }, grid: { color: 'rgba(225, 211, 174, 0.35)' } },
                x: { ticks: { font: { family: window.chartDefaultFont, size: 10 } }, grid: { display: false } }
            }
        }
    });

    // 2. Chart Andil Inflasi
    new Chart(document.getElementById('chartAdminAndil'), {
        type: 'bar',
        data: {
            labels: @json($labelAndil),
            datasets: [{
                label: 'Andil YoY (%)',
                data: @json($dataAndilYoy),
                backgroundColor: (c) => (c.raw < 0 ? '#12965A' : '#C0922C'),
                borderRadius: 5,
                barPercentage: 0.65
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (c) => ` Andil: ${c.formattedValue}%` } }
            },
            scales: {
                x: { ticks: { callback: (v) => v + '%', font: { family: window.chartDefaultFont, size: 9 } }, grid: { color: 'rgba(225, 211, 174, 0.35)' } },
                y: { ticks: { font: { family: window.chartDefaultFont, size: 9 } }, grid: { display: false } }
            }
        }
    });

    // 3. Chart Harga Pangan Mingguan vs HET Per Komoditi (Line Chart / Diagram Garis)
    new Chart(document.getElementById('chartAdminPangan'), {
        type: 'line',
        data: {
            labels: @json($labelMingguPangan),
            datasets: [
                {
                    label: 'Harga Pasar (Rp)',
                    data: @json($seriHargaMingguan),
                    borderColor: '#D41E43',
                    backgroundColor: 'rgba(212, 30, 67, 0.12)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#D41E43',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'HET/HA Limit (Rp)',
                    data: @json($seriHetMingguan),
                    borderColor: '#C0922C',
                    borderWidth: 2.5,
                    borderDash: [6, 4],
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#C0922C',
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: window.chartDefaultFont, size: 11, weight: '600' }, usePointStyle: true } },
                tooltip: { callbacks: { label: (c) => ` ${c.dataset.label}: Rp ${c.formattedValue}` } }
            },
            scales: {
                y: { ticks: { callback: (v) => 'Rp ' + (v/1000) + 'rb', font: { family: window.chartDefaultFont, size: 10 } }, grid: { color: 'rgba(225, 211, 174, 0.35)' } },
                x: { ticks: { font: { family: window.chartDefaultFont, size: 10 } }, grid: { display: false } }
            }
        }
    });
</script>
@endpush
