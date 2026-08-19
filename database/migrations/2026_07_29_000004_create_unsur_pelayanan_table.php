<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unsur_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10); // U1, U2, ... U9
            $table->string('nama_unsur');   // Contoh: Persyaratan Pelayanan
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->boolean('is_active')->default(true); // dikelola dinkes secara global
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unsur_pelayanan');
    }
};
