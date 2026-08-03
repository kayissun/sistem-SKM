<?php

namespace App\Support;

class OpsiDataDiri
{
    /**
     * Kategori usia sesuai kategori Kementerian Kesehatan RI.
     */
    public static function usia(): array
    {
        return [
            'Balita (0-5 tahun)',
            'Kanak-kanak (5-11 tahun)',
            'Remaja Awal (12-16 tahun)',
            'Remaja Akhir (17-25 tahun)',
            'Dewasa Awal (26-35 tahun)',
            'Dewasa Akhir (36-45 tahun)',
            'Lansia Awal (46-55 tahun)',
            'Lansia Akhir (56-65 tahun ke atas)',
        ];
    }

    public static function pendidikan(): array
    {
        return ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3'];
    }

    public static function pekerjaan(): array
    {
        return [
            'PNS', 'WIRAUSAHA', 'PEDAGANG', 'POLRI',
            'SWASTA', 'PETANI', 'PELAJAR', 'LAINNYA',
        ];
    }
}
