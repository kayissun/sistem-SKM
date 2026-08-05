<?php

namespace App\Services;

use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Models\UnsurPelayanan;
use Illuminate\Support\Collection;
use Phpml\Clustering\KMeans;

class ClusteringService
{
    public function __construct(private readonly SkmCalculatorService $skmService) {}

    /**
     * Kelompokkan unit (puskesmas/RSU) berdasarkan kemiripan profil nilai 9 unsur pelayanan
     * (bukan cuma dari 1 angka rata-rata, tapi dari bentuk/pola ke-9 nilainya).
     *
     * Catatan desain: semua 9 fitur sudah sama-sama berskala 0-100 (nilai interval konversi),
     * jadi sengaja tidak distandardisasi (z-score) dulu sebelum clustering — beda dengan
     * dataset yang unitnya campur-campur (misal umur vs pendapatan), di sini semua fitur
     * memang sepadan/comparable secara langsung.
     *
     * @return array{kelompok: Collection, dikecualikan: Collection, kodeUnsur: array<string>}
     */
    public function klasterPuskesmas(PeriodeSurvei $periode, int $jumlahKlaster = 4): array
    {
        $kodeUnsur = UnsurPelayanan::aktif()->pluck('kode')->all();

        $daftarUnit = Puskesmas::where('is_active', true)
            ->whereIn('jenis', ['puskesmas', 'rsu'])
            ->get();

        $sampel = [];       // [puskesmas_id => [nilai_u1, nilai_u2, ...]]
        $hasilPerUnit = []; // [puskesmas_id => hasil lengkap dari SkmCalculatorService]
        $dikecualikan = collect();

        foreach ($daftarUnit as $puskesmas) {
            $hasil = $this->skmService->hitung($puskesmas, $periode);

            if ($hasil['jumlah_responden'] === 0 || ! empty($hasil['unsur_belum_terpetakan'])) {
                $dikecualikan->push([
                    'nama' => $puskesmas->nama,
                    'alasan' => $hasil['jumlah_responden'] === 0
                        ? 'Belum ada responden pada periode ini'
                        : 'Ada unsur wajib yang belum dipetakan ke pertanyaan',
                ]);

                continue;
            }

            $vektor = [];
            foreach ($kodeUnsur as $kode) {
                $vektor[] = $hasil['per_unsur'][$kode]['nrr_skala_100'] ?? 0;
            }

            $sampel[$puskesmas->id] = $vektor;
            $hasilPerUnit[$puskesmas->id] = $hasil;
        }

        if (empty($sampel)) {
            return ['kelompok' => collect(), 'dikecualikan' => $dikecualikan, 'kodeUnsur' => $kodeUnsur];
        }

        // kalau unit yang layak di-cluster lebih sedikit dari K yang diminta, turunkan otomatis
        $jumlahKlaster = min($jumlahKlaster, count($sampel));

        $kmeans = new KMeans($jumlahKlaster);
        $klasterMentah = $kmeans->cluster($sampel);

        $kelompok = collect($klasterMentah)->map(function ($anggota) use ($hasilPerUnit, $kodeUnsur) {
            $idAnggota = array_keys($anggota);

            // centroid = rata-rata nilai tiap unsur dari semua anggota klaster ini,
            // dipakai untuk grafik radar supaya bisa lihat "bentuk" khas tiap kelompok
            $centroid = array_fill_keys($kodeUnsur, 0.0);
            foreach ($idAnggota as $id) {
                foreach ($kodeUnsur as $kode) {
                    $centroid[$kode] += $hasilPerUnit[$id]['per_unsur'][$kode]['nrr_skala_100'];
                }
            }
            foreach ($centroid as $kode => $total) {
                $centroid[$kode] = round($total / count($idAnggota), 2);
            }

            return [
                'anggota' => collect($idAnggota)->map(fn ($id) => [
                    'nama' => $hasilPerUnit[$id]['puskesmas'],
                    'nilai_akhir_skm' => $hasilPerUnit[$id]['nilai_akhir_skm'],
                    'mutu_akhir' => $hasilPerUnit[$id]['mutu_akhir'],
                ])->sortByDesc('nilai_akhir_skm')->values(),
                'centroid' => $centroid,
                'rata_rata_skor' => round(collect($idAnggota)->avg(fn ($id) => $hasilPerUnit[$id]['nilai_akhir_skm']), 2),
            ];
        })
            ->sortByDesc('rata_rata_skor')
            ->values();

        $label = $this->labelKelompok($kelompok->count());
        $kelompok = $kelompok->map(function ($k, $i) use ($label) {
            $k['label'] = $label[$i] ?? ('Kelompok ' . ($i + 1));

            return $k;
        });

        return ['kelompok' => $kelompok, 'dikecualikan' => $dikecualikan, 'kodeUnsur' => $kodeUnsur];
    }

    /**
     * Label deskriptif berdasarkan peringkat rata-rata skor, dari yang tertinggi ke terendah.
     * Kalau jumlah klaster di luar 2-4, jatuh ke label generik "Kelompok N".
     */
    private function labelKelompok(int $jumlah): array
    {
        return match ($jumlah) {
            1 => ['Seluruh Unit'],
            2 => ['Performa Tinggi', 'Perlu Perhatian'],
            3 => ['Performa Tinggi', 'Performa Menengah', 'Perlu Perhatian'],
            4 => ['Performa Terbaik', 'Performa Baik', 'Performa Menengah', 'Perlu Perhatian Khusus'],
            default => array_map(fn ($i) => 'Kelompok ' . ($i + 1), range(0, $jumlah - 1)),
        };
    }
}
