<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // reset cache permission spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // lingkup dinkes (superadmin)
            'manage-puskesmas',      // CRUD akun & data puskesmas/rsu
            'manage-master-unsur',   // CRUD master unsur pelayanan
            'manage-periode',        // CRUD periode survei
            'view-all-laporan',      // lihat rekap semua unit

            // lingkup admin-puskesmas
            'manage-unit-layanan',   // kelola unit layanan internal (opsional, versi lanjutan)
            'manage-petugas',        // kelola akun petugas di unitnya sendiri
            'view-laporan-sendiri',  // lihat rekap & laporan IKM unitnya sendiri
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $dinkes = Role::firstOrCreate(['name' => 'dinkes']);
        $dinkes->syncPermissions([
            'manage-puskesmas',
            'manage-master-unsur',
            'manage-periode',
            'view-all-laporan',
        ]);

        $adminPuskesmas = Role::firstOrCreate(['name' => 'admin-puskesmas']);
        $adminPuskesmas->syncPermissions([
            'manage-unit-layanan',
            'manage-petugas',
            'view-laporan-sendiri',
        ]);

        $dinkesSkm = Role::firstOrCreate(['name' => 'dinkes-skm']);
        $dinkesSkm->syncPermissions([
            'manage-unit-layanan',
            'manage-petugas',
            'view-laporan-sendiri',
        ]);

        // role turunan, opsional: petugas hanya bisa lihat laporan, tidak kelola akun
        $petugas = Role::firstOrCreate(['name' => 'petugas']);
        $petugas->syncPermissions([
            'view-laporan-sendiri',
        ]);
    }
}
