<?php

namespace Database\Seeders;

use App\Models\UnsurPelayanan;
use Illuminate\Database\Seeder;

class UnsurPelayananSeeder extends Seeder
{
    public function run(): void
    {
        $unsur = [
            ['kode' => 'U1', 'pertanyaan' => 'Persyaratan pelayanan', 'urutan' => 1],
            ['kode' => 'U2', 'pertanyaan' => 'Sistem, mekanisme, dan prosedur pelayanan', 'urutan' => 2],
            ['kode' => 'U3', 'pertanyaan' => 'Waktu penyelesaian pelayanan', 'urutan' => 3],
            ['kode' => 'U4', 'pertanyaan' => 'Biaya/tarif pelayanan', 'urutan' => 4],
            ['kode' => 'U5', 'pertanyaan' => 'Produk spesifikasi jenis pelayanan', 'urutan' => 5],
            ['kode' => 'U6', 'pertanyaan' => 'Kompetensi pelaksana pelayanan', 'urutan' => 6],
            ['kode' => 'U7', 'pertanyaan' => 'Perilaku pelaksana pelayanan', 'urutan' => 7],
            ['kode' => 'U8', 'pertanyaan' => 'Penanganan pengaduan, saran, dan masukan', 'urutan' => 8],
            ['kode' => 'U9', 'pertanyaan' => 'Sarana dan prasarana pelayanan', 'urutan' => 9],
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
