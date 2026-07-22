<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "What's Not Included" - same {icon, feature} shape as the existing
     * features column ("What's Included"), stored separately rather than
     * folded into it.
     */
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->json('exclusions')->nullable()->after('features');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn('exclusions');
        });
    }
};
