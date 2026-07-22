<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trip attributes: group size, guide language, age range, intensity.
     * intensity is a plain tinyint (1-5) validated at the app layer rather
     * than a MySQL enum, so the scale can change without an ALTER later.
     */
    public function up(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->unsignedInteger('group_size_min')->nullable()->after('duration_nights');
            $table->unsignedInteger('group_size_max')->nullable()->after('group_size_min');
            $table->string('guide_language')->nullable()->after('group_size_max');
            $table->unsignedInteger('age_range_min')->nullable()->after('guide_language');
            $table->unsignedInteger('age_range_max')->nullable()->after('age_range_min');
            $table->unsignedTinyInteger('intensity')->nullable()->after('age_range_max');
        });
    }

    public function down(): void
    {
        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropColumn(['group_size_min', 'group_size_max', 'guide_language', 'age_range_min', 'age_range_max', 'intensity']);
        });
    }
};
