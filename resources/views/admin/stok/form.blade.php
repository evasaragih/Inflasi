@extends('admin.layout')

@section('title', isset($item) ? 'Edit Stok Pangan' : 'Tambah Stok Pangan')

@section('content')

    <div class="mb-6 sm:mb-8 flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.stok.index') }}" class="text-xs font-bold text-emas-dim hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>Kembali ke Stok Pangan</span>
                </a>
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-ink">
                {{ isset($item) ? 'Ubah Data Stok Pangan' : 'Tambah Data Stok Pangan Baru' }}
            </h1>
            <p class="text-ink-soft text-xs sm:text-sm mt-1">Isi formulir berikut untuk mengelola data ketersediaan stok pasokan dan kebutuhan pangan Kota Padang.</p>
        </div>
    </div>

    <div class="kartu p-6 sm:p-8 max-w-3xl">
        <form method="POST" action="{{ isset($item) ? route('admin.stok.update', $item->id) : route('admin.stok.store') }}" class="space-y-5">
            @csrf
            @if(isset($item))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tahun --}}
                <div>
                    <label class="form-label">Tahun Periode <span class="text-marun">*</span></label>
                    <select name="tahun" required class="form-input">
                        @foreach($daftarTahun as $t)
                            <option value="{{ $t }}" @selected(old('tahun', $item->tahun ?? 2026) == $t)>Tahun {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Bulan --}}
                <div>
                    <label class="form-label">Bulan Periode <span class="text-marun">*</span></label>
                    <select name="urutan_bulan" required class="form-input">
                        @foreach($daftarBulan as $u => $namaBln)
                            <option value="{{ $u }}" @selected(old('urutan_bulan', $item->urutan_bulan ?? 12) == $u)>{{ $namaBln }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Nama Komoditas --}}
            <div>
                <label class="form-label">Nama Komoditas Pangan <span class="text-marun">*</span></label>
                <input type="text" name="komoditas" value="{{ old('komoditas', $item->komoditas ?? '') }}" required placeholder="Contoh: Beras Cap Anak Daro / Cabe Merah" class="form-input font-bold text-ink">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Ketersediaan Stok --}}
                <div>
                    <label class="form-label">Ketersediaan Stok <span class="text-marun">*</span></label>
                    <input type="number" step="0.01" name="stok" value="{{ old('stok', $item->stok ?? '') }}" required placeholder="Contoh: 14500" class="form-input angka-data font-bold text-hijau">
                </div>

                {{-- Kebutuhan Bulanan --}}
                <div>
                    <label class="form-label">Kebutuhan Bulanan <span class="text-marun">*</span></label>
                    <input type="number" step="0.01" name="kebutuhan" value="{{ old('kebutuhan', $item->kebutuhan ?? '') }}" required placeholder="Contoh: 12000" class="form-input angka-data font-bold text-ink">
                </div>

                {{-- Satuan --}}
                <div>
                    <label class="form-label">Satuan Volume <span class="text-marun">*</span></label>
                    <select name="satuan" required class="form-input">
                        <option value="Ton" @selected(old('satuan', $item->satuan ?? 'Ton') == 'Ton')>Ton</option>
                        <option value="Kilo Liter" @selected(old('satuan', $item->satuan ?? '') == 'Kilo Liter')>Kilo Liter (KL)</option>
                        <option value="Kg" @selected(old('satuan', $item->satuan ?? '') == 'Kg')>Kg</option>
                        <option value="Liter" @selected(old('satuan', $item->satuan ?? '') == 'Liter')>Liter</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-garis/40 pt-4">
                {{-- Harga Pasar --}}
                <div>
                    <label class="form-label">Harga Pasar Terkini (Rp)</label>
                    <input type="number" step="0.01" name="harga_pasar" value="{{ old('harga_pasar', $item->harga_pasar ?? '') }}" placeholder="Contoh: 16000" class="form-input angka-data">
                </div>

                {{-- HET/HA --}}
                <div>
                    <label class="form-label">Batas HET/HA (Rp)</label>
                    <input type="number" step="0.01" name="het" value="{{ old('het', $item->het ?? '') }}" placeholder="Contoh: 15400" class="form-input angka-data">
                </div>
            </div>

            {{-- Keterangan / Catatan --}}
            <div>
                <label class="form-label">Catatan Keterangan Pasokan</label>
                <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan khusus pasokan (opsional)..." class="form-input">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-4 border-t border-garis/60">
                <button type="submit" class="rounded-xl bg-marun hover:bg-marun-dark text-white px-6 py-2.5 text-xs sm:text-sm font-bold shadow-md transition-all">
                    {{ isset($item) ? 'Simpan Perubahan' : 'Tambah Data Stok Pangan' }}
                </button>
                <a href="{{ route('admin.stok.index') }}" class="rounded-xl bg-sand-dark/30 hover:bg-sand-dark/60 text-ink px-5 py-2.5 text-xs sm:text-sm font-semibold transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection
