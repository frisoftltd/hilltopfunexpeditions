<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->foreignId('tour_departure_id')->nullable()->after('tour_package_id')->constrained()->nullOnDelete();
            $table->foreignId('price_category_id')->nullable()->after('tour_departure_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tour_departure_id');
            $table->dropConstrainedForeignId('price_category_id');
        });
    }
};
