<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            if (! Schema::hasColumn('pertanyaan_survei', 'header_image')) {
                $table->string('header_image')->nullable()->after('layout_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pertanyaan_survei', function (Blueprint $table) {
            if (Schema::hasColumn('pertanyaan_survei', 'header_image')) {
                $table->dropColumn('header_image');
            }
        });
    }
};
