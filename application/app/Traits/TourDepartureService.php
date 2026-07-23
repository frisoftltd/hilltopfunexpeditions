<?php

namespace App\Traits;

use App\Models\DeparturePrice;
use App\Models\PriceCategory;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Shared by Admin\TourDepartureController and Agency\TourDepartureController.
 * Each caller is responsible for resolving the TourPackage/TourDeparture with
 * whatever ownership scoping applies to that guard before calling in here.
 */
trait TourDepartureService
{
    protected function storeDeparture(Request $request, TourPackage $tourPackage)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'seats_total' => 'required|integer|min:1',
            'prices' => 'required|array',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $this->createDeparture($tourPackage, [
                'start_date' => $request->start_date,
                'seats_total' => $request->seats_total,
                'prices' => $request->prices,
            ]);

            DB::commit();
            $notify[] = ['success', 'Departure added successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Departure store failed: ' . $e->getMessage(), ['exception' => $e]);
            $notify[] = ['error', 'Something went wrong'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Create one or more new departures (with per-category prices) for a
     * package from an already-validated array - used when departures are
     * staged alongside the rest of the package form (create/edit) instead of
     * submitted one at a time via storeDeparture(). The caller owns the
     * surrounding transaction.
     *
     * @param array<int, array{start_date: string, seats_total: int, prices: array}> $departuresData
     */
    protected function createDeparturesForPackage(TourPackage $tourPackage, array $departuresData): void
    {
        foreach ($departuresData as $data) {
            $this->createDeparture($tourPackage, $data);
        }
    }

    private function createDeparture(TourPackage $tourPackage, array $data): TourDeparture
    {
        $categories = PriceCategory::active()->get();

        $departure = new TourDeparture();
        $departure->tour_package_id = $tourPackage->id;
        $departure->start_date = $data['start_date'];
        $departure->seats_total = $data['seats_total'];
        $departure->status = 1;
        $departure->save();

        $this->savePrices($departure, $data['prices'] ?? [], $categories);

        return $departure;
    }

    protected function updateDeparture(Request $request, TourDeparture $departure)
    {
        $categories = PriceCategory::active()->get();

        $request->validate([
            'start_date' => 'required|date',
            'seats_total' => 'required|integer|min:' . max(1, $departure->seats_booked),
            'prices' => 'required|array',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $departure->start_date = $request->start_date;
            $departure->seats_total = $request->seats_total;
            $departure->save();

            $this->savePrices($departure, $request->prices, $categories);

            DB::commit();
            $notify[] = ['success', 'Departure updated successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Departure update failed: ' . $e->getMessage(), ['exception' => $e]);
            $notify[] = ['error', 'Something went wrong'];
        }

        return back()->withNotify($notify);
    }

    protected function destroyDeparture(TourDeparture $departure)
    {
        if ($departure->bookings()->exists()) {
            $notify[] = ['error', 'This departure already has bookings and cannot be deleted'];
            return back()->withNotify($notify);
        }

        $departure->departurePrices()->delete();
        $departure->delete();

        $notify[] = ['success', 'Departure deleted successfully'];
        return back()->withNotify($notify);
    }

    private function savePrices(TourDeparture $departure, array $prices, $categories): void
    {
        foreach ($prices as $categoryId => $data) {
            if (!$categories->contains('id', (int) $categoryId)) {
                continue;
            }

            DeparturePrice::updateOrCreate(
                ['tour_departure_id' => $departure->id, 'price_category_id' => $categoryId],
                [
                    'price' => $this->nullIfBlank($data['price'] ?? null),
                    'discount' => $this->nullIfBlank($data['discount'] ?? null),
                ]
            );
        }
    }

    /**
     * An empty ('') form field is present-but-blank, not absent - `?? null`
     * doesn't catch it. Left uncorrected, MySQL strict mode rejects '' for a
     * numeric column outright (this is what "something went wrong" was
     * hiding for a blank discount/price). !== '' rather than empty() so a
     * legitimate 0 is preserved.
     */
    private function nullIfBlank($value)
    {
        return ($value === null || $value === '') ? null : $value;
    }
}
