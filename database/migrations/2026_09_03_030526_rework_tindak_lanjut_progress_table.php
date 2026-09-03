<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rework tabel tindak_lanjut_progress sesuai alur baru:
     * Progress hanya menyimpan foto kegiatan + keterangan teks.
     * Nilai SKM triwulan berikutnya diambil otomatis dari data survei.
     */
    public function up(): void
    {
        // ── 1. Hapus kolom lama — urutan khusus karena MySQL ───────────────
        // MySQL tidak izinkan drop unique index yang masih diperlukan oleh FK.
        // Drop FK tindak_lanjut_id dulu, lalu drop unique, lalu drop kolom,
        // lalu buat ulang FK.
        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            $table->dropForeign(['tindak_lanjut_id']);
        });

        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_tl_progress');
            } catch (\Exception $e) {
                // constraint mungkin tidak ada
            }
        });

        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            $table->dropColumn(['triwulan_target', 'tahun_target', 'nilai_akhir', 'tercapai']);
        });

        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            // Buat ulang FK setelah kolom-kolom lama dihapus
            $table->foreign('tindak_lanjut_id')
                  ->references('id')
                  ->on('tindak_lanjuts')
                  ->cascadeOnDelete();
        });

        // ── 2. Tambah kolom baru ───────────────────────────────────────────
        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            $table->json('foto')
                  ->nullable()
                  ->comment('Foto dokumentasi progress kegiatan')
                  ->after('tindak_lanjut_id');
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('keterangan');
        });

        // ── 3. Hapus kolom 'bukti' dari tindak_lanjuts ────────────────────
        if (Schema::hasColumn('tindak_lanjuts', 'bukti')) {
            Schema::table('tindak_lanjuts', function (Blueprint $table) {
                $table->dropColumn('bukti');
            });
        }
    }

    public function down(): void
    {
        // Rollback: hapus kolom baru dari progress
        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['foto', 'created_by']);
        });

        // Rollback: kembalikan kolom lama
        Schema::table('tindak_lanjut_progress', function (Blueprint $table) {
            $table->unsignedTinyInteger('triwulan_target')->default(1);
            $table->unsignedSmallInteger('tahun_target')->default(2026);
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->boolean('tercapai')->default(false);
            $table->unique(['tindak_lanjut_id', 'triwulan_target', 'tahun_target'], 'unique_tl_progress');
        });

        // Rollback: kembalikan kolom bukti
        Schema::table('tindak_lanjuts', function (Blueprint $table) {
            $table->text('bukti')->nullable()->comment('Deskripsi bukti pendukung');
        });
    }
};
