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

        // pertanyaan baseline dari 9 unsur wajib (kalau puskesmas ini baru dibuat lewat seeder,
        // bukan lewat form dinkes, jadi belum otomatis punya pertanyaan seperti di PuskesmasController)
        foreach (UnsurPelayanan::aktif()->get() as $unsur) {
            PertanyaanSurvei::firstOrCreate(
                ['puskesmas_id' => $puskesmas->id, 'unsur_pelayanan_id' => $unsur->id],
                [
                    'teks_pertanyaan' => $unsur->pertanyaan,
                    'urutan' => $unsur->urutan,
                    'is_active' => true,
                ]
            );
        }

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
