<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index gabungan 'satu_nilai_per_unsur' (survei_jawaban_id + unsur_pelayanan_id) ternyata
        // juga jadi index pendukung untuk foreign key survei_jawaban_id, bukan cuma unsur_pelayanan_id.
        // Jadi sebelum index gabungan itu dihapus, survei_jawaban_id butuh index sendiri dulu,
        // supaya FK-nya tetap punya index pendukung dan tidak diblokir MySQL (error 1553).
        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->index('survei_jawaban_id', 'survei_jawaban_detail_survei_jawaban_id_index');
        });

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->dropForeign(['unsur_pelayanan_id']);
            $table->dropUnique('satu_nilai_per_unsur');
            $table->dropColumn('unsur_pelayanan_id');
        });

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->foreignId('pertanyaan_survei_id')
                ->after('survei_jawaban_id')
                ->constrained('pertanyaan_survei')
                ->cascadeOnDelete();

            $table->unique(['survei_jawaban_id', 'pertanyaan_survei_id'], 'satu_nilai_per_pertanyaan');
        });
    }

    public function down(): void
    {
        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->dropForeign(['pertanyaan_survei_id']);
            $table->dropUnique('satu_nilai_per_pertanyaan');
            $table->dropColumn('pertanyaan_survei_id');
        });

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->foreignId('unsur_pelayanan_id')->nullable()->constrained('unsur_pelayanan')->cascadeOnDelete();
            $table->unique(['survei_jawaban_id', 'unsur_pelayanan_id'], 'satu_nilai_per_unsur');
        });

        // index bantu dari up() sengaja dibiarkan ada, tidak mengganggu dan lebih aman
        // daripada berisiko mengulang error 1553 yang sama saat rollback
    }
};
