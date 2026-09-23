<?php

namespace App\Http\Controllers\Api;

use App\Models\AndilInflasi;
use App\Models\HargaPangan;
use App\Models\InflasiIndikator;
use App\Models\StokPangan;
use Illuminate\Http\Request;

class RingkasanApiController extends BaseApiController
{
    /**
     * GET /api/v1/ringkasan
     * Satu panggilan untuk seluruh angka utama dashboard.
     */
    public function index(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);

        $bulanTerakhir = (int) (InflasiIndikator::where('tahun', $tahun)->max('urutan_bulan') ?? 0);
        $bulan = (int) $request->query('bulan', $bulanTerakhir ?: 1);

        $indikator = [];

        foreach (config('inflasi_api.wilayah') as $wilayah) {
            $baris = InflasiIndikator::where('tahun', $tahun)
                ->where('wilayah', $wilayah)
                ->where('urutan_bulan', $bulan)
                ->first();

            $indikator[$wilayah] = $baris ? [
                'ihk' => $baris->ihk,
                'mtm' => $baris->mtm,
                'ytd' => $baris->ytd,
                'yoy' => $baris->yoy,
            ] : null;
        }

        // Deret YoY untuk grafik garis.
        $deret = ['label' => [], 'padang' => [], 'sumbar' => [], 'nasional' => []];

        $semua = InflasiIndikator::where('tahun', $tahun)
            ->where('urutan_bulan', '<=', max($bulanTerakhir, $bulan))
            ->get();

        for ($u = 1; $u <= max($bulanTerakhir, $bulan); $u++) {
            $deret['label'][] = substr($this->namaBulan($u), 0, 3);

            foreach (['padang', 'sumbar', 'nasional'] as $wilayah) {
                $deret[$wilayah][] = $semua->first(
                    fn ($i) => $i->wilayah === $wilayah && (int) $i->urutan_bulan === $u
                )?->yoy;
            }
        }

        $andil = AndilInflasi::where('tahun', $tahun)
            ->where('urutan_bulan', $bulan)
            ->where('kelompok', '!=', 'Umum (Headline)')
            ->get()
            ->unique('kelompok');

        $stok = StokPangan::where('tahun', $tahun)->where('urutan_bulan', $bulan)->get();

        return $this->ok([
            'periode' => [
                'tahun' => $tahun,
                'urutan_bulan' => $bulan,
                'bulan' => $this->namaBulan($bulan),
            ],
            'jumlah_data' => [
                'inflasi' => InflasiIndikator::count(),
                'andil' => AndilInflasi::count(),
                'harga_pangan' => HargaPangan::count(),
                'stok_pangan' => StokPangan::count(),
            ],
            'indikator' => $indikator,
            'deret_yoy' => $deret,
            'andil_tertinggi' => $andil->sortByDesc('andil_yoy')->take(5)->map(fn ($i) => [
                'kelompok' => $i->kelompok,
                'andil_yoy' => $i->andil_yoy,
                'yoy' => $i->yoy,
            ])->values(),
            'stok_pangan' => [
                'jumlah_komoditas' => $stok->count(),
                'waspada' => $stok->where('rasio', '<', 98)->count(),
            ],
        ], 'Ringkasan dashboard inflasi');
    }

    /**
     * GET /api/v1/meta
     * Daftar nilai filter yang tersedia — berguna untuk mengisi dropdown di klien.
     */
    public function meta()
    {
        return $this->ok([
            'wilayah' => config('inflasi_api.wilayah'),
            'bulan' => collect($this->bulanPenuh)->map(fn ($nama, $urutan) => [
                'urutan' => $urutan,
                'nama' => $nama,
            ])->values(),
            'tahun' => InflasiIndikator::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun'),
            'kelompok' => AndilInflasi::select('kelompok')->distinct()->orderBy('kelompok')->pluck('kelompok'),
            'komoditas_harga' => HargaPangan::select('komoditas')->distinct()->orderBy('komoditas')->pluck('komoditas'),
            'komoditas_stok' => StokPangan::select('komoditas')->distinct()->orderBy('komoditas')->pluck('komoditas'),
        ], 'Metadata filter');
    }
}
