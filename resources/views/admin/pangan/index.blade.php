@extends('admin.layout')

@section('title', 'Harga Pangan')

@section('content')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 min-w-0">
        <div class="min-w-0">
            <h1 class="font-display text-2xl sm:text-3xl truncate">Harga Pangan Strategis</h1>
            <p class="text-ink-soft text-xs sm:text-sm">Data mingguan per komoditas</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('admin.pangan.index') }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari data..." class="rounded-lg border border-garis px-3 py-1.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-emas">
                <button type="submit" class="rounded-lg bg-sand border border-garis px-3 py-1.5 text-xs sm:text-sm font-semibold hover:bg-garis">Cari</button>
            </form>
            <a href="{{ route('admin.pangan.create') }}" class="rounded-lg bg-marun hover:bg-marun-dark text-white text-xs sm:text-sm font-semibold px-3.5 sm:px-4 py-2 sm:py-2.5 transition-colors self-start sm:self-auto shrink-0">+ Tambah Data</a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden min-w-0 w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs sm:text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-left text-slate-700 border-b border-slate-200 bg-slate-100/90">
                        <th class="py-3 sm:py-3.5 px-4 sm:px-5 font-bold">Waktu</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Komoditas</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">HET/HA</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">Harga</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold">%</th>
                        <th class="py-3 sm:py-3.5 px-3 sm:px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($data as $row)
                        <tr class="border-b border-slate-200/70 last:border-0 hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-3 sm:py-3.5 px-4 sm:px-5 font-semibold text-slate-900">{{ $row->bulan }} {{ $row->tahun ?? 2026 }} &middot; M{{ $row->minggu }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 text-slate-800 font-medium">{{ $row->komoditas }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data text-slate-500">Rp {{ fnum($row->het, 0) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-bold text-slate-900">Rp {{ fnum($row->harga_ini, 0) }}</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4 angka-data font-semibold {{ $row->persen < 0 ? 'text-hijau' : 'text-marun' }}">{{ fsign($row->persen) }}%</td>
                            <td class="py-3 sm:py-3.5 px-3 sm:px-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.pangan.edit', $row) }}" class="text-marun font-bold hover:underline">Ubah</a>
                                    <form method="POST" action="{{ route('admin.pangan.destroy', $row) }}" onsubmit="return confirm('Hapus data ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-500 hover:text-marun font-bold">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-slate-500">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $data->links() }}</div>

@endsection
