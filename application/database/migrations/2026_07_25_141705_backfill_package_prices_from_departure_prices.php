<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * package_prices is one row per (package, category); departure_prices was
 * one row per (departure, category). Where a package's departures all agree
 * on the price for a category, that's an unambiguous copy. Where they don't
 * (a category priced differently on two staged departures), there's no
 * correct value to pick automatically - log it and leave that category
 * unpriced for manual entry via the package edit form instead of guessing.
 */
return new class extends Migration
{
    public function up(): void
    {
        $rowsByPackageAndCategory = DB::table('departure_prices')
            ->join('tour_departures', 'tour_departures.id', '=', 'departure_prices.tour_departure_id')
            ->select(
                'tour_departures.tour_package_id',
                'departure_prices.price_category_id',
                'departure_prices.price',
                'departure_prices.discount'
            )
            ->get()
            ->groupBy(fn ($row) => $row->tour_package_id . ':' . $row->price_category_id);

        $now = now();

        foreach ($rowsByPackageAndCategory as $rows) {
            $distinctPrices = $rows->pluck('price')->map(fn ($price) => (string) $price)->unique();
            $distinctDiscounts = $rows->pluck('discount')->map(fn ($discount) => $discount === null ? null : (string) $discount)->unique();

            if ($distinctPrices->count() > 1 || $distinctDiscounts->count() > 1) {
                Log::warning('package_prices backfill: conflicting departure prices for the same package/category - skipped, needs manual entry', [
                    'tour_package_id' => $rows->first()->tour_package_id,
                    'price_category_id' => $rows->first()->price_category_id,
                    'prices_seen' => $distinctPrices->values()->all(),
                    'discounts_seen' => $distinctDiscounts->values()->all(),
                ]);

                continue;
            }

            $row = $rows->first();

            DB::table('package_prices')->updateOrInsert(
                [
                    'tour_package_id' => $row->tour_package_id,
                    'price_category_id' => $row->price_category_id,
                ],
                [
                    'price' => $row->price,
                    'discount' => $row->discount,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('package_prices')->truncate();
    }
};
