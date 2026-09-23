<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InflasiIndikatorSeeder::class,
            AndilInflasiSeeder::class,
            HargaPanganSeeder::class,
            SettingSeeder::class,
            StokPanganSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
