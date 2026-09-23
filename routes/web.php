<?php

use App\Http\Controllers\Admin\AndilInflasiController as AdminAndilController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HargaPanganController as AdminPanganController;
use App\Http\Controllers\Admin\InflasiIndikatorController as AdminInflasiController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StokPanganController as AdminStokController;
use App\Http\Controllers\AndilController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InflasiController;
use App\Http\Controllers\PanganController;
use Illuminate\Support\Facades\Route;

// ==== HALAMAN PUBLIK ====
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/data-inflasi', [InflasiController::class, 'index'])->name('inflasi.index');
Route::get('/andil-inflasi', [AndilController::class, 'index'])->name('andil.index');
Route::get('/harga-pangan', [PanganController::class, 'index'])->name('pangan.index');

// ==== LOGIN / LOGOUT ====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ==== PANEL ADMIN ====
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/data-inflasi', [AdminInflasiController::class, 'index'])->name('inflasi.index');
    Route::get('/data-inflasi/tambah', [AdminInflasiController::class, 'create'])->name('inflasi.create');
    Route::post('/data-inflasi', [AdminInflasiController::class, 'store'])->name('inflasi.store');
    Route::get('/data-inflasi/{inflasi}/ubah', [AdminInflasiController::class, 'edit'])->name('inflasi.edit');
    Route::put('/data-inflasi/{inflasi}', [AdminInflasiController::class, 'update'])->name('inflasi.update');
    Route::delete('/data-inflasi/{inflasi}', [AdminInflasiController::class, 'destroy'])->name('inflasi.destroy');

    Route::get('/andil-inflasi', [AdminAndilController::class, 'index'])->name('andil.index');
    Route::get('/andil-inflasi/tambah', [AdminAndilController::class, 'create'])->name('andil.create');
    Route::post('/andil-inflasi', [AdminAndilController::class, 'store'])->name('andil.store');
    Route::get('/andil-inflasi/{andil}/ubah', [AdminAndilController::class, 'edit'])->name('andil.edit');
    Route::put('/andil-inflasi/{andil}', [AdminAndilController::class, 'update'])->name('andil.update');
    Route::delete('/andil-inflasi/{andil}', [AdminAndilController::class, 'destroy'])->name('andil.destroy');

    Route::get('/harga-pangan', [AdminPanganController::class, 'index'])->name('pangan.index');
    Route::get('/harga-pangan/tambah', [AdminPanganController::class, 'create'])->name('pangan.create');
    Route::post('/harga-pangan', [AdminPanganController::class, 'store'])->name('pangan.store');
    Route::get('/harga-pangan/{pangan}/ubah', [AdminPanganController::class, 'edit'])->name('pangan.edit');
    Route::put('/harga-pangan/{pangan}', [AdminPanganController::class, 'update'])->name('pangan.update');
    Route::delete('/harga-pangan/{pangan}', [AdminPanganController::class, 'destroy'])->name('pangan.destroy');

    Route::get('/stok-pangan', [AdminStokController::class, 'index'])->name('stok.index');
    Route::get('/stok-pangan/tambah', [AdminStokController::class, 'create'])->name('stok.create');
    Route::post('/stok-pangan', [AdminStokController::class, 'store'])->name('stok.store');
    Route::get('/stok-pangan/{stok}/ubah', [AdminStokController::class, 'edit'])->name('stok.edit');
    Route::put('/stok-pangan/{stok}', [AdminStokController::class, 'update'])->name('stok.update');
    Route::delete('/stok-pangan/{stok}', [AdminStokController::class, 'destroy'])->name('stok.destroy');

    Route::get('/pengaturan', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::post('/pengaturan', [AdminSettingController::class, 'update'])->name('settings.update');
});
