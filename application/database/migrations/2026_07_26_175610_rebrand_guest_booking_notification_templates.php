<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Updates the two guest-checkout notification templates' email bodies -
 * done via migration (DB write), not the admin template editor, because
 * that editor's WYSIWYG textarea has already been seen silently stripping
 * an <a href="{{set_password_url}}"> whose href isn't a real URL (see the
 * GUEST_BOOKING_WELCOME/set-password-link investigation). Fixes three
 * things found in a real sent email:
 * 1. {{fullname}} rendered literally - it's part of the template body, not
 *    the site's global email wrapper, so it only resolves if 'fullname' is
 *    in the notify() shortCodes array. It wasn't; PaymentController now
 *    adds it (see sendGuestSignupNotification()), and this body actually
 *    uses the shortcode once (in the heading) so the fix is visible.
 * 2. Duplicate greeting - the site's global email template (general_settings
 *    .email_template, edited at Admin > Notification > Global Template)
 *    already renders its own "Hi {{fullname}} ({{username}}),\" before this
 *    body gets spliced into its {{message}} slot, so this body's own
 *    "Hi {{fullname}}," line was a second, redundant greeting. Removed;
 *    {{fullname}} is still used once, naturally, in the heading below.
 * 3. No Hilltop branding - added a self-contained branded card (logo,
 *    navy header/footer) directly in the body rather than editing the
 *    shared global wrapper, which would affect every other notification
 *    type sent by the site, not just these two.
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

        $footer = '<div style="background:#0a2540;color:#ffffff;padding:16px 24px;text-align:center;font-size:12px;">'
            . '<p style="margin:0;">{{site_name}}</p>'
            . '<p style="margin:4px 0 0;">This is an automated message, please do not reply directly to this email.</p>'
            . '</div>'
            . '</div>';

        $header = '<div style="max-width:560px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;border:1px solid #e5e5e5;border-radius:8px;overflow:hidden;">'
            . '<div style="background:#ffffff;padding:24px;text-align:center;border-bottom:3px solid #0a2540;">'
            . '<img src="{{logo_url}}" alt="{{site_name}}" style="max-height:48px;">'
            . '</div>';

        $linkFallback = '<p style="font-size:13px;color:#777777;">If the button doesn\'t work, copy and paste this link into your browser:<br>{{set_password_url}}</p>';

        $rows = [
            'GUEST_BOOKING_WELCOME' => [
                'email_body' => $header
                    . '<div style="padding:24px;color:#333333;">'
                    . '<h2 style="color:#0a2540;margin-top:0;">Welcome to {{site_name}}, {{fullname}}!</h2>'
                    . '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) is confirmed - thank you!</p>'
                    . '<p>We\'ve created an account for you with this email address so you can view and manage your booking any time. Set a password to log in:</p>'
                    . '<p style="text-align:center;margin:32px 0;"><a href="{{set_password_url}}" style="background:#0a2540;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Set Your Password</a></p>'
                    . $linkFallback
                    . '</div>'
                    . $footer,
                'shortcodes' => json_encode([
                    'fullname' => 'Full name of the guest',
                    'tour_title' => 'Tour package title',
                    'price' => 'Booking price',
                    'set_password_url' => 'Link to set a password / log in',
                    'logo_url' => 'Site logo image URL',
                ]),
            ],
            'GUEST_BOOKING_EXISTING_ACCOUNT' => [
                'email_body' => $header
                    . '<div style="padding:24px;color:#333333;">'
                    . '<h2 style="color:#0a2540;margin-top:0;">Booking Confirmed, {{fullname}}!</h2>'
                    . '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) is confirmed - thank you!</p>'
                    . '<p>This email address is already registered with {{site_name}}, so we\'ve attached this booking to your existing account. Log in to view it, or set a new password below if you\'ve forgotten yours:</p>'
                    . '<p style="text-align:center;margin:32px 0;"><a href="{{set_password_url}}" style="background:#0a2540;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Log In / Set Password</a></p>'
                    . $linkFallback
                    . '</div>'
                    . $footer,
                'shortcodes' => json_encode([
                    'fullname' => 'Full name of the guest',
                    'tour_title' => 'Tour package title',
                    'price' => 'Booking price',
                    'set_password_url' => 'Link to set a password / log in',
                    'logo_url' => 'Site logo image URL',
                ]),
            ],
        ];

        foreach ($rows as $act => $row) {
            $filteredRow = array_intersect_key($row, array_flip($existingColumns));
            if (empty($filteredRow)) {
                continue;
            }
            DB::table('notification_templates')->where('act', $act)->update($filteredRow);
        }
    }

    public function down(): void
    {
        // Not reversible to "the old body" without reintroducing the bugs
        // this fixes - no-op, matching the original seed migration's stance
        // on non-reversible content changes.
    }
};
