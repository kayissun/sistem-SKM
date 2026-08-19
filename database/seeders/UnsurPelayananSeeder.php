<?php

namespace Database\Seeders;

use App\Models\UnsurPelayanan;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Support\PresetLabelSkala;
use Illuminate\Database\Seeder;

class UnsurPelayananSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definisikan 9 Unsur Baku & Teks Pertanyaan Default-nya
        $skmDinkesData = [
            [
                'kode' => 'U1',
                'nama_unsur' => 'Persyaratan',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kesesuaian persyaratan yang harus dipenuhi untuk mendapatkan pelayanan?'
            ],
            [
                'kode' => 'U2',
                'nama_unsur' => 'Sistem, Mekanisme, dan Prosedur',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kemudahan prosedur pelayanan di unit ini?'
            ],
            [
                'kode' => 'U3',
                'nama_unsur' => 'Waktu Penyelesaian',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan?'
            ],
            [
                'kode' => 'U4',
                'nama_unsur' => 'Biaya/Tarif',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif pelayanan yang diberikan?'
            ],
            [
                'kode' => 'U5',
                'nama_unsur' => 'Produk Spesifikasi Jenis Pelayanan',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan yang diberikan dengan standar pelayanan yang ditetapkan?'
            ],
            [
                'kode' => 'U6',
                'nama_unsur' => 'Kompetensi Pelaksana',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kompetensi dan kemampuan petugas dalam pelayanan?'
            ],
            [
                'kode' => 'U7',
                'nama_unsur' => 'Perilaku Pelaksana',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kesopanan dan keramahan petugas dalam memberikan pelayanan?'
            ],
            [
                'kode' => 'U8',
                'nama_unsur' => 'Penanganan Pengaduan, Saran dan Masukan',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang penanganan pengaduan, saran, dan masukan yang diberikan?'
            ],
            [
                'kode' => 'U9',
                'nama_unsur' => 'Sarana dan Prasarana',
                'pertanyaan_dinkes' => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana yang tersedia untuk pelayanan?'
            ],
        ];

        // 2. Ambil SEMUA unit / Puskesmas / RSU yang terdaftar
        $semuaPuskesmas = Puskesmas::all();

        // 3. Loop untuk membuat Unsur & memasukkan Pertanyaan Default ke SETIAP Unit
        foreach ($skmDinkesData as $index => $item) {
            // A. Buat Master Unsur Pelayanan (Hanya simpan kode & nama_unsur)
            $unsur = UnsurPelayanan::create([
                'kode'       => $item['kode'],
                'nama_unsur' => $item['nama_unsur'],
            ]);

            // B. Generate Pertanyaan ke SETIAP Puskesmas/RSU/Dinkes yang ada
            $preset = PresetLabelSkala::daftar()[strtolower($item['kode'])] ?? null;
            $labelSkala = $preset['label'] ?? ['1', '2', '3', '4'];

            foreach ($semuaPuskesmas as $pusk) {
                PertanyaanSurvei::create([
                    'puskesmas_id'       => $pusk->id,
                    'unsur_pelayanan_id' => $unsur->id,
                    'teks_pertanyaan'    => $item['pertanyaan_dinkes'],
                    'tipe_input'         => 'skala',
                    'gaya_tampilan'      => 'radio',
                    'label_skala_1'      => $labelSkala[0],
                    'label_skala_2'      => $labelSkala[1],
                    'label_skala_3'      => $labelSkala[2],
                    'label_skala_4'      => $labelSkala[3],
                    'urutan'             => $index + 1,
                    'is_active'          => true,
                ]);
            }
        }
    }
}