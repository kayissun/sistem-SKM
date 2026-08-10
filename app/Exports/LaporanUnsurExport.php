<?php

namespace App\Exports;

use App\Exports\Sheets\LaporanUnsurSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanUnsurExport implements WithMultipleSheets
{
    /**
     * @param  array  $hasilUtama  hasil SkmCalculatorService::hitung() untuk seluruh layanan unit
     * @param  Collection  $hasilPerPoli  hasil SkmCalculatorService::hitungPerUnitLayanan() — tiap
     *                                     elemen jadi 1 sheet/tab terpisah
     */
    public function __construct(
        private readonly array $hasilUtama,
        private readonly Collection $hasilPerPoli = new Collection,
    ) {}

    public function sheets(): array
    {
        $sheets = [
            new LaporanUnsurSheet($this->hasilUtama, 'Seluruh Layanan'),
        ];

        foreach ($this->hasilPerPoli as $poli) {
            // lewati poli yang belum ada respondennya sama sekali, biar file tidak penuh
            // sheet kosong untuk poli yang memang belum dipakai
            if ($poli['jumlah_responden'] === 0) {
                continue;
            }

            $sheets[] = new LaporanUnsurSheet($poli, $poli['unit_layanan_nama']);
        }

        return $sheets;
    }
}
