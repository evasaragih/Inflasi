<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Respons sukses standar.
     */
    protected function ok($data = null, string $message = 'Berhasil', array $meta = [], int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        $payload['data'] = $data;

        return response()->json($payload, $status, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Respons gagal standar.
     */
    protected function fail(string $message = 'Terjadi kesalahan', int $status = 400, $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Respons untuk data yang dipaginasi.
     */
    protected function paginated(LengthAwarePaginator $paginator, string $message = 'Berhasil', ?callable $transform = null): JsonResponse
    {
        $items = collect($paginator->items());

        if ($transform !== null) {
            $items = $items->map($transform);
        }

        return $this->ok($items->values(), $message, [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ]);
    }
}
