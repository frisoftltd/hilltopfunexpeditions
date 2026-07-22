<?php

namespace App\Traits;

use App\Models\TourPackage;
use App\Models\TourPackageImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\TourPackageRequest;


trait TourService
{
    use TourDepartureService;

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
            $tourPackage->group_size_min = $request->group_size_min;
            $tourPackage->group_size_max = $request->group_size_max;
            $tourPackage->guide_language = $request->guide_language;
            $tourPackage->age_range_min = $request->age_range_min;
            $tourPackage->age_range_max = $request->age_range_max;
            $tourPackage->intensity = $request->intensity;

            $tourPackage->status = 1;

            $tourPackage->save();

            $this->createDeparturesForPackage($tourPackage, $request->departures ?? []);

            if ($request->hasFile('images')) {

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
            }
            DB::commit();
            $notify[] = ['success', 'Tour Package created successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
            $notify[] = ['error', 'something went wrong'];
        }

        return back()->withNotify($notify);
    }


    public function update(TourPackageRequest $request, $id)
    {
        $tourPackage =  TourPackage::with('category')->where('id', $id)->first();
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

        $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);
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
        $tourPackage->group_size_min = $request->group_size_min;
        $tourPackage->group_size_max = $request->group_size_max;
        $tourPackage->guide_language = $request->guide_language;
        $tourPackage->age_range_min = $request->age_range_min;
        $tourPackage->age_range_max = $request->age_range_max;
        $tourPackage->intensity = $request->intensity;

        $tourPackage->save();

        $this->createDeparturesForPackage($tourPackage, $request->departures ?? []);

        if ($request->hasFile('images')) {
            foreach ($request->images as $index => $img) {
                $tourPackageImage = new TourPackageImage();
                $tourPackageImage->tour_package_id = $tourPackage->id;
                $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'));
                $tourPackageImage->save();
            }
        }

        DB::commit();
        $notify[] = ['success', 'Tour Package updated successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
             $notify[] = ['error', 'something went wrong'];

        }
        return back()->withNotify($notify);
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
            $notify[] = ['error', 'Couldn\'t delete your image'];
            return back()->withNotify($notify);
        }
    }

    public function delete($id){
      
        try {
            $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);
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
            $notify[] = ['error', 'something went wrong'];
            return back()->withNotify($notify);
        }
    }
}
