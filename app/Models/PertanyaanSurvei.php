<?php

namespace App\Models;

use App\Support\PresetLabelSkala;
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
    protected $guarded = ['id'];

    protected $fillable = [
        'puskesmas_id', 
        'unsur_pelayanan_id', 
        'teks_pertanyaan', 
        'header_image', 
        'layout_mode',
        'tipe_input',
        'gaya_tampilan', 
        'label_skala_1', 
        'label_skala_2', 
        'label_skala_3', 
        'label_skala_4',
        'urutan', 
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $pertanyaan) {
            $pertanyaan->isiLabelPresetJikaKosong();
        });

        static::updating(function (self $pertanyaan) {
            if ($pertanyaan->isDirty('unsur_pelayanan_id') || $pertanyaan->isDirty('tipe_input')) {
                $pertanyaan->isiLabelPresetJikaKosong();
            }
        });
    }

    public function headerImageUrl(): ?string
    {
        return $this->header_image ? asset('storage/' . $this->header_image) : null;
    }

    public function labelSkala(): array
    {
        return [
            1 => $this->label_skala_1 ?: '1',
            2 => $this->label_skala_2 ?: '2',
            3 => $this->label_skala_3 ?: '3',
            4 => $this->label_skala_4 ?: '4',
        ];
    }

    protected function isiLabelPresetJikaKosong(): void
    {
        if (($this->tipe_input ?? 'skala') !== 'skala' || empty($this->unsur_pelayanan_id)) {
            return;
        }

        $sudahAdaLabel = array_filter([
            $this->label_skala_1,
            $this->label_skala_2,
            $this->label_skala_3,
            $this->label_skala_4,
        ], fn ($value) => $value !== null && $value !== '');

        if (! empty($sudahAdaLabel)) {
            return;
        }

        $unsur = UnsurPelayanan::find($this->unsur_pelayanan_id);
        if (! $unsur) {
            return;
        }

        $preset = PresetLabelSkala::daftar()[strtolower($unsur->kode)] ?? null;
        if (! $preset) {
            return;
        }

        [$this->label_skala_1, $this->label_skala_2, $this->label_skala_3, $this->label_skala_4] = $preset['label'];
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
        return $this->belongsTo(UnsurPelayanan::class, 'unsur_pelayanan_id');
    }

    public function surveiJawabanDetail(): HasMany
    {
        return $this->hasMany(SurveiJawabanDetail::class);
    }

    public function scopeUnsurWajib($query)
    {
        return $query->whereNotNull('unsur_pelayanan_id');
    }

    public function scopeTambahan($query)
    {
        return $query->whereNull('unsur_pelayanan_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}