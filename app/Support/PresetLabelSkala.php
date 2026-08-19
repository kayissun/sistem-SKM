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
            'u1' => [
                'nama' => 'U1',
                'label' => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai'],
            ],
            'u2' => [
                'nama' => 'U2',
                'label' => ['Tidak Mudah', 'Kurang Mudah', 'Mudah', 'Sangat Mudah'],
            ],
            'u3' => [
                'nama' => 'U3',
                'label' => ['Tidak Cepat', 'Kurang Cepat', 'Cepat', 'Sangat Cepat'],
            ],
            'u4' => [
                'nama' => 'U4',
                'label' => ['Sangat Mahal', 'Cukup Mahal', 'Murah', 'Gratis'],
            ],
            'u5' => [
                'nama' => 'U5',
                'label' => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai'],
            ],
            'u6' => [
                'nama' => 'U6',
                'label' => ['Tidak Kompeten', 'Kurang Kompeten', 'Kompeten', 'Sangat Kompeten'],
            ],
            'u7' => [
                'nama' => 'U7',
                'label' => ['Tidak Sopan dan Ramah', 'Kurang Sopan dan Ramah', 'Sopan dan Ramah', 'Sangat Sopan dan Ramah'],
            ],
            'u8' => [
                'nama' => 'U8',
                'label' => ['Buruk', 'Cukup', 'Baik', 'Sangat Baik'],
            ],
            'u9' => [
                'nama' => 'U9',
                'label' => ['Tidak Ada', 'Ada Tetapi Tidak Berfungsi', 'Berfungsi Kurang Maksimal', 'Dikelola Dengan Baik'],
            ],
            'u10' => [
                'nama' => 'U10',
                'label' => ['Tidak Menjelaskan', 'Tidak Jelas', 'Jelas', 'Sangat Jelas'],
            ],
        ];
    }
}
