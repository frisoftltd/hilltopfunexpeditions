<?php

namespace App\Http\Controllers\User;

use App\Models\TourBooking;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    public function bookingNow(Request $request)
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

        if (auth('agency')->user()) {
            $notify[] = ['error', 'Agency is not booking'];
            return back()->withNotify($notify);
        };

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        Session::put('tourPackageSession', [
            'tour_package_id' => $request->tour_package_id,
            'price_category_id' => $packagePrice->price_category_id,
            'start_date' => $request->start_date,
            'party_size' => $request->party_size,
        ]);

        $partySize = $request->party_size;
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'tourPackage', 'packagePrice', 'partySize'));
    }

    public function bookingList(Request $request)
    {
        $pageTitle = 'My Booking-List';
        $tourBookingList = $this->tourPackageData('userAll');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }


    public function bookingDetails($id)
    {

        $pageTitle = 'Tour & Booking Details';
        $bookingDetails = TourBooking::with(['user', 'owner', 'admin', 'tour_package', 'tour_package.category'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        return view($this->activeTemplate . 'user.tour_booking.details', compact('pageTitle', 'bookingDetails'));
    }


    public function pending()
    {
        $pageTitle = 'User Pending Booking-List';
        $tourBookingList = $this->tourPackageData('userPending');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    public function approved()
    {
        $pageTitle = 'User Approved Booking-List';
        $tourBookingList = $this->tourPackageData('userApproved');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    public function canceled()
    {
        $pageTitle = 'User Canceled Booking-List';
        $tourBookingList = $this->tourPackageData('userCanceled');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    public function bookingAgencyList()
    {
        $pageTitle = 'Agency Booking-List';
        $tourBookingList = $this->tourPackageData('agency');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    protected function tourPackageData($scope = null)
    {
        if ($scope) {
            $tourBooking = TourBooking::$scope();
        } else {
            $TourBooking = TourBooking::query();
        }
        //search
        $request = request();
        if ($request->search) {
            $search = $request->search;
            $tourBooking  = $tourBooking->with('tour_package', 'deposit')
                ->whereHas('tour_package', function ($query) use ($search) {
                    $query->where('title', 'like', "%$search%");
                });
        }
        return $tourBooking->with('deposit', 'user', 'tour_package.TourPackagePrimaryImage')->orderBy('id', 'desc')->paginate(getPaginate());
    }
}
