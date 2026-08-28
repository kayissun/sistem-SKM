<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puskesmas', function (Blueprint $table) {
            if (! Schema::hasColumn('puskesmas', 'form_pisah_halaman')) {
                $table->boolean('form_pisah_halaman')->default(false)->after('form_header_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('puskesmas', function (Blueprint $table) {
            if (Schema::hasColumn('puskesmas', 'form_pisah_halaman')) {
                $table->dropColumn('form_pisah_halaman');
            }
        });
    }
};
