<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Gaya tabel standar semua export Excel dinkes/puskesmas:
 * border tipis seluruh sel, header bold berlatar krem (senada tema PDF),
 * data rata-atas, wrap text opsional untuk kolom panjang,
 * dan format angka desimal per rentang kolom.
 *
 * Pemakaian: class export menambah `use WithEvents;` + trait ini, lalu
 * memanggil terapkanGayaTabel() dari event AfterSheet.
 */
trait StyledSheet
{
    /**
     * @param  string|null  $kolomWrap  rentang kolom yang perlu wrap text, contoh 'J-K' (tanpa nomor baris)
     * @param  array<string, string>  $formatAngka  peta "A2:A99" => kode format angka, contoh '0.00'
     */
    protected function terapkanGayaTabel($sheet, ?string $kolomWrap = null, array $formatAngka = []): void
    {
        $kolomTerakhir = $sheet->getHighestColumn();
        $barisTerakhir = $sheet->getHighestRow();

        // Border tipis seluruh tabel
        $sheet->getStyle("A1:{$kolomTerakhir}{$barisTerakhir}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Header: bold + latar krem senada tema PDF (#f7f0da)
        $sheet->getStyle("A1:{$kolomTerakhir}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'F7F0DA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Data rata-atas supaya baris multi-baris tetap rapi
        if ($barisTerakhir > 1) {
            $sheet->getStyle("A2:{$kolomTerakhir}{$barisTerakhir}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_TOP);
        }

        // Wrap text kolom panjang (rencana tindak lanjut, insight, dst.)
        if ($kolomWrap) {
            [$awal, $akhir] = array_map('trim', explode('-', $kolomWrap));
            $akhirBarisData = max(2, $barisTerakhir);

            $sheet->getStyle("{$awal}2:{$akhir}{$akhirBarisData}")
                ->getAlignment()
                ->setWrapText(true);
        }

        // Format angka desimal konsisten
        foreach ($formatAngka as $rentang => $format) {
            $sheet->getStyle($rentang)->getNumberFormat()->setFormatCode($format);
        }
    }

    /**
     * Huruf kolom Excel dari indeks angka (1 = A, 4 = D, ...).
     */
    protected function hurufKolom(int $indeks): string
    {
        return Coordinate::stringFromColumnIndex($indeks);
    }
}
