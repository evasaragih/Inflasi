<?php

namespace Database\Seeders;

use App\Models\HargaPangan;
use App\Models\StokPangan;
use Illuminate\Database\Seeder;

class StokPanganSeeder extends Seeder
{
    private array $bulanNama = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    private array $stokMaster = [
        'Beras Cap Anak Daro' => ['stok' => 14500, 'kebutuhan' => 12000, 'satuan' => 'Ton'],
        'Cabe Merah Keriting Lokal' => ['stok' => 780, 'kebutuhan' => 850, 'satuan' => 'Ton'],
        'Bawang Merah Biasa' => ['stok' => 640, 'kebutuhan' => 600, 'satuan' => 'Ton'],
        'Cabe Rawit Merah' => ['stok' => 410, 'kebutuhan' => 450, 'satuan' => 'Ton'],
        'Gula Pasir' => ['stok' => 3800, 'kebutuhan' => 3200, 'satuan' => 'Ton'],
        'Minyak Goreng' => ['stok' => 4200, 'kebutuhan' => 3800, 'satuan' => 'Kilo Liter'],
        'Daging Ayam Ras' => ['stok' => 2100, 'kebutuhan' => 1900, 'satuan' => 'Ton'],
        'Telur Ayam Ras' => ['stok' => 2800, 'kebutuhan' => 2400, 'satuan' => 'Ton'],
        'Daging Sapi Paha Belakang' => ['stok' => 950, 'kebutuhan' => 900, 'satuan' => 'Ton'],
        'Bawang Putih Honan' => ['stok' => 520, 'kebutuhan' => 500, 'satuan' => 'Ton'],
    ];

    public function run(): void
    {
        $tahunList = [2024, 2025, 2026];

        foreach ($tahunList as $tahun) {
            foreach (range(1, 12) as $urutanBulan) {
                $bulan = $this->bulanNama[$urutanBulan];

                $factorStok = (match($tahun) {
                    2024 => 0.94,
                    2025 => 0.98,
                    2026 => 1.02,
                    default => 1.00
                }) * (1 + (sin($urutanBulan) * 0.05));

                foreach ($this->stokMaster as $namaKomoditas => $base) {
                    $stokVal = round($base['stok'] * $factorStok);
                    $kebutuhanVal = round($base['kebutuhan']);

                    $hargaTerkini = HargaPangan::where('tahun', $tahun)
                        ->where('urutan_bulan', $urutanBulan)
                        ->where('komoditas', $namaKomoditas)
                        ->orderByDesc('minggu')
                        ->first();

                    StokPangan::updateOrCreate(
                        [
                            'tahun' => $tahun,
                            'urutan_bulan' => $urutanBulan,
                            'komoditas' => $namaKomoditas,
                        ],
                        [
                            'bulan' => $bulan,
                            'stok' => $stokVal,
                            'kebutuhan' => $kebutuhanVal,
                            'satuan' => $base['satuan'],
                            'harga_pasar' => $hargaTerkini?->harga_ini ?? 0,
                            'het' => $hargaTerkini?->het ?? 0,
                            'keterangan' => 'Estimasi stok pasokan pangan Kota Padang bulan ' . $bulan . ' ' . $tahun,
                        ]
                    );
                }
            }
        }
    }
}
