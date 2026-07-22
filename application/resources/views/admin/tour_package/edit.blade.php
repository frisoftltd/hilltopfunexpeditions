@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <div class="row gy">
                    <span class="badge text-dark bg-warning d-none" id="authfail">@lang('Google Maps API authentication
                        failed! Please check your API key. Please Go to global settings and set Maps API key.')</span>

                    <form class="navbar-search">
                        <input type="text" name="" id="locationInput" class="controls my-2"
                            placeholder="@lang('Enter a location')" autocomplete="off">
                    </form>
                    <form action="{{ route('admin.tour.package.update', $tourPackage->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="card  mt-2">
                            <h5 class="card-header">@lang('Basic Information')</h5>
                            <div class="card-body purpose">

                                <div id="map" class="mb-3"></div>

                                <div class="row d-none">

                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" name="user_id" class="form-control" hidden
                                                value="{{ auth('admin')->id() }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" name="user_type" class="form-control" hidden
                                                value="admin">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="lat" name="latitude" class="form-control" hidden
                                                value="{{ $tourPackage->latitude }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="lon" name="longitude" class="form-control"
                                                value="{{ $tourPackage->longitude }}" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="city" name="city" class="form-control"
                                                value="{{ $tourPackage->city }}" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="zipCode" name="zipcode" class="form-control"
                                                value="{{ $tourPackage->zip_code }}" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="state" name="state" class="form-control"
                                                value="{{ $tourPackage->state }}" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <input type="text" id="country" name="country" class="form-control"
                                                value="{{ $tourPackage->country }}" hidden>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Title')</label>
                                            <input type="text" name="tour_title" class="form-control"
                                                placeholder="@lang('Title')" value="{{ $tourPackage->title }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="location">@lang('Address')</label>
                                            <input type="text" id="location" name="address" class="form-control"
                                                value="{{ $tourPackage->address }}" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Category')</label>
                                            <select name="category_id" id="status" class="form-control" required>
                                                <option>@lang('Select category')</option>
                                                @foreach ($categories ?? [] as $item)
                                                <option value="{{ $item->id }}" {{ $item->id ==
                                                    $tourPackage->category_id ? 'selected' : '' }}>
                                                    {{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Stay day & nights')</label>
                                            <input type="text" step="any" name="day_nights" class="form-control"
                                                placeholder="@lang('3 day & 2 nights')"
                                                value="{{ $tourPackage->day_nights }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Duration (nights)')</label>
                                            <input type="number" min="1" name="duration_nights" class="form-control"
                                                placeholder="@lang('e.g. 4')"
                                                value="{{ $tourPackage->duration_nights }}" required>
                                            <small class="text-muted">@lang('Used to compute each departure\'s end date.')</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Group Size Min')</label>
                                            <input type="number" min="1" name="group_size_min" class="form-control"
                                                value="{{ $tourPackage->group_size_min }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Group Size Max')</label>
                                            <input type="number" min="1" name="group_size_max" class="form-control"
                                                value="{{ $tourPackage->group_size_max }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Guided In (Language)')</label>
                                            <input type="text" name="guide_language" class="form-control"
                                                placeholder="@lang('e.g. English')"
                                                value="{{ $tourPackage->guide_language }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Intensity')</label>
                                            <select name="intensity" class="form-control">
                                                <option value="">@lang('Not set')</option>
                                                @foreach (\App\Models\TourPackage::INTENSITY_LABELS as $value => $label)
                                                    <option value="{{ $value }}" {{ $tourPackage->intensity == $value ? 'selected' : '' }}>
                                                        {{ __($label) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Age Range Min')</label>
                                            <input type="number" min="0" name="age_range_min" class="form-control"
                                                value="{{ $tourPackage->age_range_min }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Age Range Max')</label>
                                            <input type="number" min="0" name="age_range_max" class="form-control"
                                                value="{{ $tourPackage->age_range_max }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="images">@lang('Images')</label>
                                            <input type="file" name="images[]" id="images" accept=".png, .jpg, .jpeg"
                                                multiple class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div id="image_preview" class="image_preview-wrapper">
                                                @foreach ($tourPackage->tour_package_images as $i => $img)
                                                <div class='img-div' id='img-div{{ $i }}' @if ($i !=0)
                                                    onclick=imageDelete(this,{{ $img->id }}); @endif>

                                                    <input type="hidden" name="old_tour_package_images[]"
                                                        value="{{ $img->id }}">
                                                    <img src="{{ getImage(getFilePath('tourPackageImage') . '/' . $img->image) }}"
                                                        class='img-responsive image img-thumbnail'
                                                        title='{{ $img->image }}' alt="tour-image">
                                                    @if ($i != 0)
                                                    <div class='middle'>
                                                        <button id='action-icon' value='img-div{{ $i }}'
                                                            class='delete-btn btn-danger' role='{{ $img->image }}'>
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
                                            <textarea name="description" class="trumEdit1"
                                                placeholder="@lang('Description')">{{ $tourPackage->description }}</textarea>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card  mt-2">
                            <h5 class="card-header">@lang('Destination Overview')</h5>
                            <div class="card-body purpose">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="file-upload">
                                            <label class="mb-2 form--label">@lang('Departure from')</label>
                                            <input type="text" name="destination_overview[departure_form]"
                                                id="departure_form" class="form-control form--control mb-0" required
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
                                            <input type="text" name="destination_overview[transportation]"
                                                id="transportation" class="form-control form--control mb-0" required
                                                value="{{ $tourPackage->destination_overview->transportation }}"
                                                placeholder="@lang('Transportation')" />
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="file-upload">
                                            <label class="mb-2 form--label">@lang('Accommodation')</label>
                                            <input type="text" name="destination_overview[accommodation]"
                                                id="accommodation" class="form-control form--control mb-0" required
                                                value="{{ $tourPackage->destination_overview->accommodation }}"
                                                placeholder="@lang('Accommodation')" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <h5 class="card-header d-flex justify-content-between align-items-center">
                                @lang('Day-by-Day Itinerary')
                                <button type="button" class="btn btn--primary btn--sm addItineraryDay">
                                    <i class="fa fa-plus"></i> @lang('Add Day')
                                </button>
                            </h5>
                            <div class="card-body purpose">
                                <div id="itineraryContainer">
                                    @foreach ($tourPackage->itinerary ?? [] as $index => $day)
                                        <div class="row align-items-start itinerary-day mb-3 pb-3 border-bottom">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="mb-2 form--label">@lang('Day')</label>
                                                    <input type="number" min="1" class="form-control"
                                                        name="itinerary[{{ $index }}][day]" value="{{ $day->day }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="mb-2 form--label">@lang('Title')</label>
                                                    <input type="text" class="form-control"
                                                        name="itinerary[{{ $index }}][title]" value="{{ $day->title }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="mb-2 form--label">@lang('Description')</label>
                                                    <textarea class="form-control" rows="2"
                                                        name="itinerary[{{ $index }}][description]">{{ $day->description }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pt-4">
                                                <button type="button" class="btn btn--danger btn-sm remove-itinerary-day"><i
                                                        class="la la-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-muted mb-0" id="noItineraryDays" @if (!empty($tourPackage->itinerary)) style="display:none" @endif>
                                    @lang('No itinerary days added yet.')</p>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <h5 class="card-header">@lang('Destination Information')</h5>
                            <div class="card-body purpose">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="text-end">
                                                <button type="button" class="btn btn--primary btn--sm addHighlights">
                                                    <i class="fa fa-plus"></i> @lang('Add New')
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Destination
                                                            Highlights')</label>
                                                        <input type="text" name="highlights[]" id="highlights"
                                                            class="form-control form--control mb-0" required
                                                            placeholder="@lang('Destination Highlights')"
                                                            value="{{ $tourPackage->highlights[0] }}" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="fileUploadsContainer">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="text-end">
                                                <button type="button" class="btn btn--primary btn--sm addFeatures">
                                                    <i class="fa fa-plus"></i> @lang('Add New')
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Destination icons')</label>
                                                        <div class="file-upload input-group">
                                                            <input type="text" name="icons[]" id="inputIcon"
                                                                class="form-control form--control iconPicker icon"
                                                                value="{{ $tourPackage->features[0]->icon }}"
                                                                placeholder="@lang('Icons')" required>
                                                            <span class="input-group-text input-group-addon"
                                                                data-icon="las la-home">@php echo
                                                                $tourPackage->features[0]->icon @endphp</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="file-upload">
                                                        <label class="form-label">@lang('Destination Features')</label>
                                                        <input type="text" name="features[]" id="features"
                                                            value="{{ $tourPackage->features[0]->feature }}"
                                                            class="form-control form--control mb-0" required
                                                            placeholder="@lang('Destination Features')" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="fileUploadFeatures">
                                                @php
                                                $features = $tourPackage->features;
                                                unset($features[0]);
                                                @endphp
                                                @foreach ($features as $item)
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="file-upload">
                                                            <label class="form-label">@lang('Destination icons')</label>
                                                            <div class="file-upload input-group">
                                                                <input type="text" name="icons[]" id="inputIcon"
                                                                    class="form-control form--control iconPicker icon"
                                                                    value="{{ $item->icon }}"
                                                                    placeholder="@lang('Icons')" required>
                                                                <span class="input-group-text input-group-addon"
                                                                    data-icon="las la-home">
                                                                    @php echo $item->icon @endphp
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="file-upload">
                                                            <label class="form-label">@lang('Destination
                                                                Features')</label>
                                                            <input type="text" name="features[]" id="features"
                                                                class="form-control form--control mb-0" required
                                                                value="{{ $item->feature }}"
                                                                placeholder="@lang('Destination Features')" />
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="text-end">
                                                <button type="button" class="btn btn--primary btn--sm addExclusions">
                                                    <i class="fa fa-plus"></i> @lang('Add New')
                                                </button>
                                            </div>
                                            @if (!empty($tourPackage->exclusions))
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="file-upload">
                                                            <label class="form-label">@lang('Not Included icon')</label>
                                                            <div class="file-upload input-group">
                                                                <input type="text" name="exclusion_icons[]" id="inputExclusionIcon"
                                                                    class="form-control form--control iconPicker icon"
                                                                    value="{{ $tourPackage->exclusions[0]->icon }}"
                                                                    placeholder="@lang('Icons')">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="file-upload">
                                                            <label class="form-label">@lang('Not Included')</label>
                                                            <input type="text" name="exclusions[]"
                                                                value="{{ $tourPackage->exclusions[0]->feature }}"
                                                                class="form-control form--control mb-0"
                                                                placeholder="@lang('e.g. International flights')" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="fileUploadExclusions">
                                                    @php
                                                        $exclusions = $tourPackage->exclusions;
                                                        unset($exclusions[0]);
                                                    @endphp
                                                    @foreach ($exclusions as $item)
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <div class="file-upload">
                                                                    <label class="form-label">@lang('Not Included icon')</label>
                                                                    <div class="file-upload input-group">
                                                                        <input type="text" name="exclusion_icons[]" id="inputExclusionIcon"
                                                                            class="form-control form--control iconPicker icon"
                                                                            value="{{ $item->icon }}" placeholder="@lang('Icons')">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="file-upload">
                                                                    <label class="form-label">@lang('Not Included')</label>
                                                                    <input type="text" name="exclusions[]"
                                                                        class="form-control form--control mb-0"
                                                                        value="{{ $item->feature }}"
                                                                        placeholder="@lang('e.g. International flights')" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <div class="file-upload">
                                                            <label class="form-label">@lang('Not Included icon')</label>
                                                            <div class="file-upload input-group">
                                                                <input type="text" name="exclusion_icons[]" id="inputExclusionIcon"
                                                                    class="form-control form--control iconPicker icon"
                                                                    placeholder="@lang('Icons')">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="file-upload">
                                                            <label class="form-label">@lang('Not Included')</label>
                                                            <input type="text" name="exclusions[]"
                                                                class="form-control form--control mb-0"
                                                                placeholder="@lang('e.g. International flights')" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="fileUploadExclusions"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <h5 class="card-header d-flex justify-content-between align-items-center">
                                @lang('Add New Departures')
                                <button type="button" class="btn btn--primary btn--sm addDepartureRow">
                                    <i class="fa fa-plus"></i> @lang('Add Departure')
                                </button>
                            </h5>
                            <div class="card-body purpose">
                                <p class="text-muted">@lang('Existing departures are managed in the Departures table below. Add rows here for brand new ones - they\'ll be created when you click Update.')</p>
                                <div id="departuresContainer"></div>
                                <p class="text-muted mb-0" id="noDepartureRows">@lang('No new departures staged.')</p>
                                @if ($priceCategories->isEmpty())
                                    <p class="text-danger mb-0">@lang('No active price categories yet - add one under Price Categories first if you want to set prices now.')</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-12 text-end mt-3">
                            <button type="submit" class="btn btn--primary">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                @lang('Departures')
                <button type="button" class="btn btn-sm btn--primary addDepartureModal"><i class="fa fa-plus"></i>
                    @lang('Add Departure')</button>
            </h5>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Start Date')</th>
                                <th>@lang('End Date')</th>
                                <th>@lang('Seats')</th>
                                <th>@lang('Prices')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tourPackage->departures as $departure)
                                <tr>
                                    <td>{{ $departure->start_date->format('M d, Y') }}</td>
                                    <td>{{ $departure->end_date?->format('M d, Y') ?? '—' }}</td>
                                    <td>{{ $departure->seats_booked }} / {{ $departure->seats_total }}
                                        ({{ $departure->seats_available }} @lang('left'))</td>
                                    <td>
                                        @foreach ($departure->departurePrices as $dp)
                                            <div>{{ $priceCategories->firstWhere('id', $dp->price_category_id)->name ?? '—' }}:
                                                {{ $general->cur_sym }}{{ $dp->price }}
                                                @if ($dp->discount)
                                                    <small class="text-muted">(-{{ $dp->discount }}%)</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($departure->status == 1)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--danger">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary editDeparture"
                                            data-url="{{ route('admin.tour.departure.update', $departure->id) }}"
                                            data-start_date="{{ $departure->start_date->format('Y-m-d') }}"
                                            data-seats_total="{{ $departure->seats_total }}"
                                            data-prices="{{ $departure->departurePrices->mapWithKeys(fn($dp) => [$dp->price_category_id => ['price' => $dp->price, 'discount' => $dp->discount]])->toJson() }}">
                                            <i class="la la-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.tour.departure.destroy', $departure->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('@lang('Delete this departure?')');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn--danger"><i
                                                    class="la la-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No departures yet.')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- add departure modal -->
<div class="modal fade" id="addDepartureModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.tour.departure.store', $tourPackage->id) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Departure')</h5>
                    <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal"><i
                            class="las la-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Start Date'):</label>
                        <input type="date" class="form-control" name="start_date"
                            min="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Total Seats'):</label>
                        <input type="number" min="1" class="form-control" name="seats_total" required>
                    </div>
                    <hr>
                    @foreach ($priceCategories as $category)
                        <div class="row">
                            <div class="col-7">
                                <div class="form-group">
                                    <label>{{ $category->name }} @lang('Price'):</label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        name="prices[{{ $category->id }}][price]" required>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label>@lang('Discount %'):</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control"
                                        name="prices[{{ $category->id }}][discount]">
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if ($priceCategories->isEmpty())
                        <p class="text-danger">@lang('No active price categories. Add one under Price Categories first.')</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- edit departure modal -->
<div class="modal fade" id="editDepartureModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="editDepartureForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Departure')</h5>
                    <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal"><i
                            class="las la-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Start Date'):</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Total Seats'):</label>
                        <input type="number" min="1" class="form-control" name="seats_total" required>
                    </div>
                    <hr>
                    @foreach ($priceCategories as $category)
                        <div class="row">
                            <div class="col-7">
                                <div class="form-group">
                                    <label>{{ $category->name }} @lang('Price'):</label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        name="prices[{{ $category->id }}][price]" data-category="{{ $category->id }}"
                                        data-field="price" required>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label>@lang('Discount %'):</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control"
                                        name="prices[{{ $category->id }}][discount]"
                                        data-category="{{ $category->id }}" data-field="discount">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Row templates - kept OUTSIDE any form so their unreplaced __INDEX__
     placeholders are never submitted; only the clones JS appends into
     #itineraryContainer/#departuresContainer (inside the main edit form)
     get submitted. --}}
<div id="itineraryDayTemplate" class="d-none">
    <div class="row align-items-start itinerary-day mb-3 pb-3 border-bottom">
        <div class="col-md-2">
            <div class="form-group">
                <label class="mb-2 form--label">@lang('Day')</label>
                <input type="number" min="1" class="form-control" name="itinerary[__INDEX__][day]" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="mb-2 form--label">@lang('Title')</label>
                <input type="text" class="form-control" name="itinerary[__INDEX__][title]"
                    placeholder="@lang('e.g. Arrival in Kigali')" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="mb-2 form--label">@lang('Description')</label>
                <textarea class="form-control" rows="2" name="itinerary[__INDEX__][description]"></textarea>
            </div>
        </div>
        <div class="col-md-1 pt-4">
            <button type="button" class="btn btn--danger btn-sm remove-itinerary-day"><i class="la la-trash"></i></button>
        </div>
    </div>
</div>

<div id="departureRowTemplate" class="d-none">
    <div class="row align-items-end departure-row mb-3 pb-3 border-bottom">
        <div class="col-md-3">
            <div class="form-group">
                <label class="mb-2 form--label">@lang('Start Date')</label>
                <input type="date" class="form-control" name="departures[__INDEX__][start_date]"
                    min="{{ now()->toDateString() }}" required>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="mb-2 form--label">@lang('Total Seats')</label>
                <input type="number" min="1" class="form-control" name="departures[__INDEX__][seats_total]" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                @foreach ($priceCategories as $category)
                    <div class="col-md-6 mb-2">
                        <label class="mb-1 form--label">{{ $category->name }}</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" min="0" class="form-control"
                                name="departures[__INDEX__][prices][{{ $category->id }}][price]"
                                placeholder="@lang('Price')" required>
                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                name="departures[__INDEX__][prices][{{ $category->id }}][discount]"
                                placeholder="@lang('Disc %')">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn--danger btn-sm remove-departure-row"><i class="la la-trash"></i></button>
        </div>
    </div>
</div>

@endsection

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
        border: 1px solid black !important;
    }
</style>
@endpush

@push('style-lib')
<link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
<script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
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
                            <span class="input-group-text input-group-addon" data-icon="las la-home"></span>
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
                            i + "' class='delete-btn btn--danger' role='" + total_file[i].name +
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

<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $general->map_api_key }}&callback=initMap"
    async defer></script>

<script>
    (function($) {
            "use strict";
            var map;
            var marker;

            var initialLat = {{ $tourPackage->latitude }};
            var initialLng = {{ $tourPackage->longitude }};
            window.gm_authFailure = function () {
                $('#authfail').removeClass('d-none');
            };
            window.initMap = function() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: initialLat, //-33.8688,
                        lng: initialLng // 151.2195
                    },
                    zoom: 13
                });

                marker = new google.maps.Marker({
                    position: {
                        lat: initialLat,
                        lng: initialLng
                    },
                    map: map,
                    draggable: true,
                    anchorPoint: new google.maps.Point(0, -29),
                    title: "Current Location"
                });

                var input = document.getElementById('locationInput');
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);

                var infowindow = new google.maps.InfoWindow();
                // marker = new google.maps.Marker({
                //     map: map,
                //     draggable: true,
                //     anchorPoint: new google.maps.Point(0, -29),
                //     title: "This marker is draggable."
                // });

                var addressElement = document.getElementById('location');
                var latElement = document.getElementById('lat');
                var lonElement = document.getElementById('lon');
                var cityElement = document.getElementById('city');
                var zipCodeElement = document.getElementById('zipCode');
                var stateElement = document.getElementById('state');
                var countryElement = document.getElementById('country');

                infowindow.setContent('');

                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }

                    marker.setPosition(place.geometry.location);
                    map.setCenter(place.geometry.location);
                    marker.setVisible(true);

                    marker.setTitle(place.name);

                    infowindow.setContent('Name: ' + place.name);

                    addressElement.value = place.formatted_address;
                    latElement.value = place.geometry.location.lat();
                    lonElement.value = place.geometry.location.lng();
                    cityElement.value = getComponentValue(place, 'locality');
                    zipCodeElement.value = getComponentValue(place, 'postal_code');
                    stateElement.value = getComponentValue(place, 'administrative_area_level_1');
                    countryElement.value = getComponentValue(place, 'country');

                });

                google.maps.event.addListener(map, 'click', function(event) {
                    var latLng = event.latLng;
                    var lat = latLng.lat();
                    var lng = latLng.lng();

                    marker.setPosition(event.latLng);
                    marker.setVisible(true);
                    marker.setTitle('Custom Name');

                    latElement.value = lat;
                    lonElement.value = lng;

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        location: event.latLng
                    }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            var placeData = results[0];
                            addressElement.value = placeData.formatted_address;
                            cityElement.value = getComponentValue(placeData, 'locality');
                            zipCodeElement.value = getComponentValue(placeData, 'postal_code');
                            stateElement.value = getComponentValue(placeData,
                                'administrative_area_level_1');
                            countryElement.value = getComponentValue(placeData, 'country');
                        } else {
                            // Handle error if geocoding fails
                        }
                    });

                    infowindow.setContent('Place Name: ' + addressElement.value + '<br>Latitude: ' + lat +
                        '<br>Longitude: ' + lng);
                    infowindow.open(map, marker);
                });

                marker.addListener('dragend', function(event) {
                    var lat = event.latLng.lat();
                    var lng = event.latLng.lng();

                    latElement.value = lat;
                    lonElement.value = lng;

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        location: event.latLng
                    }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            var placeData = results[0];
                            addressElement.value = placeData.formatted_address;
                            cityElement.value = getComponentValue(placeData, 'locality');
                            zipCodeElement.value = getComponentValue(placeData, 'postal_code');
                            stateElement.value = getComponentValue(placeData,
                                'administrative_area_level_1');
                            countryElement.value = getComponentValue(placeData, 'country');
                        } else {
                            // Handle error if geocoding fails
                        }
                    });

                    infowindow.setContent('Place Name: ' + addressElement.value + '<br>Latitude: ' + lat +
                        '<br>Longitude: ' + lng);
                    infowindow.open(map, marker);
                });
            }

            function getComponentValue(placeData, componentType) {
                for (var i = 0; i < placeData.address_components.length; i++) {
                    var component = placeData.address_components[i];
                    for (var j = 0; j < component.types.length; j++) {
                        if (component.types[j] === componentType) {
                            return component.long_name;
                        }
                    }
                }
                return '';
            }


            // window.addEventListener('load', initMap);

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
        $('.addDepartureModal').on('click', function() {
            $('#addDepartureModal').modal('show');
        });

        var editModal = $('#editDepartureModal');
        $(document).on('click', '.editDeparture', function() {
            var startDate = $(this).data('start_date');
            var seatsTotal = $(this).data('seats_total');
            var prices = $(this).data('prices');

            editModal.find('#editDepartureForm').attr('action', $(this).data('url'));
            editModal.find('input[name=start_date]').val(startDate);
            editModal.find('input[name=seats_total]').val(seatsTotal);

            editModal.find('input[data-category]').val('');
            $.each(prices, function(categoryId, data) {
                editModal.find('input[data-category="' + categoryId + '"][data-field="price"]').val(data.price);
                editModal.find('input[data-category="' + categoryId + '"][data-field="discount"]').val(data
                    .discount);
            });

            editModal.modal('show');
        });
    })(jQuery);
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
        var itineraryIndex = {{ count($tourPackage->itinerary ?? []) }};
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
        var departureIndex = 0;
        $('.addDepartureRow').on('click', function() {
            var html = $('#departureRowTemplate').html().replace(/__INDEX__/g, departureIndex);
            $('#departuresContainer').append(html);
            departureIndex++;
            $('#noDepartureRows').hide();
        });
        $(document).on('click', '.remove-departure-row', function() {
            $(this).closest('.departure-row').remove();
            if ($('#departuresContainer .departure-row').length === 0) {
                $('#noDepartureRows').show();
            }
        });
    })(jQuery);
</script>
@endpush