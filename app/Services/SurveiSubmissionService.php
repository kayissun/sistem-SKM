<?php

namespace App\Services;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\Responden;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SurveiSubmissionService
{
    public function simpan(
        Puskesmas $puskesmas,
        array $data,
        PeriodeSurvei $periode,
        Collection $daftarPertanyaan
    ): Responden {
        return DB::transaction(function () use ($data, $puskesmas, $periode, $daftarPertanyaan) {
            $responden = Responden::create([
                'puskesmas_id' => $puskesmas->id,
                'periode_survei_id' => $periode->id,
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

                if ($pertanyaan->tipe_input === 'teks' && blank($nilaiMentah)) {
                    continue;
                }

                $responden->detail()->create([
                    'pertanyaan_survei_id' => $pertanyaan->id,
                    'nilai' => $pertanyaan->tipe_input === 'teks' ? null : $nilaiMentah,
                    'jawaban_teks' => $pertanyaan->tipe_input === 'teks' ? $nilaiMentah : null,
                ]);
            }

            return $responden;
        });
    }
}