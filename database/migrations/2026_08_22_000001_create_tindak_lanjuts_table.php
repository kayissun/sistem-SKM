<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            $table->foreignId('unsur_pelayanan_id')->constrained('unsur_pelayanan')->cascadeOnDelete();
            $table->unsignedTinyInteger('triwulan'); // 1, 2, 3, 4
            $table->unsignedSmallInteger('tahun');
            $table->decimal('nilai_kondisi', 5, 2)->nullable()->comment('Nilai unsur saat lemah');
            $table->text('tindakan_perbaikan')->comment('Rencana/tindakan perbaikan');
            $table->text('bukti')->nullable()->comment('Deskripsi bukti pendukung');
            $table->string('status', 20)->default('draft'); // draft, submitted, approved, rejected
            $table->text('catatan_dinkes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['puskesmas_id', 'unsur_pelayanan_id', 'triwulan', 'tahun'], 'unique_tl_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjuts');
    }
};
