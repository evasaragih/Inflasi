@extends('admin.layout')

@section('title', $item ? 'Ubah Harga Pangan' : 'Tambah Harga Pangan')

@section('content')

    <h1 class="font-display text-3xl mb-6">{{ $item ? 'Ubah Harga Pangan' : 'Tambah Harga Pangan' }}</h1>

    <form method="POST" action="{{ $item ? route('admin.pangan.update', $item) : route('admin.pangan.store') }}" class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5 max-w-xl">
        @csrf
        @if($item) @method('PUT') @endif

        <div>
            <label class="form-label">Komoditas</label>
            <input type="text" list="daftar-komoditas" name="komoditas" value="{{ old('komoditas', $item->komoditas ?? '') }}" class="form-input" required placeholder="Contoh: Beras Cap Anak Daro">
            <datalist id="daftar-komoditas">
                @foreach($daftarKomoditas as $k)
                    <option value="{{ $k }}">
                @endforeach
            </datalist>
        </div>

        <div class="grid grid-cols-3 gap-4">
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
            <div>
                <label class="form-label">Minggu ke-</label>
                <select name="minggu" class="form-input" required>
                    @foreach(range(1,5) as $m)
                        <option value="{{ $m }}" @selected((int) old('minggu', $item->minggu ?? 0) === $m)>Minggu {{ $m }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" value="{{ old('satuan', $item->satuan ?? '') }}" class="form-input" placeholder="Contoh: Kg, Liter">
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="form-label">HET/HA (Rp)</label>
                <input type="number" step="0.01" name="het" value="{{ old('het', $item->het ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Harga Minggu Lalu (Rp)</label>
                <input type="number" step="0.01" name="harga_lalu" value="{{ old('harga_lalu', $item->harga_lalu ?? '') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Harga Sekarang (Rp)</label>
                <input type="number" step="0.01" name="harga_ini" value="{{ old('harga_ini', $item->harga_ini ?? '') }}" class="form-input">
            </div>
        </div>
        <p class="text-xs text-ink-soft -mt-2">Nilai perubahan (Rp) dan persentase akan dihitung otomatis dari harga minggu lalu &amp; harga sekarang.</p>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-marun hover:bg-marun-dark text-white text-sm font-semibold px-5 py-2.5 transition-colors">Simpan</button>
            <a href="{{ route('admin.pangan.index') }}" class="text-sm text-ink-soft hover:text-ink">Batal</a>
        </div>
    </form>

@endsection
