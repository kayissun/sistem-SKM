<?php

use App\Http\Controllers\Puskesmas\DashboardController;
use App\Http\Controllers\Puskesmas\LaporanController;
use App\Http\Controllers\Puskesmas\PertanyaanSurveiController;
use App\Http\Controllers\Puskesmas\PetugasController;
use App\Http\Controllers\Puskesmas\UnitLayananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route modul admin-puskesmas
|--------------------------------------------------------------------------
| Cara pakai: require file ini dari routes/web.php, contoh:
|   require __DIR__.'/puskesmas.php';
|
| - Kelola petugas, pertanyaan survei, unit layanan: hanya role admin-puskesmas
| - Laporan: admin-puskesmas & petugas boleh lihat
*/

Route::middleware(['auth', 'role:admin-puskesmas'])
    ->prefix('puskesmas')
    ->name('puskesmas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('petugas', PetugasController::class)->except(['show']);
        Route::resource('pertanyaan', PertanyaanSurveiController::class)->except(['show']);
        Route::resource('unit-layanan', UnitLayananController::class)->except(['show']);
    });

Route::middleware(['auth', 'role:admin-puskesmas|petugas'])
    ->prefix('puskesmas')
    ->name('puskesmas.')
    ->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
        Route::get('/laporan/pertanyaan/{pertanyaan}/jawaban-teks', [LaporanController::class, 'jawabanTeks'])->name('laporan.jawaban-teks');
        Route::get('/laporan/data-responden', [LaporanController::class, 'dataResponden'])->name('laporan.data-responden');
        Route::get('/laporan/data-responden/export-excel', [LaporanController::class, 'exportExcelResponden'])->name('laporan.data-responden.export-excel');
        Route::get('/laporan/publikasi', [LaporanController::class, 'publikasi'])->name('laporan.publikasi');
        Route::get('/laporan/publikasi/export-pdf', [LaporanController::class, 'exportPdfPublikasi'])->name('laporan.publikasi.export-pdf');
    });
