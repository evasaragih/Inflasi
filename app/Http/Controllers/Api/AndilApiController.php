<?php

namespace App\Http\Controllers\Api;

use App\Models\AndilInflasi;
use Illuminate\Http\Request;

class AndilApiController extends BaseApiController
{
    /**
     * GET /api/v1/andil
     * Filter: tahun, bulan, kelompok, q, sort, order, per_page
     */
    public function index(Request $request)
    {
        $query = AndilInflasi::query();

        $this->filterPeriode($query, $request);

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->query('kelompok'));
        }

        if ($request->filled('q')) {
            $query->where('kelompok', 'like', '%' . $request->query('q') . '%');
        }

        if ($request->boolean('tanpa_umum')) {
            $query->where('kelompok', '!=', 'Umum (Headline)');
        }

        $this->terapkanUrutan($query, $request, ['urutan_bulan', 'tahun', 'kelompok', 'mtm', 'ytd', 'yoy', 'andil_mtm', 'andil_yoy'], 'urutan_bulan');

        $data = $query->paginate($this->perPage($request))->appends($request->query());

        return $this->paginated($data, 'Daftar andil inflasi per kelompok', fn ($item) => $this->format($item));
    }

    /**
     * GET /api/v1/andil/peringkat
     * Kelompok penyumbang inflasi tertinggi & terendah pada satu periode.
     */
    public function peringkat(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);
        $bulan = (int) $request->query('bulan', AndilInflasi::where('tahun', $tahun)->max('urutan_bulan') ?? 1);
        $limit = max(1, min((int) $request->query('limit', 5), 20));
        $basis = in_array($request->query('basis'), ['andil_mtm', 'andil_yoy'], true)
            ? $request->query('basis')
            : 'andil_yoy';

        $semua = AndilInflasi::where('tahun', $tahun)
            ->where('urutan_bulan', $bulan)
            ->where('kelompok', '!=', 'Umum (Headline)')
            ->get()
            ->unique('kelompok')
            ->values();

        return $this->ok([
            'tahun' => $tahun,
            'urutan_bulan' => $bulan,
            'bulan' => $this->namaBulan($bulan),
            'basis' => $basis,
            'tertinggi' => $semua->sortByDesc($basis)->take($limit)->map(fn ($i) => $this->format($i))->values(),
            'terendah' => $semua->sortBy($basis)->take($limit)->map(fn ($i) => $this->format($i))->values(),
        ], 'Peringkat andil inflasi');
    }

    /**
     * GET /api/v1/andil/{id}
     */
    public function show(int $id)
    {
        $item = AndilInflasi::find($id);

        if (! $item) {
            return $this->fail('Data andil inflasi tidak ditemukan.', 404);
        }

        return $this->ok($this->format($item), 'Detail andil inflasi');
    }

    /**
     * POST /api/v1/andil  (butuh API key)
     */
    public function store(Request $request)
    {
        $item = AndilInflasi::create($this->validasi($request));

        return $this->ok($this->format($item), 'Data andil inflasi berhasil ditambahkan', [], 201);
    }

    /**
     * PUT /api/v1/andil/{id}  (butuh API key)
     */
    public function update(Request $request, int $id)
    {
        $item = AndilInflasi::find($id);

        if (! $item) {
            return $this->fail('Data andil inflasi tidak ditemukan.', 404);
        }

        $item->update($this->validasi($request, true));

        return $this->ok($this->format($item->fresh()), 'Data andil inflasi berhasil diperbarui');
    }

    /**
     * DELETE /api/v1/andil/{id}  (butuh API key)
     */
    public function destroy(int $id)
    {
        $item = AndilInflasi::find($id);

        if (! $item) {
            return $this->fail('Data andil inflasi tidak ditemukan.', 404);
        }

        $item->delete();

        return $this->ok(null, 'Data andil inflasi berhasil dihapus');
    }

    private function validasi(Request $request, bool $partial = false): array
    {
        $wajib = $partial ? 'sometimes' : 'required';

        $data = $request->validate([
            'kelompok' => [$wajib, 'string', 'max:150'],
            'urutan_bulan' => [$wajib, 'integer', 'between:1,12'],
            'tahun' => [$wajib, 'integer', 'between:2000,2100'],
            'bulan' => ['nullable', 'string', 'max:20'],
            'mtm' => ['nullable', 'numeric'],
            'ytd' => ['nullable', 'numeric'],
            'yoy' => ['nullable', 'numeric'],
            'andil_mtm' => ['nullable', 'numeric'],
            'andil_yoy' => ['nullable', 'numeric'],
        ]);

        if (empty($data['bulan']) && isset($data['urutan_bulan'])) {
            $data['bulan'] = $this->namaBulan((int) $data['urutan_bulan']);
        }

        return $data;
    }

    private function format(AndilInflasi $item): array
    {
        return [
            'id' => $item->id,
            'tahun' => $item->tahun,
            'urutan_bulan' => (int) $item->urutan_bulan,
            'bulan' => $item->bulan,
            'kelompok' => $item->kelompok,
            'mtm' => $item->mtm,
            'ytd' => $item->ytd,
            'yoy' => $item->yoy,
            'andil_mtm' => $item->andil_mtm,
            'andil_yoy' => $item->andil_yoy,
            'diperbarui_pada' => optional($item->updated_at)->toIso8601String(),
        ];
    }
}
