namespace App\Services;

use App\Models\Puskesmas;
use App\Models\RekapIkm;
use App\Models\SurveiJawaban; // Sesuaikan dengan nama model jawaban survei Anda

class KalkulatorIkmService
{
    /**
     * Hitung ulang dan simpan rekap IKM untuk Puskesmas & Periode tertentu
     */
    public static function perbaruiRekap($puskesmasId, $periodeSurveiId)
    {
        // 1. Hitung Rekap Seluruh Layanan (unit_layanan_id = null)
        self::prosesKalkulasiData($puskesmasId, $periodeSurveiId, null);

        // 2. Hitung Rekap per Poli / Unit Layanan
        // Ambil daftar unit layanan yang ada di puskesmas ini
        $unitLayananIds = SurveiJawaban::where('puskesmas_id', $puskesmasId)
            ->where('periode_survei_id', $periodeSurveiId)
            ->whereNotNull('unit_layanan_id')
            ->pluck('unit_layanan_id')
            ->unique();

        foreach ($unitLayananIds as $unitId) {
            self::prosesKalkulasiData($puskesmasId, $periodeSurveiId, $unitId);
        }
    }

    private static function prosesKalkulasiData($puskesmasId, $periodeSurveiId, $unitLayananId = null)
    {
        // GANTI LOGIK KALKULASI DI BAWAH SESUAI DENGAN RUMUS SKM/IKM LAMA ANDA
        
        $query = SurveiJawaban::where('puskesmas_id', $puskesmasId)
            ->where('periode_survei_id', $periodeSurveiId);

        if ($unitLayananId) {
            $query->where('unit_layanan_id', $unitLayananId);
        }

        $jumlahResponden = $query->count();

        if ($jumlahResponden === 0) {
            return;
        }

        // --- CONTOH PERHITUNGAN (PANGGIL/PASTE RUMUS IKM LAMA ANDA DI SINI) ---
        // Contoh sederhana penampung unsur U1 - U9
        $perUnsur = []; 
        /* 
           Jalankan rumus perhitungan NRR (Nilai Rata-Rata) per Unsur Anda di sini.
           Contoh struktur array $perUnsur:
           [
              'U1' => ['nrr_skala_100' => 82.50],
              'U2' => ['nrr_skala_100' => 88.00],
              ...
           ]
        */

        $nilaiAkhirSkm = 85.50; // Hasil rumus akhir SKM
        $mutuAkhir = 'B';       // Kategori Mutu (Sangat Baik / Baik, dll)

        // Simpan / Update ke tabel rekap_ikm (Upsert)
        RekapIkm::updateOrCreate(
            [
                'puskesmas_id' => $puskesmasId,
                'periode_survei_id' => $periodeSurveiId,
                'unit_layanan_id' => $unitLayananId,
            ],
            [
                'jumlah_responden' => $jumlahResponden,
                'nilai_akhir_skm' => $nilaiAkhirSkm,
                'mutu_akhir' => $mutuAkhir,
                'per_unsur' => $perUnsur,
            ]
        );
    }
}