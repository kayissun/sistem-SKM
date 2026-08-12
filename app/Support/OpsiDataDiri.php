<?php

namespace App\Support;

class OpsiDataDiri
{
    /**
     * Kategori usia sesuai kategori Kementerian Kesehatan RI, dihitung otomatis
     * dari angka umur (bukan lagi dropdown yang dipilih manual oleh responden).
     */
    public static function kategoriUsia(?int $umur): string
    {
        return match (true) {
            $umur === null => '-',
            $umur <= 5 => 'Balita (0-5 tahun)',
            $umur <= 11 => 'Kanak-kanak (5-11 tahun)',
            $umur <= 16 => 'Remaja Awal (12-16 tahun)',
            $umur <= 25 => 'Remaja Akhir (17-25 tahun)',
            $umur <= 35 => 'Dewasa Awal (26-35 tahun)',
            $umur <= 45 => 'Dewasa Akhir (36-45 tahun)',
            $umur <= 55 => 'Lansia Awal (46-55 tahun)',
            default => 'Lansia Akhir (56 tahun ke atas)',
        };
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
