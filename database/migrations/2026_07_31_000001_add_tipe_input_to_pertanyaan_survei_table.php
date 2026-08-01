<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            // 'skala'  = dinilai 1-4 (tampil sebagai radio/tombol atau dropdown), ikut dihitung kalau terkait unsur
            // 'teks'   = jawaban bebas/masukan tertulis, tidak bisa dikaitkan ke unsur (tidak numerik)
            $table->enum('tipe_input', ['skala', 'teks'])->default('skala')->after('teks_pertanyaan');

            // hanya relevan kalau tipe_input = 'skala'
            $table->enum('gaya_tampilan', ['radio', 'dropdown'])->nullable()->after('tipe_input');

            // label kustom untuk tiap level skala 1-4, contoh: "Tidak Pernah".."Selalu".
            // Kalau dibiarkan kosong, tampil sebagai angka biasa (1, 2, 3, 4).
            $table->string('label_skala_1')->nullable()->after('gaya_tampilan');
            $table->string('label_skala_2')->nullable()->after('label_skala_1');
            $table->string('label_skala_3')->nullable()->after('label_skala_2');
            $table->string('label_skala_4')->nullable()->after('label_skala_3');
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_input', 'gaya_tampilan',
                'label_skala_1', 'label_skala_2', 'label_skala_3', 'label_skala_4',
            ]);
        });
    }
};
