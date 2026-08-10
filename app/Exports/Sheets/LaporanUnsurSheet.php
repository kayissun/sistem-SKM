<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanUnsurSheet implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly array $hasil,
        private readonly string $judulSheet,
    ) {}

    public function array(): array
    {
        $rows = [];

        foreach ($this->hasil['per_unsur'] as $kode => $unsur) {
            $rows[] = [
                $kode,
                $unsur['pertanyaan'],
                $unsur['total_nilai'],
                $unsur['nrr'],
                $unsur['nrr_skala_100'],
                $unsur['kategori'],
                $unsur['nrr_tertimbang'],
            ];
        }

        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = ['', 'Jumlah responden', $this->hasil['jumlah_responden'], '', '', '', ''];
        $rows[] = ['', 'Nilai akhir SKM', $this->hasil['nilai_akhir_skm'], '', '', '', ''];
        $rows[] = ['', 'Mutu akhir', $this->hasil['mutu_akhir'], '', '', '', ''];

        return $rows;
    }

    public function headings(): array
    {
        return ['Kode', 'Unsur Pelayanan', 'Total Nilai', 'NRR', 'NRR Skala 100', 'Kategori', 'NRR Tertimbang'];
    }

    public function title(): string
    {
        // nama sheet Excel dibatasi 31 karakter, dan tidak boleh ada karakter \ / ? * [ ]
        $bersih = preg_replace('/[\\\\\/\?\*\[\]]/', '-', $this->judulSheet);

        return substr($bersih, 0, 31);
    }
}
