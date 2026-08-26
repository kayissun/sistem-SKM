<?php

namespace App\Exports;

use App\Exports\Concerns\StyledSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class KlasterExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    use StyledSheet;

    private int $nomorBaris = 0;

    private Collection $baris;

    public function __construct(
        Collection $kelompok,
        private readonly Collection $insight,
        private readonly string $namaPeriode,
    ) {
        $this->baris = $kelompok->flatMap(function ($kelompok) {
            $insight = $this->insight->firstWhere('cluster', $kelompok['label']);

            return $kelompok['anggota']->map(fn ($anggota) => [
                'kelompok' => $kelompok['label'],
                'nama' => $anggota['nama'],
                'nilai' => $anggota['nilai_akhir'],
                'mutu' => $anggota['mutu'],
                'unsur_lemah' => $insight['isu_utama'] ?? '-',
                'rekomendasi' => $insight['kesimpulan'] ?? '-',
            ]);
        })->values();
    }

    public function collection(): Collection
    {
        return $this->baris;
    }

    public function headings(): array
    {
        return ['No', 'Periode', 'Kelompok', 'Unit', 'Nilai SKM', 'Mutu', 'Unsur Terlemah', 'Insight'];
    }

    public function map($baris): array
    {
        $this->nomorBaris++;

        return [
            $this->nomorBaris,
            $this->namaPeriode,
            $baris['kelompok'],
            $baris['nama'],
            $baris['nilai'],
            $baris['mutu'],
            $baris['unsur_lemah'],
            $baris['rekomendasi'],
        ];
    }

    public function title(): string
    {
        return 'Klaster Performa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $barisAkhir = max(2, $sheet->getHighestRow());

                $this->terapkanGayaTabel(
                    $sheet,
                    kolomWrap: 'G-H',
                    formatAngka: [
                        // Nilai SKM: 2 desimal seragam dengan PDF klaster
                        "E2:E{$barisAkhir}" => '0.00',
                    ],
                );
            },
        ];
    }
}
