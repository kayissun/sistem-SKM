<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveiJawaban extends Model
{
    use HasFactory;

    protected $table = 'survei_jawaban';

    protected $fillable = [
        'puskesmas_id', 'periode_survei_id', 'unit_layanan_id', 'nama', 'no_hp',
        'jenis_kelamin', 'umur', 'pendidikan', 'pekerjaan',
    ];

    public function puskesmas(): BelongsTo
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function periodeSurvei(): BelongsTo
    {
        return $this->belongsTo(PeriodeSurvei::class);
    }

    public function unitLayanan(): BelongsTo
    {
        return $this->belongsTo(UnitLayanan::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(SurveiJawabanDetail::class);
    }

    // Scope wajib dipakai di controller admin supaya data otomatis
    // terbatas sesuai puskesmas milik user yang login (kecuali role dinkes)
    public function scopeUntukUser($query, $user)
    {
        if ($user->hasRole('dinkes')) {
            return $query; // dinkes bebas lihat semua unit
        }

        return $query->where('puskesmas_id', $user->puskesmas_id);
    }
}
