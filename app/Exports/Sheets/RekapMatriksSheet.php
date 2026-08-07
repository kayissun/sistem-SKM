<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapMatriksSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    /**
     * @param  array<string>  $kodeUnsur  contoh: ['U1', 'U2', ..., 'U9']
     */
    public function __construct(
        private readonly Collection $rekap,
        private readonly array $kodeUnsur,
    ) {}

    public function collection(): Collection
    {
        return $this->rekap;
    }

    public function headings(): array
    {
        return array_merge(['Unit'], $this->kodeUnsur, ['Nilai Akhir SKM', 'Mutu']);
    }

    public function map($baris): array
    {
        $nilaiPerUnsur = array_map(
            fn ($kode) => $baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0,
            $this->kodeUnsur
        );

        return array_merge([$baris['puskesmas']], $nilaiPerUnsur, [$baris['nilai_akhir_skm'], $baris['mutu_akhir']]);
    }

    public function title(): string
    {
        return 'Matriks per Unsur';
    }
}
