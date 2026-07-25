<?php

namespace App\Traits;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Guest checkout account resolution - find-or-create a User by email
 * without ever modifying or logging into an account we didn't just create
 * ourselves. The account-bootstrap fields (random password, ev/sv/tv/
 * reg_step all forced to 1 so the account clears CheckStatus/
 * RegistrationStep immediately) mirror App\Lib\SocialLogin::createUser() -
 * that's the existing, already-shipped precedent in this codebase for
 * "trust this identity enough to skip verification", not a new bypass.
 *
 * Identity is the email; phone is contact info that lives on the booking,
 * not here - deliberately never touches mobile/mobile_code/country_code,
 * so the mobile-uniqueness check in the normal registration flow never
 * applies to this path.
 */
trait GuestAccountResolver
{
    /**
     * @return array{0: User, 1: bool} [$user, $isNewAccount]
     */
    protected function resolveGuestUser(string $name, string $email): array
    {
        $email = strtolower(trim($email));

        $user = User::where('email', $email)->first();
        if ($user) {
            return [$user, false];
        }

        $general = gs();
        [$firstName, $lastName] = $this->splitGuestName($name);

        $user = new User();
        $user->username = $this->generateGuestUsername($name);
        $user->email = $email;
        $user->password = Hash::make(getTrx(8));
        $user->firstname = $firstName;
        $user->lastname = $lastName;
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => '',
            'city' => '',
        ];
        $user->status = 1;
        $user->kv = $general->kv ? 0 : 1;
        $user->ev = 1;
        $user->sv = 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->reg_step = 1;
        $user->login_by = 'guest_checkout';
        $user->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered via guest checkout';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        return [$user, true];
    }

    private function splitGuestName(string $name): array
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        $pieces = explode(' ', $name);
        $lastName = count($pieces) > 1 ? array_pop($pieces) : '';
        $firstName = implode(' ', $pieces) ?: $name;

        return [$firstName, $lastName];
    }

    /**
     * Derived from the tourist's name, made collision-safe with a random
     * numeric suffix - unlike SocialLogin::generateUsername(), which on
     * collision just finds (and would then log into) whoever already owns
     * that string instead of generating a new one.
     */
    private function generateGuestUsername(string $name): string
    {
        $base = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
        $base = substr($base, 0, 15) ?: 'guest';

        while (strlen($base) < 6) {
            $base .= random_int(0, 9);
        }

        $username = $base;
        while (User::where('username', $username)->exists()) {
            $username = substr($base, 0, 15) . random_int(1000, 9999);
        }

        return $username;
    }
}
