<?php

namespace App\Traits;

use App\Models\GatewayCurrency;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Shared by the logged-in and guest-checkout booking entry points -
 * validates the widget's date/party-size/category selection, stashes the
 * session payload PaymentController::depositInsert() reads, and renders
 * the same deposit view either way. Parameterized by an explicit $userId
 * rather than auth()->id(), so the guest path (which never establishes a
 * session for a pre-existing account) can supply the resolved user
 * directly without depositInsert() needing to know which path a request
 * came from.
 */
trait BookingSessionService
{
    /**
     * @param array<string, mixed> $extraSession merged into the session
     *   payload alongside tour_package_id/price_category_id/start_date/
     *   party_size/user_id - the guest path uses this for phone/guest_signup.
     */
    protected function buildBookingSessionResponse(Request $request, int $userId, array $extraSession = [])
    {
        $pageTitle = 'Tour Booking Payment';

        $request->validate([
            'tour_package_id' => 'required|numeric|exists:tour_packages,id',
            'price_category_id' => 'required|numeric|exists:price_categories,id',
            'start_date' => 'required|date|after_or_equal:today',
            'party_size' => 'required|numeric|min:1',
        ]);

        $tourPackage = TourPackage::with('packagePrices')->findOrFail($request->tour_package_id);

        $packagePrice = $tourPackage->packagePrices->firstWhere('price_category_id', (int) $request->price_category_id);
        if (!$packagePrice) {
            $notify[] = ['error', 'Invalid price category for this tour.'];
            return back()->withNotify($notify);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();

        Session::put('tourPackageSession', array_merge([
            'tour_package_id' => $request->tour_package_id,
            'price_category_id' => $packagePrice->price_category_id,
            'start_date' => $request->start_date,
            'party_size' => $request->party_size,
            'user_id' => $userId,
        ], $extraSession));

        $partySize = $request->party_size;
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'tourPackage', 'packagePrice', 'partySize'));
    }
}
