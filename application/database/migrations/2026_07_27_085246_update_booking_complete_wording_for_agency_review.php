<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * BOOKING_COMPLETE fires from PaymentController::userDataUpdate() the
 * moment payment clears - before the agency review workflow existed, that
 * was the same moment the booking was actually confirmed, so the template
 * said so ("Booking Confirmed..."). Now payment clearing and the agency
 * confirming the reservation are two separate steps (tour_bookings.
 * agency_status, reviewed via Agency\BookingController::approve()/
 * decline()), so this wording is no longer accurate - a paid booking can
 * still be declined. BOOKING_APPROVED_BY_AGENCY (seeded in
 * 2026_07_26_231620) already sends the real "confirmed" email once the
 * agency actually approves, so nothing new is needed there.
 *
 * Only touches email_body/subj/sms_body - none of the shortcodes
 * PaymentController passes changed (tour_title, price, tour_start, etc.),
 * so the shortcodes documentation column is left as-is. Written directly
 * to the DB, not the admin editor, per the same WYSIWYG-stripping
 * precaution as every other template change in this chain.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notification_templates')) {
            return;
        }

        $columns = DB::select(
            'SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?',
            ['notification_templates']
        );
        $existingColumns = array_column($columns, 'COLUMN_NAME');

        $update = [
            'subj' => 'Your booking for {{tour_title}} has been received',
            'email_body' => '<p>Your booking for <strong>{{tour_title}}</strong> has been received - payment confirmed, awaiting final confirmation from the tour operator.</p>'
                . '<p>You\'ll receive another email once it\'s reviewed.</p>'
                . '<p>Amount paid: {{currency_symbol}}{{price}}</p>'
                . '<p>Tour date: {{tour_start}}</p>',
            'sms_body' => 'Your booking for {{tour_title}} has been received - payment confirmed, awaiting tour operator confirmation.',
        ];

        $filteredUpdate = array_intersect_key($update, array_flip($existingColumns));
        if (empty($filteredUpdate)) {
            return;
        }

        DB::table('notification_templates')->where('act', 'BOOKING_COMPLETE')->update($filteredUpdate);
    }

    public function down(): void
    {
        // Not reversible to "says Confirmed prematurely" without
        // reintroducing the wording bug this fixes.
    }
};
