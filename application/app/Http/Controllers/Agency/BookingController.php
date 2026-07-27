<?php

namespace App\Http\Controllers\Agency;

use App\Models\User;
use App\Models\TourBooking;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookingController extends Controller
{
    public function bookingTourPackageList(Request $request)
    {
        $pageTitle = 'My Package-List';
        $bookingTourPackages = $this->tourPackageData('agencyAll');

        return view($this->activeTemplate . 'agency.tour_booking.my_booked', compact('pageTitle', 'bookingTourPackages'));
    }

    public function pending()
    {
        $pageTitle = 'Agency Pending Package-List';
        $bookingTourPackages = $this->tourPackageData('agencyPending');
        return view($this->activeTemplate . 'agency.tour_booking.my_booked', compact('pageTitle', 'bookingTourPackages'));
    }

    public function approved()
    {
        $pageTitle = 'Agency Approved Package-List';
        $bookingTourPackages = $this->tourPackageData('agencyApproved');
        return view($this->activeTemplate . 'agency.tour_booking.my_booked', compact('pageTitle', 'bookingTourPackages'));
    }

    public function canceled()
    {
        $pageTitle = 'Agency Canceled Package-List';
        $bookingTourPackages = $this->tourPackageData('agencyCanceled');
        return view($this->activeTemplate . 'agency.tour_booking.my_booked', compact('pageTitle', 'bookingTourPackages'));
    }


    public function bookingDetails($id)
    {
        $pageTitle = 'Tour & Booking Details';
        $bookingDetails = TourBooking::with(['user', 'owner', 'admin', 'tour_package', 'tour_package.category', 'priceCategory'])
            ->where('id', $id)
            ->where('owner_id', auth('agency')->id())
            ->where('owner_type', 'agency')
            ->first();
        return view($this->activeTemplate . 'agency.tour_booking.details', compact('pageTitle', 'bookingDetails'));
    }

    /**
     * Agency's own review layer (tour_bookings.agency_status) - independent
     * of the payment status. Reachable regardless of current payment state,
     * since an already-paid booking can still be declined (refund handled
     * manually outside the system).
     */
    public function approve($id)
    {
        $bookingDetails = TourBooking::with(['user', 'tour_package'])
            ->where('id', $id)
            ->where('owner_id', auth('agency')->id())
            ->where('owner_type', 'agency')
            ->firstOrFail();

        $bookingDetails->agency_status = 1;
        $bookingDetails->save();

        notify($bookingDetails->user, 'BOOKING_APPROVED_BY_AGENCY', [
            'tour_title' => $bookingDetails->tour_package->title,
        ]);

        $notify[] = ['success', 'Booking approved successfully'];
        return back()->withNotify($notify);
    }

    public function decline(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $bookingDetails = TourBooking::with(['user', 'tour_package'])
            ->where('id', $id)
            ->where('owner_id', auth('agency')->id())
            ->where('owner_type', 'agency')
            ->firstOrFail();

        $bookingDetails->agency_status = 2;
        $bookingDetails->decline_reason = $request->reason;
        $bookingDetails->save();

        notify($bookingDetails->user, 'BOOKING_DECLINED', [
            'tour_title' => $bookingDetails->tour_package->title,
            'reason' => $request->reason ?: 'Not specified',
        ]);

        $notify[] = ['success', 'Booking declined successfully'];
        return back()->withNotify($notify);
    }

    protected function tourPackageData($scope = null)
    {
        if ($scope) {
            $bookingTourPackages = TourPackage::$scope();
        } else {
            $bookingTourPackages = TourPackage::query();
        }
        //search
        $request = request();
        if ($request->search) {
            $search = $request->search;
            $bookingTourPackages  = $bookingTourPackages->with('tour_bookings.deposit')
                ->where('title', 'like', "%$search%");
        }
        return $bookingTourPackages->with('tour_bookings.deposit', 'tour_bookings.user', 'tour_bookings.owner', 'TourPackagePrimaryImage', 'packagePrices')->orderBy('id', 'desc')->paginate(getPaginate());
    }


    public function userList(Request $request, $id)
    {
        return $this->userBookingList($request, $id);
    }

    public function userListPending(Request $request, $id)
    {
        return $this->userBookingList($request, $id, 'pending');
    }

    public function userListApproved(Request $request, $id)
    {
        return $this->userBookingList($request, $id, 'approved');
    }

    public function userListDeclined(Request $request, $id)
    {
        return $this->userBookingList($request, $id, 'declined');
    }

    /**
     * Mirrors tourPackageData()'s scope-per-tab pattern, but filtering
     * individual bookings by agency_status (the agency's own review layer)
     * instead of the package-level status - so past decisions (approved/
     * declined) stay reachable instead of disappearing once reviewed.
     */
    protected function userBookingList(Request $request, $id, $reviewFilter = null)
    {
        $pageTitle = 'User Booking-List';

        $tourBookings = TourBooking::with('user', 'tour_package', 'priceCategory')
            ->where('tour_package_id', $id)
            ->where('owner_id', auth('agency')->id())
            ->where('owner_type', 'agency')
            ->when($reviewFilter === 'pending', fn ($query) => $query->whereNull('agency_status'))
            ->when($reviewFilter === 'approved', fn ($query) => $query->where('agency_status', 1))
            ->when($reviewFilter === 'declined', fn ($query) => $query->where('agency_status', 2))
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('firstname', 'like', "%$search%")->orWhere('lastname', 'like', "%$search%")->orWhere('username', 'like', "%$search%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view($this->activeTemplate . 'agency.tour_booking.my_booked_user_list', compact('pageTitle', 'tourBookings', 'id'));
    }
}
