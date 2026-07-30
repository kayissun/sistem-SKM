<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_survei', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // contoh: "Triwulan I 2026"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false); // dikelola dinkes, hanya 1 yang aktif idealnya
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_survei');
    }
};
