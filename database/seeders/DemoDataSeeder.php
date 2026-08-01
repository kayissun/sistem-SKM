<?php

namespace Database\Seeders;

use App\Models\PeriodeSurvei;
use App\Models\PertanyaanSurvei;
use App\Models\Puskesmas;
use App\Models\UnitLayanan;
use App\Models\UnsurPelayanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // akun dinkes (superadmin)
        $dinkes = User::firstOrCreate(
            ['email' => 'dinkes@example.test'],
            [
                'name' => 'Admin Dinas Kesehatan',
                'password' => Hash::make('password'),
                'puskesmas_id' => null,
            ]
        );
        $dinkes->assignRole('dinkes');

        // contoh 1 puskesmas
        $puskesmas = Puskesmas::firstOrCreate(
            ['slug' => 'puskesmas-contoh'],
            [
                'nama' => 'Puskesmas Contoh',
                'jenis' => 'puskesmas',
                'alamat' => 'Jl. Contoh No. 1',
                'is_active' => true,
            ]
        );

        // pertanyaan baseline dari 9 unsur wajib, dengan label skala kustom (sesuai preset resmi
        // per unsur) supaya langsung kelihatan hasilnya saat dites, bukan cuma angka 1-4 polos.
        // Catatan: unit yang dibuat lewat form dinkes (PuskesmasController) SENGAJA mulai kosong,
        // ini cuma buat kebutuhan demo/testing lewat seeder.
        $labelPerKode = [
            'U1' => ['Tidak Sesuai', 'Kurang Sesuai', 'Sesuai', 'Sangat Sesuai'],
            'U2' => ['Tidak Mudah', 'Kurang Mudah', 'Mudah', 'Sangat Mudah'],
            'U3' => ['Tidak Cepat', 'Kurang Cepat', 'Cepat', 'Sangat Cepat'],
            'U4' => ['Sangat Mahal', 'Cukup Mahal', 'Murah', 'Sangat Murah'],
            'U5' => ['Buruk', 'Cukup', 'Baik', 'Sangat Baik'],
            'U6' => ['Tidak Kompeten/Tidak Mampu', 'Kurang Kompeten/Kurang Mampu', 'Kompeten/Mampu', 'Sangat Kompeten/Sangat Mampu'],
            'U7' => ['Tidak Sopan/Tidak Ramah', 'Kurang Sopan/Kurang Ramah', 'Sopan/Ramah', 'Sangat Sopan/Sangat Ramah'],
            'U8' => ['Tidak Ada', 'Ada Tapi Tidak Berfungsi', 'Berfungsi Kurang Maksimal', 'Dikelola Dengan Baik'],
            'U9' => ['Tidak Ada', 'Ada Tapi Tidak Berfungsi', 'Berfungsi Kurang Maksimal', 'Dikelola Dengan Baik'],
        ];

        foreach (UnsurPelayanan::aktif()->get() as $unsur) {
            $label = $labelPerKode[$unsur->kode] ?? ['1', '2', '3', '4'];

            PertanyaanSurvei::firstOrCreate(
                ['puskesmas_id' => $puskesmas->id, 'unsur_pelayanan_id' => $unsur->id],
                [
                    'teks_pertanyaan' => $unsur->pertanyaan,
                    'tipe_input' => 'skala',
                    'gaya_tampilan' => 'radio',
                    'label_skala_1' => $label[0],
                    'label_skala_2' => $label[1],
                    'label_skala_3' => $label[2],
                    'label_skala_4' => $label[3],
                    'urutan' => $unsur->urutan,
                    'is_active' => true,
                ]
            );
        }

        // contoh pertanyaan tambahan tipe teks bebas, di luar 9 unsur wajib
        PertanyaanSurvei::firstOrCreate(
            ['puskesmas_id' => $puskesmas->id, 'teks_pertanyaan' => 'Ada saran atau masukan lain untuk kami?'],
            [
                'unsur_pelayanan_id' => null,
                'tipe_input' => 'teks',
                'urutan' => 10,
                'is_active' => true,
            ]
        );

        // contoh unit layanan supaya dropdown di form survei tidak kosong saat dites
        foreach (['Poli Umum', 'Poli Gigi', 'UGD'] as $nama) {
            UnitLayanan::firstOrCreate(
                ['puskesmas_id' => $puskesmas->id, 'nama' => $nama],
                ['is_active' => true]
            );
        }

        // akun admin untuk puskesmas tsb
        $admin = User::firstOrCreate(
            ['email' => 'admin.puskesmas@example.test'],
            [
                'name' => 'Admin Puskesmas Contoh',
                'password' => Hash::make('password'),
                'puskesmas_id' => $puskesmas->id,
            ]
        );
        $admin->assignRole('admin-puskesmas');

        // periode survei aktif
        PeriodeSurvei::firstOrCreate(
            ['nama' => 'Triwulan III 2026'],
            [
                'tanggal_mulai' => now()->startOfQuarter(),
                'tanggal_selesai' => now()->endOfQuarter(),
                'is_active' => true,
            ]
        );
    }
}
