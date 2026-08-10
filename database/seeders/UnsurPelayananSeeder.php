<?php

namespace Database\Seeders;

use App\Models\UnsurPelayanan;
use Illuminate\Database\Seeder;

class UnsurPelayananSeeder extends Seeder
{
    public function run(): void
    {
        $unsur = [
            ['kode' => 'U1', 'pertanyaan' => 'Persyaratan', 'urutan' => 1],
            ['kode' => 'U2', 'pertanyaan' => 'Sistem, Mekanisme, dan Prosedur', 'urutan' => 2],
            ['kode' => 'U3', 'pertanyaan' => 'Waktu Penyelesaian', 'urutan' => 3],
            ['kode' => 'U4', 'pertanyaan' => 'Biaya/Tarif', 'urutan' => 4],
            ['kode' => 'U5', 'pertanyaan' => 'Produk Spesifikasi jenis pelayanan', 'urutan' => 5],
            ['kode' => 'U6', 'pertanyaan' => 'Kompetensi Pelaksana', 'urutan' => 6],
            ['kode' => 'U7', 'pertanyaan' => 'Perilaku Pelaksana', 'urutan' => 7],
            ['kode' => 'U8', 'pertanyaan' => 'Penanganan Pengaduan, Saran, dan Masukan', 'urutan' => 8],
            ['kode' => 'U9', 'pertanyaan' => 'Sarana dan Prasarana', 'urutan' => 9],
        ];

        foreach ($unsur as $item) {
            UnsurPelayanan::firstOrCreate(
                ['kode' => $item['kode']],
                [
                    'pertanyaan' => $item['pertanyaan'],
                    'urutan' => $item['urutan'],
                    'is_active' => true,
                ]
            );
        }
    }
}
