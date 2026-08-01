<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            // skala   = pilihan 4 tingkat dengan label bebas (dipakai untuk unsur wajib U1-U9)
            // dropdown = daftar pilihan bebas, tidak dihitung ke nilai SKM
            // teks    = jawaban bebas/isian, tidak dihitung ke nilai SKM
            $table->enum('tipe_field', ['skala', 'dropdown', 'teks'])
                ->default('skala')
                ->after('teks_pertanyaan');

            // untuk tipe 'skala': array 4 label, urutan index menentukan nilai 1-4
            // untuk tipe 'dropdown': array label pilihan (jumlah bebas)
            // untuk tipe 'teks': null, tidak dipakai
            $table->json('opsi')->nullable()->after('tipe_field');
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            $table->dropColumn(['tipe_field', 'opsi']);
        });
    }
};
