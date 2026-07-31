<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survei_jawaban', function (Blueprint $table) {
            $table->dropColumn('unit_layanan');

            $table->foreignId('unit_layanan_id')
                ->nullable()
                ->after('periode_survei_id')
                ->constrained('unit_layanan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('survei_jawaban', function (Blueprint $table) {
            $table->dropForeign(['unit_layanan_id']);
            $table->dropColumn('unit_layanan_id');

            $table->string('unit_layanan')->nullable();
        });
    }
};
