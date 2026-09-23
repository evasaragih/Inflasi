<?php

namespace App\Http\Controllers\Api;

use App\Models\InflasiIndikator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InflasiApiController extends BaseApiController
{
    /**
     * GET /api/v1/inflasi
     * Filter: wilayah, tahun, bulan, dari_bulan, sampai_bulan, sort, order, per_page
     */
    public function index(Request $request)
    {
        $query = InflasiIndikator::query();

        if ($request->filled('wilayah')) {
            $query->where('wilayah', strtolower($request->query('wilayah')));
        }

        $this->filterPeriode($query, $request);
        $this->terapkanUrutan($query, $request, ['urutan_bulan', 'tahun', 'ihk', 'mtm', 'ytd', 'yoy', 'wilayah'], 'urutan_bulan');

        $data = $query->paginate($this->perPage($request))->appends($request->query());

        return $this->paginated($data, 'Daftar indikator inflasi', fn ($item) => $this->format($item));
    }

    /**
     * GET /api/v1/inflasi/terbaru
     * Mengembalikan data bulan terakhir untuk setiap wilayah pada tahun tertentu.
     */
    public function terbaru(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);

        $hasil = [];

        foreach (config('inflasi_api.wilayah') as $wilayah) {
            $baris = InflasiIndikator::where('tahun', $tahun)
                ->where('wilayah', $wilayah)
                ->orderByDesc('urutan_bulan')
                ->first();

            $hasil[$wilayah] = $baris ? $this->format($baris) : null;
        }

        return $this->ok($hasil, "Indikator inflasi terbaru tahun {$tahun}");
    }

    /**
     * GET /api/v1/inflasi/{id}
     */
    public function show(int $id)
    {
        $item = InflasiIndikator::find($id);

        if (! $item) {
            return $this->fail('Data indikator inflasi tidak ditemukan.', 404);
        }

        return $this->ok($this->format($item), 'Detail indikator inflasi');
    }

    /**
     * POST /api/v1/inflasi  (butuh API key)
     */
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $item = InflasiIndikator::create($data);

        return $this->ok($this->format($item), 'Data indikator inflasi berhasil ditambahkan', [], 201);
    }

    /**
     * PUT /api/v1/inflasi/{id}  (butuh API key)
     */
    public function update(Request $request, int $id)
    {
        $item = InflasiIndikator::find($id);

        if (! $item) {
            return $this->fail('Data indikator inflasi tidak ditemukan.', 404);
        }

        $item->update($this->validasi($request, true));

        return $this->ok($this->format($item->fresh()), 'Data indikator inflasi berhasil diperbarui');
    }

    /**
     * DELETE /api/v1/inflasi/{id}  (butuh API key)
     */
    public function destroy(int $id)
    {
        $item = InflasiIndikator::find($id);

        if (! $item) {
            return $this->fail('Data indikator inflasi tidak ditemukan.', 404);
        }

        $item->delete();

        return $this->ok(null, 'Data indikator inflasi berhasil dihapus');
    }

    private function validasi(Request $request, bool $partial = false): array
    {
        $wajib = $partial ? 'sometimes' : 'required';

        $data = $request->validate([
            'wilayah' => [$wajib, Rule::in(config('inflasi_api.wilayah'))],
            'urutan_bulan' => [$wajib, 'integer', 'between:1,12'],
            'tahun' => [$wajib, 'integer', 'between:2000,2100'],
            'bulan' => ['nullable', 'string', 'max:20'],
            'ihk' => ['nullable', 'numeric'],
            'mtm' => ['nullable', 'numeric'],
            'ytd' => ['nullable', 'numeric'],
            'yoy' => ['nullable', 'numeric'],
        ]);

        if (empty($data['bulan']) && isset($data['urutan_bulan'])) {
            $data['bulan'] = $this->namaBulan((int) $data['urutan_bulan']);
        }

        return $data;
    }

    private function format(InflasiIndikator $item): array
    {
        return [
            'id' => $item->id,
            'wilayah' => $item->wilayah,
            'tahun' => $item->tahun,
            'urutan_bulan' => (int) $item->urutan_bulan,
            'bulan' => $item->bulan,
            'ihk' => $item->ihk,
            'mtm' => $item->mtm,
            'ytd' => $item->ytd,
            'yoy' => $item->yoy,
            'diperbarui_pada' => optional($item->updated_at)->toIso8601String(),
        ];
    }
}
