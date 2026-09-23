<?php

namespace App\Http\Controllers;

use App\Models\InflasiIndikator;
use Illuminate\Http\Request;

class InflasiController extends Controller
{
    public function index(Request $request)
    {
        $bulanUrut = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
            6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
            11 => 'November', 12 => 'Desember',
        ];

        $daftarTahun = InflasiIndikator::select('tahun')->distinct()->pluck('tahun')
            ->merge([2026, 2025, 2024])
            ->unique()
            ->sortDesc()
            ->values();
        $tahunDipilih = (int) $request->get('tahun', 2026);
        $searchQuery = trim($request->get('q', ''));

        $semua = InflasiIndikator::where('tahun', $tahunDipilih)
            ->orderBy('urutan_bulan')
            ->get()
            ->unique(fn ($item) => $item->wilayah . '_' . $item->urutan_bulan);
        $bulanTerakhir = $semua->max('urutan_bulan') ?? 12;

        $padang = $semua->where('wilayah', 'padang')->keyBy('urutan_bulan');
        $sumbar = $semua->where('wilayah', 'sumbar')->keyBy('urutan_bulan');
        $nasional = $semua->where('wilayah', 'nasional')->keyBy('urutan_bulan');

        $tabel = [];
        $labelBulan = [];
        $seriPadangYoy = [];
        $seriSumbarYoy = [];
        $seriNasionalYoy = [];
        $seriPadangMtm = [];
        $seriPadangYtd = [];

        foreach (range(1, $bulanTerakhir) as $u) {
            $labelBulan[] = $bulanUrut[$u];
            $seriPadangYoy[] = $padang->get($u)?->yoy;
            $seriSumbarYoy[] = $sumbar->get($u)?->yoy;
            $seriNasionalYoy[] = $nasional->get($u)?->yoy;
            $seriPadangMtm[] = $padang->get($u)?->mtm;
            $seriPadangYtd[] = $padang->get($u)?->ytd;

            $baris = [
                'bulan' => $bulanUrut[$u],
                'padang' => $padang->get($u),
                'sumbar' => $sumbar->get($u),
                'nasional' => $nasional->get($u),
            ];

            if ($searchQuery !== '') {
                $teksSearch = strtolower($bulanUrut[$u] . ' ' . json_encode($baris));
                if (str_contains($teksSearch, strtolower($searchQuery))) {
                    $tabel[] = $baris;
                }
            } else {
                $tabel[] = $baris;
            }
        }

        return view('inflasi.index', compact(
            'daftarTahun',
            'tahunDipilih',
            'searchQuery',
            'tabel',
            'labelBulan',
            'seriPadangYoy',
            'seriSumbarYoy',
            'seriNasionalYoy',
            'seriPadangMtm',
            'seriPadangYtd'
        ));
    }
}
