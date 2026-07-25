<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\BookingSessionService;
use App\Traits\GuestAccountResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Entry point for guest checkout - a tourist who isn't logged in submits
 * name/email/phone alongside the usual date/party-size/category from the
 * booking widget. Resolves (or creates) the account, then hands off to the
 * exact same session-payload builder the logged-in path uses, so
 * PaymentController::depositInsert() and everything after it runs unchanged
 * either way.
 *
 * New account: safe to log in immediately - we just created it ourselves.
 * Existing account: never logged in here (see App\Traits\GuestAccountResolver) -
 * the booking still gets attached to that account via the session payload's
 * user_id, the browser just doesn't gain an authenticated session for it.
 */
class GuestBookingController extends Controller
{
    use BookingSessionService;
    use GuestAccountResolver;

    public function bookingNow(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:190',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
        ]);

        [$user, $isNew] = $this->resolveGuestUser($request->name, $request->email);

        if ($isNew) {
            Auth::login($user);
        }

        return $this->buildBookingSessionResponse($request, $user->id, [
            'phone' => $request->phone,
            'guest_signup' => $isNew ? 'new' : 'existing',
        ]);
    }

    /**
     * Guest-side equivalent of user.deposit.history - gatewayRedirectUrl(true)
     * sends a logged-out payment flow here instead, since a guest (especially
     * the existing-account case, which never gets Auth::login()'d) has no
     * dashboard to show a history list on.
     */
    public function depositSuccess()
    {
        $pageTitle = 'Booking Confirmed';
        return view($this->activeTemplate . 'user.payment.guest_success', compact('pageTitle'));
    }
}
