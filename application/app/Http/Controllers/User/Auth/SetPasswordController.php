<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * Standalone "set your password" landing page for the guest-checkout
 * welcome/existing-account emails - deliberately separate from
 * ForgotPasswordController/ResetPasswordController, which need a manually
 * typed code plus a session key set by an earlier step in that flow. This
 * verifies email+token together straight from the URL/form, nothing else
 * required.
 */
class SetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->activeTemplate = activeTemplate();
    }

    public function show(Request $request, string $token)
    {
        $pageTitle = 'Set Your Password';
        $email = $request->query('email');

        if (!$email || !$this->validReset($token, $email)) {
            $notify[] = ['error', 'This link is invalid or has expired.'];
            return to_route('user.login')->withNotify($notify);
        }

        return view($this->activeTemplate . 'user.auth.passwords.set_password', compact('pageTitle', 'token', 'email'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        if (!$this->validReset($request->token, $request->email)) {
            $notify[] = ['error', 'This link is invalid or has expired.'];
            return to_route('user.login')->withNotify($notify);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $notify[] = ['error', 'This link is invalid or has expired.'];
            return to_route('user.login')->withNotify($notify);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        PasswordReset::where('email', $request->email)->delete();

        //the request just proved ownership of the email by presenting a
        //token that was only ever sent to that address - safe to log in
        Auth::login($user);

        $notify[] = ['success', 'Password set successfully.'];
        return to_route('user.home')->withNotify($notify);
    }

    private function validReset(string $token, string $email): bool
    {
        return PasswordReset::where('token', $token)->where('email', $email)->exists();
    }
}
