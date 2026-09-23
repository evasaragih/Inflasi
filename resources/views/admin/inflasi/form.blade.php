@extends('admin.layout')

@section('title', $item ? 'Ubah Data Inflasi' : 'Tambah Data Inflasi')

@section('content')

    <h1 class="font-display text-3xl mb-6">{{ $item ? 'Ubah Data Inflasi' : 'Tambah Data Inflasi' }}</h1>

    <form method="POST" action="{{ $item ? route('admin.inflasi.update', $item) : route('admin.inflasi.store') }}" class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5 max-w-xl">
        @csrf
        @if($item) @method('PUT') @endif

        <div>
            <label class="form-label">Wilayah</label>
            <select name="wilayah" class="form-input" required>
                @foreach(['nasional' => 'Nasional', 'sumbar' => 'Provinsi Sumatera Barat', 'padang' => 'Kota Padang'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('wilayah', $item->wilayah ?? '') === $val)>{{ $label }}</option>
                @endforeach
            </select>
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
                <label class="form-label">IHK (opsional)</label>
                <input type="number" step="0.01" name="ihk" value="{{ old('ihk', $item->ihk ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">MTM (%)</label>
                <input type="number" step="0.01" name="mtm" value="{{ old('mtm', $item->mtm ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">YTD (%)</label>
                <input type="number" step="0.01" name="ytd" value="{{ old('ytd', $item->ytd ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">YoY (%)</label>
                <input type="number" step="0.01" name="yoy" value="{{ old('yoy', $item->yoy ?? '') }}" class="form-input">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-marun hover:bg-marun-dark text-white text-sm font-semibold px-5 py-2.5 transition-colors">Simpan</button>
            <a href="{{ route('admin.inflasi.index') }}" class="text-sm text-ink-soft hover:text-ink">Batal</a>
        </div>
    </form>

@endsection
