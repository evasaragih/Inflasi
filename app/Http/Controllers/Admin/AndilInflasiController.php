<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AndilInflasi;
use Illuminate\Http\Request;

class AndilInflasiController extends Controller
{
    private array $bulanNama = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    private array $kelompokPengeluaran = [
        'Umum (Headline)',
        'Makanan, Minuman, dan Tembakau',
        'Pakaian dan Alas Kaki',
        'Perumahan, Air, Listrik, dan Bahan Bakar Rumah Tangga',
        'Perlengkapan, Peralatan, dan Pemeliharaan Rutin Rumah Tangga',
        'Kesehatan',
        'Transportasi',
        'Informasi, Komunikasi, dan Jasa Keuangan',
        'Rekreasi, Olahraga, dan Budaya',
        'Pendidikan',
        'Penyediaan Makanan dan Minuman/Restoran',
        'Perawatan Pribadi dan Jasa Lainnya',
    ];

    public function index(Request $request)
    {
        $query = AndilInflasi::query();
        if ($q = $request->get('q')) {
            $query->where(function($w) use ($q) {
                $w->where('kelompok', 'like', "%{$q}%")
                  ->orWhere('bulan', 'like', "%{$q}%")
                  ->orWhere('tahun', 'like', "%{$q}%");
            });
        }
        $data = $query->orderByDesc('tahun')->orderByDesc('urutan_bulan')->orderBy('kelompok')->paginate(15)->withQueryString();

        return view('admin.andil.index', ['data' => $data, 'q' => $q]);
    }

    public function create()
    {
        return view('admin.andil.form', [
            'item' => null,
            'bulanNama' => $this->bulanNama,
            'kelompokPengeluaran' => $this->kelompokPengeluaran,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['bulan'] = $this->bulanNama[$validated['urutan_bulan']];
        $validated['tahun'] = $validated['tahun'] ?? 2026;

        AndilInflasi::create($validated);

        return redirect()->route('admin.andil.index')->with('sukses', 'Data andil inflasi berhasil ditambahkan.');
    }

    public function edit(AndilInflasi $andil)
    {
        return view('admin.andil.form', [
            'item' => $andil,
            'bulanNama' => $this->bulanNama,
            'kelompokPengeluaran' => $this->kelompokPengeluaran,
        ]);
    }

    public function update(Request $request, AndilInflasi $andil)
    {
        $validated = $this->validated($request);
        $validated['bulan'] = $this->bulanNama[$validated['urutan_bulan']];
        $validated['tahun'] = $validated['tahun'] ?? 2026;

        $andil->update($validated);

        return redirect()->route('admin.andil.index')->with('sukses', 'Data andil inflasi berhasil diperbarui.');
    }

    public function destroy(AndilInflasi $andil)
    {
        $andil->delete();

        return back()->with('sukses', 'Data andil inflasi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kelompok' => ['required', 'string', 'max:255'],
            'urutan_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'mtm' => ['nullable', 'numeric'],
            'ytd' => ['nullable', 'numeric'],
            'yoy' => ['nullable', 'numeric'],
            'andil_mtm' => ['nullable', 'numeric'],
            'andil_yoy' => ['nullable', 'numeric'],
        ]);
    }
}
