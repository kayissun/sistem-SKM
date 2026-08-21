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
        Schema::table('rekap_ikm', function (Blueprint $table) {
            $table->string('mutu_akhir', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rekap_ikm', function (Blueprint $table) {
            $table->string('mutu_akhir', 100)->nullable()->change();
        });
    }
};
