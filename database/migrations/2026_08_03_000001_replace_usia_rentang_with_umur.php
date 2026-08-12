<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survei_jawaban', function (Blueprint $table) {
            $table->dropColumn('usia_rentang');
            $table->unsignedTinyInteger('umur')->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('survei_jawaban', function (Blueprint $table) {
            $table->dropColumn('umur');
            $table->string('usia_rentang')->nullable()->after('no_hp');
        });
    }
};
