<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survei_jawaban_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survei_jawaban_id')->constrained('survei_jawaban')->cascadeOnDelete();
            $table->foreignId('unsur_pelayanan_id')->constrained('unsur_pelayanan')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai'); // skala 1-4 sesuai Permenpan RB 14/2017
            $table->timestamps();

            $table->unique(['survei_jawaban_id', 'unsur_pelayanan_id'], 'satu_nilai_per_unsur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei_jawaban_detail');
    }
};
