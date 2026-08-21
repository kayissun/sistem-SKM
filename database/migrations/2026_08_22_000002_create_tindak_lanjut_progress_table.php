<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tindak_lanjut_id')->constrained('tindak_lanjuts')->cascadeOnDelete();
            $table->unsignedTinyInteger('triwulan_target'); // triwulan yang menjadi target capaian
            $table->unsignedSmallInteger('tahun_target');
            $table->decimal('nilai_akhir', 5, 2)->nullable()->comment('Nilai unsur setelah perbaikan');
            $table->boolean('tercapai')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['tindak_lanjut_id', 'triwulan_target', 'tahun_target'], 'unique_tl_progress');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_progress');
    }
};
