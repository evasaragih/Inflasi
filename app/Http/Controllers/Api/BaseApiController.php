<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BaseApiController extends Controller
{
    use ApiResponse;

    protected array $bulanPenuh = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober',
        11 => 'November', 12 => 'Desember',
    ];

    /**
     * Ambil jumlah data per halaman dari query string, dibatasi nilai maksimum.
     */
    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', config('inflasi_api.per_page', 25));
        $max = (int) config('inflasi_api.max_per_page', 200);

        return max(1, min($perPage, $max));
    }

    /**
     * Terapkan filter tahun dan bulan yang dipakai hampir semua tabel.
     */
    protected function filterPeriode(Builder $query, Request $request): Builder
    {
        if ($request->filled('tahun')) {
            $query->where('tahun', (int) $request->query('tahun'));
        }

        if ($request->filled('bulan')) {
            $bulan = $request->query('bulan');

            if (is_numeric($bulan)) {
                $query->where('urutan_bulan', (int) $bulan);
            } else {
                $query->where('bulan', $bulan);
            }
        }

        if ($request->filled('dari_bulan')) {
            $query->where('urutan_bulan', '>=', (int) $request->query('dari_bulan'));
        }

        if ($request->filled('sampai_bulan')) {
            $query->where('urutan_bulan', '<=', (int) $request->query('sampai_bulan'));
        }

        return $query;
    }

    /**
     * Terapkan pengurutan berdasarkan daftar kolom yang diizinkan.
     */
    protected function terapkanUrutan(Builder $query, Request $request, array $kolomDiizinkan, string $default = 'urutan_bulan'): Builder
    {
        $sort = (string) $request->query('sort', $default);
        $arah = strtolower((string) $request->query('order', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (! in_array($sort, $kolomDiizinkan, true)) {
            $sort = $default;
        }

        return $query->orderBy($sort, $arah);
    }

    /**
     * Konversi nomor bulan menjadi nama bulan Indonesia.
     */
    protected function namaBulan(int $urutan): string
    {
        return $this->bulanPenuh[$urutan] ?? '-';
    }
}
