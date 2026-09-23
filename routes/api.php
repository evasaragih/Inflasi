<?php

use App\Http\Controllers\Api\AndilApiController;
use App\Http\Controllers\Api\HargaPanganApiController;
use App\Http\Controllers\Api\InflasiApiController;
use App\Http\Controllers\Api\RingkasanApiController;
use App\Http\Controllers\Api\StokPanganApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Dashboard Inflasi Kota Padang — versi 1
|--------------------------------------------------------------------------
| Semua endpoint di bawah otomatis berawalan /api
| Endpoint GET terbuka (data publik), endpoint tulis butuh header X-API-KEY.
*/

Route::prefix('v1')->group(function () {

    // ---------- CEK STATUS ----------
    Route::get('/ping', fn () => response()->json([
        'success' => true,
        'message' => 'API Dashboard Inflasi aktif',
        'versi' => 'v1',
        'waktu' => now()->toIso8601String(),
    ]));

    // ---------- ENDPOINT PUBLIK (BACA) ----------
    Route::get('/meta', [RingkasanApiController::class, 'meta']);
    Route::get('/ringkasan', [RingkasanApiController::class, 'index']);

    Route::get('/inflasi/terbaru', [InflasiApiController::class, 'terbaru']);
    Route::get('/inflasi', [InflasiApiController::class, 'index']);
    Route::get('/inflasi/{id}', [InflasiApiController::class, 'show'])->whereNumber('id');

    Route::get('/andil/peringkat', [AndilApiController::class, 'peringkat']);
    Route::get('/andil', [AndilApiController::class, 'index']);
    Route::get('/andil/{id}', [AndilApiController::class, 'show'])->whereNumber('id');

    Route::get('/harga-pangan/terkini', [HargaPanganApiController::class, 'terkini']);
    Route::get('/harga-pangan/riwayat', [HargaPanganApiController::class, 'riwayat']);
    Route::get('/harga-pangan', [HargaPanganApiController::class, 'index']);
    Route::get('/harga-pangan/{id}', [HargaPanganApiController::class, 'show'])->whereNumber('id');

    Route::get('/stok-pangan/ringkasan', [StokPanganApiController::class, 'ringkasan']);
    Route::get('/stok-pangan', [StokPanganApiController::class, 'index']);
    Route::get('/stok-pangan/{id}', [StokPanganApiController::class, 'show'])->whereNumber('id');

    // ---------- ENDPOINT TERPROTEKSI (TULIS) ----------
    Route::middleware('api.key')->group(function () {
        Route::post('/inflasi', [InflasiApiController::class, 'store']);
        Route::put('/inflasi/{id}', [InflasiApiController::class, 'update'])->whereNumber('id');
        Route::delete('/inflasi/{id}', [InflasiApiController::class, 'destroy'])->whereNumber('id');

        Route::post('/andil', [AndilApiController::class, 'store']);
        Route::put('/andil/{id}', [AndilApiController::class, 'update'])->whereNumber('id');
        Route::delete('/andil/{id}', [AndilApiController::class, 'destroy'])->whereNumber('id');

        Route::post('/harga-pangan', [HargaPanganApiController::class, 'store']);
        Route::put('/harga-pangan/{id}', [HargaPanganApiController::class, 'update'])->whereNumber('id');
        Route::delete('/harga-pangan/{id}', [HargaPanganApiController::class, 'destroy'])->whereNumber('id');

        Route::post('/stok-pangan', [StokPanganApiController::class, 'store']);
        Route::put('/stok-pangan/{id}', [StokPanganApiController::class, 'update'])->whereNumber('id');
        Route::delete('/stok-pangan/{id}', [StokPanganApiController::class, 'destroy'])->whereNumber('id');
    });
});

// ---------- 404 KHUSUS API ----------
Route::fallback(fn () => response()->json([
    'success' => false,
    'message' => 'Endpoint tidak ditemukan. Lihat dokumentasi di PANDUAN_API.md',
], 404));
