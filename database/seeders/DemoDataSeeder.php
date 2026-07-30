<?php

namespace Database\Seeders;

use App\Models\PeriodeSurvei;
use App\Models\Puskesmas;
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
