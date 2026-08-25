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
    private int $nomorBaris = 0;

    /**
     * @param  array<string>  $kodeUnsur  contoh: ['U1', 'U2', ..., 'U9']
     */
    public function __construct(
        private readonly Collection $rekap,
        private readonly array $kodeUnsur,
        private readonly string $namaPeriode,
    ) {}

    public function collection(): Collection
    {
        return $this->rekap;
    }

    public function headings(): array
    {
        return array_merge(
            ['No', 'OPD/Unit Pelayanan Publik', 'Periode Pelaksanaan'],
            $this->kodeUnsur,
            ['IKM', 'Kategori', 'Jumlah Responden', 'Metode SKM', 'Unsur Prioritas Perbaikan', 'Rencana Tindak Lanjut']
        );
    }

    public function map($baris): array
    {
        $this->nomorBaris++;

        $nilaiPerUnsur = array_map(
            fn ($kode) => $baris['per_unsur'][$kode]['nrr_skala_100'] ?? 0,
            $this->kodeUnsur
        );

        return array_merge(
            [$this->nomorBaris, $baris['puskesmas'], $this->namaPeriode],
            $nilaiPerUnsur,
            [
                $baris['nilai_akhir_skm'],
                $baris['mutu_akhir'],
                $baris['jumlah_responden'],
                'SKM Online',
                implode("\n", $baris['unsur_prioritas']),
                implode("\n", $baris['rencana_tindak_lanjut']),
            ]
        );
    }

    public function title(): string
    {
        return substr('Rekap ' . $this->namaPeriode, 0, 31);
    }
}
