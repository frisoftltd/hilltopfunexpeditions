<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Same fix as BOOKING_COMPLETE (2026_07_27_085246): payment clearing no
 * longer means a booking is confirmed - the agency still has to approve
 * it (tour_bookings.agency_status), and a paid booking can still be
 * declined. GUEST_BOOKING_EXISTING_ACCOUNT said "is confirmed - thank
 * you!"; explicitly asked to fix that one. GUEST_BOOKING_WELCOME had the
 * identical "is confirmed" claim seeded alongside it (2026_07_25_210906)
 * and wasn't named, but the same reasoning applies to it unchanged - a
 * new-account guest's payment succeeding is exactly as unconfirmed as
 * everyone else's, so fixing one and not the other would leave an
 * obvious inconsistency. Fixing both here.
 *
 * BOOKING_APPROVED_BY_AGENCY already sends the real "approved" email
 * once the agency actually confirms - unchanged, still correct.
 *
 * Only email_body/subj/sms_body change - no new shortcodes, so the
 * shortcodes documentation column is left alone. DB write, not the
 * admin editor, per the established WYSIWYG-stripping precaution.
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

        $linkFallback = '<p style="font-size:13px;color:#777777;">If the button doesn\'t work, copy and paste this link into your browser:<br>{{set_password_url}}</p>';
        $signOff = '<p>Thank you,<br>{{site_name}}</p>';
        $receivedLine = '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) has been received - payment confirmed, awaiting final confirmation from the tour operator. You\'ll receive another email once it\'s reviewed.</p>';

        $updates = [
            'GUEST_BOOKING_WELCOME' => [
                'subj' => 'Your booking for {{tour_title}} has been received - set your password',
                'email_body' => '<h2 style="color:#0a2540;margin-top:0;">Welcome to {{site_name}}, {{fullname}}!</h2>'
                    . $receivedLine
                    . '<p>We\'ve created an account for you with this email address so you can view and manage your booking any time. Set a password to log in:</p>'
                    . '<p style="text-align:center;margin:32px 0;"><a href="{{set_password_url}}" style="background:#0a2540;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Set Your Password</a></p>'
                    . $linkFallback
                    . $signOff,
                'sms_body' => 'Your booking for {{tour_title}} has been received and is awaiting tour operator confirmation. Set a password to manage it: {{set_password_url}}',
            ],
            'GUEST_BOOKING_EXISTING_ACCOUNT' => [
                'subj' => 'Your booking for {{tour_title}} has been received',
                'email_body' => '<h2 style="color:#0a2540;margin-top:0;">Booking Received, {{fullname}}</h2>'
                    . $receivedLine
                    . '<p>This email address is already registered with {{site_name}}, so we\'ve attached this booking to your existing account. Log in to view it, or set a new password below if you\'ve forgotten yours:</p>'
                    . '<p style="text-align:center;margin:32px 0;"><a href="{{set_password_url}}" style="background:#0a2540;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Log In / Set Password</a></p>'
                    . $linkFallback
                    . $signOff,
                'sms_body' => 'Your booking for {{tour_title}} has been received under your existing account and is awaiting tour operator confirmation. Log in to view it.',
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
        // Not reversible to "says confirmed prematurely" without
        // reintroducing the wording bug this fixes.
    }
};
