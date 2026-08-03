<?php

namespace App\Http\Controllers;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawaban;
use App\Models\UnitLayanan;
use App\Support\OpsiDataDiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        $opsiUsia = OpsiDataDiri::usia();
        $opsiPendidikan = OpsiDataDiri::pendidikan();
        $opsiPekerjaan = OpsiDataDiri::pekerjaan();

        return view('survei.form', compact(
            'puskesmas', 'periodeAktif', 'daftarPertanyaan', 'daftarUnitLayanan',
            'opsiUsia', 'opsiPendidikan', 'opsiPekerjaan'
        ));
    }

    public function store(Request $request, Puskesmas $puskesmas)
    {
        if (! $puskesmas->is_active) {
            abort(404);
        }

        // periode aktif diambil ulang dari server, bukan dari input form,
        // supaya tidak bisa dimanipulasi/telat berubah kalau periode ganti saat isi form
        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();

        if (! $periodeAktif) {
            return back()->with('error', 'Survei sedang tidak dibuka untuk periode ini.');
        }

        $daftarPertanyaan = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)->aktif()->get();

        if ($daftarPertanyaan->isEmpty()) {
            return back()->with('error', 'Kuesioner belum tersedia untuk unit ini.');
        }

        $rules = [
            'unit_layanan_id' => [
                'nullable',
                Rule::exists('unit_layanan', 'id')->where('puskesmas_id', $puskesmas->id),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:25'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'usia_rentang' => ['required', Rule::in(OpsiDataDiri::usia())],
            'pendidikan' => ['required', Rule::in(OpsiDataDiri::pendidikan())],
            'pekerjaan' => ['required', Rule::in(OpsiDataDiri::pekerjaan())],
        ];

        foreach ($daftarPertanyaan as $pertanyaan) {
            $rules["jawaban.{$pertanyaan->id}"] = $pertanyaan->tipe_input === 'teks'
                ? ['nullable', 'string', 'max:2000']
                : ['required', 'integer', 'between:1,4'];
        }

        $data = $request->validate($rules, [
            'jawaban.*.required' => 'Semua pertanyaan skala wajib dinilai.',
        ]);

        DB::transaction(function () use ($data, $puskesmas, $periodeAktif, $daftarPertanyaan) {
            $jawaban = SurveiJawaban::create([
                'puskesmas_id' => $puskesmas->id,
                'periode_survei_id' => $periodeAktif->id,
                'unit_layanan_id' => $data['unit_layanan_id'] ?? null,
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'],
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'usia_rentang' => $data['usia_rentang'],
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
