<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the branded header/logo/navy-footer treatment (originally applied
 * to just the two guest-checkout templates) to every notification email
 * site-wide, by wrapping general_settings.email_template - the single
 * shared wrapper every notify() call renders through (see
 * NotifyProcess::getMessage()/replaceShortCode()) - rather than editing
 * each of the ~20 individual templates one by one.
 *
 * This is a WRAP, not a REPLACE: the existing email_template value (its
 * "Hi {{fullname}} ({{username}}),", {{message}} slot, sign-off, whatever
 * else it already contains) is preserved untouched inside the branded
 * card's body area, with the logo header prepended and the navy footer
 * appended around it. This was chosen specifically because there was no
 * way to inspect the live value from this environment (no DB access) -
 * wrapping means that didn't need to happen for this to be safe. The one
 * risk this doesn't rule out: if email_template were a full standalone
 * HTML document (its own <html>/<body>) rather than a fragment, wrapping
 * it in a <div> would nest invalidly - unlikely, since it's edited through
 * a WYSIWYG textarea (Admin > Notification > Global Template), which
 * implies a body fragment, not a full document, but flagging it since it
 * couldn't be confirmed directly.
 *
 * The old value is preserved verbatim in a new backup column rather than
 * just logged, so down() can restore it exactly, and so it stays
 * inspectable on the live server after deploy even without re-running
 * anything.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            return;
        }

        $columns = DB::select(
            'SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?',
            ['general_settings']
        );
        $existingColumns = array_column($columns, 'COLUMN_NAME');

        if (!in_array('email_template', $existingColumns)) {
            return;
        }

        if (!Schema::hasColumn('general_settings', 'email_template_pre_branding_backup')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->longText('email_template_pre_branding_backup')->nullable()->after('email_template');
            });
        }

        $row = DB::table('general_settings')->first();
        if (!$row) {
            return;
        }

        // Idempotent - if this already ran (backup already populated),
        // don't wrap an already-wrapped template on a second deploy.
        if (!empty($row->email_template_pre_branding_backup)) {
            return;
        }

        $oldTemplate = (string) $row->email_template;

        $header = '<div style="max-width:560px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;border:1px solid #e5e5e5;border-radius:8px;overflow:hidden;">'
            . '<div style="background:#ffffff;padding:24px;text-align:center;border-bottom:3px solid #0a2540;">'
            . '<img src="{{logo_url}}" alt="{{site_name}}" style="max-height:48px;">'
            . '</div>'
            . '<div style="padding:24px;color:#333333;">';

        $footer = '</div>'
            . '<div style="background:#0a2540;color:#ffffff;padding:16px 24px;text-align:center;font-size:12px;">'
            . '<p style="margin:0;">{{site_name}}</p>'
            . '<p style="margin:4px 0 0;">This is an automated message, please do not reply directly to this email.</p>'
            . '</div>'
            . '</div>';

        DB::table('general_settings')->update([
            'email_template_pre_branding_backup' => $oldTemplate,
            'email_template' => $header . $oldTemplate . $footer,
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('general_settings') || !Schema::hasColumn('general_settings', 'email_template_pre_branding_backup')) {
            return;
        }

        $row = DB::table('general_settings')->first();
        if ($row && $row->email_template_pre_branding_backup) {
            DB::table('general_settings')->update([
                'email_template' => $row->email_template_pre_branding_backup,
            ]);
        }
    }
};
