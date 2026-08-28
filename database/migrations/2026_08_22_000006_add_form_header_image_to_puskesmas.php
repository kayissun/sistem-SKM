<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puskesmas', function (Blueprint $table) {
            if (! Schema::hasColumn('puskesmas', 'form_header_image')) {
                $table->string('form_header_image')->nullable()->after('no_telepon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('puskesmas', function (Blueprint $table) {
            if (Schema::hasColumn('puskesmas', 'form_header_image')) {
                $table->dropColumn('form_header_image');
            }
        });
    }
};
