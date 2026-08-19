<?php

use App\Http\Controllers\Api\SkmController;
use App\Http\Controllers\Api\SubmitSurveiController;
use Illuminate\Support\Facades\Route;

Route::get('/instansi/{puskesmas:slug}/skm', [SkmController::class, 'show'])
    ->name('api.instansi.skm');

Route::post('/instansi/{puskesmas:slug}/survei', [SubmitSurveiController::class, 'store'])
    ->middleware('throttle:15,10')
    ->name('api.instansi.survei.store');