<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the notification template User\BookingController::cancel() fires
 * at the owning agency when a traveler cancels their own booking. Mirrors
 * the seeding pattern already used for BOOKING_APPROVED_BY_AGENCY/
 * BOOKING_DECLINED (2026_07_26_231620) - written directly to the DB for
 * the same WYSIWYG-strips-<a href>-values reason, idempotent on `act`.
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
            'name' => 'Booking Cancelled by Traveler',
            'act' => 'BOOKING_CANCELLED_BY_TRAVELER',
            'subj' => 'A booking for {{tour_title}} has been cancelled',
            'email_body' => '<p>A traveler has cancelled their booking for <strong>{{tour_title}}</strong>.</p>'
                . '<p>If this booking had already been paid, the corresponding balance has been reversed on your account.</p>',
            'email_status' => 1,
            'sms_body' => 'A booking for {{tour_title}} has been cancelled by the traveler.',
            'sms_status' => 0,
            'shortcodes' => json_encode(['tour_title' => 'Tour package title']),
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
        DB::table('notification_templates')->where('act', 'BOOKING_CANCELLED_BY_TRAVELER')->delete();
    }
};
