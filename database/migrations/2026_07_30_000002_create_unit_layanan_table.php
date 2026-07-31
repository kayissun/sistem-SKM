<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            $table->string('nama'); // contoh: "Poli Umum", "UGD", "Poli Gigi"
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['puskesmas_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_layanan');
    }
};
