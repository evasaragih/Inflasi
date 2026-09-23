<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $latestLogo = null;
        $brandingDir = storage_path('app/public/branding');
        if (is_dir($brandingDir)) {
            $files = glob($brandingDir . '/*.{png,jpg,jpeg,svg,webp}', GLOB_BRACE);
            if (!empty($files)) {
                usort($files, fn ($a, $b) => filemtime($b) - filemtime($a));
                $latestLogo = 'branding/' . basename($files[0]);
            }
        }

        $defaults = [
            'site_name' => 'Dashboard Inflasi',
            'site_subtitle' => 'Kota Padang',
            'logo_path' => $latestLogo,
            'inflasi_desc' => 'Perbandingan tingkat inflasi bulanan (MTM), akumulasi tahun berjalan (YTD), dan tahunan (YoY) berdasarkan data resmi BPS.',
            'andil_desc' => 'Seberapa besar tiap kelompok pengeluaran menyumbang terhadap angka inflasi headline.',
            'pangan_desc' => 'Harga mingguan komoditas pokok di Kota Padang dibandingkan Harga Eceran Tertinggi (HET/HA).',
            'footer_text' => 'Dashboard Inflasi Kota Padang · Sumber data: Badan Pusat Statistik (BPS)',
            'footer_credit' => 'Dibangun dengan Laravel & Tailwind CSS',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
