<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjutProgress extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjut_progress';

    protected $fillable = [
        'tindak_lanjut_id',
        'triwulan_target',
        'tahun_target',
        'nilai_akhir',
        'tercapai',
        'keterangan',
    ];

    protected $casts = [
        'triwulan_target' => 'integer',
        'tahun_target' => 'integer',
        'nilai_akhir' => 'float',
        'tercapai' => 'boolean',
    ];

    public function tindakLanjut(): BelongsTo
    {
        return $this->belongsTo(TindakLanjut::class);
    }
}
