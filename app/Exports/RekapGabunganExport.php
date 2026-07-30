<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapGabunganExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly Collection $rekap,
        private readonly string $namaPeriode,
    ) {}

    public function collection(): Collection
    {
        return $this->rekap;
    }

    public function headings(): array
    {
        return ['Unit', 'Jumlah Responden', 'Nilai Akhir SKM', 'Mutu'];
    }

    public function map($baris): array
    {
        return [
            $baris['puskesmas'],
            $baris['jumlah_responden'],
            $baris['nilai_akhir_skm'],
            $baris['mutu_akhir'],
        ];
    }

    public function title(): string
    {
        return substr('Rekap ' . $this->namaPeriode, 0, 31);
    }
}
