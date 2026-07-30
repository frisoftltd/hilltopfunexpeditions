<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the notification Admin\DepositController::approve() fires to the
 * traveler when confirming a pay-on-arrival reservation. Deliberately a
 * new, separate template rather than reusing DEPOSIT_APPROVE/BOOKING_COMPLETE
 * (the ones userDataUpdate() sends for a real payment) - those say the
 * payment was received, which would be wrong here since no money has
 * actually moved through the platform. Mirrors the seeding pattern
 * already used for QUOTE_REQUESTED etc. - written directly to the DB,
 * idempotent on `act`.
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
            'name' => 'Booking Reserved - Pay on Arrival Confirmed',
            'act' => 'BOOKING_RESERVED_CONFIRMED',
            'subj' => 'Your reservation for {{tour_title}} is confirmed',
            'email_body' => '<p>Your reservation for <strong>{{tour_title}}</strong> has been confirmed.</p>'
                . '<p>No payment has been collected yet - you will pay on arrival.</p>',
            'email_status' => 1,
            'sms_body' => 'Your reservation for {{tour_title}} is confirmed. Payment is due on arrival.',
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
        DB::table('notification_templates')->where('act', 'BOOKING_RESERVED_CONFIRMED')->delete();
    }
};
