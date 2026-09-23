<?php

namespace App\Http\Controllers;

use App\Models\HargaPangan;
use Illuminate\Http\Request;

class PanganController extends Controller
{
    public function index(Request $request)
    {
        $daftarTahun = HargaPangan::select('tahun')->distinct()->pluck('tahun')
            ->merge([2026, 2025, 2024])
            ->unique()
            ->sortDesc()
            ->values();
        $tahunDipilih = (int) $request->get('tahun', 2026);
        $searchQuery = trim($request->get('q', ''));

        $daftarKomoditas = HargaPangan::where('tahun', $tahunDipilih)
            ->select('komoditas')
            ->distinct()
            ->orderBy('komoditas')
            ->pluck('komoditas');

        $komoditasDipilih = $request->get('komoditas', $daftarKomoditas->first());

        $bulanUrut = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei',
            6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt',
            11 => 'Nov', 12 => 'Des',
        ];

        $riwayat = HargaPangan::where('tahun', $tahunDipilih)
            ->where('komoditas', $komoditasDipilih)
            ->orderBy('urutan_bulan')
            ->orderBy('minggu')
            ->get()
            ->unique(fn ($item) => $item->urutan_bulan . '_' . $item->minggu)
            ->values();

        $labelMingguan = $riwayat->map(fn ($r) => $bulanUrut[$r->urutan_bulan] . ' M' . $r->minggu)->toArray();
        $seriHarga = $riwayat->map(fn ($r) => $r->harga_ini)->toArray();
        $seriHet = $riwayat->map(fn ($r) => $r->het)->toArray();

        $bulanTerakhir = $riwayat->max('urutan_bulan') ?? 12;
        $satuan = $riwayat->last()?->satuan;
        $hargaSekarang = $riwayat->last()?->harga_ini;
        $hargaAwal = $riwayat->first()?->harga_ini;
        $perubahanTotal = ($hargaSekarang !== null && $hargaAwal) ? round((($hargaSekarang - $hargaAwal) / $hargaAwal) * 100, 2) : null;

        // Ringkasan seluruh komoditas untuk bulan terakhir minggu terakhir (tabel)
        $mingguTerakhirGlobal = HargaPangan::where('tahun', $tahunDipilih)->where('urutan_bulan', $bulanTerakhir)->max('minggu') ?? 1;
        $ringkasanSemua = HargaPangan::where('tahun', $tahunDipilih)
            ->where('urutan_bulan', $bulanTerakhir)
            ->where('minggu', $mingguTerakhirGlobal)
            ->orderBy('komoditas')
            ->get()
            ->unique('komoditas')
            ->values();

        // Ringkasan Stok Pangan (Stok, Kebutuhan, Status Pasokan)
        $bulanStok = (int) $request->get('bulan_stok', $bulanTerakhir);
        $stokMaster = [
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

        $factorStok = (match($tahunDipilih) {
            2024 => 0.94,
            2025 => 0.98,
            2026 => 1.02,
            default => 1.00
        }) * (1 + (sin($bulanStok) * 0.05));

        $stokPanganList = $ringkasanSemua->map(function ($item) use ($stokMaster, $factorStok) {
            $nama = $item->komoditas;
            $default = ['stok' => 1000, 'kebutuhan' => 900, 'satuan' => $item->satuan ?? 'Ton'];
            $base = $stokMaster[$nama] ?? $default;

            $stokVal = round($base['stok'] * $factorStok);
            $kebutuhanVal = round($base['kebutuhan']);
            $rasio = $kebutuhanVal > 0 ? ($stokVal / $kebutuhanVal) * 100 : 100;

            if ($rasio >= 115) {
                $status = 'Melimpah';
                $badgeClass = 'bg-hijau/10 text-hijau border-hijau/20';
            } elseif ($rasio >= 98) {
                $status = 'Aman (Cukup)';
                $badgeClass = 'bg-blue-500/10 text-blue-700 border-blue-200';
            } else {
                $status = 'Waspada / Perlu Pasokan';
                $badgeClass = 'bg-marun/10 text-marun border-marun/20';
            }

            return (object) [
                'komoditas' => $nama,
                'stok' => $stokVal,
                'kebutuhan' => $kebutuhanVal,
                'satuan' => $base['satuan'],
                'rasio' => round($rasio, 1),
                'status' => $status,
                'badgeClass' => $badgeClass,
                'harga_ini' => $item->harga_ini,
                'het' => $item->het,
            ];
        });

        return view('pangan.index', compact(
            'daftarTahun',
            'tahunDipilih',
            'searchQuery',
            'daftarKomoditas',
            'komoditasDipilih',
            'riwayat',
            'labelMingguan',
            'seriHarga',
            'seriHet',
            'satuan',
            'hargaSekarang',
            'perubahanTotal',
            'ringkasanSemua',
            'stokPanganList',
            'bulanStok'
        ));
    }
}
