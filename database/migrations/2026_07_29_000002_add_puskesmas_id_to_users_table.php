<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // null untuk role "dinkes" (bukan milik unit manapun)
            // diisi untuk role "admin-puskesmas" / "petugas"
            $table->foreignId('puskesmas_id')
                ->nullable()
                ->after('id')
                ->constrained('puskesmas')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true)->after('puskesmas_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('puskesmas_id');
            $table->dropColumn('is_active');
        });
    }
};
