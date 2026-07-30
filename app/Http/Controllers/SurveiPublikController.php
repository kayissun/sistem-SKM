<?php

namespace App\Http\Controllers;

use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawaban;
use App\Models\UnsurPelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveiPublikController extends Controller
{
    public function create(Puskesmas $puskesmas)
    {
        if (! $puskesmas->is_active) {
            abort(404);
        }

        $periodeAktif = PeriodeSurvei::where('is_active', true)->first();
        $unsurAktif = UnsurPelayanan::aktif()->get();

        return view('survei.form', compact('puskesmas', 'periodeAktif', 'unsurAktif'));
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

        $unsurAktif = UnsurPelayanan::aktif()->get();

        $rules = [
            'unit_layanan' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'usia_rentang' => ['nullable', 'string', 'max:50'],
            'pendidikan' => ['nullable', 'string', 'max:100'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
        ];

        foreach ($unsurAktif as $unsur) {
            $rules["jawaban.{$unsur->id}"] = ['required', 'integer', 'between:1,4'];
        }

        $data = $request->validate($rules, [
            'jawaban.*.required' => 'Semua unsur pelayanan wajib dinilai.',
        ]);

        DB::transaction(function () use ($data, $puskesmas, $periodeAktif) {
            $jawaban = SurveiJawaban::create([
                'puskesmas_id' => $puskesmas->id,
                'periode_survei_id' => $periodeAktif->id,
                'unit_layanan' => $data['unit_layanan'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'usia_rentang' => $data['usia_rentang'] ?? null,
                'pendidikan' => $data['pendidikan'] ?? null,
                'pekerjaan' => $data['pekerjaan'] ?? null,
            ]);

            foreach ($data['jawaban'] as $unsurId => $nilai) {
                $jawaban->detail()->create([
                    'unsur_pelayanan_id' => $unsurId,
                    'nilai' => $nilai,
                ]);
            }
        });

        return redirect()->route('survei.selesai', $puskesmas);
    }

    public function selesai(Puskesmas $puskesmas)
    {
        return view('survei.selesai', compact('puskesmas'));
    }
}
