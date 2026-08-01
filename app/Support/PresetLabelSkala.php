<?php

namespace App\Support;

class PresetLabelSkala
{
    /**
     * Setiap preset: [label level 1, level 2, level 3, level 4].
     * Key dipakai sebagai value <option>, dibaca lewat JS di form untuk auto-isi 4 input label.
     */
    public static function daftar(): array
    {
        return [
            'mutu' => [
                'nama' => 'Mutu umum (Buruk - Sangat Baik)',
                'label' => ['Buruk', 'Cukup', 'Baik', 'Sangat Baik'],
            ],
            'frekuensi' => [
                'nama' => 'Frekuensi (Tidak Pernah - Selalu)',
                'label' => ['Tidak Pernah', 'Kadang-kadang', 'Sering', 'Selalu'],
            ],
            'ketersediaan' => [
                'nama' => 'Ketersediaan sarana',
                'label' => ['Tidak Ada', 'Ada Tapi Tidak Berfungsi', 'Berfungsi Kurang Maksimal', 'Dikelola Dengan Baik'],
            ],
            'keramahan' => [
                'nama' => 'Keramahan petugas',
                'label' => ['Tidak Sopan/Tidak Ramah', 'Kurang Sopan/Kurang Ramah', 'Sopan/Ramah', 'Sangat Sopan/Sangat Ramah'],
            ],
            'kompetensi' => [
                'nama' => 'Kompetensi petugas',
                'label' => ['Tidak Kompeten/Tidak Mampu', 'Kurang Kompeten/Kurang Mampu', 'Kompeten/Mampu', 'Sangat Kompeten/Sangat Mampu'],
            ],
            'angka' => [
                'nama' => 'Angka biasa (1-4)',
                'label' => ['1', '2', '3', '4'],
            ],
        ];
    }
}
