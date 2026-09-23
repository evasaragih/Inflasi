<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private array $keys = [
        'site_name', 'site_subtitle', 'inflasi_desc', 'andil_desc',
        'pangan_desc', 'footer_text', 'footer_credit',
    ];

    public function edit()
    {
        $settings = [];
        foreach ($this->keys as $key) {
            $settings[$key] = Setting::get($key);
        }
        $settings['logo_path'] = Setting::get('logo_path');

        return view('admin.settings.edit', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_subtitle' => ['nullable', 'string', 'max:100'],
            'inflasi_desc' => ['nullable', 'string', 'max:500'],
            'andil_desc' => ['nullable', 'string', 'max:500'],
            'pangan_desc' => ['nullable', 'string', 'max:500'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'footer_credit' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:5120'],
            'hapus_logo' => ['nullable', 'boolean'],
        ]);

        foreach ($this->keys as $key) {
            Setting::set($key, $validated[$key] ?? null);
        }

        if ($request->boolean('hapus_logo')) {
            $lama = Setting::get('logo_path');
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
            Setting::set('logo_path', null);
        }

        if ($request->hasFile('logo')) {
            $lama = Setting::get('logo_path');
            if ($lama) {
                Storage::disk('public')->delete($lama);
            }
            $path = $request->file('logo')->store('branding', 'public');
            Setting::set('logo_path', $path);
        }

        return redirect()->route('admin.settings.edit')->with('sukses', 'Pengaturan situs berhasil disimpan.');
    }
}
