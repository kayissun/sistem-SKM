<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survei_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            $table->foreignId('periode_survei_id')->constrained('periode_survei')->cascadeOnDelete();

            // data demografis responden, semua nullable karena isi survei publik & opsional
            $table->string('unit_layanan')->nullable(); // contoh: Poli Umum, UGD, dll (bebas isi/pilih)
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('usia_rentang')->nullable(); // contoh: "17-30", "31-45"
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();

            $table->timestamps();

            $table->index(['puskesmas_id', 'periode_survei_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei_jawaban');
    }
};
