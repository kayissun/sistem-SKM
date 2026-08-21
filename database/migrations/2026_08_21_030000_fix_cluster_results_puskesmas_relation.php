<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This migration was originally a fix for the column name,
     * but the create migration has been corrected directly.
     * Kept for safe migration ordering on existing installs.
     */
    public function up(): void
    {
        // no-op: column is already correct (puskesmas_id)
    }

    public function down(): void
    {
        // no-op
    }
};
