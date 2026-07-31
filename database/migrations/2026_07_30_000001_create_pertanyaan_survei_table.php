<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan_survei', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();

            // null = pertanyaan tambahan di luar 9 unsur wajib (tidak dihitung ke nilai SKM resmi)
            $table->foreignId('unsur_pelayanan_id')->nullable()->constrained('unsur_pelayanan')->nullOnDelete();

            $table->string('teks_pertanyaan');
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['puskesmas_id', 'is_active']);

            // 1 unit cuma boleh punya 1 pertanyaan aktif per unsur baku (cegah double-mapping unsur yang sama)
            $table->unique(['puskesmas_id', 'unsur_pelayanan_id'], 'satu_pertanyaan_per_unsur_per_unit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_survei');
    }
};
