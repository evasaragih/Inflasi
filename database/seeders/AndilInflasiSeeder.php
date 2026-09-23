<?php

namespace Database\Seeders;

use App\Models\AndilInflasi;
use Illuminate\Database\Seeder;

class AndilInflasiSeeder extends Seeder
{
    public function run(): void
    {
        AndilInflasi::query()->delete();
        $path = __DIR__ . '/data/andil_inflasi.json';
        $rows = json_decode(file_get_contents($path), true);

        foreach ([2024, 2025, 2026] as $tahun) {
            $offset = match($tahun) {
                2024 => -0.05,
                2025 => -0.02,
                2026 => 0.0,
            };

            foreach ($rows as $row) {
                $item = $row;
                $item['tahun'] = $tahun;
                if (isset($item['andil_yoy'])) $item['andil_yoy'] = round($item['andil_yoy'] + $offset, 2);
                if (isset($item['yoy'])) $item['yoy'] = round($item['yoy'] + ($offset * 5), 2);
                AndilInflasi::create($item);
            }
        }
    }
}
