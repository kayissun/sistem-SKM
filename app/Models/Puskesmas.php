<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function surveiJawaban(): HasMany
    {
        return $this->hasMany(SurveiJawaban::class);
    }
}
