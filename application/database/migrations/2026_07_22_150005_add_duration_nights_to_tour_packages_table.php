<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * day_nights is a free-text display label (e.g. "3 Days 2 Nights"), not a
     * number, so it can't drive a computed departure end_date. This column is
     * the structured trip length that actually does.
     */
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->unsignedInteger('duration_nights')->nullable()->after('day_nights');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('duration_nights');
        });
    }
};
