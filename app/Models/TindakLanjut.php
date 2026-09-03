<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TindakLanjut extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjuts';

    protected $fillable = [
        'puskesmas_id',
        'unsur_pelayanan_id',
        'triwulan',
        'tahun',
        'nilai_kondisi',
        'tindakan_perbaikan',
        'bukti',
        'foto',
        'status',
        'catatan_dinkes',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'triwulan' => 'integer',
        'tahun' => 'integer',
        'nilai_kondisi' => 'float',
        'foto' => 'array',
        'verified_at' => 'datetime',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function puskesmas(): BelongsTo
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function unsurPelayanan(): BelongsTo
    {
        return $this->belongsTo(UnsurPelayanan::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(TindakLanjutProgress::class);
    }

    public function scopeForPuskesmas($query, int $puskesmasId)
    {
        return $query->where('puskesmas_id', $puskesmasId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTriwulan($query, int $triwulan)
    {
        return $query->where('triwulan', $triwulan);
    }

    public function scopeByTahun($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Terkirim',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        // Sama dengan konvensi badge Dinkes: submitted = hijau, selainnya = kuning/amber.
        return $this->status === self::STATUS_SUBMITTED ? 'submitted' : 'draft';
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT || $this->status === self::STATUS_REJECTED;
    }
}
