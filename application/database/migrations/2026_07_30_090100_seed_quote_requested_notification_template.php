<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the notification template QuoteRequestController::store() fires
 * at the owning agency when a tourist submits a custom quote request.
 * Mirrors the seeding pattern already used for BOOKING_APPROVED_BY_AGENCY/
 * BOOKING_DECLINED/BOOKING_CANCELLED_BY_TRAVELER - written directly to the
 * DB for the same WYSIWYG-strips-<a href>-values reason, idempotent on
 * `act`.
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
            'name' => 'Quote Requested',
            'act' => 'QUOTE_REQUESTED',
            'subj' => 'New quote request for {{tour_title}}',
            'email_body' => '<p>You\'ve received a new custom quote request for <strong>{{tour_title}}</strong>.</p>'
                . '<p>Name: {{name}}</p>'
                . '<p>Email: {{email}}</p>'
                . '<p>Phone: {{phone}}</p>'
                . '<p>Party size: {{party_size}}</p>'
                . '<p>Log in to your dashboard to view the full request and respond.</p>',
            'email_status' => 1,
            'sms_body' => 'New quote request for {{tour_title}} from {{name}}.',
            'sms_status' => 0,
            'shortcodes' => json_encode([
                'tour_title' => 'Tour package title',
                'name' => 'Requester name',
                'email' => 'Requester email',
                'phone' => 'Requester phone',
                'party_size' => 'Number of travelers',
            ]),
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
        DB::table('notification_templates')->where('act', 'QUOTE_REQUESTED')->delete();
    }
};
