<?php

namespace App\Http\Controllers\Api;

use App\Models\StokPangan;
use Illuminate\Http\Request;

class StokPanganApiController extends BaseApiController
{
    /**
     * GET /api/v1/stok-pangan
     * Filter: tahun, bulan, komoditas, q, sort, order, per_page
     */
    public function index(Request $request)
    {
        $query = StokPangan::query();

        $this->filterPeriode($query, $request);

        if ($request->filled('komoditas')) {
            $query->where('komoditas', $request->query('komoditas'));
        }

        if ($request->filled('q')) {
            $query->where('komoditas', 'like', '%' . $request->query('q') . '%');
        }

        $this->terapkanUrutan($query, $request, ['urutan_bulan', 'tahun', 'komoditas', 'stok', 'kebutuhan', 'harga_pasar'], 'komoditas');

        $data = $query->paginate($this->perPage($request))->appends($request->query());

        return $this->paginated($data, 'Daftar stok pangan', fn ($item) => $this->format($item));
    }

    /**
     * GET /api/v1/stok-pangan/ringkasan
     * Rekap status ketersediaan pangan pada satu periode.
     */
    public function ringkasan(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);
        $bulan = (int) $request->query('bulan', StokPangan::where('tahun', $tahun)->max('urutan_bulan') ?? 1);

        $items = StokPangan::where('tahun', $tahun)
            ->where('urutan_bulan', $bulan)
            ->orderBy('komoditas')
            ->get();

        return $this->ok([
            'tahun' => $tahun,
            'urutan_bulan' => $bulan,
            'bulan' => $this->namaBulan($bulan),
            'jumlah_komoditas' => $items->count(),
            'melimpah' => $items->where('rasio', '>=', 115)->count(),
            'aman' => $items->filter(fn ($i) => $i->rasio >= 98 && $i->rasio < 115)->count(),
            'waspada' => $items->where('rasio', '<', 98)->count(),
            'komoditas' => $items->map(fn ($i) => $this->format($i))->values(),
        ], 'Ringkasan stok pangan');
    }

    /**
     * GET /api/v1/stok-pangan/{id}
     */
    public function show(int $id)
    {
        $item = StokPangan::find($id);

        if (! $item) {
            return $this->fail('Data stok pangan tidak ditemukan.', 404);
        }

        return $this->ok($this->format($item), 'Detail stok pangan');
    }

    /**
     * POST /api/v1/stok-pangan  (butuh API key)
     * Memakai updateOrCreate karena tabel punya unique (tahun, urutan_bulan, komoditas).
     */
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        $item = StokPangan::updateOrCreate(
            [
                'tahun' => $data['tahun'],
                'urutan_bulan' => $data['urutan_bulan'],
                'komoditas' => $data['komoditas'],
            ],
            $data
        );

        return $this->ok($this->format($item), 'Data stok pangan berhasil disimpan', [], 201);
    }

    /**
     * PUT /api/v1/stok-pangan/{id}  (butuh API key)
     */
    public function update(Request $request, int $id)
    {
        $item = StokPangan::find($id);

        if (! $item) {
            return $this->fail('Data stok pangan tidak ditemukan.', 404);
        }

        $item->update($this->validasi($request, true));

        return $this->ok($this->format($item->fresh()), 'Data stok pangan berhasil diperbarui');
    }

    /**
     * DELETE /api/v1/stok-pangan/{id}  (butuh API key)
     */
    public function destroy(int $id)
    {
        $item = StokPangan::find($id);

        if (! $item) {
            return $this->fail('Data stok pangan tidak ditemukan.', 404);
        }

        $item->delete();

        return $this->ok(null, 'Data stok pangan berhasil dihapus');
    }

    private function validasi(Request $request, bool $partial = false): array
    {
        $wajib = $partial ? 'sometimes' : 'required';

        $data = $request->validate([
            'komoditas' => [$wajib, 'string', 'max:150'],
            'urutan_bulan' => [$wajib, 'integer', 'between:1,12'],
            'tahun' => [$wajib, 'integer', 'between:2000,2100'],
            'bulan' => ['nullable', 'string', 'max:20'],
            'stok' => ['nullable', 'numeric', 'min:0'],
            'kebutuhan' => ['nullable', 'numeric', 'min:0'],
            'satuan' => ['nullable', 'string', 'max:20'],
            'harga_pasar' => ['nullable', 'numeric', 'min:0'],
            'het' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if (empty($data['bulan']) && isset($data['urutan_bulan'])) {
            $data['bulan'] = $this->namaBulan((int) $data['urutan_bulan']);
        }

        return $data;
    }

    private function format(StokPangan $item): array
    {
        return [
            'id' => $item->id,
            'tahun' => (int) $item->tahun,
            'urutan_bulan' => (int) $item->urutan_bulan,
            'bulan' => $item->bulan,
            'komoditas' => $item->komoditas,
            'stok' => (float) $item->stok,
            'kebutuhan' => (float) $item->kebutuhan,
            'satuan' => $item->satuan,
            'rasio' => $item->rasio,
            'status' => $item->status,
            'harga_pasar' => $item->harga_pasar !== null ? (float) $item->harga_pasar : null,
            'het' => $item->het !== null ? (float) $item->het : null,
            'keterangan' => $item->keterangan,
            'diperbarui_pada' => optional($item->updated_at)->toIso8601String(),
        ];
    }
}
