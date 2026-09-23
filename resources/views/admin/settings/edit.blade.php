@extends('admin.layout')

@section('title', 'Pengaturan Situs')

@section('content')

    <h1 class="font-display text-3xl mb-1">Pengaturan Situs</h1>
    <p class="text-ink-soft text-sm mb-6">Ubah logo, nama situs, dan teks-teks yang tampil di halaman publik.</p>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6 max-w-2xl">
        @csrf

        <div class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5">
            <h2 class="font-display text-lg">Logo</h2>

            <div class="flex items-center gap-5">
                <div class="h-16 w-auto min-w-16 max-w-[220px] shrink-0 rounded-xl {{ $settings['logo_path'] ? 'bg-[repeating-conic-gradient(#e6ddc4_0%_25%,#f1e9d6_0%_50%)] bg-[length:16px_16px]' : 'bg-ink' }} grid place-items-center overflow-hidden border border-garis px-2">
                    @if($settings['logo_path'])
                        <img src="{{ asset('storage/' . $settings['logo_path']) }}" alt="Logo" class="h-full w-auto max-w-[200px] object-contain">
                    @else
                        <svg viewBox="0 0 40 40" class="h-8 w-8" fill="none"><path d="M4 32 L4 22 C 9 8, 13 8, 16 22 C 19 8, 21 8, 24 22 C 27 8, 31 8, 36 22 L36 32 Z" fill="#E3C578"/></svg>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="logo" accept="image/*" class="text-sm">
                    <p class="text-xs text-ink-soft mt-1">PNG/SVG/WEBP transparan disarankan. Maks 5MB.</p>
                    @if($settings['logo_path'])
                        <label class="flex items-center gap-2 text-xs text-marun-dark mt-2">
                            <input type="checkbox" name="hapus_logo" value="1" class="rounded border-garis">
                            Hapus logo, kembali ke ikon bawaan
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5">
            <h2 class="font-display text-lg">Identitas Situs</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Situs</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Sub-judul</label>
                    <input type="text" name="site_subtitle" value="{{ old('site_subtitle', $settings['site_subtitle']) }}" class="form-input">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5">
            <h2 class="font-display text-lg">Teks Deskripsi Halaman</h2>
            <div>
                <label class="form-label">Deskripsi halaman Data Inflasi</label>
                <textarea name="inflasi_desc" rows="2" class="form-input">{{ old('inflasi_desc', $settings['inflasi_desc']) }}</textarea>
            </div>
            <div>
                <label class="form-label">Deskripsi halaman Andil Inflasi</label>
                <textarea name="andil_desc" rows="2" class="form-input">{{ old('andil_desc', $settings['andil_desc']) }}</textarea>
            </div>
            <div>
                <label class="form-label">Deskripsi halaman Harga Pangan</label>
                <textarea name="pangan_desc" rows="2" class="form-input">{{ old('pangan_desc', $settings['pangan_desc']) }}</textarea>
            </div>
        </div>

        <div class="rounded-2xl border border-garis bg-white/70 p-6 space-y-5">
            <h2 class="font-display text-lg">Footer</h2>
            <div>
                <label class="form-label">Teks footer (kiri)</label>
                <input type="text" name="footer_text" value="{{ old('footer_text', $settings['footer_text']) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Teks footer (kanan)</label>
                <input type="text" name="footer_credit" value="{{ old('footer_credit', $settings['footer_credit']) }}" class="form-input">
            </div>
        </div>

        <button type="submit" class="rounded-lg bg-marun hover:bg-marun-dark text-white text-sm font-semibold px-5 py-2.5 transition-colors">Simpan Pengaturan</button>
    </form>

@endsection
