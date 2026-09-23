@extends('layouts.app')

@section('title', 'Andil Inflasi')

@section('content')

    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4 min-w-0">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-widest text-emas-dim truncate">Andil Inflasi per Kelompok Pengeluaran</p>
            <h1 class="font-display text-2xl sm:text-4xl mt-1 truncate">Kota Padang</h1>
            <p class="text-ink-soft text-xs sm:text-sm mt-1 sm:mt-2 max-w-2xl">{{ setting('andil_desc', 'Seberapa besar tiap kelompok pengeluaran menyumbang terhadap angka inflasi headline.') }}</p>
        </div>

        {{-- Filter Bar: Search, Tahun, & Bulan --}}
        <form method="GET" action="{{ route('andil.index') }}" class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-56 min-w-[150px]">
                <svg viewBox="0 0 20 20" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft/70 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="6"/><path d="M14 14l4 4" stroke-linecap="round"/>
                </svg>
                <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari kelompok..."
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

            @if(isset($daftarBulan) && count($daftarBulan) > 0)
            <div class="relative">
                <select name="bulan" onchange="this.form.submit()"
                    class="appearance-none rounded-xl border border-garis bg-white/90 pl-3 pr-8 py-2 text-xs sm:text-sm font-semibold text-ink shadow-sm hover:border-emas/50 focus:border-emas focus:outline-none transition-all cursor-pointer">
                    @foreach($daftarBulan as $u => $namaBln)
                        <option value="{{ $u }}" @selected(($bulanDipilih ?? 1) == $u)>{{ $namaBln }}</option>
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
        #dropdownPanel { transform-origin: top right; }
        #dropdownPanel.open {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    </style>
    <script>
        (function () {
            const trigger  = document.getElementById('dropdownTrigger');
            const panel    = document.getElementById('dropdownPanel');
            const chevron  = document.getElementById('dropdownChevron');
            const label    = document.getElementById('dropdownLabel');
            const hidden   = document.getElementById('hiddenBulan');
            const form     = document.getElementById('formBulan');
            const items    = document.querySelectorAll('.dropdown-bulan-item');

            function openPanel() {
                panel.style.display = 'block';
                requestAnimationFrame(() => panel.classList.add('open'));
                chevron.style.transform = 'rotate(180deg)';
                trigger.setAttribute('aria-expanded', 'true');
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
                    const val  = this.dataset.value;
                    const text = this.textContent.trim();
                    hidden.value = val;
                    label.textContent = text;
                    closePanel();
                    form.submit();
                });
            });

            document.addEventListener('click', function (e) {
                if (!document.getElementById('dropdownBulan').contains(e.target)) {
                    closePanel();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closePanel();
            });
        })();
    </script>

    @if($headline)
    <section class="kartu kartu-hover p-4 sm:p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6 min-w-0 overflow-hidden">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs text-ink-soft font-bold uppercase tracking-wide truncate">Inflasi Headline (Umum)</p>
            <p class="font-display text-3xl sm:text-4xl font-extrabold text-marun mt-0.5 sm:mt-1 truncate">{{ fsign($headline->yoy) }}%</p>
        </div>
        <div class="h-10 w-px bg-garis hidden sm:block"></div>
        <div class="flex gap-5 sm:gap-6 text-xs sm:text-sm min-w-0">
            <div><span class="text-ink-soft block font-medium">MTM</span><span class="angka-data font-bold text-sm sm:text-base">{{ fsign($headline->mtm) }}%</span></div>
            <div><span class="text-ink-soft block font-medium">YTD</span><span class="angka-data font-bold text-sm sm:text-base">{{ fsign($headline->ytd) }}%</span></div>
        </div>
    </section>
    @endif

    <section class="kartu kartu-hover mt-6 sm:mt-8 p-4 sm:p-6 md:p-8 min-w-0 w-full overflow-hidden">
        <h2 class="font-display text-base sm:text-xl font-bold mb-1 truncate">Andil Inflasi Year on Year</h2>
        <p class="text-xs sm:text-sm text-ink-soft mb-4 sm:mb-5">Kontribusi tiap kelompok pengeluaran terhadap inflasi tahunan (%).</p>
        <div style="height: {{ max(280, count($labelKelompok) * 42) }}px" class="w-full max-w-full overflow-hidden">
            <canvas id="chartAndil"></canvas>
        </div>
    </section>

    <section class="kartu kartu-hover mt-6 sm:mt-8 overflow-hidden min-w-0 w-full">
        <div class="p-4 sm:p-6 md:p-8 pb-3 sm:pb-4 min-w-0">
            <h2 class="font-display text-base sm:text-xl font-bold truncate">Rincian per Kelompok Pengeluaran</h2>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs sm:text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-left text-ink-soft border-y border-garis bg-sand-dark/50">
                        <th class="py-3 sm:py-3.5 px-4 sm:px-6 font-bold">Kelompok Pengeluaran</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Inflasi MTM</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Inflasi YTD</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Inflasi YoY</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Andil MTM</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Andil YoY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelompokLain as $k)
                        <tr class="border-b border-garis/60 last:border-0 hover:bg-white/80 transition-colors duration-150">
                            <td class="py-3 sm:py-3.5 px-4 sm:px-6 font-semibold">{{ $k->kelompok }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data">{{ fsign($k->mtm) }}%</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data">{{ fsign($k->ytd) }}%</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data">{{ fsign($k->yoy) }}%</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data">{{ fsign($k->andil_mtm) }}%</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-bold {{ $k->andil_yoy < 0 ? 'text-hijau' : 'text-marun-dark' }}">{{ fsign($k->andil_yoy) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    const labelKelompok = @json($labelKelompok);
    const andilYoy = @json($andilYoy);

    new Chart(document.getElementById('chartAndil'), {
        type: 'bar',
        data: {
            labels: labelKelompok,
            datasets: [{
                label: 'Andil YoY (%)',
                data: andilYoy,
                backgroundColor: andilYoy.map(v => v < 0 ? '#12965A' : '#D41E43'),
                borderRadius: 6,
                barThickness: 20,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (c) => `Andil: ${c.formattedValue}%` } },
            },
            scales: {
                x: { ticks: { callback: (v) => v + '%', font: { family: window.chartDefaultFont } }, grid: { color: '#E1D3AE55' } },
                y: { ticks: { font: { family: window.chartDefaultFont, size: 11 } }, grid: { display: false } },
            }
        }
    });
</script>
@endpush
