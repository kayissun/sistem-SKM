<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClusterResult extends Model
{
    protected $fillable = [
        'puskesmas_id',
        'periode',
        'cluster',
        'cluster_nama',
        'nilai_rata2',
        'rekomendasi'
    ];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }
}
