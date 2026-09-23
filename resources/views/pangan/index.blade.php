@extends('layouts.app')

@section('title', 'Harga Pangan Strategis')

@section('content')

    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4 min-w-0">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-widest text-emas-dim truncate">Pemantauan Harga Pangan</p>
            <h1 class="font-display text-2xl sm:text-4xl mt-1 truncate">Komoditas Strategis</h1>
            <p class="text-ink-soft text-xs sm:text-sm mt-1 sm:mt-2 max-w-2xl">{{ setting('pangan_desc', 'Harga mingguan komoditas pokok di Kota Padang dibandingkan Harga Eceran Tertinggi (HET/HA).') }}</p>
        </div>

        {{-- Filter Bar: Search, Tahun, & Komoditas --}}
        <form method="GET" action="{{ route('pangan.index') }}" class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-56 min-w-[150px]">
                <svg viewBox="0 0 20 20" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft/70 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="6"/><path d="M14 14l4 4" stroke-linecap="round"/>
                </svg>
                <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari komoditas..."
                    class="w-full rounded-xl border border-garis bg-white/90 pl-9 pr-3 py-2 text-xs sm:text-sm font-medium text-ink placeholder:text-ink-soft/60 shadow-sm hover:border-emas/50 focus:border-emas focus:outline-none transition-all">
            </div>

            @if(isset($daftarTahun) && count($daftarTahun) > 0)
            <div class="relative">
                <select name="tahun" onchange="this.form.submit()"
                    class="appearance-none rounded-xl border border-garis bg-white/90 pl-3 pr-8 py-2 text-xs sm:text-sm font-semibold text-ink shadow-sm hover:border-emas/50 focus:border-emas focus:outline-none transition-all cursor-pointer">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" @selected(($tahunDipilih ?? 2026) == $t)>Tahun {{ $t }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 text-ink-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            @endif

            @if(isset($daftarKomoditas) && count($daftarKomoditas) > 0)
            <div class="relative">
                <select name="komoditas" onchange="this.form.submit()"
                    class="appearance-none rounded-xl border border-garis bg-white/90 pl-3 pr-8 py-2 text-xs sm:text-sm font-semibold text-ink shadow-sm hover:border-emas/50 focus:border-emas focus:outline-none transition-all cursor-pointer max-w-[180px] truncate">
                    @foreach($daftarKomoditas as $k)
                        <option value="{{ $k }}" @selected(($komoditasDipilih ?? '') == $k)>{{ $k }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 text-ink-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            @endif

            <button type="submit" class="rounded-xl bg-marun hover:bg-marun-dark text-white px-3.5 py-2 text-xs sm:text-sm font-semibold shadow-sm transition-all shrink-0">
                Cari
            </button>
        </form>
    </div>

    <style>
        #dropdownPanelK { transform-origin: top right; }
        #dropdownPanelK.open {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        #dropdownPanelK::-webkit-scrollbar { width: 4px; }
        #dropdownPanelK::-webkit-scrollbar-track { background: transparent; }
        #dropdownPanelK::-webkit-scrollbar-thumb { background: #E1D3AE; border-radius: 4px; }
        #dropdownPanelK::-webkit-scrollbar-thumb:hover { background: #C0922C; }
    </style>
    <script>
        (function () {
            const trigger = document.getElementById('dropdownTriggerK');
            const panel   = document.getElementById('dropdownPanelK');
            const chevron = document.getElementById('dropdownChevronK');
            const label   = document.getElementById('dropdownLabelK');
            const hidden  = document.getElementById('hiddenKomoditas');
            const form    = document.getElementById('formKomoditas');
            const items   = document.querySelectorAll('.dropdown-komoditas-item');

            function openPanel() {
                panel.style.display = 'block';
                requestAnimationFrame(() => panel.classList.add('open'));
                chevron.style.transform = 'rotate(180deg)';
                trigger.setAttribute('aria-expanded', 'true');
                // Scroll ke item aktif
                const active = panel.querySelector('[aria-selected="true"]');
                if (active) active.scrollIntoView({ block: 'nearest' });
            }

            function closePanel() {
                panel.classList.remove('open');
                chevron.style.transform = 'rotate(0deg)';
                trigger.setAttribute('aria-expanded', 'false');
                setTimeout(() => { panel.style.display = 'none'; }, 180);
            }

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                panel.style.display === 'none' ? openPanel() : closePanel();
            });

            items.forEach(function (item) {
                item.addEventListener('click', function () {
                    hidden.value = this.dataset.value;
                    label.textContent = this.dataset.value;
                    closePanel();
                    form.submit();
                });
            });

            document.addEventListener('click', function (e) {
                if (!document.getElementById('dropdownKomoditas').contains(e.target)) {
                    closePanel();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closePanel();
            });
        })();
    </script>

    <section class="kartu kartu-hover p-4 sm:p-6 md:p-8 min-w-0 w-full overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-4 sm:mb-5 min-w-0">
            <div class="min-w-0">
                <h2 class="font-display text-lg sm:text-xl font-bold truncate">{{ $komoditasDipilih }}</h2>
                <p class="text-xs sm:text-sm text-ink-soft">Pergerakan harga mingguan Januari &ndash; Juli 2026</p>
            </div>
            <div class="text-left sm:text-right min-w-0">
                <p class="text-[10px] sm:text-xs text-ink-soft font-bold uppercase tracking-wide">Harga Terkini</p>
                <p class="font-display text-2xl sm:text-3xl font-extrabold text-marun">Rp {{ fnum($hargaSekarang, 0) }}</p>
                <p class="text-[10px] sm:text-xs {{ ($perubahanTotal ?? 0) < 0 ? 'text-hijau' : 'text-marun-dark' }} font-bold mt-0.5">{{ fsign($perubahanTotal) }}% sejak Januari</p>
            </div>
        </div>
        <div class="h-60 sm:h-72 w-full max-w-full overflow-hidden">
            <canvas id="chartPangan"></canvas>
        </div>
    </section>

    <section class="kartu kartu-hover mt-6 sm:mt-8 overflow-hidden min-w-0 w-full">
        <div class="p-4 sm:p-6 md:p-8 pb-3 sm:pb-4 min-w-0">
            <h2 class="font-display text-base sm:text-xl font-bold truncate">Riwayat Mingguan &mdash; {{ $komoditasDipilih }}</h2>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs sm:text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-left text-ink-soft border-y border-garis bg-sand-dark/50">
                        <th class="py-3 sm:py-3.5 px-4 sm:px-6 font-bold">Bulan</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Minggu</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">HET/HA</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Harga</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Perubahan</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                        <tr class="border-b border-garis/60 last:border-0 hover:bg-white/80 transition-colors duration-150">
                            <td class="py-3 sm:py-3.5 px-4 sm:px-6 font-semibold">{{ $r->bulan }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4">Minggu {{ $r->minggu }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data text-ink-soft">Rp {{ fnum($r->het, 0) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-bold">Rp {{ fnum($r->harga_ini, 0) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-semibold {{ $r->perubahan < 0 ? 'text-hijau' : ($r->perubahan > 0 ? 'text-marun-dark' : 'text-ink-soft') }}">{{ fsign($r->perubahan, 0) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-semibold {{ $r->persen < 0 ? 'text-hijau' : ($r->persen > 0 ? 'text-marun-dark' : 'text-ink-soft') }}">{{ fsign($r->persen) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="kartu kartu-hover mt-6 sm:mt-8 overflow-hidden min-w-0 w-full">
        <div class="p-4 sm:p-6 md:p-8 pb-3 sm:pb-4 min-w-0">
            <h2 class="font-display text-base sm:text-xl font-bold truncate">Semua Komoditas &mdash; Minggu Terakhir</h2>
            <p class="text-xs sm:text-sm text-ink-soft">Perbandingan cepat seluruh komoditas pangan strategis pada data terbaru.</p>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs sm:text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-left text-ink-soft border-y border-garis bg-sand-dark/50">
                        <th class="py-3 sm:py-3.5 px-4 sm:px-6 font-bold">Komoditas</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Satuan</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Harga</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ringkasanSemua as $r)
                        <tr class="border-b border-garis/60 last:border-0 hover:bg-white/80 transition-colors duration-150 {{ $r->komoditas === $komoditasDipilih ? 'bg-emas/15' : '' }}">
                            <td class="py-3 sm:py-3.5 px-4 sm:px-6 font-semibold">
                                <a href="{{ route('pangan.index', ['komoditas' => $r->komoditas]) }}" class="hover:text-marun hover:underline">{{ $r->komoditas }}</a>
                            </td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 text-ink-soft">{{ $r->satuan }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-bold">Rp {{ fnum($r->harga_ini, 0) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4">
                                <span class="chip {{ $r->persen < 0 ? 'chip-turun' : 'chip-naik' }}">{{ fsign($r->persen) }}%</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('chartPangan'), {
        type: 'line',
        data: {
            labels: @json($labelMingguan),
            datasets: [
                { label: 'Harga Pasar', data: @json($seriHarga), borderColor: '#D41E43', backgroundColor: 'rgba(212,30,67,0.10)', borderWidth: 3, tension: 0.3, fill: true, pointRadius: 2 },
                { label: 'HET/HA', data: @json($seriHet), borderColor: '#C0922C', borderWidth: 2, borderDash: [6,4], tension: 0, pointRadius: 0 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: window.chartDefaultFont }, usePointStyle: true } },
                tooltip: { callbacks: { label: (c) => `${c.dataset.label}: Rp ${c.formattedValue}` } },
            },
            scales: {
                y: { ticks: { callback: (v) => 'Rp' + (v/1000) + 'rb', font: { family: window.chartDefaultFont } }, grid: { color: '#E1D3AE55' } },
                x: { ticks: { font: { family: window.chartDefaultFont, size: 10 } }, grid: { display: false } },
            }
        }
    });
</script>
@endpush
