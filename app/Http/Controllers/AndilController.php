<?php

namespace App\Http\Controllers;

use App\Models\AndilInflasi;
use Illuminate\Http\Request;

class AndilController extends Controller
{
    public function index(Request $request)
    {
        $bulanUrut = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
            6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
            11 => 'November', 12 => 'Desember',
        ];

        $daftarTahun = AndilInflasi::select('tahun')->distinct()->pluck('tahun')
            ->merge([2026, 2025, 2024])
            ->unique()
            ->sortDesc()
            ->values();
        $tahunDipilih = (int) $request->get('tahun', 2026);

        $bulanTerakhir = AndilInflasi::where('tahun', $tahunDipilih)->max('urutan_bulan') ?? 12;
        $bulanDipilih = (int) $request->get('bulan', $bulanTerakhir);
        if ($bulanDipilih < 1 || $bulanDipilih > 12) {
            $bulanDipilih = $bulanTerakhir;
        }

        $searchQuery = trim($request->get('q', ''));

        $daftarBulan = collect(range(1, $bulanTerakhir))->mapWithKeys(fn ($u) => [$u => $bulanUrut[$u]]);

        $data = AndilInflasi::where('tahun', $tahunDipilih)
            ->where('urutan_bulan', $bulanDipilih)
            ->orderByDesc('yoy')
            ->get()
            ->unique('kelompok')
            ->values();

        $headline = $data->firstWhere('kelompok', 'Umum (Headline)');
        $kelompokLain = $data->where('kelompok', '!=', 'Umum (Headline)')->values();

        if ($searchQuery !== '') {
            $kelompokLain = $kelompokLain->filter(function ($item) use ($searchQuery) {
                return stripos($item->kelompok, $searchQuery) !== false;
            })->values();
        }

        $labelKelompok = $kelompokLain->map(fn ($k) => $k->kelompok)->toArray();
        $andilYoy = $kelompokLain->map(fn ($k) => $k->andil_yoy)->toArray();
        $andilMtm = $kelompokLain->map(fn ($k) => $k->andil_mtm)->toArray();

        return view('andil.index', compact(
            'daftarTahun',
            'tahunDipilih',
            'daftarBulan',
            'bulanDipilih',
            'searchQuery',
            'headline',
            'kelompokLain',
            'labelKelompok',
            'andilYoy',
            'andilMtm'
        ));
    }
}
