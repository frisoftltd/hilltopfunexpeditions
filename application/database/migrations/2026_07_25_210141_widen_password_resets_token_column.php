<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The existing forgot-password flow stores a 6-digit code in this column.
 * The new guest-checkout set-password link needs a longer, higher-entropy
 * token (it's embedded directly in a URL rather than manually typed), so
 * this widens the column if it's currently narrower than that - a no-op if
 * it's already wide enough. Introspects the live schema rather than
 * assuming a width we can't otherwise confirm (no migration created this
 * table originally).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('password_resets') || !Schema::hasColumn('password_resets', 'token')) {
            return;
        }

        $column = DB::selectOne(
            'SELECT CHARACTER_MAXIMUM_LENGTH AS length, DATA_TYPE AS type
             FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
            ['password_resets', 'token']
        );

        if (!$column || $column->type !== 'varchar' || (int) $column->length >= 64) {
            return;
        }

        DB::statement('ALTER TABLE `password_resets` MODIFY `token` VARCHAR(64) NULL');
    }

    public function down(): void
    {
        // Widening a column is not meaningfully reversible (and shrinking it
        // back could truncate real tokens written under the new width).
    }
};
