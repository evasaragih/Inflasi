@extends('admin.layout')

@section('title', 'Kelola Stok Pangan Kota Padang')

@section('content')

    {{-- HEADER & TOMBOL TAMBAH --}}
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="live-dot shrink-0"></span>
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-widest text-emas-dim">Manajemen Stok &amp; Ketahanan Pangan</p>
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-ink">Ringkasan Stok Pangan Kota Padang</h1>
            <p class="text-ink-soft text-xs sm:text-sm mt-1">Kelola data estimasi ketersediaan stok pasokan, kebutuhan bulanan, rasio ketahanan, dan status pasokan pangan.</p>
        </div>

        <a href="{{ route('admin.stok.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-marun hover:bg-marun-dark text-white px-4 py-2.5 text-xs sm:text-sm font-bold shadow-md hover:shadow-lg transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Tambah Data Stok</span>
        </a>
    </div>

    {{-- SUMMARY STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5 mb-6 sm:mb-8">
        <div class="kartu p-4 sm:p-5">
            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft mb-1">Komoditas Terdata</p>
            <p class="angka-data text-2xl sm:text-3xl font-extrabold text-ink">{{ $totalKomoditas }}</p>
            <p class="text-[11px] text-ink-soft mt-1.5 border-t border-garis/40 pt-1.5">Periode {{ $daftarBulan[$bulanDipilih] ?? '' }} {{ $tahunDipilih }}</p>
        </div>

        <div class="kartu p-4 sm:p-5">
            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft mb-1">Total Volume Stok</p>
            <p class="angka-data text-2xl sm:text-3xl font-extrabold text-hijau">{{ fnum($totalStok, 0) }}</p>
            <p class="text-[11px] text-ink-soft mt-1.5 border-t border-garis/40 pt-1.5">Akumulasi Ton / KL</p>
        </div>

        <div class="kartu p-4 sm:p-5">
            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft mb-1">Total Kebutuhan</p>
            <p class="angka-data text-2xl sm:text-3xl font-extrabold text-emas-dim">{{ fnum($totalKebutuhan, 0) }}</p>
            <p class="text-[11px] text-ink-soft mt-1.5 border-t border-garis/40 pt-1.5">Konsumsi Warga Kota Padang</p>
        </div>

        <div class="kartu p-4 sm:p-5">
            <p class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-ink-soft mb-1">Status Pasokan</p>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-hijau/10 text-hijau border border-hijau/20">🟢 {{ $jumlahMelimpah }} Melimpah</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-700 border border-blue-200">🔵 {{ $jumlahAman }} Aman</span>
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-marun/10 text-marun border border-marun/20">🔴 {{ $jumlahWaspada }} Waspada</span>
            </div>
            <p class="text-[11px] text-ink-soft mt-2.5 border-t border-garis/40 pt-1.5">Rasio Ketersediaan Pangan</p>
        </div>
    </div>

    {{-- FILTER & SEARCH BAR --}}
    <div class="kartu p-4 sm:p-6 overflow-hidden mb-6">
        <form method="GET" action="{{ route('admin.stok.index') }}" class="flex flex-wrap items-center gap-3">
            {{-- Search Box --}}
            <div class="relative flex-1 min-w-[180px]">
                <svg viewBox="0 0 20 20" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft/70 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="9" r="6"/><path d="M14 14l4 4" stroke-linecap="round"/>
                </svg>
                <input type="text" name="q" value="{{ $searchQuery ?? '' }}" placeholder="Cari nama komoditas..."
                    class="w-full rounded-xl border border-garis bg-white pl-9 pr-3 py-2 text-xs sm:text-sm font-medium text-ink placeholder:text-ink-soft/60 focus:border-emas focus:outline-none transition-all">
            </div>

            {{-- Tahun --}}
            <div class="relative min-w-[120px]">
                <select name="tahun" onchange="this.form.submit()"
                    class="w-full appearance-none rounded-xl border border-garis bg-white pl-3 pr-8 py-2 text-xs sm:text-sm font-semibold text-ink focus:border-emas focus:outline-none transition-all cursor-pointer">
                    @foreach($daftarTahun as $t)
                        <option value="{{ $t }}" @selected($tahunDipilih == $t)>Tahun {{ $t }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 text-ink-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            {{-- Bulan --}}
            <div class="relative min-w-[130px]">
                <select name="bulan" onchange="this.form.submit()"
                    class="w-full appearance-none rounded-xl border border-garis bg-white pl-3 pr-8 py-2 text-xs sm:text-sm font-semibold text-ink focus:border-emas focus:outline-none transition-all cursor-pointer">
                    @foreach($daftarBulan as $u => $namaBln)
                        <option value="{{ $u }}" @selected($bulanDipilih == $u)>{{ $namaBln }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 text-ink-soft absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 6l4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>

            <button type="submit" class="rounded-xl bg-ink text-white px-4 py-2 text-xs sm:text-sm font-semibold hover:bg-ink/80 transition-all shrink-0">
                Filter
            </button>
        </form>
    </div>

    {{-- TABEL DATA STOK PANGAN --}}
    <div class="kartu overflow-hidden">
        <div class="p-4 sm:p-6 pb-3 border-b border-garis/50 flex items-center justify-between">
            <h2 class="font-display text-base sm:text-lg font-bold text-ink">Daftar Stok Pangan ({{ $daftarBulan[$bulanDipilih] ?? '' }} {{ $tahunDipilih }})</h2>
            <span class="text-xs text-ink-soft font-semibold">{{ $stokList->count() }} Data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-ink border-collapse">
                <thead>
                    <tr class="border-b border-garis/60 bg-sand-dark/20 text-ink-soft uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3 px-4">Komoditas Pangan</th>
                        <th class="py-3 px-4 text-right">Ketersediaan Stok</th>
                        <th class="py-3 px-4 text-right">Kebutuhan Bulanan</th>
                        <th class="py-3 px-4 text-center">Ketahanan Stok</th>
                        <th class="py-3 px-4 text-center">Status Pasokan</th>
                        <th class="py-3 px-4 text-right">Harga Pasar / HET/HA</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-garis/30">
                    @forelse($stokList as $stok)
                        <tr class="hover:bg-sand-dark/10 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-ink">
                                {{ $stok->komoditas }}
                                @if($stok->keterangan)
                                    <span class="text-[11px] text-ink-soft block font-normal">{{ $stok->keterangan }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right angka-data font-extrabold text-ink">
                                {{ fnum($stok->stok, 0) }} <span class="text-[10px] font-normal text-ink-soft">{{ $stok->satuan }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right angka-data font-semibold text-ink-soft">
                                {{ fnum($stok->kebutuhan, 0) }} <span class="text-[10px] font-normal">{{ $stok->satuan }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="w-24 mx-auto bg-garis/40 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full {{ $stok->rasio >= 115 ? 'bg-hijau' : ($stok->rasio >= 98 ? 'bg-blue-600' : 'bg-marun') }}" style="width: {{ min(100, $stok->rasio) }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold angka-data mt-1 block text-ink-soft">{{ $stok->rasio }}%</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $stok->badge_class }}">
                                    {{ $stok->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right angka-data">
                                <span class="font-bold text-marun">Rp {{ fnum($stok->harga_pasar, 0) }}</span>
                                <span class="text-[10px] text-ink-soft block">HET/HA: Rp {{ fnum($stok->het, 0) }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.stok.edit', $stok->id) }}" class="rounded-lg bg-sand-dark/40 hover:bg-emas/20 text-ink p-1.5 transition-all" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.stok.destroy', $stok->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data stok {{ $stok->komoditas }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-marun/10 hover:bg-marun/20 text-marun p-1.5 transition-all" title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-ink-soft">
                                <p class="font-semibold text-sm">Tidak ada data stok pangan untuk filter yang dipilih.</p>
                                <p class="text-xs mt-1">Silakan pilih tahun/bulan lain atau klik tombol "Tambah Data Stok" di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
