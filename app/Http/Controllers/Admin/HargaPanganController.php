<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HargaPangan;
use Illuminate\Http\Request;

class HargaPanganController extends Controller
{
    private array $bulanNama = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $query = HargaPangan::query();
        if ($q = $request->get('q')) {
            $query->where(function($w) use ($q) {
                $w->where('komoditas', 'like', "%{$q}%")
                  ->orWhere('bulan', 'like', "%{$q}%")
                  ->orWhere('tahun', 'like', "%{$q}%");
            });
        }
        $data = $query->orderByDesc('tahun')->orderByDesc('urutan_bulan')->orderByDesc('minggu')->orderBy('komoditas')->paginate(15)->withQueryString();

        return view('admin.pangan.index', ['data' => $data, 'q' => $q]);
    }

    public function create()
    {
        return view('admin.pangan.form', [
            'item' => null,
            'bulanNama' => $this->bulanNama,
            'daftarKomoditas' => $this->daftarKomoditas(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated = $this->lengkapiHitungan($validated);

        HargaPangan::create($validated);

        return redirect()->route('admin.pangan.index')->with('sukses', 'Data harga pangan berhasil ditambahkan.');
    }

    public function edit(HargaPangan $pangan)
    {
        return view('admin.pangan.form', [
            'item' => $pangan,
            'bulanNama' => $this->bulanNama,
            'daftarKomoditas' => $this->daftarKomoditas(),
        ]);
    }

    public function update(Request $request, HargaPangan $pangan)
    {
        $validated = $this->validated($request);
        $validated = $this->lengkapiHitungan($validated);

        $pangan->update($validated);

        return redirect()->route('admin.pangan.index')->with('sukses', 'Data harga pangan berhasil diperbarui.');
    }

    public function destroy(HargaPangan $pangan)
    {
        $pangan->delete();

        return back()->with('sukses', 'Data harga pangan berhasil dihapus.');
    }

    private function daftarKomoditas(): array
    {
        return HargaPangan::select('komoditas')->distinct()->orderBy('komoditas')->pluck('komoditas')->toArray();
    }

    private function lengkapiHitungan(array $validated): array
    {
        $validated['bulan'] = $this->bulanNama[$validated['urutan_bulan']];
        $validated['tahun'] = $validated['tahun'] ?? 2026;

        if ($validated['harga_ini'] !== null && $validated['harga_lalu'] !== null && $validated['harga_lalu'] != 0) {
            $validated['perubahan'] = $validated['harga_ini'] - $validated['harga_lalu'];
            $validated['persen'] = round(($validated['perubahan'] / $validated['harga_lalu']) * 100, 2);
        } else {
            $validated['perubahan'] = 0;
            $validated['persen'] = 0;
        }

        return $validated;
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'komoditas' => ['required', 'string', 'max:255'],
            'urutan_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'minggu' => ['required', 'integer', 'min:1', 'max:5'],
            'satuan' => ['nullable', 'string', 'max:50'],
            'het' => ['nullable', 'numeric'],
            'harga_ini' => ['nullable', 'numeric'],
            'harga_lalu' => ['nullable', 'numeric'],
        ]);
    }
}
