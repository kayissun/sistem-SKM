<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveiJawabanRequest;
use App\Models\Puskesmas;
use App\Services\SurveiSubmissionService;

class SubmitSurveiController extends Controller
{
    public function store(StoreSurveiJawabanRequest $request, Puskesmas $puskesmas, SurveiSubmissionService $submission)
    {
        $submission->simpan(
            $puskesmas,
            $request->validated(),
            $request->periodeAktif(),
            $request->daftarPertanyaanAktif()
        );

        return response()->json([
            'message' => 'Survei berhasil dikirim.',
        ], 201);
    }
}