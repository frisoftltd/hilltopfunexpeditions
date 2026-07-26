<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the two notification templates the agency booking-review flow
 * fires from Agency\BookingController::approve()/decline(). Kept as plain
 * content fragments - no per-template header/logo/footer - since every
 * notification already gets that from the branded global wrapper
 * (general_settings.email_template, see the rebrand-global-email-template
 * migration). Written directly to the DB, not through the admin template
 * editor, for the same reason as every other template in this chain: its
 * WYSIWYG textarea has been seen silently stripping unrecognized <a href>
 * values. Idempotent - skips any row whose `act` already exists.
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

        $rows = [
            [
                'name' => 'Booking Approved by Agency',
                'act' => 'BOOKING_APPROVED_BY_AGENCY',
                'subj' => 'Your booking for {{tour_title}} has been approved',
                'email_body' => '<p>Good news! Your booking for <strong>{{tour_title}}</strong> has been reviewed and approved by the tour operator.</p>'
                    . '<p>We look forward to hosting you.</p>',
                'email_status' => 1,
                'sms_body' => 'Your booking for {{tour_title}} has been approved by the tour operator.',
                'sms_status' => 0,
                'shortcodes' => json_encode(['tour_title' => 'Tour package title']),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Booking Declined by Agency',
                'act' => 'BOOKING_DECLINED',
                'subj' => 'Your booking for {{tour_title}} could not be confirmed',
                'email_body' => '<p>We\'re sorry - your booking for <strong>{{tour_title}}</strong> could not be confirmed by the tour operator.</p>'
                    . '<p>Reason: {{reason}}</p>'
                    . '<p>If a payment was already collected, our team will follow up separately about a refund.</p>',
                'email_status' => 1,
                'sms_body' => 'Your booking for {{tour_title}} could not be confirmed by the tour operator.',
                'sms_status' => 0,
                'shortcodes' => json_encode(['tour_title' => 'Tour package title', 'reason' => 'Decline reason, if given']),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($rows as $row) {
            if (DB::table('notification_templates')->where('act', $row['act'])->exists()) {
                continue;
            }

            $filteredRow = array_intersect_key($row, array_flip($existingColumns));
            DB::table('notification_templates')->insert($filteredRow);
        }
    }

    public function down(): void
    {
        DB::table('notification_templates')->whereIn('act', ['BOOKING_APPROVED_BY_AGENCY', 'BOOKING_DECLINED'])->delete();
    }
};
