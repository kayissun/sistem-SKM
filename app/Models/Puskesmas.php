<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Puskesmas extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'puskesmas';

    protected $fillable = [
        'nama', 'slug', 'jenis', 'alamat', 'kecamatan', 'no_telepon', 'is_active',
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
            ->useLogName('puskesmas');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function petugas(): HasMany
    {
        return $this->hasMany(Petugas::class)->petugas();
    }

    public function admin(): HasOne
    {
        return $this->hasOne(User::class)->orderBy('id');
    }

    public function surveiJawaban(): HasMany
    {
        return $this->hasMany(SurveiJawaban::class);
    }

    public function responden(): HasMany
    {
        return $this->hasMany(Responden::class);
    }

    public function pertanyaanSurvei(): HasMany
    {
        return $this->hasMany(PertanyaanSurvei::class);
    }

    public function unitLayanan(): HasMany
    {
        return $this->hasMany(UnitLayanan::class);
    }
}
