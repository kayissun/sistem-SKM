<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveiJawabanDetail extends Model
{
    use HasFactory;

    protected $table = 'survei_jawaban_detail';

    protected $fillable = [
        'survei_jawaban_id', 'unsur_pelayanan_id', 'nilai',
    ];

    public function surveiJawaban(): BelongsTo
    {
        return $this->belongsTo(SurveiJawaban::class);
    }

    public function unsurPelayanan(): BelongsTo
    {
        return $this->belongsTo(UnsurPelayanan::class);
    }
}
