<?php

namespace App\Services;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawabanDetail;
use App\Models\UnsurPelayanan;
use Illuminate\Support\Collection;

class SkmCalculatorService
{
    /**
     * Hitung rekap SKM untuk satu puskesmas pada satu periode.
     *
     * Setiap puskesmas boleh punya pertanyaan sendiri (teks berbeda-beda, bahkan jumlahnya
     * lebih dari 9), tapi nilai SKM resmi tetap dihitung berdasarkan 9 unsur wajib
     * (Permenpan RB 14/2017). Pertanyaan yang tidak dikaitkan ke unsur mana pun
     * ("pertanyaan tambahan") ditampilkan terpisah, tidak masuk ke rumus SKM.
     */
    public function hitung(Puskesmas $puskesmas, PeriodeSurvei $periode): array
    {
        $unsurAktif = UnsurPelayanan::aktif()->get();
        $jumlahResponden = $puskesmas->surveiJawaban()
            ->where('periode_survei_id', $periode->id)
            ->count();

        $hasilPerUnsur = [];
        $totalIndeksSkm = 0;
        $unsurBelumTerpetakan = [];

        foreach ($unsurAktif as $unsur) {
            // semua pertanyaan aktif milik puskesmas ini yang dikaitkan ke unsur ini
            // (biasanya 1, tapi puskesmas boleh pecah jadi lebih dari 1 pertanyaan per unsur)
            $pertanyaanUnsurIni = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)
                ->where('unsur_pelayanan_id', $unsur->id)
                ->where('is_active', true)
                ->get();

            $jumlahPertanyaan = $pertanyaanUnsurIni->count();

            if ($jumlahPertanyaan === 0) {
                // puskesmas belum/tidak punya pertanyaan aktif untuk unsur wajib ini
                $unsurBelumTerpetakan[] = $unsur->kode . ' - ' . $unsur->pertanyaan;
            }

            $totalNilai = SurveiJawabanDetail::whereIn('pertanyaan_survei_id', $pertanyaanUnsurIni->pluck('id'))
                ->whereHas('surveiJawaban', function ($q) use ($puskesmas, $periode) {
                    $q->where('puskesmas_id', $puskesmas->id)
                        ->where('periode_survei_id', $periode->id);
                })
                ->sum('nilai');

            // penyebut = jumlah responden x jumlah pertanyaan yang mewakili unsur ini,
            // supaya NRR tetap merepresentasikan "rata-rata nilai per unsur", walau
            // unsur tsb diwakili lebih dari 1 pertanyaan
            $penyebut = $jumlahResponden * max($jumlahPertanyaan, 1);
            $nrr = $penyebut > 0 ? $totalNilai / $penyebut : 0;
            $nrrSkala100 = $nrr * 25;
            $nrrTertimbang = $unsurAktif->count() > 0 ? $nrr * (1 / $unsurAktif->count()) : 0;

            $hasilPerUnsur[$unsur->kode] = [
                'pertanyaan' => $unsur->pertanyaan,
                'jumlah_pertanyaan_unit' => $jumlahPertanyaan,
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
            'pertanyaan_tambahan' => $this->hitungPertanyaanTambahan($puskesmas, $periode),
            'unsur_belum_terpetakan' => $unsurBelumTerpetakan,
            'total_indeks_skm' => round($totalIndeksSkm, 4),
            'nilai_akhir_skm' => round($nilaiAkhirSkm, 2),
            'mutu_akhir' => $this->kategoriMutu($nilaiAkhirSkm),
        ];
    }

    /**
     * Rata-rata nilai untuk pertanyaan tambahan (di luar 9 unsur wajib), sekadar informasi
     * tambahan bagi puskesmas — tidak memengaruhi nilai akhir SKM resmi.
     */
    private function hitungPertanyaanTambahan(Puskesmas $puskesmas, PeriodeSurvei $periode): array
    {
        $pertanyaanTambahan = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)
            ->whereNull('unsur_pelayanan_id')
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        return $pertanyaanTambahan->map(function (PertanyaanSurvei $pertanyaan) use ($puskesmas, $periode) {
            $jawaban = SurveiJawabanDetail::where('pertanyaan_survei_id', $pertanyaan->id)
                ->whereHas('surveiJawaban', function ($q) use ($puskesmas, $periode) {
                    $q->where('puskesmas_id', $puskesmas->id)
                        ->where('periode_survei_id', $periode->id);
                });

            $jumlah = $jawaban->count();

            return [
                'id' => $pertanyaan->id,
                'teks_pertanyaan' => $pertanyaan->teks_pertanyaan,
                'tipe_input' => $pertanyaan->tipe_input,
                'jumlah_jawaban' => $jumlah,
                'rata_rata' => ($pertanyaan->tipe_input === 'skala' && $jumlah > 0)
                    ? round($jawaban->avg('nilai'), 2)
                    : null,
            ];
        })->all();
    }

    /**
     * Rekap gabungan seluruh puskesmas untuk satu periode (khusus dinkes).
     */
    public function hitungGabungan(PeriodeSurvei $periode): Collection
    {
        return Puskesmas::where('is_active', true)
            ->whereIn('jenis', ['puskesmas', 'rsu']) // SKM Dinas Kesehatan sendiri dilihat terpisah, tidak dicampur di rekap ini
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
