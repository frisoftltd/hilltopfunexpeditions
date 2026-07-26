<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * agency_status is the owning agency's own review of a booking - entirely
 * separate from `status` (the payment lifecycle: 0 awaiting payment,
 * 1 paid, 2 pending manual confirmation, 3 payment rejected). A booking can
 * be paid (status=1) and still awaiting agency review (agency_status=null);
 * declining doesn't touch `status` or trigger any refund logic - refunds
 * for an already-collected payment are handled manually outside the system.
 *
 * null = pending agency review (default for every existing + future row)
 * 1    = approved by agency
 * 2    = declined by agency
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->unsignedTinyInteger('agency_status')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropColumn('agency_status');
        });
    }
};
