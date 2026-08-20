<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveiJawabanRequest;
use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\UnitLayanan;
use App\Services\KalkulatorIkmService; // <--- 1. IMPORT SERVICE DI SINI
use App\Services\SurveiSubmissionService;
use App\Support\OpsiDataDiri;

class SurveiPublikController extends Controller
{
    public function create(Puskesmas $puskesmas)
    {
        if (! $puskesmas->is_active) {
            abort(404);
        }

        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();
        $daftarPertanyaan = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)->aktif()->get();
        $daftarUnitLayanan = UnitLayanan::where('puskesmas_id', $puskesmas->id)->aktif()->get();

        $opsiPendidikan = OpsiDataDiri::pendidikan();
        $opsiPekerjaan = OpsiDataDiri::pekerjaan();

        return view('survei.form', compact(
            'puskesmas', 'periodeAktif', 'daftarPertanyaan', 'daftarUnitLayanan',
            'opsiPendidikan', 'opsiPekerjaan'
        ));
    }

    public function store(StoreSurveiJawabanRequest $request, Puskesmas $puskesmas, SurveiSubmissionService $submission)
    {
        // 3 prasyarat (unit aktif, periode aktif ada, kuesioner tidak kosong) sudah dicek
        // di StoreSurveiJawabanRequest::authorize(), begitu juga aturan validasi tiap field.
        $data = $request->validated();
        $periodeAktif = $request->periodeAktif();
        $daftarPertanyaan = $request->daftarPertanyaanAktif();

        // 1. Simpan jawaban survei responden
        $submission->simpan($puskesmas, $data, $periodeAktif, $daftarPertanyaan);

        // 2. TRIGGER REKAP AUTOMATIS DI SINI 🚀
        // Langsung perbarui tabel rekap_ikm untuk puskesmas & periode aktif ini
        KalkulatorIkmService::perbaruiRekap($puskesmas->id, $periodeAktif->id);

        return redirect()->route('survei.selesai', $puskesmas);
    }

    public function selesai(Puskesmas $puskesmas)
    {
        return view('survei.selesai', compact('puskesmas'));
    }
}