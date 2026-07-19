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
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{

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

        $user = auth()->user();
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


        $tourPackageSession = Session::get('tourPackageSession');
        $tourPackageSession = collect($tourPackageSession);

        $tourPackage = TourPackage::findOrFail($tourPackageSession['tour_package_id']);

        $tourBooking = new TourBooking();
        $tourBooking->user_id = auth()->id();
        $tourBooking->owner_id = $tourPackage->user_id;
        $tourBooking->owner_type = $tourPackage->user_type;
        $tourBooking->price = showTourPackageCalculateDiscount($tourPackage->price * $tourPackageSession['seat'], $tourPackage->discount);
        $tourBooking->discount = $tourPackage->discount;
        $tourBooking->tour_package_id = $tourPackage->id;
        $tourBooking->user_proposal_date = $tourPackageSession['user_proposal_date'] ?? $tourPackage->tour_start;
        $tourBooking->seat = $tourPackageSession['seat'];
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
        return to_route('user.deposit.confirm');
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
            return to_route('user.deposit.manual.confirm');
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

            // set tourPackage total booking person 
            $tourPackage = $tourBooking->tour_package;
            if ($tourPackage) {
                $tourPackage->booking_person += $tourBooking->seat;
                $tourPackage->save();
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
                'booking_seats' => $tourBooking->seat,
                'tour_start' => showDateTime($tourBooking->tour_package->tour_start),
                'tour_end' => showDateTime($tourBooking->tour_package->tour_end),
                'tour_stay' => $tourBooking->tour_package->day_nights,
            ]);

            $ownerName = ($tourBooking->owner_type == "agency") ? Agency::findOrFail($tourBooking->owner_id) : Admin::findOrFail($tourBooking->owner_id);
            notify($ownerName, 'TOUR_BOOKED', [
                'tour_title' => $tourBooking->tour_package->title,
                'first_name' => $tourBooking->user->firstname,
                'last_name' => $tourBooking->user->lastname,
                'email' => $tourBooking->user->email,
                'phone' => $tourBooking->user->phone
            ]);
        }
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
        $data = Deposit::with('gateway', 'tour_booking')->where('status', 0)->where('trx', $track)->first();
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

        $notify[] = ['success', 'You have payment request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
