<?php

namespace Database\Seeders;

use App\Models\HargaPangan;
use Illuminate\Database\Seeder;

class HargaPanganSeeder extends Seeder
{
    public function run(): void
    {
        HargaPangan::query()->delete();
        $path = __DIR__ . '/data/harga_pangan.json';
        $rows = json_decode(file_get_contents($path), true);

        foreach ([2024, 2025, 2026] as $tahun) {
            $multiplier = match($tahun) {
                2024 => 0.92,
                2025 => 0.96,
                2026 => 1.00,
            };

            foreach ($rows as $row) {
                $item = $row;
                $item['tahun'] = $tahun;
                if (isset($item['harga_ini'])) $item['harga_ini'] = round($item['harga_ini'] * $multiplier);
                if (isset($item['harga_lalu'])) $item['harga_lalu'] = round($item['harga_lalu'] * $multiplier);
                if (isset($item['het'])) $item['het'] = round($item['het'] * $multiplier);
                if (isset($item['perubahan']) && isset($item['harga_ini']) && isset($item['harga_lalu'])) {
                    $item['perubahan'] = $item['harga_ini'] - $item['harga_lalu'];
                }
                HargaPangan::create($item);
            }
        }
    }
}
