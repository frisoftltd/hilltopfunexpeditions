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
                'images' => 'required|array|min:1',
                'images.*' => ['max:3072','image', new FileTypeValidate(['jpg','jpeg','png','JPG','JPEG','PNG'])]

            ];
        if ($this->method() == "PUT" && request()->old_tour_package_images) {
            $rules['images'] = 'nullable|array';
            $rules['images.*'] = ['nullable', 'max:3072', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'])];
        }

        return $rules;
    }
}
