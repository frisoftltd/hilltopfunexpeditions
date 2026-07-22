<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryId = DB::table('price_categories')->insertGetId([
            'name' => 'Standard',
            'status' => 1,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $packages = DB::table('tour_packages')->orderBy('id')->get();

        foreach ($packages as $package) {
            $seatsTotal = (int) ($package->person_capability ?? 0);
            $seatsBooked = min((int) ($package->booking_person ?? 0), $seatsTotal);

            $departureId = DB::table('tour_departures')->insertGetId([
                'tour_package_id' => $package->id,
                'start_date' => $this->resolveStartDate($package),
                'seats_total' => $seatsTotal,
                'seats_booked' => $seatsBooked,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('departure_prices')->insert([
                'tour_departure_id' => $departureId,
                'price_category_id' => $categoryId,
                'price' => $package->price ?? 0,
                'discount' => $package->discount ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tour_bookings')
                ->where('tour_package_id', $package->id)
                ->update([
                    'tour_departure_id' => $departureId,
                    'price_category_id' => $categoryId,
                ]);

            $durationNights = $this->resolveDurationNights($package);
            if ($durationNights !== null) {
                DB::table('tour_packages')->where('id', $package->id)->update([
                    'duration_nights' => $durationNights,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tour_bookings')->update(['tour_departure_id' => null, 'price_category_id' => null]);
        DB::table('departure_prices')->truncate();
        DB::table('tour_departures')->truncate();
        DB::table('price_categories')->where('name', 'Standard')->delete();
    }

    /**
     * tour_packages.tour_start carries an `ON UPDATE current_timestamp()` column
     * attribute in the live schema, so any unrelated edit to the row silently
     * overwrites it with the edit time. Don't trust it if it shows that signature.
     */
    private function resolveStartDate(object $package): string
    {
        $fallback = now()->addDays(30)->toDateString();

        if (empty($package->tour_start) || !$this->tourStartIsTrustworthy($package)) {
            return $fallback;
        }

        try {
            return Carbon::parse($package->tour_start)->toDateString();
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    /**
     * Derived only when tour_start survived the corruption check above and
     * tour_end parses cleanly; otherwise left null for manual entry via the
     * new departures UI.
     */
    private function resolveDurationNights(object $package): ?int
    {
        if (empty($package->tour_start) || empty($package->tour_end) || !$this->tourStartIsTrustworthy($package)) {
            return null;
        }

        try {
            $nights = Carbon::parse($package->tour_start)->diffInDays(Carbon::parse($package->tour_end));
        } catch (\Throwable $e) {
            return null;
        }

        return $nights > 0 ? $nights : null;
    }

    private function tourStartIsTrustworthy(object $package): bool
    {
        if (empty($package->tour_start)) {
            return false;
        }

        if (empty($package->updated_at)) {
            return true;
        }

        try {
            return !Carbon::parse($package->tour_start)->eq(Carbon::parse($package->updated_at));
        } catch (\Throwable $e) {
            return false;
        }
    }
};
