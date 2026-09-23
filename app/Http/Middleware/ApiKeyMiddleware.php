<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Memeriksa API key pada header X-API-KEY (atau Authorization: Bearer ...).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('inflasi_api.key');

        if ($expected === '') {
            return response()->json([
                'success' => false,
                'message' => 'API key belum dikonfigurasi di server. Isi INFLASI_API_KEY pada file .env.',
            ], 500);
        }

        $given = (string) ($request->header('X-API-KEY') ?? $request->bearerToken() ?? '');

        if ($given === '' || ! hash_equals($expected, $given)) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak valid atau tidak dikirim.',
            ], 401);
        }

        return $next($request);
    }
}
