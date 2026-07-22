<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Day-by-day breakdown: [{day, title, description}, ...]. A JSON column
     * rather than a new table - matches the existing destination_overview/
     * highlights/features convention, and itinerary days have no lifecycle
     * independent of their package (never queried or related on their own).
     */
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->json('itinerary')->nullable()->after('exclusions');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('itinerary');
        });
    }
};
