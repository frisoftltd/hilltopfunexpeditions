<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\TourPackage;
use App\Models\TourPackageImage;
use Illuminate\Support\Facades\DB;
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
            if (!($request->latitude && $request->longitude)) {
                $notify[] = ['error', 'Please location select Perfectly'];
                return back()->withNotify($notify);
            }
            $startDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->start_date);
            $endDate   = Carbon::createFromFormat('m/d/Y , h:i a', $request->end_date);

            if ($startDate->lt(now())) {
                $notify[] = ['error', 'Start date must be today or a future date.'];
                return back()->withNotify($notify);
            }
            if (!$endDate->gt($startDate)) {
                $notify[] = ['error', 'End date must be greater than start date.'];
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

            $tourPackage = new TourPackage();
            $purifier = new \HTMLPurifier();
            $tourPackage->user_id = $request->user_id;
            $tourPackage->user_type = $request->user_type;
            $tourPackage->title = $request->tour_title;
            $tourPackage->address = $request->address;
            $tourPackage->description = $purifier->purify($request->description);
            $tourPackage->price = $request->price;
            $tourPackage->discount = $request->discount;
            $tourPackage->day_nights = $request->day_nights;
            $tourPackage->person_capability = $request->person_capability;
            $tourPackage->flexible_date = $request->flexible_date;
            $tourPackage->tour_start = $startDate->toDateTimeString();
            $tourPackage->tour_end = $endDate->toDateTimeString();
            $tourPackage->category_id = $request->category_id;
            $tourPackage->latitude = $request->latitude;
            $tourPackage->longitude = $request->longitude;
            $tourPackage->city = $request->city;
            $tourPackage->state = $request->state;
            $tourPackage->country = $request->country;
            $tourPackage->zip_code = $request->zipcode;
            $tourPackage->features = $fullArray;
            $tourPackage->destination_overview = str_replace('"', "'", ($request->destination_overview));
            $tourPackage->highlights = $request->highlights;
            
            $tourPackage->status = 1;

            $tourPackage->save();

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
            $notify[] = ['success', 'something went wrong'];
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
        if (!($request->latitude && $request->longitude)) {
            $notify[] = ['error', 'Please location select Perfectly'];
            return back()->withNotify($notify);
        }
        $startDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->start_date);
        $endDate   = Carbon::createFromFormat('m/d/Y , h:i a', $request->end_date);
        if (!$endDate->gt($startDate)) {
            $notify[] = ['error', 'End date must be greater than start date.'];
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

        $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);
        $purifier = new \HTMLPurifier();
        $tourPackage->title = $request->tour_title;
        $tourPackage->address = $request->address;
        $tourPackage->description = $purifier->purify($request->description);
        $tourPackage->price = $request->price;
        $tourPackage->discount = $request->discount;
        $tourPackage->day_nights = $request->day_nights;
        $tourPackage->person_capability = $request->person_capability;
        $tourPackage->flexible_date = $request->flexible_date;
        $tourPackage->tour_start = $startDate->toDateTimeString();
        $tourPackage->tour_end = $endDate->toDateTimeString();
        $tourPackage->category_id = $request->category_id;
        $tourPackage->latitude = $request->latitude;
        $tourPackage->longitude = $request->longitude;
        $tourPackage->city = $request->city;
        $tourPackage->state = $request->state;
        $tourPackage->country = $request->country;
        $tourPackage->zip_code = $request->zipcode;
        $tourPackage->features = $fullArray;
        $tourPackage->destination_overview = str_replace('"', "'", ($request->destination_overview));
        $tourPackage->highlights = $request->highlights;

        $tourPackage->save();

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
             $notify[] = ['success', 'something went wrong'];

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
            $notify[] = ['success', 'something went wrong'];
            return back()->withNotify($notify);
        }
    }
}
