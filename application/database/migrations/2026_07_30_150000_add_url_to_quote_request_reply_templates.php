<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * QUOTE_REQUEST_REPLY_TO_TRAVELER/QUOTE_REQUEST_REPLY_TO_OWNER (seeded in
 * 2026_07_30_120100) were seeded with a plain, unlinked "Log in to view
 * the full conversation and reply." line - no quote_request_url shortcode
 * was ever passed into either notify() call, so there was nothing to
 * render even if the template had referenced one. Both controllers now
 * pass quote_request_url; this turns that same line into an actual link.
 * Written directly to the DB, same WYSIWYG-strips-<a href>-values reason
 * as every other template edit in this chain.
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

        $updates = [
            'QUOTE_REQUEST_REPLY_TO_TRAVELER' => [
                'email_body' => '<p>You have a new reply on your quote request for <strong>{{tour_title}}</strong>:</p>'
                    . '<p>{{reply_message}}</p>'
                    . '<p><a href="{{quote_request_url}}">Log in to view the full conversation and reply</a>.</p>',
                'shortcodes' => json_encode([
                    'tour_title' => 'Tour package title',
                    'reply_message' => 'The reply text',
                    'quote_request_url' => 'Link to the quote request detail page',
                ]),
            ],
            'QUOTE_REQUEST_REPLY_TO_OWNER' => [
                'email_body' => '<p>The traveler has replied on the quote request for <strong>{{tour_title}}</strong>:</p>'
                    . '<p>{{reply_message}}</p>'
                    . '<p><a href="{{quote_request_url}}">Log in to view the full conversation and reply</a>.</p>',
                'shortcodes' => json_encode([
                    'tour_title' => 'Tour package title',
                    'reply_message' => 'The reply text',
                    'quote_request_url' => 'Link to the quote request detail page',
                ]),
            ],
        ];

        foreach ($updates as $act => $update) {
            $filteredUpdate = array_intersect_key($update, array_flip($existingColumns));
            if (empty($filteredUpdate)) {
                continue;
            }

            DB::table('notification_templates')->where('act', $act)->update($filteredUpdate);
        }
    }

    public function down(): void
    {
        // Not reversible to "unlinked text" without reintroducing the gap
        // this fixes.
    }
};
