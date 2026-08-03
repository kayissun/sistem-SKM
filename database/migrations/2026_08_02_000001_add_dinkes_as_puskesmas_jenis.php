<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'dinkes' sebagai jenis baru di tabel puskesmas, supaya Dinas Kesehatan bisa
        // punya "unit" sendiri dan otomatis reuse seluruh infrastruktur SKM yang sudah ada:
        // pertanyaan survei kustom, unit layanan, laporan, link & QR code publik.
        // Pakai raw SQL (bukan ->change()) karena Doctrine DBAL sering bermasalah dengan kolom ENUM MySQL.
        DB::statement("ALTER TABLE puskesmas MODIFY jenis ENUM('puskesmas','rsu','dinkes') NOT NULL DEFAULT 'puskesmas'");

        $dinkesPuskesmasId = DB::table('puskesmas')->where('slug', 'dinas-kesehatan')->value('id');

        if (! $dinkesPuskesmasId) {
            $dinkesPuskesmasId = DB::table('puskesmas')->insertGetId([
                'nama' => 'Dinas Kesehatan',
                'slug' => 'dinas-kesehatan',
                'jenis' => 'dinkes',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Best-effort untuk instalasi yang SUDAH punya data (bukan instalasi baru lewat migrate:fresh):
        // otomatis kaitkan user ber-role 'dinkes' yang sudah ada ke unit Dinas Kesehatan ini,
        // plus kasih role tambahan 'admin-puskesmas' supaya langsung bisa akses menu
        // Pertanyaan Survei/Unit Layanan/Laporan yang sudah ada (dipakai bersama admin-puskesmas biasa).
        $dinkesRoleId = DB::table('roles')->where('name', 'dinkes')->value('id');
        $adminPuskesmasRoleId = DB::table('roles')->where('name', 'admin-puskesmas')->value('id');

        if ($dinkesRoleId) {
            $userIds = DB::table('model_has_roles')
                ->where('role_id', $dinkesRoleId)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id');

            if ($userIds->isNotEmpty()) {
                DB::table('users')->whereIn('id', $userIds)->update(['puskesmas_id' => $dinkesPuskesmasId]);

                if ($adminPuskesmasRoleId) {
                    foreach ($userIds as $userId) {
                        $sudahAda = DB::table('model_has_roles')
                            ->where('role_id', $adminPuskesmasRoleId)
                            ->where('model_type', 'App\\Models\\User')
                            ->where('model_id', $userId)
                            ->exists();

                        if (! $sudahAda) {
                            DB::table('model_has_roles')->insert([
                                'role_id' => $adminPuskesmasRoleId,
                                'model_type' => 'App\\Models\\User',
                                'model_id' => $userId,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down(): void
    {
        DB::table('puskesmas')->where('slug', 'dinas-kesehatan')->delete();
        DB::statement("ALTER TABLE puskesmas MODIFY jenis ENUM('puskesmas','rsu') NOT NULL DEFAULT 'puskesmas'");
    }
};
