<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            if (! Schema::hasColumn('pertanyaan_survei', 'layout_mode')) {
                $table->string('layout_mode')->default('default')->after('gaya_tampilan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            if (Schema::hasColumn('pertanyaan_survei', 'layout_mode')) {
                $table->dropColumn('layout_mode');
            }
        });
    }
};
