<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gateway\PaymentController::userDataUpdate() already computes and passes
 * a tour_end shortcode into the BOOKING_COMPLETE notify() call (derived
 * from TourBooking::getEndDateAttribute()), but the email_body seeded in
 * 2026_07_27_085246 only ever rendered {{tour_start}} - tour_end was
 * silently dropped. Replaces the single "Tour date: {{tour_start}}" line
 * with a "Tour dates: {{tour_start}} - {{tour_end}}" range. Written
 * directly to the DB, not the admin editor, per the same
 * WYSIWYG-stripping precaution as every other template change in this
 * chain.
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
            'email_body' => '<p>Your booking for <strong>{{tour_title}}</strong> has been received - payment confirmed, awaiting final confirmation from the tour operator.</p>'
                . '<p>You\'ll receive another email once it\'s reviewed.</p>'
                . '<p>Amount paid: {{currency_symbol}}{{price}}</p>'
                . '<p>Tour dates: {{tour_start}} - {{tour_end}}</p>',
        ];

        $filteredUpdate = array_intersect_key($update, array_flip($existingColumns));
        if (empty($filteredUpdate)) {
            return;
        }

        DB::table('notification_templates')->where('act', 'BOOKING_COMPLETE')->update($filteredUpdate);
    }

    public function down(): void
    {
        // Not reversible to "drops tour_end" without reintroducing the
        // gap this fixes.
    }
};
