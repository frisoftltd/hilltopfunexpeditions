<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Two additions for guest checkout, both nullable/no-default so every
 * existing row and every logged-in-user booking just leaves them null:
 *
 * - phone: contact info entered on the booking widget by a guest. Lives
 *   here, not on the user account - identity is the email, phone is not
 *   enforced unique anywhere, and the same account can carry a different
 *   phone on a later booking.
 * - guest_signup: whether this booking came through guest checkout and, if
 *   so, whether the account behind it was newly created or already
 *   existed - purely so PaymentController::userDataUpdate() knows which
 *   notification to fire on payment success.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('user_id');
            $table->string('guest_signup', 20)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('tour_bookings', function (Blueprint $table) {
            $table->dropColumn(['phone', 'guest_signup']);
        });
    }
};
