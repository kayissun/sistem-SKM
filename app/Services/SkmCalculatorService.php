<?php

namespace App\Services;

use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Models\UnsurPelayanan;
use Illuminate\Support\Collection;

class SkmCalculatorService
{
    /**
     * Hitung rekap SKM untuk satu puskesmas pada satu periode.
     * Rumus mengikuti Permenpan RB No 14 Tahun 2017.
     */
    public function hitung(Puskesmas $puskesmas, PeriodeSurvei $periode): array
    {
        $unsurAktif = UnsurPelayanan::aktif()->get();

        $jumlahResponden = $puskesmas->surveiJawaban()
            ->where('periode_survei_id', $periode->id)
            ->count();

        $hasilPerUnsur = [];
        $totalIndeksSkm = 0;

        foreach ($unsurAktif as $unsur) {
            // rata-rata nilai untuk unsur ini, khusus jawaban milik puskesmas+periode terkait
            $totalNilai = $unsur->surveiJawabanDetail()
                ->whereHas('surveiJawaban', function ($q) use ($puskesmas, $periode) {
                    $q->where('puskesmas_id', $puskesmas->id)
                        ->where('periode_survei_id', $periode->id);
                })
                ->sum('nilai');

            $nrr = $jumlahResponden > 0 ? $totalNilai / $jumlahResponden : 0;
            $nrrSkala100 = $nrr * 25;
            $nrrTertimbang = $unsurAktif->count() > 0 ? $nrr * (1 / $unsurAktif->count()) : 0;

            $hasilPerUnsur[$unsur->kode] = [
                'pertanyaan' => $unsur->pertanyaan,
                'total_nilai' => $totalNilai,
                'nrr' => round($nrr, 2),
                'nrr_skala_100' => round($nrrSkala100, 2),
                'kategori' => $this->kategoriMutu($nrrSkala100),
                'nrr_tertimbang' => round($nrrTertimbang, 4),
            ];

            $totalIndeksSkm += $nrrTertimbang;
        }

        $nilaiAkhirSkm = $totalIndeksSkm * 25;

        return [
            'jumlah_responden' => $jumlahResponden,
            'per_unsur' => $hasilPerUnsur,
            'total_indeks_skm' => round($totalIndeksSkm, 4),
            'nilai_akhir_skm' => round($nilaiAkhirSkm, 2),
            'mutu_akhir' => $this->kategoriMutu($nilaiAkhirSkm),
        ];
    }

    /**
     * Rekap gabungan seluruh puskesmas untuk satu periode (khusus dinkes).
     */
    public function hitungGabungan(PeriodeSurvei $periode): Collection
    {
        return Puskesmas::where('is_active', true)
            ->get()
            ->map(function (Puskesmas $puskesmas) use ($periode) {
                $hasil = $this->hitung($puskesmas, $periode);

                return [
                    'puskesmas_id' => $puskesmas->id,
                    'puskesmas' => $puskesmas->nama,
                    'jumlah_responden' => $hasil['jumlah_responden'],
                    'nilai_akhir_skm' => $hasil['nilai_akhir_skm'],
                    'mutu_akhir' => $hasil['mutu_akhir'],
                ];
            });
    }

    private function kategoriMutu(float $nilaiSkala100): string
    {
        return match (true) {
            $nilaiSkala100 >= 88.31 => 'A (Sangat Baik)',
            $nilaiSkala100 >= 76.61 => 'B (Baik)',
            $nilaiSkala100 >= 65.00 => 'C (Kurang Baik)',
            default => 'D (Tidak Baik)',
        };
    }
}
