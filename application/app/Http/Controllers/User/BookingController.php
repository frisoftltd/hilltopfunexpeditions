<?php

namespace App\Http\Controllers\User;

use App\Models\TourBooking;
use App\Models\TourDeparture;
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
            'tour_departure_id' => 'required|numeric|exists:tour_departures,id',
            'price_category_id' => 'required|numeric|exists:price_categories,id',
            'seat' => 'required|numeric|min:1',
        ]);

        $tourPackage = TourPackage::findOrFail($request->tour_package_id);

        $departure = TourDeparture::with('departurePrices')
            ->where('id', $request->tour_departure_id)
            ->where('tour_package_id', $tourPackage->id)
            ->active()
            ->first();

        if (!$departure) {
            $notify[] = ['error', 'This departure is no longer available.'];
            return back()->withNotify($notify);
        }

        $departurePrice = $departure->departurePrices->firstWhere('price_category_id', (int) $request->price_category_id);
        if (!$departurePrice) {
            $notify[] = ['error', 'Invalid price category for this departure.'];
            return back()->withNotify($notify);
        }

        if (auth('agency')->user()) {
            $notify[] = ['error', 'Agency is not booking'];
            return back()->withNotify($notify);
        };

        // departure date check
        if ($departure->start_date->lt(now()->startOfDay())) {
            $notify[] = ['error', "This departure has already started or expired"];
            return back()->withNotify($notify);
        }

        // Seat availability check
        if ($departure->seats_available <= 0) {
            $notify[] = ['error', "Seats are not available for this departure"];
            return back()->withNotify($notify);
        }

        // Seat availability check plus requested seats
        if ($departure->seats_available < $request->seat) {
            $notify[] = ['warning', "Only " . $departure->seats_available . " seat(s) left for this departure"];
            return back()->withNotify($notify);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        Session::put('tourPackageSession', [
            'tour_package_id' => $request->tour_package_id,
            'tour_departure_id' => $departure->id,
            'price_category_id' => $departurePrice->price_category_id,
            'seat' => $request->seat,
        ]);

        $seat = $request->seat;
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'tourPackage', 'departurePrice', 'seat'));
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
        return $tourBooking->with('deposit', 'user', 'departure', 'tour_package.TourPackagePrimaryImage')->orderBy('id', 'desc')->paginate(getPaginate());
    }
}
