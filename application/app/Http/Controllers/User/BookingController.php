<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
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
        // Step 1: First validate tour_package_id only
        $request->validate([
            'tour_package_id' => 'required|numeric|exists:tour_packages,id',
        ]);

        // Step 2: Now safely get the TourPackage
        $tourPackage = TourPackage::findOrFail($request->tour_package_id);

        // Step 3: Then validate the rest
        $request->validate([
            'tour_package_id' => 'required|numeric|exists:tour_packages,id',
            'seat' => 'required|numeric',
        ]);

        if ($tourPackage->flexible_date == 1 && $request->user_proposal_date) {
            $userProposalDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->user_proposal_date);
            if ($userProposalDate->lt(now())) {
                $notify[] = ['error', 'Proposed date must be today or a future date.'];
                return back()->withNotify($notify);
            }
        }

        if (auth('agency')->user()) {
            $notify[] = ['error', 'Agency is not booking'];
            return back()->withNotify($notify);
        };
        
        // tour end time check
        if ($tourPackage->tour_end < now()) {
            $notify[] = ['error', "Tour package is expired"];
            return back()->withNotify($notify);
        }

        // Seat availability check
        if ($tourPackage->person_capability <= $tourPackage->booking_person) {
            $notify[] = ['error', "Seats are not available for this tour package. Available Seat"];
            return back()->withNotify($notify);
        }

        // Seat availability check plus requests seat
        if ($tourPackage->person_capability < $tourPackage->booking_person + $request->seat) {
            $notify[] = ['warning', "Seats are not available for this tour package. Available seat is " . $tourPackage->person_capability - $tourPackage->booking_person];
            return back()->withNotify($notify);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        Session::put('tourPackageSession', [
            'tour_package_id' => $request->tour_package_id,
            'user_proposal_date' => $userProposalDate ?? $tourPackage->tour_start,
            'seat' => $request->seat,
        ]);

        $seat = $request->seat;
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'tourPackage', 'seat'));
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
