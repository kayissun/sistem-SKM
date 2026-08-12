<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveiJawabanRequest;
use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawaban;
use App\Models\UnitLayanan;
use App\Support\OpsiDataDiri;
use Illuminate\Support\Facades\DB;

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

    public function store(StoreSurveiJawabanRequest $request, Puskesmas $puskesmas)
    {
        // 3 prasyarat (unit aktif, periode aktif ada, kuesioner tidak kosong) sudah dicek
        // di StoreSurveiJawabanRequest::authorize(), begitu juga aturan validasi tiap field.
        $data = $request->validated();
        $periodeAktif = $request->periodeAktif();
        $daftarPertanyaan = $request->daftarPertanyaanAktif();

        DB::transaction(function () use ($data, $puskesmas, $periodeAktif, $daftarPertanyaan) {
            $jawaban = SurveiJawaban::create([
                'puskesmas_id' => $puskesmas->id,
                'periode_survei_id' => $periodeAktif->id,
                'unit_layanan_id' => $data['unit_layanan_id'] ?? null,
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'],
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'umur' => $data['umur'],
                'pendidikan' => $data['pendidikan'],
                'pekerjaan' => $data['pekerjaan'],
            ]);

            foreach ($daftarPertanyaan as $pertanyaan) {
                $nilaiMentah = $data['jawaban'][$pertanyaan->id] ?? null;

                if ($pertanyaan->tipe_input === 'teks') {
                    // lewati kalau responden tidak isi masukan teks (opsional)
                    if (blank($nilaiMentah)) {
                        continue;
                    }

                    $jawaban->detail()->create([
                        'pertanyaan_survei_id' => $pertanyaan->id,
                        'nilai' => null,
                        'jawaban_teks' => $nilaiMentah,
                    ]);
                } else {
                    $jawaban->detail()->create([
                        'pertanyaan_survei_id' => $pertanyaan->id,
                        'nilai' => $nilaiMentah,
                    ]);
                }
            }
        });

        return redirect()->route('survei.selesai', $puskesmas);
    }

    public function selesai(Puskesmas $puskesmas)
    {
        return view('survei.selesai', compact('puskesmas'));
    }
}
