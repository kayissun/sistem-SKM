<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SkmResultRequest;
use App\Models\Puskesmas;
use App\Repositories\Puskesmas\PuskesmasLaporanRepository;
use App\Services\SkmCalculatorService;

class SkmController extends Controller
{
    public function show(
        SkmResultRequest $request,
        Puskesmas $puskesmas,
        SkmCalculatorService $calculator,
        PuskesmasLaporanRepository $repository
    ) {
        abort_unless($puskesmas->is_active, 404, 'Instansi tidak ditemukan.');

        $periode = $repository->cariPeriode($request->integer('periode_survei_id') ?: null);
        abort_unless($periode, 404, 'Periode survei tidak ditemukan.');

        $unitLayanan = $repository->cariUnitLayanan(
            $puskesmas,
            $request->integer('unit_layanan_id') ?: null
        );

        if ($request->filled('unit_layanan_id') && ! $unitLayanan) {
            return response()->json([
                'message' => 'Unit layanan tidak ditemukan pada instansi ini.',
            ], 422);
        }

        return response()->json([
            'data' => $calculator->hitung($puskesmas, $periode, $unitLayanan),
            'meta' => [
                'periode_survei_id' => $periode->id,
                'unit_layanan_id' => $unitLayanan?->id,
            ],
        ]);
    }
}