@extends('admin.layout')

@section('title', $item ? 'Ubah Andil Inflasi' : 'Tambah Andil Inflasi')

@section('content')

    <h1 class="font-display text-3xl mb-6">{{ $item ? 'Ubah Andil Inflasi' : 'Tambah Andil Inflasi' }}</h1>

    <form method="POST" action="{{ $item ? route('admin.andil.update', $item) : route('admin.andil.store') }}" class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5 max-w-xl">
        @csrf
        @if($item) @method('PUT') @endif

        <div>
            <label class="form-label">Kelompok Pengeluaran</label>
            <input type="text" list="daftar-kelompok" name="kelompok" value="{{ old('kelompok', $item->kelompok ?? '') }}" class="form-input" required placeholder="Contoh: Makanan, Minuman, dan Tembakau">
            <datalist id="daftar-kelompok">
                @foreach($kelompokPengeluaran as $k)
                    <option value="{{ $k }}">
                @endforeach
            </datalist>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Bulan</label>
                <select name="urutan_bulan" class="form-input" required>
                    @foreach($bulanNama as $u => $nama)
                        <option value="{{ $u }}" @selected((int) old('urutan_bulan', $item->urutan_bulan ?? 0) === $u)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tahun</label>
                <input type="number" min="2000" max="2100" name="tahun" value="{{ old('tahun', $item->tahun ?? 2026) }}" class="form-input" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">Inflasi MTM (%)</label>
                <input type="number" step="0.01" name="mtm" value="{{ old('mtm', $item->mtm ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Inflasi YTD (%)</label>
                <input type="number" step="0.01" name="ytd" value="{{ old('ytd', $item->ytd ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Inflasi YoY (%)</label>
                <input type="number" step="0.01" name="yoy" value="{{ old('yoy', $item->yoy ?? '') }}" class="form-input">
            </div>
            <div></div>
            <div>
                <label class="form-label">Andil MTM (%)</label>
                <input type="number" step="0.01" name="andil_mtm" value="{{ old('andil_mtm', $item->andil_mtm ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Andil YoY (%)</label>
                <input type="number" step="0.01" name="andil_yoy" value="{{ old('andil_yoy', $item->andil_yoy ?? '') }}" class="form-input">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-marun hover:bg-marun-dark text-white text-sm font-semibold px-5 py-2.5 transition-colors">Simpan</button>
            <a href="{{ route('admin.andil.index') }}" class="text-sm text-ink-soft hover:text-ink">Batal</a>
        </div>
    </form>

@endsection
