<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the two reply-notification templates for the quote request
 * message thread - bidirectional, unlike the ticket system's
 * ADMIN_SUPPORT_REPLY (which only ever notifies the customer, never the
 * reverse). Mirrors the seeding pattern already used for QUOTE_REQUESTED
 * (2026_07_30_090100) - written directly to the DB, idempotent on `act`.
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
                'name' => 'Quote Request Reply (to Traveler)',
                'act' => 'QUOTE_REQUEST_REPLY_TO_TRAVELER',
                'subj' => 'New reply on your quote request for {{tour_title}}',
                'email_body' => '<p>You have a new reply on your quote request for <strong>{{tour_title}}</strong>:</p>'
                    . '<p>{{reply_message}}</p>'
                    . '<p>Log in to view the full conversation and reply.</p>',
                'email_status' => 1,
                'sms_body' => 'New reply on your quote request for {{tour_title}}.',
                'sms_status' => 0,
                'shortcodes' => json_encode(['tour_title' => 'Tour package title', 'reply_message' => 'The reply text']),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Quote Request Reply (to Agency/Admin)',
                'act' => 'QUOTE_REQUEST_REPLY_TO_OWNER',
                'subj' => 'Traveler replied on the quote request for {{tour_title}}',
                'email_body' => '<p>The traveler has replied on the quote request for <strong>{{tour_title}}</strong>:</p>'
                    . '<p>{{reply_message}}</p>'
                    . '<p>Log in to view the full conversation and reply.</p>',
                'email_status' => 1,
                'sms_body' => 'Traveler replied on the quote request for {{tour_title}}.',
                'sms_status' => 0,
                'shortcodes' => json_encode(['tour_title' => 'Tour package title', 'reply_message' => 'The reply text']),
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
        DB::table('notification_templates')->whereIn('act', ['QUOTE_REQUEST_REPLY_TO_TRAVELER', 'QUOTE_REQUEST_REPLY_TO_OWNER'])->delete();
    }
};
