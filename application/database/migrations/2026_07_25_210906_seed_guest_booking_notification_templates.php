<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeds the two notification templates the guest-checkout flow fires from
 * PaymentController::userDataUpdate() on payment success. notify_templates
 * has no migration of its own (installer-seeded, like tour_packages before
 * it) - this only writes columns NotificationController::templateUpdate()
 * already reads/writes, and skips any row whose `act` already exists, so
 * it's safe to run more than once and safe if the admin has since edited
 * these by hand.
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
                'name' => 'Guest Booking - Welcome (New Account)',
                'act' => 'GUEST_BOOKING_WELCOME',
                'subj' => 'Your booking for {{tour_title}} is confirmed - set your password',
                'email_body' => '<p>Hi {{fullname}},</p>'
                    . '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) is confirmed - thank you!</p>'
                    . '<p>We set up an account for you with this email address so you can view your booking any time. '
                    . 'Set a password to log in:</p>'
                    . '<p><a href="{{set_password_url}}">{{set_password_url}}</a></p>'
                    . '<p>{{site_name}}</p>',
                'email_status' => 1,
                'sms_body' => 'Your booking for {{tour_title}} is confirmed. Set a password to manage it: {{set_password_url}}',
                'sms_status' => 0,
                'shortcodes' => json_encode(['tour_title', 'price', 'set_password_url']),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Guest Booking - Existing Account',
                'act' => 'GUEST_BOOKING_EXISTING_ACCOUNT',
                'subj' => 'Your booking for {{tour_title}} is confirmed',
                'email_body' => '<p>Hi {{fullname}},</p>'
                    . '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) is confirmed - thank you!</p>'
                    . '<p>This email is already registered with {{site_name}}. We\'ve attached this booking to your '
                    . 'existing account. Log in to view it, or set a new password if you\'ve forgotten yours:</p>'
                    . '<p><a href="{{set_password_url}}">{{set_password_url}}</a></p>'
                    . '<p>{{site_name}}</p>',
                'email_status' => 1,
                'sms_body' => 'Your booking for {{tour_title}} is confirmed under your existing account. Log in to view it.',
                'sms_status' => 0,
                'shortcodes' => json_encode(['tour_title', 'price', 'set_password_url']),
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
        DB::table('notification_templates')->whereIn('act', ['GUEST_BOOKING_WELCOME', 'GUEST_BOOKING_EXISTING_ACCOUNT'])->delete();
    }
};
