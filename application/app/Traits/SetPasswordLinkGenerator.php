<?php

namespace App\Traits;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Builds a one-click "set your password" link for the guest-checkout
 * notifications. Reuses the PasswordReset table the existing forgot-
 * password flow already uses, but that flow is code-entry-plus-session
 * coupled (a manually typed 6-digit code, verified against session('
 * fpass_email') before the reset form will even load) - not something a
 * bare emailed link can drive. This generates its own longer, URL-safe
 * token and the set.password route verifies email+token together with no
 * session prerequisite, instead of fighting that coupling.
 */
trait SetPasswordLinkGenerator
{
    /**
     * Static so PaymentController::userDataUpdate() (a static method) can
     * call it directly.
     */
    protected static function generateSetPasswordLink(User $user): string
    {
        PasswordReset::where('email', $user->email)->delete();

        $token = Str::random(40);
        $reset = new PasswordReset();
        $reset->email = $user->email;
        $reset->token = $token;
        $reset->created_at = now();
        $reset->save();

        return route('user.set.password', ['token' => $token, 'email' => $user->email]);
    }
}
