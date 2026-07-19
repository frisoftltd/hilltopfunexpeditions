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
                'flexible_date' => ['required', 'integer', 'in:1,2'],
                'tour_title' => 'required|string',
                'address' => 'nullable|string',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
                'country' => 'nullable|string',
                'city' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'state' => 'nullable|string',
                'start_date' => ['required','string'],
                'end_date'   => ['required','string'],
                'person_capability' => 'required|string',
                'day_nights' => 'required|string',
                'price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'discount' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
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
