<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tours no longer have fixed departures - the tourist now picks their own
 * start date and party size at booking time instead of choosing from a
 * TourDeparture. price_category_id is untouched (still Foreigner/EAC/Rwandan,
 * now priced per package instead of per departure - see package_prices).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tour_departure_id');
        });

        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->renameColumn('user_proposal_date', 'start_date');
            $table->renameColumn('seat', 'party_size');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->renameColumn('start_date', 'user_proposal_date');
            $table->renameColumn('party_size', 'seat');
        });

        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->foreignId('tour_departure_id')->nullable()->after('tour_package_id')->constrained()->nullOnDelete();
        });
    }
};
