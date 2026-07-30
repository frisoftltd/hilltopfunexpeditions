<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the notification SendAbandonedBookingReminders fires to a
 * traveler who started checkout (picked a gateway/amount) but never
 * submitted the proof/confirmation form, leaving their TourBooking at
 * status 0. Mirrors the seeding pattern already used for
 * BOOKING_RESERVED_CONFIRMED - written directly to the DB, idempotent
 * on `act`.
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

        $now = now();

        $row = [
            'name' => 'Booking Abandoned Reminder',
            'act' => 'BOOKING_ABANDONED_REMINDER',
            'subj' => 'Complete your booking for {{tour_title}}',
            'email_body' => '<p>You started booking <strong>{{tour_title}}</strong> but haven\'t finished yet.</p>'
                . '<p><a href="{{booking_url}}">Click here to complete your booking</a> before it expires.</p>',
            'email_status' => 1,
            'sms_body' => 'You started booking {{tour_title}} but haven\'t finished yet. Complete it here: {{booking_url}}',
            'sms_status' => 0,
            'shortcodes' => json_encode(['tour_title' => 'Tour package title', 'booking_url' => 'Link to resume booking']),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (DB::table('notification_templates')->where('act', $row['act'])->exists()) {
            return;
        }

        $filteredRow = array_intersect_key($row, array_flip($existingColumns));
        DB::table('notification_templates')->insert($filteredRow);
    }

    public function down(): void
    {
        DB::table('notification_templates')->where('act', 'BOOKING_ABANDONED_REMINDER')->delete();
    }
};
