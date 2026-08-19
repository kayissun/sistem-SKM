<?php

namespace App\Repositories\Puskesmas;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawabanDetail;
use App\Models\UnitLayanan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PuskesmasLaporanRepository
{
    public function daftarPeriode(): Collection
    {
        return PeriodeSurvei::orderByDesc('tanggal_mulai')->get();
    }

    public function cariPeriode(?int $periodeId): ?PeriodeSurvei
    {
        $periodeId ??= PeriodeSurvei::where('is_active', true)->value('id');

        return $periodeId ? PeriodeSurvei::find($periodeId) : null;
    }

    public function daftarUnitLayanan(Puskesmas $puskesmas): Collection
    {
        return $puskesmas->unitLayanan()->aktif()->get();
    }

    public function cariUnitLayanan(Puskesmas $puskesmas, ?int $unitLayananId): ?UnitLayanan
    {
        if (! $unitLayananId) {
            return null;
        }

        return $puskesmas->unitLayanan()
            ->aktif()
            ->whereKey($unitLayananId)
            ->first();
    }

    public function jawabanTeks(
        PertanyaanSurvei $pertanyaan,
        Puskesmas $puskesmas,
        PeriodeSurvei $periode,
        int $perHalaman = 20
    ): LengthAwarePaginator {
        return SurveiJawabanDetail::where('pertanyaan_survei_id', $pertanyaan->id)
            ->whereNotNull('jawaban_teks')
            ->whereHas('surveiJawaban', function ($query) use ($puskesmas, $periode) {
                $query->where('puskesmas_id', $puskesmas->id)
                    ->where('periode_survei_id', $periode->id);
            })
            ->with('surveiJawaban')
            ->latest()
            ->paginate($perHalaman)
            ->withQueryString();
    }
}