<?php

namespace App\Http\Controllers;

use App\Constants\QuoteRequestStatus;
use App\Models\Agency;
use App\Models\AdminNotification;
use App\Models\QuoteRequest;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'tour_package_id' => 'required|integer|exists:tour_packages,id',
            'party_size' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'message' => 'nullable|string|max:2000',
        ];

        if (!auth()->check()) {
            $rules['name'] = 'required|string|max:190';
            $rules['email'] = 'required|email';
            $rules['phone'] = 'required|string|max:30';
        }

        $request->validate($rules);

        $tourPackage = TourPackage::findOrFail($request->tour_package_id);
        $user = auth()->user();

        $quoteRequest = new QuoteRequest();
        $quoteRequest->tour_package_id = $tourPackage->id;
        $quoteRequest->name = $user->fullname ?? $request->name;
        $quoteRequest->email = $user->email ?? $request->email;
        $quoteRequest->phone = $user->mobile ?? $request->phone;
        $quoteRequest->party_size = $request->party_size;
        $quoteRequest->start_date = $request->start_date;
        $quoteRequest->end_date = $request->end_date;
        $quoteRequest->message = $request->message;
        $quoteRequest->status = QuoteRequestStatus::PENDING;

        if ($user) {
            $quoteRequest->user_id = $user->id;
        }

        if ($tourPackage->user_type == 'agency') {
            $quoteRequest->agency_id = $tourPackage->user_id;
        }

        $quoteRequest->save();

        $adminNotification = new AdminNotification();
        if ($user) {
            $adminNotification->user_id = $user->id;
        }
        $adminNotification->title = 'New quote request for ' . $tourPackage->title;
        $adminNotification->click_url = urlPath('admin.quote.requests.view', $quoteRequest->id);
        $adminNotification->save();

        if ($quoteRequest->agency_id) {
            $agency = Agency::find($quoteRequest->agency_id);
            if ($agency) {
                notify($agency, 'QUOTE_REQUESTED', [
                    'tour_title' => $tourPackage->title,
                    'name' => $quoteRequest->name,
                    'email' => $quoteRequest->email,
                    'phone' => $quoteRequest->phone ?? '',
                    'party_size' => $quoteRequest->party_size,
                ]);
            }
        }

        $notify[] = ['success', "Your request has been sent — we'll get back to you with a quote."];
        return back()->withNotify($notify);
    }
}
