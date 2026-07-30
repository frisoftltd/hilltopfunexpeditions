<?php

namespace App\Http\Controllers\User;

use App\Constants\BookingStatus;
use App\Models\AdminNotification;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Transaction;
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

    /**
     * Cancel window is enforced here (not just hidden client-side) - 24
     * hours before start_date. Balance is only reversed when the booking
     * had actually been credited to the agency (status was PAID at the
     * time of cancellation) - unpaid/pending-manual bookings never
     * touched agencies.balance, so there's nothing to reverse there.
     */
    public function cancel($id)
    {
        $booking = TourBooking::with(['tour_package', 'agency'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (in_array($booking->status, [BookingStatus::REJECTED, BookingStatus::CANCELLED_BY_TRAVELER, BookingStatus::EXPIRED])) {
            $notify[] = ['error', 'This booking cannot be cancelled.'];
            return back()->withNotify($notify);
        }

        if (!$booking->start_date || now()->greaterThanOrEqualTo($booking->start_date->copy()->subHours(24))) {
            $notify[] = ['error', 'Cancellation is only allowed until 24 hours before the tour start date.'];
            return back()->withNotify($notify);
        }

        $wasPaid = $booking->status == BookingStatus::PAID;

        $booking->status = BookingStatus::CANCELLED_BY_TRAVELER;
        $booking->save();

        if ($wasPaid && $booking->owner_type == 'agency' && $booking->agency) {
            $agency = $booking->agency;
            $agency->balance -= $booking->price;
            $agency->save();

            $transaction = new Transaction();
            $transaction->user_id = $booking->user_id;
            $transaction->agency_id = $agency->id;
            $transaction->amount = $booking->price;
            $transaction->post_balance = $agency->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->remark = 'booking_cancelled';
            $transaction->details = 'Balance reversed - booking #' . $booking->id . ' cancelled by traveler';
            $transaction->trx = getTrx();
            $transaction->save();
        }

        if ($booking->owner_type == 'agency' && $booking->agency) {
            notify($booking->agency, 'BOOKING_CANCELLED_BY_TRAVELER', [
                'tour_title' => $booking->tour_package->title ?? '',
            ]);
        }

        $this->openCancellationTicket($booking);

        $notify[] = ['success', 'Your booking has been cancelled.'];
        return back()->withNotify($notify);
    }

    /**
     * Mirrors SupportTicketManager::storeSupportTicket()'s row shape, but
     * set directly rather than through the trait since this ticket isn't
     * coming from the manual create-ticket form. Sets both user_id and
     * agency_id (when the package is agency-owned) so the ticket shows up
     * in both the traveler's and the agency's own Support Tickets list -
     * the table already supports both columns on one row (see
     * SupportTicket::user()/agency() and the agency_id-based ticket
     * widgets already used elsewhere), no new visibility mechanism needed.
     */
    protected function openCancellationTicket(TourBooking $booking)
    {
        $user = auth()->user();
        $tourTitle = $booking->tour_package->title ?? 'your tour';

        $ticket = new SupportTicket();
        $ticket->user_id = $user->id;
        if ($booking->owner_type == 'agency') {
            $ticket->agency_id = $booking->owner_id;
        }
        $ticket->ticket = rand(100000, 999999);
        $ticket->name = $user->fullname;
        $ticket->email = $user->email;
        $ticket->subject = 'Booking Cancellation — Refund Request (#' . $booking->id . ')';
        $ticket->last_reply = now();
        $ticket->status = 0;
        $ticket->priority = 3;
        $ticket->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = "This ticket was auto-generated from a traveler cancellation.\n\n"
            . "Tour package: {$tourTitle}\n"
            . "Booking ID: #{$booking->id}\n"
            . 'Price: ' . showAmount($booking->price) . "\n\n"
            . 'Please review this booking cancellation and process a refund if applicable.';
        $message->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'Booking cancellation - refund request opened';
        $adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
        $adminNotification->save();
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
