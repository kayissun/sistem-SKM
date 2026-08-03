<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survei_jawaban', function (Blueprint $table) {
            $table->string('nama')->after('unit_layanan_id');
            $table->string('no_hp', 25)->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('survei_jawaban', function (Blueprint $table) {
            $table->dropColumn(['nama', 'no_hp']);
        });
    }
};
