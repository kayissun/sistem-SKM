<?php

namespace App\Exports;

use App\Exports\Concerns\StyledSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DataRespondenExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithEvents
{
    use StyledSheet;

    /**
     * Nomor baris rekap yang berisi nilai desimal (diisi saat array() dibangun,
     * dipakai AfterSheet untuk memberi format angka).
     *
     * @var array<int, int>
     */
    private array $barisDesimal = [];

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
            $baris = [
                $b['no'],
                $b['nama'],
                $b['unit_dinilai'],
                $b['no_hp'],
                $b['umur'] ?? '',
                $b['usia_kategori'] ?? '',
                $b['pendidikan'] ?? '',
                $b['pekerjaan'] ?? '',
            ];
            foreach ($this->kodeUnsur as $kode) {
                $baris[] = $b['nilai'][$kode] ?? '';
            }
            $rows[] = $baris;
        }

        // +1 baris kosong, lalu 4 baris rekap per unsur
        $kosong = array_fill(0, 8 + count($this->kodeUnsur), '');
        $rows[] = $kosong;

        $rows[] = array_merge(['', 'Σ Nilai / Unsur', '', '', '', '', '', ''], array_map(
            fn ($k) => $this->hasil['per_unsur'][$k]['total_nilai'] ?? 0,
            $this->kodeUnsur
        ));

        $this->barisDesimal[] = count($rows);
        $rows[] = array_merge(['', 'NRR / Unsur', '', '', '', '', '', ''], array_map(
            fn ($k) => $this->hasil['per_unsur'][$k]['nrr'] ?? 0,
            $this->kodeUnsur
        ));

        $this->barisDesimal[] = count($rows);
        $rows[] = array_merge(['', 'NRR Tertimbang / Unsur', '', '', '', '', '', ''], array_map(
            fn ($k) => $this->hasil['per_unsur'][$k]['nrr_tertimbang'] ?? 0,
            $this->kodeUnsur
        ));

        $rows[] = array_merge(['', 'Kategori per Unsur', '', '', '', '', '', ''], array_map(
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
        return array_merge(
            ['No. Res', 'Nama', 'Unit yang Dinilai', 'No. HP/WA', 'Umur', 'Kategori Usia', 'Pendidikan', 'Pekerjaan'],
            $this->kodeUnsur
        );
    }

    public function title(): string
    {
        return 'Data Responden';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $barisAkhir = max(2, $sheet->getHighestRow());
                $kolomUnsurAkhir = 8 + count($this->kodeUnsur);

                $formatAngka = [];
                foreach ($this->barisDesimal as $noBaris) {
                    // NRR & NRR tertimbang per unsur: 3 desimal
                    $formatAngka["I{$noBaris}:{$this->hurufKolom($kolomUnsurAkhir)}{$noBaris}"] = '0.000';
                }

                // Nilai IKM unit di baris rekap bawah: 3 desimal juga (kolom C)
                if ($barisAkhir >= 2) {
                    $barisIkm = $barisAkhir - 1;
                    $formatAngka["C{$barisIkm}:C{$barisIkm}"] = '0.000';
                }

                $this->terapkanGayaTabel($sheet, kolomWrap: null, formatAngka: $formatAngka);

                // Baris rekap (Σ / NRR / IKM / Mutu) dicetak tebal pada labelnya
                $rekapMulai = $barisAkhir - 5;
                if ($rekapMulai > 1) {
                    $sheet->getStyle("B{$rekapMulai}:B{$barisAkhir}")->getFont()->setBold(true);
                }
            },
        ];
    }
}
