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

class DemoDataSeeder extends Seeder
{
    /**
     * Label skala kustom per kode unsur (dipakai untuk demo, supaya kelihatan hasilnya
     * saat dites, bukan cuma angka 1-4 polos). Unit yang dibuat lewat form dinkes/PuskesmasController
     * SENGAJA mulai kosong tanpa pertanyaan apa pun — ini cuma untuk kebutuhan seeder/testing.
     */
    private array $labelPerKode = [
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

    public function run(): void
    {
        // === Unit "Dinas Kesehatan" sendiri (jenis khusus, dibuat lewat migration) ===
        // Dinkes sekarang juga punya SKM sendiri: kuesioner, unit layanan, laporan, link & QR —
        // reuse penuh infrastruktur puskesmas, cukup dikaitkan lewat puskesmas_id + role tambahan.
        $dinasKesehatan = Puskesmas::firstOrCreate(
            ['slug' => 'dinas-kesehatan'],
            ['nama' => 'Dinas Kesehatan', 'jenis' => 'dinkes', 'is_active' => true]
        );

        $dinkes = User::firstOrCreate(
            ['email' => 'dinkes@example.test'],
            [
                'name' => 'Admin Dinas Kesehatan',
                'password' => Hash::make('password'),
                'puskesmas_id' => $dinasKesehatan->id,
            ]
        );
        // role 'dinkes' untuk akses panel pengawasan semua unit,
        // role 'admin-puskesmas' supaya bisa pakai menu Pertanyaan Survei/Unit Layanan/Laporan
        // yang sama seperti unit lain, tapi untuk kuesioner miliknya sendiri.
        $dinkes->syncRoles(['dinkes', 'admin-puskesmas']);
        $dinkes->update(['puskesmas_id' => $dinasKesehatan->id]);

        $this->seedPertanyaanBaseline($dinasKesehatan);

        foreach (['Loket Pengaduan', 'Layanan Perizinan Kesehatan', 'Layanan Administrasi Umum'] as $nama) {
            UnitLayanan::firstOrCreate(
                ['puskesmas_id' => $dinasKesehatan->id, 'nama' => $nama],
                ['is_active' => true]
            );
        }

        // === Contoh 1 puskesmas biasa ===
        $puskesmas = Puskesmas::firstOrCreate(
            ['slug' => 'puskesmas-contoh'],
            [
                'nama' => 'Puskesmas Contoh',
                'jenis' => 'puskesmas',
                'alamat' => 'Jl. Contoh No. 1',
                'is_active' => true,
            ]
        );

        $this->seedPertanyaanBaseline($puskesmas);

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

        // periode survei aktif (dipakai bersama oleh semua unit, termasuk Dinas Kesehatan)
        PeriodeSurvei::firstOrCreate(
            ['nama' => 'Triwulan III 2026'],
            [
                'tanggal_mulai' => now()->startOfQuarter(),
                'tanggal_selesai' => now()->endOfQuarter(),
                'is_active' => true,
            ]
        );
    }

    /**
     * Isi 9 pertanyaan baseline (dari 9 unsur wajib, dengan label skala kustom) + 1 pertanyaan
     * tambahan tipe teks bebas, untuk satu unit (dipakai baik untuk puskesmas maupun Dinas Kesehatan).
     */
    private function seedPertanyaanBaseline(Puskesmas $unit): void
    {
        foreach (UnsurPelayanan::aktif()->get() as $unsur) {
            $label = $this->labelPerKode[$unsur->kode] ?? ['1', '2', '3', '4'];

            PertanyaanSurvei::firstOrCreate(
                ['puskesmas_id' => $unit->id, 'unsur_pelayanan_id' => $unsur->id],
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

        PertanyaanSurvei::firstOrCreate(
            ['puskesmas_id' => $unit->id, 'teks_pertanyaan' => 'Ada saran atau masukan lain untuk kami?'],
            [
                'unsur_pelayanan_id' => null,
                'tipe_input' => 'teks',
                'urutan' => 10,
                'is_active' => true,
            ]
        );
    }
}
