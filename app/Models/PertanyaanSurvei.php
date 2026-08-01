<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PertanyaanSurvei extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pertanyaan_survei';

    protected $fillable = [
        'puskesmas_id', 'unsur_pelayanan_id', 'teks_pertanyaan', 'tipe_input',
        'gaya_tampilan', 'label_skala_1', 'label_skala_2', 'label_skala_3', 'label_skala_4',
        'urutan', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Label tampilan untuk tiap level skala 1-4. Kalau admin-puskesmas tidak isi label kustom,
     * jatuh ke angka biasa supaya form tetap bisa dipakai.
     */
    public function labelSkala(): array
    {
        return [
            1 => $this->label_skala_1 ?: '1',
            2 => $this->label_skala_2 ?: '2',
            3 => $this->label_skala_3 ?: '3',
            4 => $this->label_skala_4 ?: '4',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('pertanyaan-survei');
    }

    public function puskesmas(): BelongsTo
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function unsurPelayanan(): BelongsTo
    {
        return $this->belongsTo(UnsurPelayanan::class);
    }

    public function surveiJawabanDetail(): HasMany
    {
        return $this->hasMany(SurveiJawabanDetail::class);
    }

    /**
     * Pertanyaan yang terkait salah satu unsur wajib (U1-U9), dihitung ke nilai SKM resmi.
     */
    public function scopeUnsurWajib($query)
    {
        return $query->whereNotNull('unsur_pelayanan_id');
    }

    /**
     * Pertanyaan tambahan di luar 9 unsur wajib, tidak dihitung ke nilai SKM resmi.
     */
    public function scopeTambahan($query)
    {
        return $query->whereNull('unsur_pelayanan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
