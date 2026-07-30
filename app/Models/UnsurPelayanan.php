<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnsurPelayanan extends Model
{
    use HasFactory;

    protected $table = 'unsur_pelayanan';

    protected $fillable = [
        'kode', 'pertanyaan', 'urutan', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function surveiJawabanDetail(): HasMany
    {
        return $this->hasMany(SurveiJawabanDetail::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
