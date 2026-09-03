<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjutProgress extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjut_progress';

    protected $fillable = [
        'tindak_lanjut_id',
        'foto',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function tindakLanjut(): BelongsTo
    {
        return $this->belongsTo(TindakLanjut::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
