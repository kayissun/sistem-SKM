<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Nama domain untuk satu pengisian survei yang dibuat oleh responden.
 * Data historis tetap memakai tabel survei_jawaban.
 */
class Responden extends SurveiJawaban
{
    public function detail(): HasMany
    {
        return $this->hasMany(SurveiJawabanDetail::class, 'survei_jawaban_id');
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class, 'puskesmas_id');
    }
}