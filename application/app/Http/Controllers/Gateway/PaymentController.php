<?php

namespace App\Http\Controllers\Gateway;

use App\Models\User;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Agency;
use App\Models\Deposit;
use App\Lib\FormProcessor;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Traits\SetPasswordLinkGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    use SetPasswordLinkGenerator;

    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Deposit Methods';
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency' => 'required',
        ]);

        $tourPackageSession = Session::get('tourPackageSession');
        $tourPackageSession = collect($tourPackageSession);

        //logged-in user, or - for the guest-checkout route group, which
        //never establishes a session for a pre-existing account - the
        //resolved user_id GuestBookingController stashed in this same
        //session payload
        $user = auth()->user() ?? User::find($tourPackageSession['user_id'] ?? null);
        if (!$user) {
            $notify[] = ['error', 'Your booking session has expired. Please start your booking again.'];
            return to_route('home')->withNotify($notify);
        }

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable = $request->amount + $charge;
        $final_amo = $payable * $gate->rate;

        $tourPackage = TourPackage::with('packagePrices')->findOrFail($tourPackageSession['tour_package_id']);
        $packagePrice = $tourPackage->packagePrices->firstWhere('price_category_id', $tourPackageSession['price_category_id']);

        if (!$packagePrice) {
            $notify[] = ['error', 'Invalid price category for this tour.'];
            return back()->withNotify($notify);
        }

        $tourBooking = new TourBooking();
        $tourBooking->user_id = $user->id;
        $tourBooking->owner_id = $tourPackage->user_id;
        $tourBooking->owner_type = $tourPackage->user_type;
        $tourBooking->price = showTourPackageCalculateDiscount($packagePrice->price * $tourPackageSession['party_size'], $packagePrice->discount);
        $tourBooking->discount = $packagePrice->discount;
        $tourBooking->tour_package_id = $tourPackage->id;
        $tourBooking->price_category_id = $packagePrice->price_category_id;
        $tourBooking->start_date = $tourPackageSession['start_date'];
        $tourBooking->party_size = $tourPackageSession['party_size'];
        $tourBooking->phone = $tourPackageSession['phone'] ?? null;
        $tourBooking->guest_signup = $tourPackageSession['guest_signup'] ?? null;
        $tourBooking->status = 0;
        $tourBooking->save();


        $data = new Deposit();
        $data->user_id = $user->id;
        $data->tour_booking_id = $tourBooking->id;
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $request->amount;
        $data->charge = $charge;
        $data->rate = $gate->rate;
        $data->final_amo = $final_amo;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();
        $data->try = 0;
        $data->status = 0;
        $data->save();
        Session::forget("tourPackageSession");
        session()->put('Track', $data->trx);
        return to_route(auth()->check() ? 'user.deposit.confirm' : 'guest.deposit.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            return "Sorry, invalid URL.";
        }
        $data = Deposit::where('id', $id)->where('status', 0)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', 0)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route(auth()->check() ? 'user.deposit.manual.confirm' : 'guest.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (isset($data->session)) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Tour Booking Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {

        // deposit status update
        if ($deposit->status == 0 || $deposit->status == 2) {
            $deposit->status = 1;
            $deposit->save();

            // tour booking status update
            $tourBooking = TourBooking::with(['tour_package', 'agency', 'admin', 'user'])->findOrFail($deposit->tour_booking_id);
            $tourBooking->status = 1;
            $tourBooking->save();


            // if tour-package is owner agency then give money
            if ($tourBooking->owner_type == "agency") {
                $agency = Agency::find($tourBooking->owner_id);
                $agency->balance += $tourBooking->price;
                $agency->save();
            }

            $user = User::find($deposit->user_id);
            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->agency_id = ($tourBooking->owner_type == "agency") ? $tourBooking->owner_id : 0;
            $transaction->amount = $deposit->amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = $deposit->charge;
            $transaction->trx_type = '+';
            $transaction->details = 'Payment Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->remark = 'Payment';
            $transaction->save();

            if (!$isManual) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'Deposit successful via ' . $deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name' => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount' => showAmount($deposit->final_amo),
                'amount' => showAmount($deposit->amount),
                'charge' => showAmount($deposit->charge),
                'rate' => showAmount($deposit->rate),
                'trx' => $deposit->trx,
                'post_balance' => showAmount($user->balance)
            ]);

            notify($user, 'BOOKING_COMPLETE', [
                'tour_title' => $tourBooking->tour_package->title,
                'tour_owner_name' => ($tourBooking->owner_type == "agency") ? $tourBooking->agency->fullname : $tourBooking->admin->name,
                'tour_owner_email' => ($tourBooking->owner_type == "agency") ? $tourBooking->agency->email : $tourBooking->admin->email,
                'price' => showAmount($tourBooking->price),
                'discount' => showAmount($tourBooking->discount),
                'booking_seats' => $tourBooking->party_size,
                'tour_start' => $tourBooking->start_date ? showDateTime($tourBooking->start_date) : null,
                'tour_end' => $tourBooking->end_date ? showDateTime($tourBooking->end_date) : null,
                'tour_stay' => $tourBooking->tour_package->day_nights,
            ]);

            // Manual payments already send TOUR_BOOKED from
            // manualDepositUpdate() at submission time (see
            // sendTourBookedNotification() below) - same reasoning as the
            // guest_signup guard right below: admin approval (the only way
            // $isManual is true here) can be arbitrarily delayed or never
            // happen, and the agency's own review workflow needs to know
            // about a booking as soon as it's submitted, not once its
            // payment is separately approved. Guard here so approval
            // doesn't send a second one.
            if (!$isManual) {
                self::sendTourBookedNotification($tourBooking);
            }

            // Manual payments already send this from manualDepositUpdate() at
            // submission time, since admin approval (the only way $isManual
            // is true here) can be arbitrarily delayed or never happen at
            // all - the guest shouldn't wait on that to get their account
            // welcome/set-password email. Guard here so approval doesn't
            // send a second one with a fresh (link-invalidating) token.
            if ($tourBooking->guest_signup && !$isManual) {
                self::sendGuestSignupNotification($user, $tourBooking);
            }
        }
    }

    /**
     * Shared by userDataUpdate() (online-gateway success) and
     * manualDepositUpdate() (manual/bank-transfer submission) - notifies
     * whoever owns the tour package (agency or admin) that a booking came
     * in. Fires at submission time for manual payments rather than waiting
     * for admin approval of the deposit (see the !$isManual guard in
     * userDataUpdate()) - the agency's own approve/decline review is
     * independent of payment status, so they need to know about the
     * booking immediately, the same way GUEST_BOOKING_WELCOME couldn't
     * wait on deposit approval either.
     *
     * Diagnostic logging kept from the "agency never gets TOUR_BOOKED"
     * investigation - logs exactly who this resolves to before sending,
     * and never lets a failure here (e.g. owner_id/owner_type pointing at
     * a deleted or mismatched record) break the caller's own flow.
     */
    protected static function sendTourBookedNotification($tourBooking)
    {
        try {
            $ownerName = ($tourBooking->owner_type == "agency") ? Agency::findOrFail($tourBooking->owner_id) : Admin::findOrFail($tourBooking->owner_id);

            Log::info('Sending TOUR_BOOKED notification', [
                'tour_booking_id' => $tourBooking->id,
                'owner_type' => $tourBooking->owner_type,
                'owner_id' => $tourBooking->owner_id,
                'resolved_recipient_class' => get_class($ownerName),
                'resolved_recipient_id' => $ownerName->id,
                'resolved_recipient_email' => $ownerName->email,
            ]);

            notify($ownerName, 'TOUR_BOOKED', [
                'tour_title' => $tourBooking->tour_package->title,
                'first_name' => $tourBooking->user->firstname,
                'last_name' => $tourBooking->user->lastname,
                'email' => $tourBooking->user->email,
                'phone' => $tourBooking->phone ?? $tourBooking->user->mobile
            ]);
        } catch (\Exception $e) {
            Log::error('TOUR_BOOKED notification failed', [
                'tour_booking_id' => $tourBooking->id,
                'owner_type' => $tourBooking->owner_type,
                'owner_id' => $tourBooking->owner_id,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Shared by userDataUpdate() (online-gateway success) and
     * manualDepositUpdate() (manual/bank-transfer submission) - the guest
     * account is created at booking time regardless of payment method, so
     * both paths need to fire this, just at different moments.
     */
    protected static function sendGuestSignupNotification($user, $tourBooking)
    {
        $setPasswordUrl = self::generateSetPasswordLink($user);
        $template = $tourBooking->guest_signup === 'new' ? 'GUEST_BOOKING_WELCOME' : 'GUEST_BOOKING_EXISTING_ACCOUNT';

        // fullname isn't in the notify() global shortcodes (those are
        // site_name/site_currency/currency_symbol/logo_url) - this template
        // body needs its own copy, since it's not part of the site's global
        // email wrapper here.
        notify($user, $template, [
            'fullname' => $user->fullname,
            'tour_title' => $tourBooking->tour_package->title,
            'price' => showAmount($tourBooking->price),
            'set_password_url' => $setPasswordUrl,
        ]);
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $pageTitle = 'Deposit Confirm';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway', 'tour_booking.tour_package', 'tour_booking.user')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = 2; // pending
        $data->save();

        $data->tour_booking->status = 2;
        $data->tour_booking->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Payment request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amo),
            'amount' => showAmount($data->amount),
            'charge' => showAmount($data->charge),
            'rate' => showAmount($data->rate),
            'trx' => $data->trx
        ]);

        // The account exists as soon as the guest submits, regardless of
        // payment method - manual payments sit pending until an admin
        // reviews them, which could be indefinite, so this can't wait for
        // that approval step the way the online-gateway path waits for
        // userDataUpdate(). See userDataUpdate()'s matching !$isManual
        // guard, which skips re-sending this on approval.
        if ($data->tour_booking->guest_signup) {
            self::sendGuestSignupNotification($data->user, $data->tour_booking);
        }

        // Same reasoning as guest_signup above: TOUR_BOOKED used to live
        // only in userDataUpdate(), which manual payments don't reach
        // until (and unless) an admin separately approves the deposit -
        // confirmed via a real test booking that produced zero log
        // entries for the notification at all, meaning that code path
        // simply never ran. The agency's own approve/decline review
        // (agency_status) needs to know about the booking as soon as it's
        // submitted, not once its payment happens to get reviewed too.
        self::sendTourBookedNotification($data->tour_booking);

        $notify[] = ['success', 'You have payment request has been taken'];
        return to_route(gatewayRedirectUrl(true))->withNotify($notify);
    }
}
