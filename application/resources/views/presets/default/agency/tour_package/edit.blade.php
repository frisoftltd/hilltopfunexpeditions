@extends($activeTemplate . 'layouts.agency.master')
@php
    $highlightsList = old('highlights', $tourPackage->highlights ?: ['']);
    $featuresList = old('features', collect($tourPackage->features ?? [])->pluck('feature')->all() ?: ['']);
    $iconsList = old('icons', collect($tourPackage->features ?? [])->pluck('icon')->all() ?: ['']);
    $exclusionsList = old('exclusions', $tourPackage->exclusions ? collect($tourPackage->exclusions)->pluck('feature')->all() : []);
    $exclusionIconsList = old('exclusion_icons', $tourPackage->exclusions ? collect($tourPackage->exclusions)->pluck('icon')->all() : []);
    $itineraryList = old('itinerary', $tourPackage->itinerary ?? []);
    $itineraryNextIndex = empty($itineraryList) ? 0 : max(array_keys($itineraryList)) + 1;
@endphp
@section('content')
    <div class="row mb-3">
        <div class="col-lg-12">
            <a href="{{ route('agency.tour.package.my.list') }}" class="btn btn-md btn--base pills">
                <i class="la la-arrow-left"></i> @lang('Back to Tour Package List')
            </a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="base--card">
                <form id="tourPackageForm" action="{{ route('agency.tour.package.update', $tourPackage->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="mb-5">
                        <h5 class="">@lang('Basic Information')</h5>

                        <div class="row d-none">

                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" name="user_id" class="form-control form--control" hidden
                                        value="{{ auth('agency')->id() }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" name="user_type" class="form-control form--control" hidden
                                        value="agency">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="lat" name="latitude" class="form-control form--control"
                                        hidden value="{{ old('latitude', $tourPackage->latitude) }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <input type="text" id="lon" name="longitude" class="form-control form--control"
                                        value="{{ old('longitude', $tourPackage->longitude) }}" hidden>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Title')</label>
                                    <input type="text" name="tour_title" class="form-control form--control"
                                        placeholder="@lang('Title')" value="{{ old('tour_title', $tourPackage->title) }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Location')</label>
                                    <select id="locationSelect" class="form--select form--control">
                                        <option value="">@lang('Custom (type address below)')</option>
                                        @foreach ($locations as $loc)
                                            <option value="{{ $loc->name }}"
                                                data-address="{{ $loc->location ?: $loc->name }}"
                                                data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}">
                                                {{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">@lang('Fills the address below - still editable.')</small>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="location">@lang('Address')</label>
                                    <input type="text" id="location" name="address" class="form-control form--control"
                                        placeholder="@lang('Full address')"
                                        value="{{ old('address', $tourPackage->address) }}" required>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Category')</label>
                                    <select name="category_id" id="status" class="form--select form--control" required>
                                        <option>@lang('Select category')</option>
                                        @foreach ($categories ?? [] as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == old('category_id', $tourPackage->category_id) ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('City')</label>
                                    <input type="text" name="city" class="form-control form--control"
                                        value="{{ old('city', $tourPackage->city) }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('State')</label>
                                    <input type="text" name="state" class="form-control form--control"
                                        value="{{ old('state', $tourPackage->state) }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Country')</label>
                                    <input type="text" name="country" class="form-control form--control"
                                        value="{{ old('country', $tourPackage->country) }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Zip Code')</label>
                                    <input type="text" name="zipcode" class="form-control form--control"
                                        value="{{ old('zipcode', $tourPackage->zip_code) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Stay day & nights')</label>
                                    <input type="text" step="any" name="day_nights"
                                        class="form-control form--control" placeholder="@lang('3 day & 2 nights')"
                                        value="{{ $tourPackage->day_nights }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Duration (nights)')</label>
                                    <input type="number" min="1" name="duration_nights"
                                        class="form-control form--control" placeholder="@lang('e.g. 4')"
                                        value="{{ $tourPackage->duration_nights }}" required>
                                    <small class="text-muted">@lang('Used to compute the tourist\'s trip end date at booking time.')</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Group Size Min')</label>
                                    <input type="number" min="1" name="group_size_min"
                                        class="form-control form--control" value="{{ $tourPackage->group_size_min }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Group Size Max')</label>
                                    <input type="number" min="1" name="group_size_max"
                                        class="form-control form--control" value="{{ $tourPackage->group_size_max }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Guided In (Language)')</label>
                                    <input type="text" name="guide_language" class="form-control form--control"
                                        placeholder="@lang('e.g. English')" value="{{ $tourPackage->guide_language }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Intensity')</label>
                                    <select name="intensity" class="form--select form--control">
                                        <option value="">@lang('Not set')</option>
                                        @foreach (\App\Models\TourPackage::INTENSITY_LABELS as $value => $label)
                                            <option value="{{ $value }}" {{ $tourPackage->intensity == $value ? 'selected' : '' }}>
                                                {{ __($label) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Age Range Min')</label>
                                    <input type="number" min="0" name="age_range_min"
                                        class="form-control form--control" value="{{ $tourPackage->age_range_min }}">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Age Range Max')</label>
                                    <input type="number" min="0" name="age_range_max"
                                        class="form-control form--control" value="{{ $tourPackage->age_range_max }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="images" class="form--label">@lang('Images')</label>
                                    <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg"
                                        multiple class="form-control form--control">
                                </div>
                                <div class="form-group">
                                    <div id="image_preview" class="image_preview-wrapper">
                                        @foreach ($tourPackage->tour_package_images as $i => $img)
                                            <div class='img-div' id='img-div{{ $i }}'
                                                @if ($i != 0) onclick=imageDelete(this,{{ $img->id }}); @endif>

                                                <input type="hidden" name="old_tour_package_images[]"
                                                    value="{{ $img->id }}">
                                                <img src="{{ getImage(getFilePath('tourPackageImage') . '/' . $img->image) }}"
                                                    class='img-responsive image img-thumbnail'
                                                    title='{{ $img->image }}' alt="tour-image">
                                                @if ($i != 0)
                                                    <div class='middle'>
                                                        <button id='action-icon' value='img-div{{ $i }}'
                                                            class='delete-btn btn btn--danger'
                                                            role='{{ $img->image }}'>
                                                            <i class='fa fa-trash'></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="mb-2 form--label">@lang('Description')</label>
                                    <textarea name="description" class="trumEdit1 form--control" placeholder="@lang('Description')">{{ $tourPackage->description }}</textarea>

                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="mb-5">
                        <div class="mb-3">
                            <h5>@lang('Destination Overview')</h5>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Departure from')</label>
                                    <input type="text" name="destination_overview[departure_form]" id="departure_form"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->departure_form }}"
                                        placeholder="@lang('Departure from')" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Arrival')</label>
                                    <input type="text" name="destination_overview[arrival]" id="arrival"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->arrival }}"
                                        placeholder="@lang('Arrival')" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Transportation')</label>
                                    <input type="text" name="destination_overview[transportation]" id="transportation"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->transportation }}"
                                        placeholder="@lang('Transportation')" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="file-upload">
                                    <label class="mb-2 form--label">@lang('Accommodation')</label>
                                    <input type="text" name="destination_overview[accommodation]" id="accommodation"
                                        class="form-control form--control mb-0" required
                                        value="{{ $tourPackage->destination_overview->accommodation }}"
                                        placeholder="@lang('Accommodation')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">@lang('Day-by-Day Itinerary')</h5>
                            <button type="button" class="btn btn--base btn-md pill addItineraryDay">
                                <i class="fa fa-plus"></i> @lang('Add Day')
                            </button>
                        </div>
                        <div id="itineraryContainer">
                            @foreach ($itineraryList as $index => $day)
                                @php $day = (object) $day; @endphp
                                <div class="row align-items-start itinerary-day mb-3 pb-3 border-bottom">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Day')</label>
                                            <input type="number" min="1" class="form-control form--control"
                                                name="itinerary[{{ $index }}][day]" value="{{ $day->day ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Title')</label>
                                            <input type="text" class="form-control form--control"
                                                name="itinerary[{{ $index }}][title]" value="{{ $day->title ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Description')</label>
                                            <textarea class="form-control form--control" rows="2"
                                                name="itinerary[{{ $index }}][description]">{{ $day->description ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pt-4">
                                        <button type="button" class="btn btn--danger btn-sm remove-itinerary-day"><i
                                                class="la la-trash"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted mb-0" id="noItineraryDays" @if (count($itineraryList)) style="display:none" @endif>
                            @lang('No itinerary days added yet.')</p>
                    </div>

                    <div class="mb-5">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between mb-3">
                                        <h5 class="mb-0">@lang('Destination Information')</h5>

                                        <button type="button" class="btn btn btn--base btn-md pill addHighlights">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div id="fileUploadsContainer">
                                        @foreach ($highlightsList as $item)
                                            <div class="row elements">
                                                <div class="col-sm-12 my-2">
                                                    <div class="file-upload input-group">
                                                        <input type="text" name="highlights[]"
                                                            class="form-control form--control"
                                                            value="{{ $item }}"
                                                            placeholder="@lang('Destination Highlights')" required />
                                                        <button type="button"
                                                            class="input-group-text btn--danger remove-btn border-0"><i
                                                                class="las la-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mt-3">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="">@lang('Destination Features')</h5>

                                        <button type="button" class="btn btn btn--base btn-md pill addFeatures">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div id="fileUploadFeatures">
                                        @foreach ($featuresList as $i => $featureText)
                                            <div class="row mb-4 elements">
                                                <div class="col-sm-4">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Destination icons')</label>
                                                        <div class="file-upload input-group">
                                                            <input type="text" name="icons[]"
                                                                class="form-control form--control iconPicker icon"
                                                                value="{{ $iconsList[$i] ?? '' }}"
                                                                placeholder="@lang('Icons')" required>
                                                            <span
                                                                class="input-group-text input-group-addon bg--base text--white"
                                                                data-icon="las la-home">
                                                                @php echo $iconsList[$i] ?? '' @endphp
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="file-upload input-group">
                                                        <label class="form-label w-100">@lang('Destination Features')</label>
                                                        <input type="text" name="features[]"
                                                            class="form-control form--control mb-0" required
                                                            value="{{ $featureText }}"
                                                            placeholder="@lang('Destination Features')" />
                                                        <button type="button"
                                                            class="input-group-text btn--danger remove-btn border-0"><i
                                                                class="las la-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 mt-3">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="">@lang('Not Included')</h5>

                                        <button type="button" class="btn btn btn--base btn-md pill addExclusions">
                                            <i class="fa fa-plus"></i> @lang('Add New')
                                        </button>
                                    </div>
                                    <div id="fileUploadExclusions">
                                        @foreach ($exclusionsList as $i => $exclusionText)
                                            <div class="row mb-4 elements">
                                                <div class="col-sm-4">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Not Included icon')</label>
                                                        <div class="file-upload input-group">
                                                            <input type="text" name="exclusion_icons[]"
                                                                class="form-control form--control iconPicker icon"
                                                                value="{{ $exclusionIconsList[$i] ?? '' }}"
                                                                placeholder="@lang('Icons')">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="file-upload input-group">
                                                        <label class="form-label w-100">@lang('Not Included')</label>
                                                        <input type="text" name="exclusions[]"
                                                            class="form-control form--control mb-0"
                                                            value="{{ $exclusionText }}"
                                                            placeholder="@lang('e.g. International flights')" />
                                                        <button type="button"
                                                            class="input-group-text btn--danger remove-btn border-0"><i
                                                                class="las la-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-muted mb-0" id="noExclusions" @if (count($exclusionsList)) style="display:none" @endif>
                                        @lang('No exclusions added yet (optional).')</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">@lang('Prices')</h5>
                        </div>
                        <div class="row">
                            @foreach ($priceCategories as $category)
                                @php $existingPrice = $tourPackage->packagePrices->firstWhere('price_category_id', $category->id); @endphp
                                <div class="col-md-6 mb-3">
                                    <label class="mb-1 form--label">{{ $category->name }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0" class="form-control form--control"
                                            name="prices[{{ $category->id }}][price]"
                                            value="{{ old("prices.{$category->id}.price", $existingPrice->price ?? '') }}"
                                            placeholder="@lang('Price')" required>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control form--control"
                                            name="prices[{{ $category->id }}][discount]"
                                            value="{{ old("prices.{$category->id}.discount", $existingPrice->discount ?? '') }}"
                                            placeholder="@lang('Disc %')">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($priceCategories->isEmpty())
                            <p class="text-danger mb-0">@lang('No active price categories yet - ask the site admin to add one if you want to set prices now.')</p>
                        @endif
                    </div>

                    <div class="col-lg-12 text-end mt-3">
                        <button type="submit" class="btn btn--base btn--lg pills">@lang('Update')</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Row template - kept OUTSIDE any form so its unreplaced __INDEX__
         placeholders are never submitted. --}}
    <div id="itineraryDayTemplate" class="d-none">
        <div class="row align-items-start itinerary-day mb-3 pb-3 border-bottom">
            <div class="col-md-2">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Day')</label>
                    <input type="number" min="1" class="form-control form--control" name="itinerary[__INDEX__][day]" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Title')</label>
                    <input type="text" class="form-control form--control" name="itinerary[__INDEX__][title]"
                        placeholder="@lang('e.g. Arrival in Kigali')" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="mb-2 form--label">@lang('Description')</label>
                    <textarea class="form-control form--control" rows="2" name="itinerary[__INDEX__][description]"></textarea>
                </div>
            </div>
            <div class="col-md-1 pt-4">
                <button type="button" class="btn btn--danger btn-sm remove-itinerary-day"><i class="la la-trash"></i></button>
            </div>
        </div>
    </div>

@endsection

@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
    <script src="{{ asset('assets/common/js/ckeditor.js') }}"></script>
@endpush

@push('style')
    <style>
        .ck.ck-editor__main>.ck-editor__editable {
            height: 250px;
        }

        .image_preview-wrapper {
            display: flex;
            flex-wrap: wrap;
        }

        .img-div {
            position: relative;
            width: 150px;
            margin-right: 5px;
            margin-left: 5px;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .image {
            opacity: 1;
            display: block;
            width: 100%;
            max-width: auto;
            transition: .5s ease;
            backface-visibility: hidden;
        }

        .middle {
            transition: .5s ease;
            opacity: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            text-align: center;
        }

        .img-div:hover .image {
            opacity: 0.3;
        }

        .img-div:hover .middle {
            opacity: 1;
        }

        #map {
            height: 400px;
            width: 100%;
        }

        .pac-target-input {
            width: 300px !important;
            margin-top: 40px !important;
            background-color: white !important;
            border: 1px solid hsl(var(--base)) !important;
        }
        
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addHighlights').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsContainer").append(`
                <div class="row elements">
                    <div class="col-sm-12 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="highlights[]" class="form-control form--control"
                                placeholder="@lang('Destination Highlights')" required />  
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>                                          
                        </div>
                    </div>
                </div>
            `)
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addOverview').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadsOverview").append(`
                <div class="row elements">
                    <div class="col-sm-6 my-2">
                             <div class="file-upload input-group">
                            <input type="text" name="title[]" class="form-control form--control"
                                placeholder="@lang('title')" required />                                        
                        </div>
                    </div>

                    <div class="col-sm-6 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="value[]" class="form-control form--control"
                                placeholder="@lang('value')" required />  
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>                                          
                        </div>
                    </div>
                </div>
            `)
                $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                    $(this).closest('.file-upload').find('.iconpicker-input').val(
                        `<i class="${e.iconpickerValue}"></i>`);
                });
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addFeatures').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadFeatures").append(`
                <div class="row elements">
                    <div class="col-sm-4 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="icons[]" id="inputIcon"
                                class="form-control form--control iconPicker icon" placeholder="@lang('Icons')" required>
                            <span class="input-group-text bg--base text--white input-group-addon" data-icon="las la-home"></span>
                        </div>
                    </div>

                    <div class="col-sm-8 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="features[]" class="form-control form--control"
                                placeholder="@lang('Destination Features')" required />  
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>                                          
                        </div>
                    </div>
                </div>
            `)
                $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                    $(this).closest('.file-upload').find('.iconpicker-input').val(
                        `<i class="${e.iconpickerValue}"></i>`);
                });
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });

        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict"
            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.file-upload').find('.iconpicker-input').val(
                    `<i class="${e.iconpickerValue}"></i>`);
            });
        })(jQuery);
    </script>

    <script>
        $(document).ready(function() {
            "use strict";
            var fileArr = [];
            $("#images").on('change', function() {
                // check if fileArr length is greater than 0
                if (fileArr.length > 0) fileArr = [];

                var total_file = document.getElementById("images").files;
                if (!total_file.length) return;
                for (var i = 0; i < total_file.length; i++) {
                    if (total_file[i].size > 1048576) {
                        return false;
                    } else {
                        fileArr.push(total_file[i]);
                        $('#image_preview').append("<div class='img-div' id='img-div" + i + "'><img src='" +
                            URL.createObjectURL(event.target.files[i]) +
                            "' class='img-responsive image img-thumbnail' title='" + total_file[i]
                            .name + "'><div class='middle'><button id='action-icon' value='img-div" +
                            i + "' class='delete-btn btn btn--danger' role='" + total_file[i].name +
                            "'><i class='fa fa-trash'></i></button></div></div>");
                    }
                }
            });

            $('body').on('click', '#action-icon', function(evt) {
                var divName = this.value;
                var fileName = $(this).attr('role');
                $(`#${divName}`).remove();

                for (var i = 0; i < fileArr.length; i++) {
                    if (fileArr[i].name === fileName) {
                        fileArr.splice(i, 1);
                    }
                }
                document.getElementById('images').files = FileListItem(fileArr);
                evt.preventDefault();
            });

            function FileListItem(file) {
                file = [].slice.call(Array.isArray(file) ? file : arguments)
                for (var c, b = c = file.length, d = !0; b-- && d;) d = file[b] instanceof File
                if (!d) throw new TypeError("expected argument to FileList is File or array of File objects")
                for (b = (new ClipboardEvent("")).clipboardData || new DataTransfer; c--;) b.items.add(file[c])
                return b.files
            }
        });
    </script>

    <script>
        (function($) {
            "use strict";
            $('#locationSelect').on('change', function() {
                var selected = $(this).find(':selected');
                var address = selected.data('address');
                if (address) {
                    $('#location').val(address);
                }
                $('#lat').val(selected.data('lat') || '');
                $('#lon').val(selected.data('lng') || '');
            });
        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                "use strict";
                if ($(".trumEdit1")[0]) {
                    ClassicEditor
                        .create(document.querySelector('.trumEdit1'))
                        .then(editor => {
                            window.editor = editor;
                        });
                }
            });
        })(jQuery);
    </script>

    <script>
        "use strict";
        function imageDelete(object, $id) {
            var url = "{{ route('admin.tour.package.image.delete') }}";
            var token = '{{ csrf_token() }}';
            var id = $id;
            var data = {
                id: id,
                _token: token
            }
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                success: function(data) {

                },
                error: function(data, status, error) {
                    $.each(data.responseJSON.errors, function(key, item) {
                        Toast.fire({
                            icon: 'error',
                            title: item
                        })
                    });
                }
            });
        }
    </script>

    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addExclusions').on('click', function() {
                if (fileAdded >= 20) {
                    notify('error', 'You\'ve added maximum number of file');
                    return false;
                }
                fileAdded++;
                $("#fileUploadExclusions").append(`
                <div class="row elements">
                    <div class="col-sm-4 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="exclusion_icons[]" id="inputExclusionIcon"
                                class="form-control form--control iconPicker icon" placeholder="@lang('Icons')">
                        </div>
                    </div>
                    <div class="col-sm-8 my-2">
                        <div class="file-upload input-group">
                            <input type="text" name="exclusions[]" class="form-control form--control"
                                placeholder="@lang('e.g. International flights')" />
                                <button class="input-group-text btn--danger remove-btn border-0"><i class="las la-times"></i></button>
                        </div>
                    </div>
                </div>
            `)
                $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                    $(this).closest('.file-upload').find('.iconpicker-input').val(
                        `<i class="${e.iconpickerValue}"></i>`);
                });
            });
            $(document).on('click', '.remove-btn', function() {
                fileAdded--;
                $(this).closest('.elements').remove();
            });
        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            var itineraryIndex = {{ $itineraryNextIndex }};
            $('.addItineraryDay').on('click', function() {
                var html = $('#itineraryDayTemplate').html().replace(/__INDEX__/g, itineraryIndex);
                $('#itineraryContainer').append(html);
                itineraryIndex++;
                $('#noItineraryDays').hide();
            });
            $(document).on('click', '.remove-itinerary-day', function() {
                $(this).closest('.itinerary-day').remove();
                if ($('#itineraryContainer .itinerary-day').length === 0) {
                    $('#noItineraryDays').show();
                }
            });
        })(jQuery);
    </script>

    <script>
        (function($) {
            "use strict";
            var draftKey = 'travela_draft_agency_tour_edit_{{ $tourPackage->id }}';
            var hasServerErrors = @json($errors->any());
            var $form = $('#tourPackageForm');

            function saveDraft() {
                try {
                    localStorage.setItem(draftKey, JSON.stringify($form.serializeArray()));
                } catch (e) {}
            }

            var saveTimer;
            $form.on('input change', function() {
                clearTimeout(saveTimer);
                saveTimer = setTimeout(saveDraft, 800);
            });

            $form.on('submit', function() {
                try {
                    localStorage.removeItem(draftKey);
                } catch (e) {}
            });

            function groupByName(data) {
                var byName = {};
                data.forEach(function(f) {
                    (byName[f.name] = byName[f.name] || []).push(f.value);
                });
                return byName;
            }

            function countDistinctIndex(data, regex) {
                var seen = {};
                data.forEach(function(f) {
                    var m = f.name.match(regex);
                    if (m) seen[m[1]] = true;
                });
                return Object.keys(seen).length;
            }

            function restoreDraft(data) {
                var byName = groupByName(data);
                var itineraryNeeded = countDistinctIndex(data, /^itinerary\[(\d+)\]/);
                var highlightsNeeded = (byName['highlights[]'] || []).length;
                var featuresNeeded = (byName['features[]'] || []).length;
                var exclusionsNeeded = (byName['exclusions[]'] || []).length;

                while ($('#itineraryContainer .itinerary-day').length < itineraryNeeded) {
                    $('.addItineraryDay').trigger('click');
                }
                while ($('#fileUploadsContainer .elements').length < highlightsNeeded) {
                    $('.addHighlights').trigger('click');
                }
                while ($('#fileUploadFeatures .elements').length < featuresNeeded) {
                    $('.addFeatures').trigger('click');
                }
                while ($('#fileUploadExclusions .elements').length < exclusionsNeeded) {
                    $('.addExclusions').trigger('click');
                }

                Object.keys(byName).forEach(function(name) {
                    var values = byName[name];
                    var $els = $form.find('[name="' + name + '"]').filter(function() {
                        return this.type !== 'file';
                    });
                    $els.each(function(i) {
                        if (i < values.length) {
                            $(this).val(values[i]);
                        }
                    });
                });

                if (byName['description'] && byName['description'][0]) {
                    var descVal = byName['description'][0];
                    var applyToEditor = function(retries) {
                        if (window.editor) {
                            window.editor.setData(descVal);
                        } else if (retries > 0) {
                            setTimeout(function() {
                                applyToEditor(retries - 1);
                            }, 200);
                        }
                    };
                    applyToEditor(15);
                }
            }

            if (!hasServerErrors) {
                var saved = null;
                try {
                    saved = localStorage.getItem(draftKey);
                } catch (e) {}
                if (saved) {
                    var data = null;
                    try {
                        data = JSON.parse(saved);
                    } catch (e) {}
                    if (data && data.length) {
                        if (window.confirm(@json(__('We found an unsaved draft of this form. Restore it?')))) {
                            restoreDraft(data);
                        } else {
                            try {
                                localStorage.removeItem(draftKey);
                            } catch (e) {}
                        }
                    }
                }
            }
        })(jQuery);
    </script>
@endpush
