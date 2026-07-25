<?php

namespace App\Http\Controllers\User;

use App\Models\TourBooking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\BookingSessionService;

class BookingController extends Controller
{
    use BookingSessionService;

    public function bookingNow(Request $request)
    {
        if (auth('agency')->user()) {
            $notify[] = ['error', 'Agency is not booking'];
            return back()->withNotify($notify);
        }

        return $this->buildBookingSessionResponse($request, auth()->id());
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
