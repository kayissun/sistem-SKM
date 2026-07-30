<?php

use App\Http\Controllers\Puskesmas\DashboardController;
use App\Http\Controllers\Puskesmas\LaporanController;
use App\Http\Controllers\Puskesmas\PetugasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route modul admin-puskesmas
|--------------------------------------------------------------------------
| Cara pakai: require file ini dari routes/web.php, contoh:
|   require __DIR__.'/puskesmas.php';
|
| - Kelola petugas: hanya role admin-puskesmas (petugas tidak boleh kelola akun lain)
| - Laporan: admin-puskesmas & petugas boleh lihat
*/

Route::middleware(['auth', 'role:admin-puskesmas'])
    ->prefix('puskesmas')
    ->name('puskesmas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('petugas', PetugasController::class)->except(['show']);
    });

Route::middleware(['auth', 'role:admin-puskesmas|petugas'])
    ->prefix('puskesmas')
    ->name('puskesmas.')
    ->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    });
