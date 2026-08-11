<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DataRespondenExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    /**
     * @param  array<string>  $kodeUnsur
     * @param  Collection  $baris  semua baris responden (TIDAK dipaginasi, beda dari tampilan web)
     * @param  array  $hasil  hasil SkmCalculatorService::hitung() untuk rekap di baris bawah
     */
    public function __construct(
        private readonly array $kodeUnsur,
        private readonly Collection $baris,
        private readonly array $hasil,
    ) {}

    public function array(): array
    {
        $rows = [];

        foreach ($this->baris as $b) {
            $baris = [$b['no'], $b['nama']];
            $jumlahKosong = 0;
            foreach ($this->kodeUnsur as $kode) {
                $nilai = $b['nilai'][$kode] ?? null;
                $baris[] = $nilai ?? '';
                if ($nilai === null) {
                    $jumlahKosong++;
                }
            }
            $baris[] = $jumlahKosong;
            $rows[] = $baris;
        }

        $kosong = array_fill(0, 3 + count($this->kodeUnsur), '');
        $rows[] = $kosong;

        $rows[] = array_merge(['', 'Σ Nilai / Unsur'], array_map(
            fn ($k) => $this->hasil['per_unsur'][$k]['total_nilai'] ?? 0,
            $this->kodeUnsur
        ));
        $rows[] = array_merge(['', 'NRR / Unsur'], array_map(
            fn ($k) => $this->hasil['per_unsur'][$k]['nrr'] ?? 0,
            $this->kodeUnsur
        ));
        $rows[] = array_merge(['', 'NRR Tertimbang / Unsur'], array_map(
            fn ($k) => $this->hasil['per_unsur'][$k]['nrr_tertimbang'] ?? 0,
            $this->kodeUnsur
        ));
        $rows[] = array_merge(['', 'Kategori per Unsur'], array_map(
            fn ($k) => explode(' ', $this->hasil['per_unsur'][$k]['kategori'] ?? '-')[0],
            $this->kodeUnsur
        ));

        $rows[] = $kosong;
        $rows[] = ['', 'IKM Unit Pelayanan', $this->hasil['nilai_akhir_skm']];
        $rows[] = ['', 'Mutu Pelayanan', $this->hasil['mutu_akhir']];

        return $rows;
    }

    public function headings(): array
    {
        return array_merge(['No. Res', 'Nama'], $this->kodeUnsur, ['Data Kosong']);
    }

    public function title(): string
    {
        return 'Data Responden';
    }
}
