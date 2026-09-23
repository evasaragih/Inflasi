<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InflasiIndikator;
use Illuminate\Http\Request;

class InflasiIndikatorController extends Controller
{
    private array $bulanNama = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $query = InflasiIndikator::query();
        if ($q = $request->get('q')) {
            $query->where(function($w) use ($q) {
                $w->where('wilayah', 'like', "%{$q}%")
                  ->orWhere('bulan', 'like', "%{$q}%")
                  ->orWhere('tahun', 'like', "%{$q}%");
            });
        }
        $data = $query->orderByDesc('tahun')->orderByDesc('urutan_bulan')->orderBy('wilayah')->paginate(15)->withQueryString();

        return view('admin.inflasi.index', ['data' => $data, 'q' => $q]);
    }

    public function create()
    {
        return view('admin.inflasi.form', [
            'item' => null,
            'bulanNama' => $this->bulanNama,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['bulan'] = $this->bulanNama[$validated['urutan_bulan']];
        $validated['tahun'] = $validated['tahun'] ?? 2026;

        InflasiIndikator::create($validated);

        return redirect()->route('admin.inflasi.index')->with('sukses', 'Data inflasi berhasil ditambahkan.');
    }

    public function edit(InflasiIndikator $inflasi)
    {
        return view('admin.inflasi.form', [
            'item' => $inflasi,
            'bulanNama' => $this->bulanNama,
        ]);
    }

    public function update(Request $request, InflasiIndikator $inflasi)
    {
        $validated = $this->validated($request);
        $validated['bulan'] = $this->bulanNama[$validated['urutan_bulan']];
        $validated['tahun'] = $validated['tahun'] ?? 2026;

        $inflasi->update($validated);

        return redirect()->route('admin.inflasi.index')->with('sukses', 'Data inflasi berhasil diperbarui.');
    }

    public function destroy(InflasiIndikator $inflasi)
    {
        $inflasi->delete();

        return back()->with('sukses', 'Data inflasi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'wilayah' => ['required', 'in:nasional,sumbar,padang'],
            'urutan_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'ihk' => ['nullable', 'numeric'],
            'mtm' => ['nullable', 'numeric'],
            'ytd' => ['nullable', 'numeric'],
            'yoy' => ['nullable', 'numeric'],
        ]);
    }
}
