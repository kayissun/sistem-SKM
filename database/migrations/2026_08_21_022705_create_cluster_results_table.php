<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cluster_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            $table->unsignedBigInteger('periode');
            $table->string('cluster');
            $table->string('cluster_nama');
            $table->float('nilai_rata2');
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cluster_results');
    }
};
