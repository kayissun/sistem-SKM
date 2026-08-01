<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->unsignedTinyInteger('nilai')->nullable()->change(); // null kalau pertanyaannya tipe teks
            $table->text('jawaban_teks')->nullable()->after('nilai');
        });
    }

    public function down(): void
    {
        Schema::table('survei_jawaban_detail', function (Blueprint $table) {
            $table->dropColumn('jawaban_teks');
            $table->unsignedTinyInteger('nilai')->nullable(false)->change();
        });
    }
};
