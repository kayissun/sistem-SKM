<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PeriodeSurvei extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'periode_survei';

    protected $fillable = [
        'nama', 'tanggal_mulai', 'tanggal_selesai', 'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('periode-survei');
    }

    public function surveiJawaban(): HasMany
    {
        return $this->hasMany(SurveiJawaban::class);
    }
}
