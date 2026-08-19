<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UnitLayanan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'unit_layanan';

    protected $fillable = [
        'puskesmas_id', 'nama', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('unit-layanan');
    }

    public function puskesmas(): BelongsTo
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class, 'puskesmas_id');
    }

    public function surveiJawaban(): HasMany
    {
        return $this->hasMany(SurveiJawaban::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('nama');
    }
}
