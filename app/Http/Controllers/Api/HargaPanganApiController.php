<?php

namespace App\Http\Controllers\Api;

use App\Models\HargaPangan;
use Illuminate\Http\Request;

class HargaPanganApiController extends BaseApiController
{
    /**
     * GET /api/v1/harga-pangan
     * Filter: tahun, bulan, minggu, komoditas, q, sort, order, per_page
     */
    public function index(Request $request)
    {
        $query = HargaPangan::query();

        $this->filterPeriode($query, $request);

        if ($request->filled('minggu')) {
            $query->where('minggu', (int) $request->query('minggu'));
        }

        if ($request->filled('komoditas')) {
            $query->where('komoditas', $request->query('komoditas'));
        }

        if ($request->filled('q')) {
            $query->where('komoditas', 'like', '%' . $request->query('q') . '%');
        }

        $this->terapkanUrutan($query, $request, ['urutan_bulan', 'minggu', 'tahun', 'komoditas', 'harga_ini', 'persen', 'perubahan'], 'urutan_bulan');

        $data = $query->paginate($this->perPage($request))->appends($request->query());

        return $this->paginated($data, 'Daftar harga pangan', fn ($item) => $this->format($item));
    }

    /**
     * GET /api/v1/harga-pangan/terkini
     * Harga minggu terakhir yang tersedia untuk setiap komoditas.
     */
    public function terkini(Request $request)
    {
        $tahun = (int) $request->query('tahun', now()->year);

        $bulanTerakhir = (int) (HargaPangan::where('tahun', $tahun)->max('urutan_bulan') ?? 0);

        if ($bulanTerakhir === 0) {
            return $this->ok([], "Belum ada data harga pangan untuk tahun {$tahun}");
        }

        $mingguTerakhir = (int) (HargaPangan::where('tahun', $tahun)
            ->where('urutan_bulan', $bulanTerakhir)
            ->max('minggu') ?? 1);

        $items = HargaPangan::where('tahun', $tahun)
            ->where('urutan_bulan', $bulanTerakhir)
            ->where('minggu', $mingguTerakhir)
            ->orderBy('komoditas')
            ->get()
            ->unique('komoditas')
            ->values();

        return $this->ok(
            $items->map(fn ($i) => $this->format($i)),
            'Harga pangan terkini',
            [
                'tahun' => $tahun,
                'urutan_bulan' => $bulanTerakhir,
                'bulan' => $this->namaBulan($bulanTerakhir),
                'minggu' => $mingguTerakhir,
                'jumlah_komoditas' => $items->count(),
            ]
        );
    }

    /**
     * GET /api/v1/harga-pangan/riwayat
     * Deret harga mingguan satu komoditas, cocok untuk grafik.
     */
    public function riwayat(Request $request)
    {
        $request->validate([
            'komoditas' => ['required', 'string'],
        ]);

        $tahun = (int) $request->query('tahun', now()->year);
        $komoditas = $request->query('komoditas');

        $items = HargaPangan::where('tahun', $tahun)
            ->where('komoditas', $komoditas)
            ->orderBy('urutan_bulan')
            ->orderBy('minggu')
            ->get()
            ->unique(fn ($i) => $i->urutan_bulan . '_' . $i->minggu)
            ->values();

        return $this->ok([
            'komoditas' => $komoditas,
            'tahun' => $tahun,
            'satuan' => $items->last()->satuan ?? 'kg',
            'label' => $items->map(fn ($i) => substr($this->namaBulan((int) $i->urutan_bulan), 0, 3) . ' M' . $i->minggu)->values(),
            'harga' => $items->map(fn ($i) => $i->harga_ini)->values(),
            'het' => $items->map(fn ($i) => $i->het)->values(),
        ], 'Riwayat harga mingguan');
    }

