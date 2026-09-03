<?php

namespace App\Services;

use App\Models\ClusterResult;
use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
use App\Models\UnsurPelayanan;
use Phpml\Clustering\KMeans;

class ClusteringService
{
    public function __construct(
        private readonly SkmCalculatorService $skmService
    ) {}

    /**
     * Kelompokkan unit faskes berdasarkan kemiripan pola nilai 9 unsur pelayanan
     * memakai K-Means (jarak Euclidean antar vektor skala 0-100).
     *
     * @param  bool  $simpanKeDb  true HANYA untuk aksi eksplisit "generate"
     *                            (mis. tombol di dashboard). Membuka halaman /
     *                            export tidak boleh menulis DB, supaya riwayat
     *                            tren periode lampau tidak tertimpa hasil acak
     *                            K-Means yang berubah tiap eksekusi.
     */
    public function klasterPuskesmas(PeriodeSurvei $periode, int $jumlahKlaster = 4, bool $simpanKeDb = false): array
    {
        $unsurAktif = UnsurPelayanan::aktif()->get(['kode', 'nama_unsur']);
        $kodeUnsur = $unsurAktif->pluck('kode')->all();
        $namaUnsur = $unsurAktif->pluck('nama_unsur', 'kode')->all();

        $daftarUnit = Puskesmas::where('is_active', true)
            ->whereIn('jenis', ['puskesmas', 'rsu'])
            ->get();

        $sampel = [];
        $hasilPerUnit = [];
        $dikecualikan = collect();

        /**
         * STEP 1 - BUILD DATA VECTOR
         */
        foreach ($daftarUnit as $unit) {
            $hasil = $this->skmService->hitung($unit, $periode);

            if ($hasil['jumlah_responden'] === 0 || !empty($hasil['unsur_belum_terpetakan'])) {
                $dikecualikan->push([
                    'nama' => $unit->nama,
                    'alasan' => $hasil['jumlah_responden'] === 0
                        ? 'Belum ada responden'
                        : 'Unsur belum lengkap'
                ]);
                continue;
            }

            $vector = [];
            foreach ($kodeUnsur as $kode) {
                $vector[] = $hasil['per_unsur'][$kode]['nrr_skala_100'] ?? 0;
            }

            $sampel[$unit->id] = $vector;
            $hasilPerUnit[$unit->id] = $hasil;
        }

        if (empty($sampel)) {
            return [
                'kelompok' => collect(),
                'insight' => [],
                'dikecualikan' => $dikecualikan,
                'kodeUnsur' => $kodeUnsur,
                'namaUnsur' => $namaUnsur,
                'jumlahKlaster' => 0,
                'jumlahSampel' => 0,
                'peringatanKualitas' => null,
            ];
        }

        /**
         * STEP 2 - AUTO ADJUST K + CLUSTERING
         */
        $jumlahKlaster = min($jumlahKlaster, count($sampel));
        $peringatanKualitas = count($sampel) < 6
            ? 'Hasil ini bersifat indikatif karena hanya ' . count($sampel) . ' unit yang memiliki data lengkap. Pertimbangkan validasi manual sebelum menetapkan kebijakan.'
            : null;

        $kmeans = new KMeans($jumlahKlaster);
        $hasilCluster = $kmeans->cluster($sampel);

        // Klaster kosong bisa terjadi (centroid yang tidak menarik satu pun
        // anggota) — buang sebelum dihitung centroid & rata-ratanya.
        $kelompok = collect($hasilCluster)
            ->filter(fn ($anggota) => !empty($anggota))
            ->map(function ($anggota) use ($hasilPerUnit, $kodeUnsur) {

                $ids = array_keys($anggota);

                /**
                 * CENTROID — rata-rata profil 9 unsur anggota kelompok ini
                 */
                $centroid = array_fill_keys($kodeUnsur, 0);

                foreach ($ids as $id) {
                    foreach ($kodeUnsur as $kode) {
                        $nrr = $hasilPerUnit[$id]['per_unsur'][$kode]['nrr'] 
                            ?? round(($hasilPerUnit[$id]['per_unsur'][$kode]['nrr_skala_100'] ?? 0) / 25, 3);
                        $centroid[$kode] += $nrr;
                    }
                }

                foreach ($centroid as $k => $v) {
                    $centroid[$k] = round($v / count($ids), 3);
                }

                /**
                 * ANGGOTA DETAIL
                 */
                $anggotaDetail = collect($ids)->map(function ($id) use ($hasilPerUnit, $kodeUnsur) {
                    $perUnsur = [];
                    foreach ($kodeUnsur as $kode) {
                        $nrr = $hasilPerUnit[$id]['per_unsur'][$kode]['nrr'] 
                            ?? round(($hasilPerUnit[$id]['per_unsur'][$kode]['nrr_skala_100'] ?? 0) / 25, 3);
                        $perUnsur[$kode] = $nrr;
                    }

                    return [
                        'id' => $id,
                        'nama' => $hasilPerUnit[$id]['puskesmas'],
                        'nilai_akhir' => $hasilPerUnit[$id]['nilai_akhir_skm'],
                        'mutu' => $hasilPerUnit[$id]['mutu_akhir'],
                        'per_unsur' => $perUnsur,
                    ];
                })->sortByDesc('nilai_akhir')->values();

                return [
                    'anggota' => $anggotaDetail,
                    'centroid' => $centroid,
                    'rata_rata_skor' => round((float) $anggotaDetail->avg('nilai_akhir'), 2),
                ];
            })
            ->values();

        /**
         * STEP 3 - LABELING BERDASARKAN PERFORMA
         *
         * Urutan output K-Means itu ACAK (tergantung inisialisasi centroid),
         * jadi label deskriptif wajib diberikan SETELAH kelompok diurutkan
         * dari rata-rata skor tertinggi ke terendah — bukan mengikuti index
         * algoritma. Tanpa ini, kelompok bernilai rendah bisa dapat label
         * "Sangat Baik".
         */
        $kelompok = $kelompok->sortByDesc('rata_rata_skor')->values();

        $labels = $this->labelKelompok($kelompok->count());

        $kelompok = $kelompok->map(function ($k, $i) use ($labels) {
            $k['label'] = $labels[$i] ?? 'Kelompok ' . ($i + 1);

            return $k;
        });

        /**
         * STEP 4 - INSIGHT OTOMATIS
         */
        $insight = $kelompok->map(function ($k) use ($namaUnsur) {

            $lemah = collect($k['centroid'])
                ->sort()
                ->keys()
                ->first();
            $namaLemah = $namaUnsur[$lemah] ?? $lemah;

            return [
                'cluster' => $k['label'],
                'rata_rata' => $k['rata_rata_skor'],
                'isu_utama' => $lemah,
                'isu_utama_nama' => $namaLemah,
                'kesimpulan' =>
                    "Kelompok {$k['label']} rata-rata paling lemah di {$lemah} ({$namaLemah})."
            ];
        });

        /**
         * STEP 5 - SIMPAN KE DB HANYA SAAT DIMINTA EKSPLISIT
         */
        if ($simpanKeDb) {
            foreach ($kelompok as $k) {
                foreach ($k['anggota'] as $unit) {
                    ClusterResult::updateOrCreate(
                        [
                            'puskesmas_id' => $unit['id'],
                            'periode' => $periode->id,
                        ],
                        [
                            'cluster' => $k['label'],
                            'cluster_nama' => $k['label'],
                            'nilai_rata2' => $unit['nilai_akhir'],
                            'rekomendasi' => $this->generateRekomendasi($k),
                        ]
                    );
                }
            }
        }

        /**
         * FINAL RETURN
         */
        return [
            'kelompok' => $kelompok,
            'insight' => $insight,
            'dikecualikan' => $dikecualikan,
            'kodeUnsur' => $kodeUnsur,
            'namaUnsur' => $namaUnsur,
            'jumlahKlaster' => $kelompok->count(),
            'jumlahSampel' => count($sampel),
            'peringatanKualitas' => $peringatanKualitas,
        ];
    }

    /**
     * Label urut dari performa TERBAIK (index 0) ke terendah.
     * Pemanggil wajib mengurutkan kelompok desc sebelum memakai ini.
     */
    private function labelKelompok(int $jumlah): array
    {
        return match ($jumlah) {
            1 => ['Seluruh Unit'],
            2 => ['Performa Tinggi', 'Perlu Perhatian'],
            3 => ['Baik', 'Sedang', 'Rendah'],
            4 => ['Sangat Baik', 'Baik', 'Sedang', 'Buruk'],
            default => array_map(fn ($i) => 'Kelompok ' . ($i + 1), range(0, $jumlah - 1)),
        };
    }

    private function generateRekomendasi($cluster): string
    {
        $lemah = collect($cluster['centroid'])
            ->sort()
            ->keys()
            ->first();

        return "Prioritaskan perbaikan pada unsur {$lemah} untuk meningkatkan kualitas layanan.";
    }
}
