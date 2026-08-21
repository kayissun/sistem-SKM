<?php

use App\Http\Controllers\Dinkes\AktivitasController;
use App\Http\Controllers\Dinkes\DashboardController;
use App\Http\Controllers\Dinkes\KlasterController;
use App\Http\Controllers\Dinkes\LaporanController;
use App\Http\Controllers\Dinkes\PeriodeSurveiController;
use App\Http\Controllers\Dinkes\PuskesmasController;
use App\Http\Controllers\Dinkes\UnsurPelayananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route khusus role "dinkes"
|--------------------------------------------------------------------------
| Cara pakai: require file ini dari routes/web.php, contoh:
|   require __DIR__.'/dinkes.php';
|
| Middleware 'role:dinkes' berasal dari package spatie/laravel-permission.
| Pastikan alias middleware sudah didaftarkan di bootstrap/app.php (lihat README).
*/

Route::middleware(['auth', 'role:dinkes'])
    ->prefix('dinkes')
    ->name('dinkes.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('puskesmas', PuskesmasController::class)
            ->except(['show']);

        Route::resource('unsur-pelayanan', UnsurPelayananController::class)
            ->except(['show']);

        Route::resource('periode-survei', PeriodeSurveiController::class)
            ->except(['show']);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdfGabungan'])->name('laporan.export-pdf');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcelGabungan'])->name('laporan.export-excel');
        Route::get('/laporan/{puskesma}/export/pdf', [LaporanController::class, 'exportPdfDetail'])->name('laporan.detail.export-pdf');
        Route::get('/laporan/{puskesma}/export/excel', [LaporanController::class, 'exportExcelDetail'])->name('laporan.detail.export-excel');
        Route::get('/laporan/{puskesma}/pertanyaan/{pertanyaan}/jawaban-teks', [LaporanController::class, 'jawabanTeks'])->name('laporan.jawaban-teks');
        Route::get('/laporan/{puskesma}/data-responden', [LaporanController::class, 'dataResponden'])->name('laporan.data-responden');
        Route::get('/laporan/{puskesma}/data-responden/export-excel', [LaporanController::class, 'exportExcelResponden'])->name('laporan.data-responden.export-excel');
        Route::get('/laporan/{puskesma}/publikasi', [LaporanController::class, 'publikasi'])->name('laporan.publikasi');
        Route::get('/laporan/{puskesma}/publikasi/export-pdf', [LaporanController::class, 'exportPdfPublikasi'])->name('laporan.publikasi.export-pdf');

        Route::get('/laporan/{puskesma}', [LaporanController::class, 'detail'])->name('laporan.detail');

        Route::get('/aktivitas', [AktivitasController::class, 'index'])->name('aktivitas.index');

        Route::get('/klaster', [KlasterController::class, 'index'])->name('klaster.index');
        Route::get('/klaster/export/pdf', [KlasterController::class, 'exportPdf'])->name('klaster.export-pdf');
        Route::get('/klaster/export/excel', [KlasterController::class, 'exportExcel'])->name('klaster.export-excel');
        Route::get('/cluster/generate', [DashboardController::class, 'generateCluster'])
            ->name('cluster.generate');

        Route::post('/puskesmas/aksi-massal', [PuskesmasController::class, 'aksiMassal'])->name('puskesmas.aksi-massal');
        Route::post('/periode-survei/aksi-massal', [PeriodeSurveiController::class, 'aksiMassal'])->name('periode-survei.aksi-massal');
        Route::post('/unsur-pelayanan/aksi-massal', [UnsurPelayananController::class, 'aksiMassal'])->name('unsur-pelayanan.aksi-massal');
    });
