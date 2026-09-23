@extends('layouts.app')

@section('title', 'Data Inflasi')

@section('content')

    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4 min-w-0">
        <div class="min-w-0">
            <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-widest text-emas-dim truncate">Data Inflasi {{ $tahunDipilih ?? 2026 }}</p>
            <h1 class="font-display text-2xl sm:text-4xl mt-1 break-words">Nasional &middot; Sumatera Barat &middot; Kota Padang</h1>
            <p class="text-ink-soft text-xs sm:text-sm mt-1 sm:mt-2 max-w-2xl">{{ setting('inflasi_desc', 'Perbandingan tingkat inflasi bulanan (MTM), akumulasi tahun berjalan (YTD), dan tahunan (YoY) berdasarkan data resmi BPS.') }}</p>
        </div>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('inflasi.index') }}" class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-56 min-w-[150px]">
                <svg viewBox="0 0 20 20" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft/70 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="6"/><path d="M14 14l4 4" stroke-linecap="round"/>
                </svg>
                <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari data..."
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

            <button type="submit" class="rounded-xl bg-marun hover:bg-marun-dark text-white px-3.5 py-2 text-xs sm:text-sm font-semibold shadow-sm transition-all shrink-0">
                Cari
            </button>
        </form>
    </div>

    <section class="kartu kartu-hover p-4 sm:p-6 md:p-8 min-w-0 w-full overflow-hidden">
        <h2 class="font-display text-base sm:text-xl font-bold mb-1 truncate">Tren Year on Year</h2>
        <p class="text-xs sm:text-sm text-ink-soft mb-4 sm:mb-5">Garis solid menandai Kota Padang sebagai fokus dashboard ini.</p>
        <div class="h-60 sm:h-72 w-full max-w-full overflow-hidden">
            <canvas id="chartYoy"></canvas>
        </div>
    </section>

    <section class="kartu kartu-hover mt-6 sm:mt-8 p-4 sm:p-6 md:p-8 min-w-0 w-full overflow-hidden">
        <h2 class="font-display text-base sm:text-xl font-bold mb-1 truncate">MTM &amp; YTD Kota Padang</h2>
        <p class="text-xs sm:text-sm text-ink-soft mb-4 sm:mb-5">Laju inflasi bulanan dibandingkan akumulasi sejak Januari.</p>
        <div class="h-56 sm:h-64 w-full max-w-full overflow-hidden">
            <canvas id="chartMtm"></canvas>
        </div>
    </section>

    @foreach(['padang' => 'Kota Padang', 'sumbar' => 'Provinsi Sumatera Barat', 'nasional' => 'Nasional'] as $key => $judul)
    <section class="kartu kartu-hover mt-6 sm:mt-8 overflow-hidden min-w-0 w-full">
        <div class="p-4 sm:p-6 md:p-8 pb-3 sm:pb-4 flex items-center justify-between min-w-0">
            <h2 class="font-display text-base sm:text-xl font-bold truncate">{{ $judul }}</h2>
            @if($key === 'padang')
                <span class="chip bg-marun/10 text-marun-dark font-bold text-[10px] sm:text-xs shrink-0 whitespace-nowrap">IHK tersedia</span>
            @endif
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs sm:text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-left text-ink-soft border-y border-garis bg-sand-dark/50">
                        <th class="py-3 sm:py-3.5 px-4 sm:px-6 font-bold">Bulan</th>
                        @if($key === 'padang')<th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">IHK</th>@endif
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">MTM (%)</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">YTD (%)</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">YoY (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tabel as $baris)
                        @php $d = $baris[$key]; @endphp
                        <tr class="border-b border-garis/60 last:border-0 hover:bg-white/80 transition-colors duration-150">
                            <td class="py-3 sm:py-3.5 px-4 sm:px-6 font-semibold">{{ $baris['bulan'] }}</td>
                            @if($key === 'padang')<td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data">{{ $d?->ihk ? fnum($d->ihk) : '-' }}</td>@endif
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data {{ ($d?->mtm ?? 0) < 0 ? 'text-hijau' : 'text-marun-dark' }}">{{ fsign($d?->mtm) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data {{ ($d?->ytd ?? 0) < 0 ? 'text-hijau' : 'text-marun-dark' }}">{{ fsign($d?->ytd) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-bold {{ ($d?->yoy ?? 0) < 0 ? 'text-hijau' : 'text-marun-dark' }}">{{ fsign($d?->yoy) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endforeach

@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('chartYoy'), {
        type: 'line',
        data: {
            labels: @json($labelBulan),
            datasets: [
                {
                    label: 'Kota Padang',
                    data: @json($seriPadangYoy),
                    borderColor: '#E11D48',
                    backgroundColor: 'rgba(225, 29, 72, 0.12)',
                    borderWidth: 3.5,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#E11D48',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Sumatera Barat',
                    data: @json($seriSumbarYoy),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    borderWidth: 3,
                    borderDash: [6, 4],
                    tension: 0.35,
                    fill: false,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#2563EB',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Nasional',
                    data: @json($seriNasionalYoy),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.08)',
                    borderWidth: 3,
                    borderDash: [3, 3],
                    tension: 0.35,
                    fill: false,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#059669',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: window.chartDefaultFont, size: 12, weight: '600' },
                        usePointStyle: true,
                        boxWidth: 10,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleFont: { family: window.chartDefaultFont, size: 13, weight: 'bold' },
                    bodyFont: { family: window.chartDefaultFont, size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,
                    boxPadding: 6,
                    callbacks: { label: (c) => ` ${c.dataset.label}: ${c.formattedValue}%` }
                },
            },
            scales: {
                y: {
                    ticks: { callback: (v) => v + '%', font: { family: window.chartDefaultFont, size: 11 } },
                    grid: { color: 'rgba(225, 211, 174, 0.4)' }
                },
                x: {
                    ticks: { font: { family: window.chartDefaultFont, size: 11 } },
                    grid: { display: false }
                },
            }
        }
    });

    new Chart(document.getElementById('chartMtm'), {
        type: 'bar',
        data: {
            labels: @json($labelBulan),
            datasets: [
                {
                    label: 'MTM',
                    data: @json($seriPadangMtm),
                    backgroundColor: '#2563EB',
                    borderRadius: 6,
                    barPercentage: 0.5
                },
                {
                    label: 'YTD',
                    data: @json($seriPadangYtd),
                    type: 'line',
                    borderColor: '#E11D48',
                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#E11D48',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { family: window.chartDefaultFont, size: 12, weight: '600' }, usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleFont: { family: window.chartDefaultFont, size: 13, weight: 'bold' },
                    bodyFont: { family: window.chartDefaultFont, size: 12 },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true,
                    callbacks: { label: (c) => ` ${c.dataset.label}: ${c.formattedValue}%` }
                },
            },
            scales: {
                y: {
                    ticks: { callback: (v) => v + '%', font: { family: window.chartDefaultFont } },
                    grid: { color: 'rgba(225, 211, 174, 0.4)' }
                },
                x: {
                    ticks: { font: { family: window.chartDefaultFont } },
                    grid: { display: false }
                },
            }
        }
    });
</script>
@endpush
