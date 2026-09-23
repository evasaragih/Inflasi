<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AndilInflasi;
use App\Models\HargaPangan;
use App\Models\InflasiIndikator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private array $bulanNamaPendek = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei',
        6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt',
        11 => 'Nov', 12 => 'Des',
    ];

    private array $bulanNamaPenuh = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $jumlahInflasi = InflasiIndikator::count();
        $jumlahAndil = AndilInflasi::count();
        $jumlahPangan = HargaPangan::count();

        $daftarTahun = InflasiIndikator::select('tahun')->distinct()->pluck('tahun')
            ->merge([2026, 2025, 2024])
            ->unique()
            ->sortDesc()
            ->values();

        $daftarBulan = collect(range(1, 12))
            ->mapWithKeys(fn ($u) => [$u => $this->bulanNamaPenuh[$u]]);

        // Section 1: Inflasi (Default: 2026 / Bulan Terbaru)
        $tahunInflasi = (int) $request->get('tahun_inflasi', $request->get('tahun', 2026));
        $semuaInflasi = InflasiIndikator::where('tahun', $tahunInflasi)
            ->orderBy('urutan_bulan')
            ->get()
            ->unique(fn ($item) => $item->wilayah . '_' . $item->urutan_bulan);
        $bulanTerakhirInflasi = $semuaInflasi->max('urutan_bulan') ?? 12;
        $bulanInflasi = (int) $request->get('bulan_inflasi', $request->get('bulan', $bulanTerakhirInflasi));

        $padang = $semuaInflasi->where('wilayah', 'padang')->keyBy('urutan_bulan');
        $sumbar = $semuaInflasi->where('wilayah', 'sumbar')->keyBy('urutan_bulan');
        $nasional = $semuaInflasi->where('wilayah', 'nasional')->keyBy('urutan_bulan');

        $terkini = [
            'padang' => $padang->get($bulanInflasi),
            'sumbar' => $sumbar->get($bulanInflasi),
            'nasional' => $nasional->get($bulanInflasi),
        ];

        $namaBulanTerkini = $terkini['padang']->bulan ?? ($this->bulanNamaPenuh[$bulanInflasi] ?? '-');

        $labelBulanInflasi = [];
        $seriPadangYoy = [];
        $seriSumbarYoy = [];
        $seriNasionalYoy = [];

        foreach (range(1, $bulanTerakhirInflasi) as $u) {
            $labelBulanInflasi[] = $this->bulanNamaPendek[$u];
            $seriPadangYoy[] = $padang->get($u)?->yoy;
            $seriSumbarYoy[] = $sumbar->get($u)?->yoy;
            $seriNasionalYoy[] = $nasional->get($u)?->yoy;
        }

        // Section 2: Andil Inflasi
        $tahunAndil = (int) $request->get('tahun_andil', $tahunInflasi);
        $bulanTerakhirAndil = AndilInflasi::where('tahun', $tahunAndil)->max('urutan_bulan') ?? 12;
        $bulanAndil = (int) $request->get('bulan_andil', $bulanInflasi);

        $andilSemua = AndilInflasi::where('tahun', $tahunAndil)
            ->where('urutan_bulan', $bulanAndil)
            ->where('kelompok', '!=', 'Umum (Headline)')
            ->get()
            ->unique('kelompok')
            ->values();

        $andilTertinggi = $andilSemua->sortByDesc('andil_yoy')->take(4)->values();
        $andilTerendah = $andilSemua->sortBy('andil_yoy')->take(4)->values();

        $labelAndil = $andilSemua->pluck('kelompok')->toArray();
        $dataAndilYoy = $andilSemua->pluck('andil_yoy')->toArray();

        // Section 3: Harga Pangan Per Komoditas & Tahun (Tanpa Filter Bulan)
        $tahunPangan = (int) $request->get('tahun_pangan', $tahunInflasi);

        $daftarKomoditas = HargaPangan::where('tahun', $tahunPangan)
            ->select('komoditas')
            ->distinct()
            ->orderBy('komoditas')
            ->pluck('komoditas');

        if ($daftarKomoditas->isEmpty()) {
            $daftarKomoditas = HargaPangan::select('komoditas')->distinct()->orderBy('komoditas')->pluck('komoditas');
        }

        $komoditasPangan = $request->get('komoditas_pangan', $daftarKomoditas->first() ?? 'Beras Medium');

        // Riwayat sepanjang tahun untuk komoditas terpilih di tahun tersebut
        $riwayatPangan = HargaPangan::where('tahun', $tahunPangan)
            ->where('komoditas', $komoditasPangan)
            ->orderBy('urutan_bulan')
            ->orderBy('minggu')
            ->get()
            ->unique(fn ($item) => $item->urutan_bulan . '_' . $item->minggu)
            ->values();

        $labelMingguPangan = $riwayatPangan->map(fn ($r) => ($this->bulanNamaPendek[$r->urutan_bulan] ?? '') . ' M' . $r->minggu)->toArray();
        $seriHargaMingguan = $riwayatPangan->map(fn ($r) => $r->harga_ini)->toArray();
        $seriHetMingguan = $riwayatPangan->map(fn ($r) => $r->het)->toArray();
        $satuanPangan = $riwayatPangan->last()?->satuan ?? 'kg';

        // Summary komoditas untuk kartu ringkasan
        $bulanTerakhirPangan = HargaPangan::where('tahun', $tahunPangan)->max('urutan_bulan') ?? 12;
        $mingguTerakhir = HargaPangan::where('tahun', $tahunPangan)->where('urutan_bulan', $bulanTerakhirPangan)->max('minggu') ?? 1;
        $panganSemua = HargaPangan::where('tahun', $tahunPangan)
            ->where('urutan_bulan', $bulanTerakhirPangan)
            ->where('minggu', $mingguTerakhir)
            ->get()
            ->unique('komoditas')
            ->values();

        $panganNaik = $panganSemua->sortByDesc('persen')->take(4)->values();
        $panganTurun = $panganSemua->sortBy('persen')->take(4)->values();

        // Section 4: Ringkasan Stok Pangan Per Komoditi, Tahun, & Bulan
        $tahunStok = (int) $request->get('tahun_stok', $tahunInflasi);
        $bulanStok = (int) $request->get('bulan_stok', $bulanInflasi);

        $stokPanganList = \App\Models\StokPangan::where('tahun', $tahunStok)
            ->where('urutan_bulan', $bulanStok)
            ->orderBy('komoditas')
            ->get();

        if ($stokPanganList->isEmpty()) {
            $stokPanganList = \App\Models\StokPangan::where('tahun', $tahunStok)->orderBy('komoditas')->get();
        }

        return view('admin.dashboard', compact(
            'jumlahInflasi',
            'jumlahAndil',
            'jumlahPangan',
            'daftarTahun',
            'daftarBulan',
            // Inflasi
            'tahunInflasi',
            'bulanInflasi',
            'namaBulanTerkini',
            'terkini',
            'labelBulanInflasi',
            'seriPadangYoy',
            'seriSumbarYoy',
            'seriNasionalYoy',
            // Andil
            'tahunAndil',
            'bulanAndil',
            'andilTertinggi',
            'andilTerendah',
            'labelAndil',
            'dataAndilYoy',
            // Pangan Per Komoditi (Hanya Tahun & Komoditas)
            'tahunPangan',
            'daftarKomoditas',
            'komoditasPangan',
            'satuanPangan',
            'labelMingguPangan',
            'seriHargaMingguan',
            'seriHetMingguan',
            'panganNaik',
            'panganTurun',
            // Stok Pangan
            'tahunStok',
            'bulanStok',
            'stokPanganList'
        ));
    }
}
