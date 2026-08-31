<?php

use App\Http\Controllers\Puskesmas\DashboardController;
use App\Http\Controllers\Puskesmas\LaporanController;
use App\Http\Controllers\Puskesmas\PertanyaanSurveiController;
use App\Http\Controllers\Puskesmas\PetugasController;
use App\Http\Controllers\Puskesmas\TindakLanjutController;
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

Route::middleware(['auth', 'role:admin-puskesmas|dinkes-skm'])
    ->prefix('puskesmas')
    ->name('puskesmas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('petugas', PetugasController::class)->except(['show']);

        // Resource Pertanyaan
        Route::resource('pertanyaan', PertanyaanSurveiController::class)->except(['show']);
        Route::post('/pertanyaan/reorder', [PertanyaanSurveiController::class, 'reorder'])->name('pertanyaan.reorder');
        Route::post('/pertanyaan/{pertanyaan}/duplikat', [PertanyaanSurveiController::class, 'duplikat'])->name('pertanyaan.duplikat');
        Route::post('/pertanyaan/{pertanyaan}/header-gambar', [PertanyaanSurveiController::class, 'updateHeaderImage'])->name('pertanyaan.update-header-image');
        Route::post('/pertanyaan/aksi-massal', [PertanyaanSurveiController::class, 'aksiMassal'])->name('pertanyaan.aksi-massal');
        Route::post('/pertanyaan/form-header-gambar', [PertanyaanSurveiController::class, 'uploadFormHeaderImage'])->name('pertanyaan.form-header-upload');
        Route::delete('/pertanyaan/form-header-gambar', [PertanyaanSurveiController::class, 'hapusFormHeaderImage'])->name('pertanyaan.form-header-hapus');

        Route::resource('unit-layanan', UnitLayananController::class)->except(['show']);
        Route::post('/unit-layanan/aksi-massal', [UnitLayananController::class, 'aksiMassal'])->name('unit-layanan.aksi-massal');

        // Tindak Lanjut
        Route::get('/tindak-lanjut', [TindakLanjutController::class, 'index'])->name('tindak-lanjut.index');
        Route::get('/tindak-lanjut/create', [TindakLanjutController::class, 'create'])->name('tindak-lanjut.create');
        Route::post('/tindak-lanjut', [TindakLanjutController::class, 'store'])->name('tindak-lanjut.store');
        Route::get('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'show'])->name('tindak-lanjut.show');
        Route::get('/tindak-lanjut/{tindakLanjut}/edit', [TindakLanjutController::class, 'edit'])->name('tindak-lanjut.edit');
        Route::put('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'update'])->name('tindak-lanjut.update');
        Route::delete('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'destroy'])->name('tindak-lanjut.destroy');
        Route::post('/tindak-lanjut/{tindakLanjut}/submit', [TindakLanjutController::class, 'submit'])->name('tindak-lanjut.submit');
        Route::get('/tindak-lanjut/{tindakLanjut}/progress', [TindakLanjutController::class, 'addProgress'])->name('tindak-lanjut.progress.create');
        Route::post('/tindak-lanjut/{tindakLanjut}/progress', [TindakLanjutController::class, 'storeProgress'])->name('tindak-lanjut.progress.store');
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
