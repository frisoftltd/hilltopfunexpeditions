<?php

namespace App\Http\Requests;

use App\Rules\FileTypeValidate;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      
        $rules =
            [
                'user_id' => 'required|numeric',
                'user_type' => ['required','in:admin,agency'],
                'category_id' => 'required|exists:categories,id',
                'tour_title' => 'required|string',
                'address' => 'nullable|string',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
                'country' => 'nullable|string',
                'city' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'state' => 'nullable|string',
                'day_nights' => 'required|string',
                'duration_nights' => 'required|integer|min:1',
                'description' => 'required|string',
                'destination_overview' => 'required|array',
                'destination_overview.*' => 'required',
                'highlights' => 'required|array',
                'highlights.*' => 'required',
                'icons' => 'required|array|min:1',
                'icons.*' => 'required',
                'features' => 'required|array|min:1',
                'features.*' => 'required',
                'exclusion_icons' => 'nullable|array',
                'exclusion_icons.*' => 'required',
                'exclusions' => 'nullable|array',
                'exclusions.*' => 'required',
                'images' => 'required|array|min:1',
                'images.*' => ['max:3072','image', new FileTypeValidate(['jpg','jpeg','png','JPG','JPEG','PNG'])],

                // Trip attributes
                'group_size_min' => 'nullable|integer|min:1',
                'group_size_max' => 'nullable|integer|min:1|gte:group_size_min',
                'guide_language' => 'nullable|string|max:190',
                'age_range_min' => 'nullable|integer|min:0',
                'age_range_max' => 'nullable|integer|min:0|gte:age_range_min',
                'intensity' => 'nullable|integer|in:1,2,3,4,5',

                // Day-by-day itinerary
                'itinerary' => 'nullable|array',
                'itinerary.*.day' => 'required_with:itinerary|integer|min:1',
                'itinerary.*.title' => 'required_with:itinerary|string',
                'itinerary.*.description' => 'nullable|string',

                // Per-category prices - tours have no fixed departures, so
                // this is the package's only pricing (one row per active
                // PriceCategory, keyed by category id)
                'prices' => 'nullable|array',
                'prices.*.price' => 'required|numeric|min:0',
                'prices.*.discount' => 'nullable|numeric|min:0|max:100',

            ];
        if ($this->method() == "PUT" && request()->old_tour_package_images) {
            $rules['images'] = 'nullable|array';
            $rules['images.*'] = ['nullable', 'max:3072', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'])];
        }

        return $rules;
    }
}
