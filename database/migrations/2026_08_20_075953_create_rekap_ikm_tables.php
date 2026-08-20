<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekapIkmTables extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_ikm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            $table->foreignId('periode_survei_id')->constrained('periode_survei')->cascadeOnDelete();
            // unit_layanan_id dibuat nullable (null = rekap seluruh layanan/gabungan)
            $table->foreignId('unit_layanan_id')->nullable()->constrained('unit_layanan')->cascadeOnDelete();
            
            $table->integer('jumlah_responden')->default(0);
            $table->decimal('nilai_akhir_skm', 5, 2)->default(0);
            $table->string('mutu_akhir', 50)->nullable();
            
            // Simpan detail nilai per unsur (U1, U2, dst) dalam bentuk JSON
            $table->json('per_unsur')->nullable(); 
            
            $table->timestamps();

            // Index gabungan
            $table->unique(['puskesmas_id', 'periode_survei_id', 'unit_layanan_id'], 'unique_rekap_ikm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_ikm');
    }
}