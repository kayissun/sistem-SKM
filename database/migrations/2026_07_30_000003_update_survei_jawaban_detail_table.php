<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
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

            return;
        }

        $this->dropForeignIfExists('survei_jawaban_detail_unsur_pelayanan_id_foreign');

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            // The old composite unique key also supports the foreign key on
            // survei_jawaban_id, so create a dedicated replacement first.
            $table->index('survei_jawaban_id', 'survei_jawaban_detail_survei_jawaban_id_index');
        });

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->dropUnique('satu_nilai_per_unsur');
            $table->dropColumn('unsur_pelayanan_id');

            $table->foreignId('pertanyaan_survei_id')
                ->after('survei_jawaban_id')
                ->constrained('pertanyaan_survei')
                ->cascadeOnDelete();

            $table->unique(['survei_jawaban_id', 'pertanyaan_survei_id'], 'satu_nilai_per_pertanyaan');
        });
    }

    public function down(): void
    {
        $this->dropForeignIfExists('survei_jawaban_detail_pertanyaan_survei_id_foreign');

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->dropUnique('satu_nilai_per_pertanyaan');
            $table->dropColumn('pertanyaan_survei_id');

            $table->foreignId('unsur_pelayanan_id')->nullable()->constrained('unsur_pelayanan')->cascadeOnDelete();
            $table->unique(['survei_jawaban_id', 'unsur_pelayanan_id'], 'satu_nilai_per_unsur');
        });

        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->dropIndex('survei_jawaban_detail_survei_jawaban_id_index');
        });
    }

    private function dropForeignIfExists(string $foreignKeyName): void
    {
        foreach (Schema::getForeignKeys('survei_jawaban_detail') as $foreignKey) {
            if ($foreignKey['name'] === $foreignKeyName) {
                Schema::table('survei_jawaban_detail', function (Blueprint $table) use ($foreignKeyName) {
                    $table->dropForeign($foreignKeyName);
                });

                return;
            }
        }
    }
};
