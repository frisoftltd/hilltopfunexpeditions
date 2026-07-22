<?php

namespace App\Traits;

use App\Models\DeparturePrice;
use App\Models\PriceCategory;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Shared by Admin\TourDepartureController and Agency\TourDepartureController.
 * Each caller is responsible for resolving the TourPackage/TourDeparture with
 * whatever ownership scoping applies to that guard before calling in here.
 */
trait TourDepartureService
{
    protected function storeDeparture(Request $request, TourPackage $tourPackage)
    {
        $categories = PriceCategory::active()->get();

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'seats_total' => 'required|integer|min:1',
            'prices' => 'required|array',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $departure = new TourDeparture();
            $departure->tour_package_id = $tourPackage->id;
            $departure->start_date = $request->start_date;
            $departure->seats_total = $request->seats_total;
            $departure->status = 1;
            $departure->save();

            $this->savePrices($departure, $request->prices, $categories);

            DB::commit();
            $notify[] = ['success', 'Departure added successfully'];
        } catch (\Exception $e) {
            DB::rollBack();
            $notify[] = ['error', 'Something went wrong'];
        }

        return back()->withNotify($notify);
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
                ['price' => $data['price'], 'discount' => $data['discount'] ?? null]
            );
        }
    }
}
