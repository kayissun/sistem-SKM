<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UnsurPelayanan extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'unsur_pelayanan';
    protected $guarded = ['id'];

    protected $fillable = [
        'kode', 'nama_unsur', 'urutan', 'is_active',
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
            ->useLogName('unsur-pelayanan');
    }

    // Relasi: 1 Unsur Pelayanan Punya Banyak Pertanyaan
    public function pertanyaan(): HasMany
    {
        return $this->hasMany(PertanyaanSurvei::class, 'unsur_pelayanan_id');
    }

    public function pertanyaanSurvei(): HasMany
    {
        return $this->hasMany(PertanyaanSurvei::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
