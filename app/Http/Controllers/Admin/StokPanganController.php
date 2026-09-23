<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StokPangan;
use Illuminate\Http\Request;

class StokPanganController extends Controller
{
    private array $bulanNamaPenuh = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $daftarTahun = StokPangan::select('tahun')->distinct()->pluck('tahun')
            ->merge([2026, 2025, 2024])
            ->unique()
            ->sortDesc()
            ->values();

        $daftarBulan = collect(range(1, 12))
            ->mapWithKeys(fn ($u) => [$u => $this->bulanNamaPenuh[$u]]);

        $tahunDipilih = (int) $request->get('tahun', 2026);
        $bulanDipilih = (int) $request->get('bulan', 12);
        $searchQuery = trim($request->get('q', ''));

        $query = StokPangan::where('tahun', $tahunDipilih)
            ->where('urutan_bulan', $bulanDipilih);

        if ($searchQuery !== '') {
            $query->where('komoditas', 'like', "%{$searchQuery}%");
        }

        $stokList = $query->orderBy('komoditas')->get();

        $totalKomoditas = $stokList->count();
        $totalStok = $stokList->sum('stok');
        $totalKebutuhan = $stokList->sum('kebutuhan');
        $jumlahMelimpah = $stokList->filter(fn ($s) => $s->rasio >= 115)->count();
        $jumlahAman = $stokList->filter(fn ($s) => $s->rasio >= 98 && $s->rasio < 115)->count();
        $jumlahWaspada = $stokList->filter(fn ($s) => $s->rasio < 98)->count();

        return view('admin.stok.index', compact(
            'stokList',
            'daftarTahun',
            'daftarBulan',
            'tahunDipilih',
            'bulanDipilih',
            'searchQuery',
            'totalKomoditas',
            'totalStok',
            'totalKebutuhan',
            'jumlahMelimpah',
            'jumlahAman',
            'jumlahWaspada'
        ));
    }

    public function create()
    {
        $daftarTahun = range(2026, 2024);
        $daftarBulan = $this->bulanNamaPenuh;
        return view('admin.stok.form', compact('daftarTahun', 'daftarBulan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2030',
            'urutan_bulan' => 'required|integer|min:1|max:12',
            'komoditas' => 'required|string|max:255',
            'stok' => 'required|numeric|min:0',
            'kebutuhan' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'harga_pasar' => 'nullable|numeric|min:0',
            'het' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['bulan'] = $this->bulanNamaPenuh[(int)$validated['urutan_bulan']] ?? 'Januari';

        StokPangan::updateOrCreate(
            [
                'tahun' => $validated['tahun'],
                'urutan_bulan' => $validated['urutan_bulan'],
                'komoditas' => $validated['komoditas'],
            ],
            $validated
        );

        return redirect()->route('admin.stok.index', [
            'tahun' => $validated['tahun'],
            'bulan' => $validated['urutan_bulan']
        ])->with('success', 'Data Stok Pangan ' . $validated['komoditas'] . ' berhasil disimpan!');
    }

    public function edit($id)
    {
        $item = StokPangan::findOrFail($id);
        $daftarTahun = range(2026, 2024);
        $daftarBulan = $this->bulanNamaPenuh;
        return view('admin.stok.form', compact('item', 'daftarTahun', 'daftarBulan'));
    }

    public function update(Request $request, $id)
    {
        $item = StokPangan::findOrFail($id);

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2030',
            'urutan_bulan' => 'required|integer|min:1|max:12',
            'komoditas' => 'required|string|max:255',
            'stok' => 'required|numeric|min:0',
            'kebutuhan' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'harga_pasar' => 'nullable|numeric|min:0',
            'het' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['bulan'] = $this->bulanNamaPenuh[(int)$validated['urutan_bulan']] ?? 'Januari';

        $item->update($validated);

        return redirect()->route('admin.stok.index', [
            'tahun' => $validated['tahun'],
            'bulan' => $validated['urutan_bulan']
        ])->with('success', 'Data Stok Pangan ' . $validated['komoditas'] . ' berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $item = StokPangan::findOrFail($id);
        $komoditas = $item->komoditas;
        $tahun = $item->tahun;
        $bulan = $item->urutan_bulan;
        $item->delete();

        return redirect()->route('admin.stok.index', ['tahun' => $tahun, 'bulan' => $bulan])
            ->with('success', 'Data Stok Pangan ' . $komoditas . ' berhasil dihapus.');
    }
}
