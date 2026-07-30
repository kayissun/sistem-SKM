<?php

use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SurveiPublikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route publik untuk survei responden (tanpa login)
|--------------------------------------------------------------------------
| Cara pakai: require file ini dari routes/web.php, contoh:
|   require __DIR__.'/survei.php';
*/

Route::get('/survei/{puskesmas:slug}', [SurveiPublikController::class, 'create'])->name('survei.create');
Route::post('/survei/{puskesmas:slug}', [SurveiPublikController::class, 'store'])
    ->middleware('throttle:15,10') // maksimal 15 submit per 10 menit per IP, cegah spam/bot
    ->name('survei.store');
Route::get('/survei/{puskesmas:slug}/terima-kasih', [SurveiPublikController::class, 'selesai'])->name('survei.selesai');

Route::get('/qrcode/{puskesmas:slug}.png', [QrCodeController::class, 'tampil'])->name('qrcode.tampil');
Route::get('/qrcode/{puskesmas:slug}/unduh', [QrCodeController::class, 'unduh'])->name('qrcode.unduh');
