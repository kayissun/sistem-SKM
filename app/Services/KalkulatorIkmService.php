<?php

namespace App\Services;

use App\Models\Puskesmas;
use App\Models\PeriodeSurvei;
use App\Models\RekapIkm;
use App\Models\SurveiJawaban;
use App\Models\UnitLayanan;
use Illuminate\Support\Facades\Log;

class KalkulatorIkmService
{
    /**
     * Hitung ulang dan simpan rekap IKM untuk Puskesmas & Periode tertentu.
     * Delegasi kalkulasi nyata ke SkmCalculatorService::hitung().
     *
     * Karena hitung() punya cache (kalau rekap_ikm sudah ada, langsung return),
     * kita hapus rekap lama dulu supaya hitung() selalu kalkulasi ulang dari
     * jawaban terbaru.
     */
    public static function perbaruiRekap($puskesmasId, $periodeSurveiId): void
    {
        try {
            $puskesmas = Puskesmas::find($puskesmasId);
            $periode = PeriodeSurvei::find($periodeSurveiId);

            if (! $puskesmas || ! $periode) {
                return;
            }

            // Hapus rekap lama supaya hitung() selalu kalkulasi dari awal
            RekapIkm::where('puskesmas_id', $puskesmasId)
                ->where('periode_survei_id', $periodeSurveiId)
                ->delete();

            $service = app(SkmCalculatorService::class);

            // 1. Hitung rekap seluruh layanan (unit_layanan_id = null)
            $service->hitung($puskesmas, $periode);

            // 2. Hitung rekap per poli / unit layanan
            $unitIds = SurveiJawaban::where('puskesmas_id', $puskesmasId)
                ->where('periode_survei_id', $periodeSurveiId)
                ->whereNotNull('unit_layanan_id')
                ->pluck('unit_layanan_id')
                ->unique();

            foreach ($unitIds as $unitId) {
                $unitLayanan = UnitLayanan::find($unitId);
                if ($unitLayanan) {
                    $service->hitung($puskesmas, $periode, $unitLayanan);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal menghitung rekap IKM', [
                'puskesmas_id' => $puskesmasId,
                'periode_id' => $periodeSurveiId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
