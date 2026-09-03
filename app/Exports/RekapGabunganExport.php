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

class RekapGabunganExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    use StyledSheet;

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
            fn ($kode) => $baris['per_unsur'][$kode]['nrr'] ?? (isset($baris['per_unsur'][$kode]['nrr_skala_100']) ? round($baris['per_unsur'][$kode]['nrr_skala_100'] / 25, 3) : 0),
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $barisAkhir = max(2, $sheet->getHighestRow());

                // Struktur kolom: A No | B Unit | C Periode | D.. U1..U9 | IKM | Kategori |
                // Responden | Metode | Unsur Prioritas | Rencana Tindak Lanjut
                $kolomUnsurAwal = 4;
                $kolomUnsurAkhir = 3 + count($this->kodeUnsur);

                $this->terapkanGayaTabel(
                    $sheet,
                    kolomWrap: $this->hurufKolom($kolomUnsurAkhir + 5) . '-' . $this->hurufKolom($kolomUnsurAkhir + 6),
                    formatAngka: [
                        // NRR skala 100 per unsur & nilai IKM: 2 desimal seragam
                        "D2:{$this->hurufKolom($kolomUnsurAkhir)}{$barisAkhir}" => '0.00',
                        $this->hurufKolom($kolomUnsurAkhir + 1) . "2:{$this->hurufKolom($kolomUnsurAkhir + 1)}{$barisAkhir}" => '0.00',
                    ],
                );
            },
        ];
    }
}
