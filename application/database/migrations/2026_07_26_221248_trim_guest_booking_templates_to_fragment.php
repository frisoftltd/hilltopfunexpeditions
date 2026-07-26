<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fixes a duplicate header/footer confirmed in a real sent email: the
 * guest-checkout templates (2026_07_25_210906 / 2026_07_26_175610) each
 * carried their own branded card - logo header, bordered box, navy footer.
 * Then 2026_07_26_181431 wrapped general_settings.email_template (the
 * shared wrapper every notify() call renders through) in that exact same
 * branded card, site-wide. Since these two templates' bodies get spliced
 * into that wrapper's {{message}} slot, the result was the branded card
 * appearing twice, nested.
 *
 * Trims both templates back down to a plain content fragment - heading,
 * body paragraphs, button, fallback link, sign-off - with no card
 * border, no logo, no navy footer of their own. The global wrapper now
 * supplies that chrome for these two the same as every other
 * notification template.
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

        if (!in_array('email_body', $existingColumns)) {
            return;
        }

        $linkFallback = '<p style="font-size:13px;color:#777777;">If the button doesn\'t work, copy and paste this link into your browser:<br>{{set_password_url}}</p>';
        $signOff = '<p>Thank you,<br>{{site_name}}</p>';

        $bodies = [
            'GUEST_BOOKING_WELCOME' => '<h2 style="color:#0a2540;margin-top:0;">Welcome to {{site_name}}, {{fullname}}!</h2>'
                . '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) is confirmed - thank you!</p>'
                . '<p>We\'ve created an account for you with this email address so you can view and manage your booking any time. Set a password to log in:</p>'
                . '<p style="text-align:center;margin:32px 0;"><a href="{{set_password_url}}" style="background:#0a2540;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Set Your Password</a></p>'
                . $linkFallback
                . $signOff,
            'GUEST_BOOKING_EXISTING_ACCOUNT' => '<h2 style="color:#0a2540;margin-top:0;">Booking Confirmed, {{fullname}}!</h2>'
                . '<p>Your booking for <strong>{{tour_title}}</strong> ({{currency_symbol}}{{price}}) is confirmed - thank you!</p>'
                . '<p>This email address is already registered with {{site_name}}, so we\'ve attached this booking to your existing account. Log in to view it, or set a new password below if you\'ve forgotten yours:</p>'
                . '<p style="text-align:center;margin:32px 0;"><a href="{{set_password_url}}" style="background:#0a2540;color:#ffffff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Log In / Set Password</a></p>'
                . $linkFallback
                . $signOff,
        ];

        foreach ($bodies as $act => $body) {
            DB::table('notification_templates')->where('act', $act)->update(['email_body' => $body]);
        }
    }

    public function down(): void
    {
        // Not reversible to "the double-branded body" without
        // reintroducing the duplicate-header/footer bug this fixes.
    }
};
