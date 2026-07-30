<?php

namespace App\Console\Commands;

use App\Constants\BookingStatus;
use App\Models\TourBooking;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    protected $signature = 'booking:cancel-expired';

    protected $description = 'Auto-expire bookings that were never paid/confirmed and have sat abandoned for 3+ days';

    public function handle(): int
    {
        // Never reached userDataUpdate() (still status 0), so agencies.balance
        // was never credited - nothing to reverse, matching
        // User\BookingController::cancel()'s own $wasPaid gate. The linked
        // Deposit (also still status 0) is left untouched for the same
        // reason that controller never touches it either - nothing queries
        // it in a way that assumes it advances in lockstep with the booking.
        $count = TourBooking::where('status', BookingStatus::UNPAID)
            ->where('created_at', '<', now()->subDays(3))
            ->update(['status' => BookingStatus::EXPIRED]);

        $this->line("Expired {$count} abandoned booking(s).");

        return self::SUCCESS;
    }
}
