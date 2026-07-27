<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional free-text reason an agency can give when declining a booking
 * (Agency\BookingController::decline()) - not required, mirrors the
 * existing deposits.admin_feedback pattern for a similar "reason for a
 * negative decision" field, just scoped to tour_bookings since this is
 * about the reservation itself, not the payment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->string('decline_reason', 1000)->nullable()->after('agency_status');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropColumn('decline_reason');
        });
    }
};
