<?php

namespace Database\Seeders;

use App\Models\InflasiIndikator;
use Illuminate\Database\Seeder;

class InflasiIndikatorSeeder extends Seeder
{
    public function run(): void
    {
        InflasiIndikator::query()->delete();
        $path = __DIR__ . '/data/inflasi_indikator.json';
        $rows = json_decode(file_get_contents($path), true);

        foreach ([2024, 2025, 2026] as $tahun) {
            $offset = match($tahun) {
                2024 => -0.45,
                2025 => -0.20,
                2026 => 0.0,
            };

            foreach ($rows as $row) {
                if ($row['mtm'] === null && $row['ytd'] === null && $row['yoy'] === null) {
                    continue;
                }

                $item = $row;
                $item['tahun'] = $tahun;
                if (isset($item['ihk'])) $item['ihk'] = round($item['ihk'] + ($offset * 2), 2);
                if (isset($item['yoy'])) $item['yoy'] = round($item['yoy'] + $offset, 2);
                if (isset($item['mtm'])) $item['mtm'] = round($item['mtm'] + ($offset * 0.1), 2);
                if (isset($item['ytd'])) $item['ytd'] = round($item['ytd'] + ($offset * 0.5), 2);

                InflasiIndikator::create($item);
            }
        }
    }
}
