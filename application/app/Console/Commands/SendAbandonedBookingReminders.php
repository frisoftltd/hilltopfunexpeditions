<?php

namespace App\Console\Commands;

use App\Constants\BookingStatus;
use App\Models\TourBooking;
use Illuminate\Console\Command;

class SendAbandonedBookingReminders extends Command
{
    protected $signature = 'booking:send-abandoned-reminders';

    protected $description = 'Email travelers who started checkout (picked a gateway/amount) but never submitted the proof/confirmation form';

    public function handle(): int
    {
        $this->sendStage(
            fromStage: 0,
            toStage: 1,
            olderThan: now()->subHour(),
        );

        $this->sendStage(
            fromStage: 1,
            toStage: 2,
            olderThan: now()->subDay(),
        );

        return self::SUCCESS;
    }

    protected function sendStage(int $fromStage, int $toStage, \DateTimeInterface $olderThan): void
    {
        $bookings = TourBooking::with('user', 'tour_package')
            ->where('status', BookingStatus::UNPAID)
            ->where('reminder_stage', $fromStage)
            ->where('created_at', '<', $olderThan)
            ->get();

        foreach ($bookings as $booking) {
            if (!$booking->user || !$booking->tour_package) {
                continue;
            }

            notify($booking->user, 'BOOKING_ABANDONED_REMINDER', [
                'tour_title' => $booking->tour_package->title,
                'booking_url' => route('tour.package.details', [$booking->tour_package->id, slug($booking->tour_package->title)]),
            ]);

            $booking->reminder_stage = $toStage;
            $booking->save();
        }

        $this->line("Stage {$fromStage} -> {$toStage}: sent " . $bookings->count() . ' reminder(s).');
    }
}
