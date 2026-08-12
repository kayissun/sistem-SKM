<?php

namespace App\Services;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\SurveiJawabanDetail;
use App\Models\UnitLayanan;
use App\Models\UnsurPelayanan;
use App\Support\OpsiDataDiri;
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
     *
     * Kalau $unitLayanan diisi, perhitungan cuma mencakup jawaban dari poli/layanan itu saja
     * (dipakai untuk laporan IKM per poli, lihat hitungPerUnitLayanan()).
     */
    public function hitung(Puskesmas $puskesmas, PeriodeSurvei $periode, ?UnitLayanan $unitLayanan = null): array
    {
        $unsurAktif = UnsurPelayanan::aktif()->get();

        $queryResponden = $puskesmas->surveiJawaban()->where('periode_survei_id', $periode->id);
        if ($unitLayanan) {
            $queryResponden->where('unit_layanan_id', $unitLayanan->id);
        }
        $jumlahResponden = $queryResponden->count();

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
                ->whereHas('surveiJawaban', function ($q) use ($puskesmas, $periode, $unitLayanan) {
                    $q->where('puskesmas_id', $puskesmas->id)
                        ->where('periode_survei_id', $periode->id);

                    if ($unitLayanan) {
                        $q->where('unit_layanan_id', $unitLayanan->id);
                    }
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
                'nrr' => round($nrr, 3),
                'nrr_skala_100' => round($nrrSkala100, 3),
                'kategori' => $this->kategoriMutu($nrrSkala100),
                'nrr_tertimbang' => round($nrrTertimbang, 3),
            ];

            $totalIndeksSkm += $nrrTertimbang;
        }

        $nilaiAkhirSkm = $totalIndeksSkm * 25;

        return [
            'puskesmas' => $puskesmas->nama,
            'unit_layanan_id' => $unitLayanan?->id,
            'unit_layanan_nama' => $unitLayanan?->nama,
            'jumlah_responden' => $jumlahResponden,
            'per_unsur' => $hasilPerUnsur,
            'pertanyaan_tambahan' => $unitLayanan ? [] : $this->hitungPertanyaanTambahan($puskesmas, $periode),
            'unsur_belum_terpetakan' => $unsurBelumTerpetakan,
            'total_indeks_skm' => round($totalIndeksSkm, 3),
            'nilai_akhir_skm' => round($nilaiAkhirSkm, 3),
            'mutu_akhir' => $this->kategoriMutu($nilaiAkhirSkm),
        ];
    }

    /**
     * Rekap IKM per poli/unit layanan (mis. "Poli Umum", "UGD"), masing-masing dihitung
     * terpisah dengan rumus yang sama persis seperti hitung(), cuma jawabannya difilter
     * berdasarkan unit_layanan_id yang dipilih responden saat mengisi survei.
     */
    public function hitungPerUnitLayanan(Puskesmas $puskesmas, PeriodeSurvei $periode): Collection
    {
        return $puskesmas->unitLayanan()
            ->aktif()
            ->get()
            ->map(fn (UnitLayanan $unit) => $this->hitung($puskesmas, $periode, $unit));
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
     * Data mentah per responden (belum diagregasi) — nilai tiap responden per unsur,
     * dipaginasi untuk tampilan web. Kalau ada pertanyaan yang mewakili unsur sama lebih
     * dari 1, nilainya dirata-rata per responden untuk unsur tsb.
     */
    public function dataPerResponden(
        Puskesmas $puskesmas,
        PeriodeSurvei $periode,
        ?UnitLayanan $unitLayanan = null,
        ?int $perHalaman = 30
    ): array {
        $kodeUnsur = UnsurPelayanan::aktif()->pluck('kode')->all();

        $petaPertanyaan = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)
            ->whereNotNull('unsur_pelayanan_id')
            ->with('unsurPelayanan:id,kode')
            ->get()
            ->mapWithKeys(fn (PertanyaanSurvei $p) => [$p->id => $p->unsurPelayanan->kode]);

        $query = $puskesmas->surveiJawaban()
            ->where('periode_survei_id', $periode->id)
            ->with([
                'detail' => fn ($q) => $q->whereIn('pertanyaan_survei_id', $petaPertanyaan->keys()),
                'unitLayanan:id,nama',
            ])
            ->orderBy('created_at');

        if ($unitLayanan) {
            $query->where('unit_layanan_id', $unitLayanan->id);
        }

        $halaman = $perHalaman ? $query->paginate($perHalaman)->withQueryString() : null;
        $koleksiJawaban = $halaman ? collect($halaman->items()) : $query->get();
        $nomorAwal = $halaman ? ($halaman->currentPage() - 1) * $halaman->perPage() : 0;

        $baris = $koleksiJawaban->values()->map(function ($jawaban, $i) use ($petaPertanyaan, $kodeUnsur, $nomorAwal) {
            $penampung = array_fill_keys($kodeUnsur, []);

            foreach ($jawaban->detail as $detail) {
                $kode = $petaPertanyaan[$detail->pertanyaan_survei_id] ?? null;
                if ($kode && $detail->nilai !== null) {
                    $penampung[$kode][] = $detail->nilai;
                }
            }

            $nilai = [];
            foreach ($kodeUnsur as $kode) {
                $nilai[$kode] = empty($penampung[$kode])
                    ? null
                    : round(array_sum($penampung[$kode]) / count($penampung[$kode]), 2);
            }

            return [
                'no' => $nomorAwal + $i + 1,
                'unit_dinilai' => $jawaban->unitLayanan->nama ?? '-',
                'no_hp' => $jawaban->no_hp,
                'pekerjaan' => $jawaban->pekerjaan,
                'pendidikan' => $jawaban->pendidikan,
                'umur' => $jawaban->umur,
                'usia_kategori' => OpsiDataDiri::kategoriUsia($jawaban->umur),
                'nama' => $jawaban->nama,
                'tanggal' => $jawaban->created_at,
                'nilai' => $nilai,
            ];
        });

        return ['kodeUnsur' => $kodeUnsur, 'baris' => $baris, 'halaman' => $halaman];
    }

    /**
     * Urutkan 9 unsur dari yang paling rendah nilainya (peringkat 1 = paling perlu
     * diperbaiki duluan) berdasarkan hasil hitung() yang sudah ada.
     */
    public function peringkatPrioritas(array $hasil): Collection
    {
        return collect($hasil['per_unsur'])
            ->map(fn ($u, $kode) => ['kode' => $kode, 'pertanyaan' => $u['pertanyaan'], 'nrr' => $u['nrr']])
            ->values()
            ->sortBy('nrr')
            ->values()
            ->map(fn ($item, $i) => $item + ['peringkat' => $i + 1]);
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

                $unsurTerlemah = $hasil['jumlah_responden'] > 0
                    ? $this->peringkatPrioritas($hasil)->first()
                    : null;

                return [
                    'puskesmas_id' => $puskesmas->id,
                    'puskesmas' => $puskesmas->nama,
                    'jumlah_responden' => $hasil['jumlah_responden'],
                    'per_unsur' => $hasil['per_unsur'],
                    'nilai_akhir_skm' => $hasil['nilai_akhir_skm'],
                    'mutu_akhir' => $hasil['mutu_akhir'],
                    'unsur_prioritas' => $unsurTerlemah ? "{$unsurTerlemah['kode']} - {$unsurTerlemah['pertanyaan']}" : '-',
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