    /**
     * GET /api/v1/harga-pangan/{id}
     */
    public function show(int $id)
    {
        $item = HargaPangan::find($id);

        if (! $item) {
            return $this->fail('Data harga pangan tidak ditemukan.', 404);
        }

        return $this->ok($this->format($item), 'Detail harga pangan');
    }

    /**
     * POST /api/v1/harga-pangan  (butuh API key)
     */
    public function store(Request $request)
    {
        $item = HargaPangan::create($this->validasi($request));

        return $this->ok($this->format($item), 'Data harga pangan berhasil ditambahkan', [], 201);
    }

    /**
     * PUT /api/v1/harga-pangan/{id}  (butuh API key)
     */
    public function update(Request $request, int $id)
    {
        $item = HargaPangan::find($id);

        if (! $item) {
            return $this->fail('Data harga pangan tidak ditemukan.', 404);
        }

        $item->update($this->validasi($request, true));

        return $this->ok($this->format($item->fresh()), 'Data harga pangan berhasil diperbarui');
    }

    /**
     * DELETE /api/v1/harga-pangan/{id}  (butuh API key)
     */
    public function destroy(int $id)
    {
        $item = HargaPangan::find($id);

        if (! $item) {
            return $this->fail('Data harga pangan tidak ditemukan.', 404);
        }

        $item->delete();

        return $this->ok(null, 'Data harga pangan berhasil dihapus');
    }

    private function validasi(Request $request, bool $partial = false): array
    {
        $wajib = $partial ? 'sometimes' : 'required';

        $data = $request->validate([
            'komoditas' => [$wajib, 'string', 'max:150'],
            'urutan_bulan' => [$wajib, 'integer', 'between:1,12'],
            'tahun' => [$wajib, 'integer', 'between:2000,2100'],
            'minggu' => [$wajib, 'integer', 'between:1,5'],
            'bulan' => ['nullable', 'string', 'max:20'],
            'satuan' => ['nullable', 'string', 'max:20'],
            'het' => ['nullable', 'numeric'],
            'harga_ini' => ['nullable', 'numeric'],
            'harga_lalu' => ['nullable', 'numeric'],
            'perubahan' => ['nullable', 'numeric'],
            'persen' => ['nullable', 'numeric'],
        ]);

        if (empty($data['bulan']) && isset($data['urutan_bulan'])) {
            $data['bulan'] = $this->namaBulan((int) $data['urutan_bulan']);
        }

        // Hitung otomatis selisih dan persentase bila tidak dikirim.
        $ini = $data['harga_ini'] ?? null;
        $lalu = $data['harga_lalu'] ?? null;

        if ($ini !== null && $lalu !== null) {
            if (! array_key_exists('perubahan', $data) || $data['perubahan'] === null) {
                $data['perubahan'] = round($ini - $lalu, 2);
            }

            if ((! array_key_exists('persen', $data) || $data['persen'] === null) && (float) $lalu != 0.0) {
                $data['persen'] = round((($ini - $lalu) / $lalu) * 100, 2);
            }
        }

        return $data;
    }

    private function format(HargaPangan $item): array
    {
        return [
            'id' => $item->id,
            'tahun' => $item->tahun,
            'urutan_bulan' => (int) $item->urutan_bulan,
            'bulan' => $item->bulan,
            'minggu' => (int) $item->minggu,
            'komoditas' => $item->komoditas,
            'satuan' => $item->satuan,
            'het' => $item->het,
            'harga_ini' => $item->harga_ini,
            'harga_lalu' => $item->harga_lalu,
            'perubahan' => $item->perubahan,
            'persen' => $item->persen,
            'status' => $this->statusHarga($item),
            'diperbarui_pada' => optional($item->updated_at)->toIso8601String(),
        ];
    }

    private function statusHarga(HargaPangan $item): string
    {
        if ($item->persen === null) {
            return 'stabil';
        }

        if ($item->persen > 0) {
            return 'naik';
        }

        if ($item->persen < 0) {
            return 'turun';
        }

        return 'stabil';
    }
}
