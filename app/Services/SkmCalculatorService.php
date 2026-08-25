<?php

namespace App\Services;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\RekapIkm;
use App\Models\SurveiJawabanDetail;
use App\Models\TindakLanjut;
use App\Models\UnitLayanan;
use App\Models\UnsurPelayanan;
use App\Support\OpsiDataDiri;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SkmCalculatorService
{
    /**
     * Hitung rekap SKM untuk satu puskesmas pada satu periode.
     * Cek tabel `rekap_ikm` terlebih dahulu untuk pembacaan instan.
     */
    public function hitung(Puskesmas $puskesmas, PeriodeSurvei $periode, ?UnitLayanan $unitLayanan = null): array
    {
        // 1. Cek apakah data rekap sudah ada di tabel rekap_ikm
        $rekap = RekapIkm::where('puskesmas_id', $puskesmas->id)
            ->where('periode_survei_id', $periode->id)
            ->where('unit_layanan_id', $unitLayanan?->id)
            ->first();

        // 2. Jika sudah ada di tabel rekap, kembalikan hasil secara instan
        if ($rekap) {
            return [
                'puskesmas' => $puskesmas->nama,
                'unit_layanan_id' => $unitLayanan?->id,
                'unit_layanan_nama' => $unitLayanan?->nama,
                'jumlah_responden' => $rekap->jumlah_responden,
                'per_unsur' => $rekap->per_unsur['per_unsur'] ?? [],
                'pertanyaan_tambahan' => $unitLayanan ? [] : ($rekap->per_unsur['pertanyaan_tambahan'] ?? []),
                'unsur_belum_terpetakan' => $rekap->per_unsur['unsur_belum_terpetakan'] ?? [],
                'total_indeks_skm' => $rekap->per_unsur['total_indeks_skm'] ?? 0,
                'nilai_akhir_skm' => (float) $rekap->nilai_akhir_skm,
                'mutu_akhir' => $rekap->mutu_akhir,
            ];
        }

        // 3. Jika belum ada di rekap, lakukan kalkulasi dari data mentah & simpan otomatis ke rekap_ikm
        $hasil = $this->kalkulasiMentah($puskesmas, $periode, $unitLayanan);

        RekapIkm::updateOrCreate(
            [
                'puskesmas_id' => $puskesmas->id,
                'periode_survei_id' => $periode->id,
                'unit_layanan_id' => $unitLayanan?->id,
            ],
            [
                'jumlah_responden' => $hasil['jumlah_responden'],
                'nilai_akhir_skm' => $hasil['nilai_akhir_skm'],
                'mutu_akhir' => $hasil['mutu_akhir'],
                'per_unsur' => [
                    'per_unsur' => $hasil['per_unsur'],
                    'pertanyaan_tambahan' => $hasil['pertanyaan_tambahan'],
                    'unsur_belum_terpetakan' => $hasil['unsur_belum_terpetakan'],
                    'total_indeks_skm' => $hasil['total_indeks_skm'],
                ],
            ]
        );

        return $hasil;
    }

    /**
     * Kalkulasi fisik dari data mentah (dipanggil jika rekap_ikm belum terbentuk)
     */
    private function kalkulasiMentah(Puskesmas $puskesmas, PeriodeSurvei $periode, ?UnitLayanan $unitLayanan = null): array
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
            $pertanyaanUnsurIni = PertanyaanSurvei::where('puskesmas_id', $puskesmas->id)
                ->where('unsur_pelayanan_id', $unsur->id)
                ->where('is_active', true)
                ->get();

            $jumlahPertanyaan = $pertanyaanUnsurIni->count();

            if ($jumlahPertanyaan === 0) {
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
     * Rekap IKM per poli/unit layanan.
     */
    public function hitungPerUnitLayanan(Puskesmas $puskesmas, PeriodeSurvei $periode): Collection
    {
        return $puskesmas->unitLayanan()
            ->aktif()
            ->get()
            ->map(fn (UnitLayanan $unit) => $this->hitung($puskesmas, $periode, $unit));
    }

    /**
     * Pertanyaan Tambahan.
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
     * Data per Responden.
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

    public function peringkatPrioritas(array $hasil): Collection
    {
        return collect($hasil['per_unsur'] ?? [])
            ->map(function ($u, $kode) {
                // Pastikan pertanyaan diambil sebagai String meskipun berbentuk Array/Collection
                $pertanyaanTeks = is_array($u['pertanyaan'] ?? null)
                    ? ($u['pertanyaan'][0]['teks_pertanyaan'] ?? $u['pertanyaan'][0]['pertanyaan'] ?? '-')
                    : ($u['pertanyaan'] ?? '-');

                return [
                    'kode' => $kode,
                    'pertanyaan' => $pertanyaanTeks,
                    'nrr' => $u['nrr'] ?? 0,
                ];
            })
            ->values()
            ->sortBy('nrr')
            ->values()
            ->map(fn ($item, $i) => $item + ['peringkat' => $i + 1]);
    }

    /**
     * Rekap gabungan seluruh puskesmas (khusus dinkes).
     */
    public function hitungGabungan(PeriodeSurvei $periode): Collection
    {
        return Puskesmas::where('is_active', true)
            ->whereIn('jenis', ['dinkes', 'puskesmas', 'rsu'])
            ->get()
            ->map(function (Puskesmas $puskesmas) use ($periode) {
                $hasil = $this->hitung($puskesmas, $periode);

                // Tindak lanjut yang diajukan faskes ini (dipakai untuk kolom prioritas & rencana)
                $tindakLanjuts = TindakLanjut::where('puskesmas_id', $puskesmas->id)
                    ->with('unsurPelayanan')
                    ->orderByDesc('tahun')
                    ->orderByDesc('triwulan')
                    ->get();

                $prioritas = [];
                $rencana = [];
                foreach ($tindakLanjuts as $tl) {
                    $unsur = $tl->unsurPelayanan;
                    if (! $unsur) {
                        continue;
                    }
                    $prioritas[] = "{$unsur->kode} - {$unsur->nama_unsur}";
                    $rencana[] = $tl->tindakan_perbaikan;
                }

                return [
                    'puskesmas_id' => $puskesmas->id,
                    'puskesmas' => $puskesmas->nama,
                    'jumlah_responden' => $hasil['jumlah_responden'] ?? 0,
                    'per_unsur' => $hasil['per_unsur'] ?? [],
                    'nilai_akhir_skm' => $hasil['nilai_akhir_skm'] ?? 0,
                    'mutu_akhir' => $hasil['mutu_akhir'] ?? '-',
                    'unsur_prioritas' => $prioritas ?: ['-'],
                    'rencana_tindak_lanjut' => $rencana ?: ['-'],
                ];
            });
    }

    /**
     * Format Publikasi IKM.
     */
    public function publikasiIkm(Puskesmas $puskesmas, PeriodeSurvei $periode, ?UnitLayanan $unitLayanan = null): array
    {
        $hasil = $this->hitung($puskesmas, $periode, $unitLayanan);

        $query = $puskesmas->surveiJawaban()->where('periode_survei_id', $periode->id);
        if ($unitLayanan) {
            $query->where('unit_layanan_id', $unitLayanan->id);
        }

        $jumlahLaki = (clone $query)->where('jenis_kelamin', 'L')->count();
        $jumlahPerempuan = (clone $query)->where('jenis_kelamin', 'P')->count();

        $jumlahPerPendidikan = (clone $query)
            ->whereNotNull('pendidikan')
            ->select('pendidikan', DB::raw('count(*) as jumlah'))
            ->groupBy('pendidikan')
            ->pluck('jumlah', 'pendidikan');

        $pendidikan = [];
        foreach (OpsiDataDiri::pendidikan() as $kode) {
            $pendidikan[$kode] = $jumlahPerPendidikan[$kode] ?? 0;
        }

        return [
            'nilai_akhir_skm' => $hasil['nilai_akhir_skm'],
            'mutu_akhir' => $hasil['mutu_akhir'],
            'jumlah_responden' => $hasil['jumlah_responden'],
            'jumlah_laki' => $jumlahLaki,
            'jumlah_perempuan' => $jumlahPerPerempuan ?? $jumlahPerempuan,
            'pendidikan' => $pendidikan,
        ];
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