<?php

namespace App\Traits;

use App\Models\PackagePrice;
use App\Models\PriceCategory;
use App\Models\TourPackage;
use App\Models\TourPackageImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\TourPackageRequest;


trait TourService
{
    protected $data;

    public function store(TourPackageRequest $request)
    {

  
        DB::beginTransaction();
        $request->merge([
            'country'   => $request->country   ?: 'Rwanda',
            'address'   => $request->address   ?: 'Kigali, Rwanda',
            'latitude'  => $request->latitude  ?: '-1.9441',
            'longitude' => $request->longitude ?: '30.0619',
        ]);
        try {
            if (count($request->features) != count($request->icons)) {
                $notify[] = ['error', 'Some data are missing'];
                return back()->withNotify($notify);
            }
            if (count($request->exclusions ?? []) != count($request->exclusion_icons ?? [])) {
                $notify[] = ['error', 'Some data are missing'];
                return back()->withNotify($notify);
            }
            if (!($request->latitude && $request->longitude)) {
                $notify[] = ['error', 'Please location select Perfectly'];
                return back()->withNotify($notify);
            }
            $fullArray = array_map(
                fn($icon, $feature) => [
                    'icon'    => $icon,
                    'feature' => $feature,
                ],
                $request->icons,
                $request->features
            );
            $exclusionsArray = array_map(
                fn($icon, $feature) => [
                    'icon'    => $icon,
                    'feature' => $feature,
                ],
                $request->exclusion_icons ?? [],
                $request->exclusions ?? []
            );

            $tourPackage = new TourPackage();
            $purifier = new \HTMLPurifier();
            $tourPackage->user_id = $request->user_id;
            $tourPackage->user_type = $request->user_type;
            $tourPackage->title = $request->tour_title;
            $tourPackage->address = $request->address;
            $tourPackage->description = $purifier->purify($request->description);
            $tourPackage->day_nights = $request->day_nights;
            $tourPackage->duration_nights = $request->duration_nights;
            $tourPackage->category_id = $request->category_id;
            $tourPackage->latitude = $request->latitude;
            $tourPackage->longitude = $request->longitude;
            $tourPackage->city = $request->city;
            $tourPackage->state = $request->state;
            $tourPackage->country = $request->country;
            $tourPackage->zip_code = $request->zipcode;
            $tourPackage->features = $fullArray;
            $tourPackage->exclusions = $exclusionsArray;
            $tourPackage->destination_overview = str_replace('"', "'", ($request->destination_overview));
            $tourPackage->highlights = $request->highlights;
            $tourPackage->itinerary = $request->itinerary ?? [];
            $tourPackage->group_size_min = $request->filled('group_size_min') ? $request->group_size_min : null;
            $tourPackage->group_size_max = $request->filled('group_size_max') ? $request->group_size_max : null;
            $tourPackage->guide_language = $request->guide_language;
            $tourPackage->age_range_min = $request->filled('age_range_min') ? $request->age_range_min : null;
            $tourPackage->age_range_max = $request->filled('age_range_max') ? $request->age_range_max : null;
            $tourPackage->intensity = $request->filled('intensity') ? $request->intensity : null;

            $tourPackage->status = 1;

            $tourPackage->save();

            $this->savePackagePrices($tourPackage, $request->prices ?? [], PriceCategory::active()->get());

            DB::commit();
            $notify[] = ['success', 'Tour Package created successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
            Log::error('Tour package store failed: ' . $exp->getMessage(), ['exception' => $exp]);
            $notify[] = ['error', 'something went wrong'];
            return back()->withNotify($notify);
        }

        //image resizing/disk I/O runs after the transaction has already
        //committed, so it doesn't hold DB row locks for the duration
        if ($request->hasFile('images')) {
            try {
                foreach ($request->images as $index => $img) {
                    $tourPackageImage = new TourPackageImage();
                    $tourPackageImage->tour_package_id = $tourPackage->id;
                    if ($index === 0) {
                        $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'), '', "365x230");
                    } else {
                        $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'));
                    }
                    $tourPackageImage->save();
                }
            } catch (\Exception $exp) {
                Log::error('Tour package image upload failed: ' . $exp->getMessage(), ['exception' => $exp]);
                $notify[] = ['error', 'Tour package saved, but one or more images failed to upload'];
            }
        }

        return back()->withNotify($notify);
    }


    public function update(TourPackageRequest $request, $id)
    {
        // Fetched again below (unconditionally, every call) with a
        // different eager-load that also goes unused in this method -
        // neither relation is actually read here, so one plain lookup
        // covers both the existence check and the update itself.
        $tourPackage = TourPackage::find($id);
        if(!$tourPackage){
            $notify[] = ['error', 'Your tour package id is not valid'];
            return back()->withNotify($notify);
        }

        DB::beginTransaction();
        $request->merge([
            'country'   => $request->country   ?: 'Rwanda',
            'address'   => $request->address   ?: 'Kigali, Rwanda',
            'latitude'  => $request->latitude  ?: '-1.9441',
            'longitude' => $request->longitude ?: '30.0619',
        ]);
        try {
        if (count($request->features) != count($request->icons)) {
            $notify[] = ['error', 'Some data are missing'];
            return back()->withNotify($notify);
        }
        if (count($request->exclusions ?? []) != count($request->exclusion_icons ?? [])) {
            $notify[] = ['error', 'Some data are missing'];
            return back()->withNotify($notify);
        }
        if (!($request->latitude && $request->longitude)) {
            $notify[] = ['error', 'Please location select Perfectly'];
            return back()->withNotify($notify);
        }
        $fullArray = array_map(
            fn($icon, $feature) => [
                'icon'    => $icon,
                'feature' => $feature,
            ],
            $request->icons,
            $request->features
        );
        $exclusionsArray = array_map(
            fn($icon, $feature) => [
                'icon'    => $icon,
                'feature' => $feature,
            ],
            $request->exclusion_icons ?? [],
            $request->exclusions ?? []
        );

        $purifier = new \HTMLPurifier();
        $tourPackage->title = $request->tour_title;
        $tourPackage->address = $request->address;
        $tourPackage->description = $purifier->purify($request->description);
        $tourPackage->day_nights = $request->day_nights;
        $tourPackage->duration_nights = $request->duration_nights;
        $tourPackage->category_id = $request->category_id;
        $tourPackage->latitude = $request->latitude;
        $tourPackage->longitude = $request->longitude;
        $tourPackage->city = $request->city;
        $tourPackage->state = $request->state;
        $tourPackage->country = $request->country;
        $tourPackage->zip_code = $request->zipcode;
        $tourPackage->features = $fullArray;
        $tourPackage->exclusions = $exclusionsArray;
        $tourPackage->destination_overview = str_replace('"', "'", ($request->destination_overview));
        $tourPackage->highlights = $request->highlights;
        $tourPackage->itinerary = $request->itinerary ?? [];
        $tourPackage->group_size_min = $request->filled('group_size_min') ? $request->group_size_min : null;
        $tourPackage->group_size_max = $request->filled('group_size_max') ? $request->group_size_max : null;
        $tourPackage->guide_language = $request->guide_language;
        $tourPackage->age_range_min = $request->filled('age_range_min') ? $request->age_range_min : null;
        $tourPackage->age_range_max = $request->filled('age_range_max') ? $request->age_range_max : null;
        $tourPackage->intensity = $request->filled('intensity') ? $request->intensity : null;

        $tourPackage->save();

        $this->savePackagePrices($tourPackage, $request->prices ?? [], PriceCategory::active()->get());

        DB::commit();
        $notify[] = ['success', 'Tour Package updated successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
            Log::error('Tour package update failed: ' . $exp->getMessage(), ['exception' => $exp]);
             $notify[] = ['error', 'something went wrong'];
            return back()->withNotify($notify);
        }

        //image resizing/disk I/O runs after the transaction has already
        //committed, so it doesn't hold DB row locks for the duration
        if ($request->hasFile('images')) {
            try {
                foreach ($request->images as $index => $img) {
                    $tourPackageImage = new TourPackageImage();
                    $tourPackageImage->tour_package_id = $tourPackage->id;
                    $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'));
                    $tourPackageImage->save();
                }
            } catch (\Exception $exp) {
                Log::error('Tour package image upload failed: ' . $exp->getMessage(), ['exception' => $exp]);
                $notify[] = ['error', 'Tour package saved, but one or more images failed to upload'];
            }
        }

        return back()->withNotify($notify);
    }


    private function savePackagePrices(TourPackage $tourPackage, array $prices, $categories): void
    {
        foreach ($prices as $categoryId => $data) {
            if (!$categories->contains('id', (int) $categoryId)) {
                continue;
            }

            PackagePrice::updateOrCreate(
                ['tour_package_id' => $tourPackage->id, 'price_category_id' => $categoryId],
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
     * numeric column outright.
     */
    private function nullIfBlank($value)
    {
        return ($value === null || $value === '') ? null : $value;
    }

    public function tourPackageImageDelete(Request $request)
    {
        try {
            $tourPackageImage = TourPackageImage::findOrFail($request->id);
            fileManager()->removeFile(getFilePath('tourPackageImage') . '/' . $tourPackageImage->image);
            if (file_exists(getFilePath('tourPackageImage') . '/thumb_' . $tourPackageImage->image)) {
                fileManager()->removeFile(getFilePath('tourPackageImage') . '/thumb_' . $tourPackageImage->image);
            }
            $tourPackageImage->delete();
            $data = [
                'status' => "success",
                'message' => "image delete successfully",
            ];
            return response()->json($data);
        } catch (\Exception $exp) {
            Log::error('Tour package image delete failed: ' . $exp->getMessage(), ['exception' => $exp]);
            $notify[] = ['error', 'Couldn\'t delete your image'];
            return back()->withNotify($notify);
        }
    }

    public function delete($id){
      
        try {
            $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);

            $hasUpcomingBookings = $tourPackage->tour_bookings()
                ->where('status', '!=', 3)
                ->where(function ($query) {
                    $query->whereNull('start_date')->orWhereDate('start_date', '>=', now()->toDateString());
                })
                ->exists();

            if ($hasUpcomingBookings) {
                $notify[] = ['error', 'Cannot delete: this package has upcoming bookings.'];
                return back()->withNotify($notify);
            }

            foreach($tourPackage->tour_package_images ?? [] as $item){
                fileManager()->removeFile(getFilePath('tourPackageImage') . '/' . $item->image);
                if (file_exists(getFilePath('tourPackageImage') . '/thumb_' . $item->image)) {
                    fileManager()->removeFile(getFilePath('tourPackageImage') . '/thumb_' . $item->image);
                }
                $item->delete();
            }
            $tourPackage->delete();
            $notify[] = ['success', 'Tour Package delete successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $exp) {
            Log::error('Tour package delete failed: ' . $exp->getMessage(), ['exception' => $exp]);
            $notify[] = ['error', 'something went wrong'];
            return back()->withNotify($notify);
        }
    }
}
