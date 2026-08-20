<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapIkm extends Model
{
    protected $table = 'rekap_ikm';

    protected $fillable = [
        'puskesmas_id',
        'periode_survei_id',
        'unit_layanan_id',
        'jumlah_responden',
        'nilai_akhir_skm',
        'mutu_akhir',
        'per_unsur',
    ];

    protected $casts = [
        'per_unsur' => 'array', // Otomatis convert JSON ke Array PHP
        'nilai_akhir_skm' => 'float',
        'jumlah_responden' => 'integer',
    ];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function periodeSurvei()
    {
        return $this->belongsTo(PeriodeSurvei::class, 'periode_survei_id');
    }

    public function unitLayanan()
    {
        return $this->belongsTo(UnitLayanan::class, 'unit_layanan_id');
    }
}