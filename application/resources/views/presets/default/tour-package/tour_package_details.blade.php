@php
    $imageCount = $tourPackage->tour_package_images->count();
    $colOne = $imageCount == 1 ? '12' : '6';
    $colTwo = $imageCount == 2 ? '6' : '6';
@endphp


@extends($activeTemplate . 'layouts.frontend')

@section('og_url', route('tour.package.details', [$tourPackage->id, slug($tourPackage->title)]))
@section('og_title', $tourPackage->title)
@if ($tourPackage->tour_package_images->first())
    @section('og_image', getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images->first()->image))
@endif
@if (!empty($tourPackage->description))
    @section('og_description', strLimit(strip_tags($tourPackage->description), 160))
@endif

@section('content')

    <!-- < product details  -->
    <section class="product-details section--bg pt-100">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-8">
                    <div class="product--img__preview image--popup-group mb-4">
                        <div class="row g-2">
                            <div class="col-lg-{{ $colOne }} col-md-6">
                                <div class="product--thumb  radius--20 overflow-hidden">
                                    <div class="main--thumb__preview radius--20">
                                        <a class="d-flex w--100 h--100"
                                            href="{{ getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images[0]->image) }}">
                                            <img class="fit--img" id="productImgSrc1"
                                                src="{{ getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images[0]->image) }}"
                                                alt="tour-image">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if ($imageCount >= 2)
                                <div class="col-lg-{{ $colTwo }} col-md-6">
                                    <div class="row {{ $imageCount == 2 ? 'h--100' : '' }}">
                                        <div class="col-lg-12 {{ $imageCount == 2 ? 'h--100' : '' }}">
                                            <div
                                                class="product--thumb thumb--small radius--20 overflow-hidden mb-2 {{ $imageCount == 2 ? 'h--100' : '' }}">
                                                <div class="main--thumb__preview radius--20">
                                                    <a class="d-flex w--100 h--100"
                                                        href="{{ getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images[1]->image) }}">
                                                        <img class="fit--img" id="productImgSrc2"
                                                            src="{{ getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images[1]->image) }}"
                                                            alt="tour-image">
                                                    </a>

                                                </div>
                                            </div>
                                        </div>
                                        @if ($imageCount >= 3)
                                            <div class="col-lg-12">
                                                <div class="product--thumb thumb--small radius--20 overflow-hidden mb-2">
                                                    <div class="main--thumb__preview radius--20">
                                                        <a class="d-flex w--100 h--100 position-relative"
                                                            href="{{ getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images[2]->image) }}">
                                                            <img class="fit--img" id="productImgSrc3"
                                                                src="{{ getImage(getFilePath('tourPackageImage') . '/' . $tourPackage->tour_package_images[2]->image) }}"
                                                                alt="tour-image">

                                                            @if ($imageCount - 3 > 0)
                                                                <div class="more-images-overlay heading--font">
                                                                    <span>{{ $imageCount - 3 }}+</span>
                                                                </div>
                                                            @endif
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($imageCount >= 4)
                                                @foreach ($tourPackage->tour_package_images->slice(3) as $image)
                                                    <div class="col-lg-12 d-none">
                                                        <div
                                                            class="product--thumb thumb--small radius--20 overflow-hidden mb-2">
                                                            <div class="main--thumb__preview radius--20">
                                                                <a class="d-flex w--100 h--100 position-relative"
                                                                    href="{{ getImage(getFilePath('tourPackageImage') . '/' . $image->image) }}">
                                                                    <img class="fit--img"
                                                                        src="{{ getImage(getFilePath('tourPackageImage') . '/' . $image->image) }}"
                                                                        alt="tour-image">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="base--card section--bg__two radius--16 border--none">
                        <div class="product--info__item mb-3">
                            <h6 class="fs--32 fw--600 mb-2">{{ __($tourPackage->title) }}</h6>
                            @if ($tourPackage->user_type == 'agency' && $tourPackage->agency)
                                <p class="mb-2">
                                    @lang('Sold by')
                                    <a href="{{ route('operator.profile', $tourPackage->agency->username) }}" class="text--base fw--600">
                                        {{ $tourPackage->agency->fullname }}
                                    </a>
                                </p>
                            @endif
                            <ul class="d-flex gap--20">
                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-user-group"></i>
                                        {{ $tourPackage->tour_bookings->where('status', 1)->sum('party_size') }}</span>
                                </li>
                                <li>
                                    <span class="text--black7"><i class="fa-regular fa-heart"></i>
                                        {{ $tourPackage->favorite }}</span>
                                </li>
                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-eye"></i> {{ $tourPackage->view }}</span>
                                </li>

                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-stopwatch"></i>
                                        {{ $tourPackage->duration_nights }}</span>
                                </li>

                            </ul>
                        </div>

                        <x-share-buttons :share-title="$tourPackage->title" />

                        <ul class="custom--tabs buy-sell d-flex flex-wrap gap--4 z--1 mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="btn nav-link pills active" id="Profile-tab" data-bs-toggle="tab"
                                    data-bs-target="#Profile" type="button" role="tab" aria-selected="true"><i
                                        class="fa-solid fa-info-circle"></i> @lang('Details')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn nav-link pills" id="Input-tab" data-bs-toggle="tab"
                                    data-bs-target="#Input" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="fa-solid fa-location-dot"></i> @lang('Location')</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="btn nav-link pills" id="Notes-tab" data-bs-toggle="tab"
                                    data-bs-target="#Notes" type="button" role="tab" aria-selected="false"
                                    tabindex="-1"><i class="fa-solid fa-star"></i> @lang('Reviews')</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="Profile" role="tabpanel"
                                aria-labelledby="Profile-tab">
                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Destination Overview')</h6>

                                    <div class="row gy-4">
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-plane-departure"></i>
                                                    </div>
                                                    <p class="title mb-1">@lang('Departure from')</p>     
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ __($tourPackage->destination_overview->departure_form) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-plane-arrival"></i>
                                                    </div>
                                                    <p>@lang('Arrival')</p>     
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ __($tourPackage->destination_overview->arrival) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-bus-simple"></i>
                                                    </div>
                                                    <p>@lang('Transportation')</p>   
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ __($tourPackage->destination_overview->transportation) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-bed"></i>
                                                    </div>
                                                    <p>@lang('Accommodation')</p>   
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">
                                                        {{ __($tourPackage->destination_overview->accommodation) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-cable-car"></i>
                                                    </div>
                                                    <p>@lang('Tour Type')</p> 
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">{{ __($tourPackage->category->name) }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($tourPackage->group_size_min || $tourPackage->group_size_max)
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-user-group"></i>
                                                        </div>
                                                        <p>@lang('Group Size')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">
                                                            {{ $tourPackage->group_size_min }}@if ($tourPackage->group_size_max)–{{ $tourPackage->group_size_max }}@endif
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($tourPackage->guide_language)
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-language"></i>
                                                        </div>
                                                        <p>@lang('Guided In')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">{{ $tourPackage->guide_language }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($tourPackage->age_range_min || $tourPackage->age_range_max)
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-child-reaching"></i>
                                                        </div>
                                                        <p>@lang('Age Range')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">
                                                            {{ $tourPackage->age_range_min }}@if ($tourPackage->age_range_max)–{{ $tourPackage->age_range_max }}@endif
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($tourPackage->intensity)
                                            <div class="col-lg-6">
                                                <div class="details__key-item">
                                                    <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                        <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-gauge-high"></i>
                                                        </div>
                                                        <p>@lang('Intensity')</p>
                                                    </div>
                                                    <div class="content--wrap">
                                                        <h6 class="mb-0 fw--500 text--black7">{{ __($tourPackage->intensity_label) }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Description')</h6>
                                    <div class="description">
                                        @php
                                            echo $tourPackage->description;
                                        @endphp

                                    </div>
                                </div>

                                @if (!empty($tourPackage->itinerary))
                                    <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                        <h6 class="fs--22 fw--600 mb-3">@lang('Itinerary')</h6>
                                        <div class="accordion" id="itineraryAccordion">
                                            @foreach ($tourPackage->itinerary as $index => $day)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="itineraryHeading{{ $index }}">
                                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#itineraryCollapse{{ $index }}"
                                                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                                            aria-controls="itineraryCollapse{{ $index }}">
                                                            @lang('Day') {{ $day->day }}: {{ $day->title }}
                                                        </button>
                                                    </h2>
                                                    <div id="itineraryCollapse{{ $index }}"
                                                        class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                                        aria-labelledby="itineraryHeading{{ $index }}"
                                                        data-bs-parent="#itineraryAccordion">
                                                        <div class="accordion-body">
                                                            {{ $day->description }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
                                    <h6 class="fs--22 fw--600">@lang('Highlights')</h6>
                                    <ul class="highlight__key d-flex flex-column gap--12">

                                        @foreach ($tourPackage->highlights as $item)
                                            <li class="d-flex gap--8">
                                                <span class="text--base">

                                                    <i class="fa-solid fa-circle-right"></i>
                                                </span>
                                                <p>{{ __($item) }}</p>
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>

                                <div class="row gy-4">
                                    <div class="col-lg-6">
                                        <div class="product--details__info mb-4 section--bg p-4 radius--12 h-100">
                                            <h6 class="fs--22 fw--600">@lang('What’s Included')</h6>
                                            <ul class="highlight__key d-flex flex-column gap--12">
                                                @foreach ($tourPackage->features as $item)
                                                    <li class="d-flex gap--8">
                                                        <span class="text--success">
                                                            @php echo (iconCheck($item->icon)) @endphp </span>
                                                        <p>{{ __($item->feature) }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @if (!empty($tourPackage->exclusions))
                                        <div class="col-lg-6">
                                            <div class="product--details__info mb-4 section--bg p-4 radius--12 h-100">
                                                <h6 class="fs--22 fw--600">@lang('Not Included')</h6>
                                                <ul class="highlight__key d-flex flex-column gap--12">
                                                    @foreach ($tourPackage->exclusions as $item)
                                                        <li class="d-flex gap--8">
                                                            <span class="text--danger">
                                                                @php echo (iconCheck($item->icon)) @endphp </span>
                                                            <p>{{ __($item->feature) }}</p>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="tab-pane fade" id="Input" role="tabpanel" aria-labelledby="Input-tab">
                                <div class="map-section radius--12 overflow-hidden">
                                    <div class="map-box">
                                        <iframe
                                            src="https://maps.google.com/maps?q={{ $tourPackage->latitude }},{{ $tourPackage->longitude }}&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                            allowfullscreen="" loading="lazy">
                                        </iframe>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="Notes" role="tabpanel" aria-labelledby="Notes-tab">
                                <div class="note--wrap">
                                    <div class="row gy-4">
                                        @forelse ($tourPackage->reviews ?? [] as $item)
                                            <div class="col-lg-6">
                                                <x-review-card :review="$item" />
                                            </div>
                                        @empty
                                            <h5 class="text-center no-review">@lang('No Reviews')</h5>
                                        @endforelse

                                        <div class="row mt-4">
                                            <form action="{{ route('user.review.submit') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="review-box mb-4">
                                                    <input type="hidden" name="tour_package_id"
                                                        value="{{ $tourPackage->id }}">
                                                    <input type="hidden" name="star" id="rating" value="0">
                                                    <div
                                                        class="d-flex align-items-center star rating-wrap rating-stars mb-3 gap-1">
                                                        <i class="far fa-star star--color" data-rating="1"></i>
                                                        <i class="far fa-star star--color" data-rating="2"></i>
                                                        <i class="far fa-star star--color" data-rating="3"></i>
                                                        <i class="far fa-star star--color" data-rating="4"></i>
                                                        <i class="far fa-star star--color" data-rating="5"></i>
                                                    </div>
                                                    <textarea class="form--control mb-3" name="review" placeholder="@lang('Write Your Review')"></textarea>

                                                    <div class="form-group mb-3">
                                                        <label class="mb-2 form--label">@lang('Photos (optional, up to 5)')</label>
                                                        <input type="file" class="form--control" name="images[]" accept="image/*" multiple>
                                                    </div>

                                                    <div class="text-end">
                                                        <button type="submit"
                                                            class="btn btn--base btn--lg pills">@lang('Submit')</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="product--info__wrap booking-widget position-sticky" id="bookingWidget">
                        <h6 class="fs--20 fw--600 mb-2">@lang('Book This Tour')</h6>
                        <div class="bg--white radius--20 p-3">
                            @if ($tourPackage->packagePrices->isEmpty())
                                <p class="text-danger mb-0">@lang('Pricing for this tour is not set yet.')</p>
                            @else
                                <form method="POST" action="{{ Auth::check() ? route('user.tour.package.booking.now') : route('guest.tour.package.booking.now') }}" id="bookingWidgetForm">
                                    @csrf
                                    <input type="hidden" value="{{ $tourPackage->id }}" name="tour_package_id">

                                    @unless (Auth::check())
                                        <div class="product--info__item">
                                            <div class="form-group">
                                                <label class="mb-2 form--label">@lang('Full Name')</label>
                                                <input class="form--control" type="text" name="name" value="{{ old('name') }}" required>
                                            </div>
                                        </div>
                                        <div class="product--info__item">
                                            <div class="form-group">
                                                <label class="mb-2 form--label">@lang('Email')</label>
                                                <input class="form--control" type="email" name="email" value="{{ old('email') }}" required>
                                            </div>
                                        </div>
                                        <div class="product--info__item">
                                            <div class="form-group">
                                                <label class="mb-2 form--label">@lang('Phone')</label>
                                                <input class="form--control" type="text" name="phone" value="{{ old('phone') }}" required>
                                            </div>
                                        </div>
                                    @endunless

                                    <div class="product--info__item">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Start Date')</label>
                                            <input class="form--control" type="date" name="start_date"
                                                id="bookingStartDate" data-duration-nights="{{ (int) $tourPackage->duration_nights }}"
                                                min="{{ now()->toDateString() }}" required>
                                            <small class="text--black7 d-none" id="bookingEndDatePreview"></small>
                                        </div>
                                    </div>

                                    <div class="product--info__item">
                                        <label class="mb-2 form--label">@lang('Price Category')</label>
                                        @foreach ($tourPackage->packagePrices as $packagePrice)
                                            <div class="form--check mb-2">
                                                <input class="form-check-input price-option" type="radio" name="price_category_id"
                                                    id="priceCategory{{ $packagePrice->price_category_id }}"
                                                    value="{{ $packagePrice->price_category_id }}"
                                                    data-price="{{ $packagePrice->final_price }}"
                                                    {{ $loop->first ? 'checked' : '' }} required>
                                                <label class="form-check-label"
                                                    for="priceCategory{{ $packagePrice->price_category_id }}">
                                                    {{ $packagePrice->priceCategory->name }} —
                                                    {{ $general->cur_sym }}{{ showAmount($packagePrice->final_price) }}
                                                    @if ($packagePrice->discount)
                                                        <del class="text--black7 fs--14">{{ $general->cur_sym }}{{ $packagePrice->price }}</del>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="product--info__item">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Travelers')</label>
                                            <input class="form--control" type="number" min="1" step="1"
                                                id="partySizeInput" name="party_size"
                                                value="{{ (int) request()->query('travelers') ?: 1 }}" required>
                                        </div>
                                    </div>

                                    <div class="product--info__item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text--black7">@lang('Estimated total')</span>
                                            <h6 class="mb-0 fw--600" id="bookingPricePreview">{{ $general->cur_sym }}0.00</h6>
                                        </div>
                                    </div>

                                    <div class="product--info__item">
                                        <button class="btn btn--base btn--lg w--100 pills" type="submit">@lang('Request to Book')
                                            <i class="fa-solid fa-arrow-right-long"></i></button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile-only sticky "Book This Tour" CTA - jumps to #bookingWidget above -->
        <div class="mobile-booking-cta d-lg-none">
            @if ($tourPackage->packagePrices->isNotEmpty())
                <div class="mobile-booking-cta__info">
                    <span class="mobile-booking-cta__label">@lang('From')</span>
                    <span class="mobile-booking-cta__price">{{ $general->cur_sym }}{{ showAmount($tourPackage->packagePrices->min('final_price')) }}</span>
                </div>
            @endif
            <a href="#bookingWidget" class="btn btn--base pills mobile-booking-cta__btn">@lang('Book This Tour')
                <i class="fa-solid fa-arrow-right-long"></i></a>
        </div>
    </section>

    <section class="section--bg py-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="base--card radius--20">
                        <h5 class="mb-2">@lang('Customize Your Tour')</h5>
                        <p class="text--black7 mb-20">@lang("Don't see a date or package that fits? Tell us what you're looking for and we'll get back to you with a quote.")</p>

                        <form method="POST" action="{{ route('quote.request.store') }}">
                            @csrf
                            <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">

                            <div class="row gy-3">
                                @unless (Auth::check())
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Full Name')</label>
                                            <input class="form--control" type="text" name="name" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Email')</label>
                                            <input class="form--control" type="email" name="email" value="{{ old('email') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="mb-2 form--label">@lang('Phone')</label>
                                            <input class="form--control" type="text" name="phone" value="{{ old('phone') }}" required>
                                        </div>
                                    </div>
                                @endunless

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="mb-2 form--label">@lang('Start Date')</label>
                                        <input class="form--control" type="date" name="start_date"
                                            min="{{ now()->toDateString() }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="mb-2 form--label">@lang('End Date')</label>
                                        <input class="form--control" type="date" name="end_date"
                                            min="{{ now()->toDateString() }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="mb-2 form--label">@lang('Travelers')</label>
                                        <input class="form--control" type="number" min="1" step="1" name="party_size" value="1" required>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="mb-2 form--label">@lang('Tell us more about what you\'re looking for')</label>
                                        <textarea class="form--control" name="message" rows="4"></textarea>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <button class="btn btn--base btn--lg pills" type="submit">@lang('Request a Quote')
                                        <i class="fa-solid fa-arrow-right-long"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="recent--section section--bg position-relative py-100">
        <div class="container">

            <div class="row justify-content-start">
                <div class="col-lg-6">
                    <div class="section-content mb-50">
                        <div class="title-wrap">
                            <h6 class="heading third--font text-start fs--32 fw--700 text--base mb-0">
                                @lang('Recently Viewed')</h6>
                            <h2 class="title text-start mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                                data-splitting data-wow-delay="0.2s">@lang('Recently Viewed')</h2>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center gy-4">
                @include($activeTemplate . 'components.single_tour_package')
            </div>
        </div>
    </section>

@endsection

@push('script')
    <script>
        // rating set
        $(document).ready(function() {
            'use strict'

            var initialRating = parseInt($('#rating').val());
            if (initialRating > 0) {
                updateStars(initialRating);
            }

            $('.rating-stars i').on('click', function() {
                var rating = parseInt($(this).data('rating'));
                $('#rating').val(rating);
                updateStars(rating);
            });

            $('#rating').on('input', function() {
                var rating = $(this).val();
                updateStars(rating);
            });

            function updateStars(rating) {
                var stars = $('.rating-stars i');
                stars.removeClass('fas').addClass('far');
                stars.each(function(index) {
                    if (index < rating) {
                        $(this).removeClass('far').addClass('fas');
                    }
                });
            }

        });
        // end rating set
    </script>

    <script>
        function addToWishlist(element) {
            "use strict";

            var isAddingToWishlist = false;
            var isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

            if (!isAddingToWishlist && isLoggedIn) {
                isAddingToWishlist = true;
                var tourPackageId = $(element).data('tour_package_id');
                var url = $(element).data('url');

                $.ajax({
                    url: url,
                    type: 'get',
                    data: {
                        tourPackageId: tourPackageId,
                    },
                    complete: function() {
                        isAddingToWishlist = false;
                    },
                    success: function(response) {
                        if (response.hasOwnProperty('message')) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                            var heartIcon = $(element).find('i');
                            if (response.message.includes('added')) {
                                heartIcon.removeClass('far fa-heart').addClass('fas fa-heart text--base');
                            } else if (response.message.includes('removed')) {
                                heartIcon.removeClass('fas fa-heart text--base').addClass('far fa-heart');
                            }
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: response.error
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Error occurred while updating the wishlist.';
                        Toast.fire({
                            icon: 'error',
                            title: errorMessage
                        });
                    }
                });
            } else if (!isLoggedIn) {
                var errorMessage = 'Please log in to manage your wishlist.';
                Toast.fire({
                    icon: 'warning',
                    title: errorMessage
                });
            }
        }
    </script>

    <script>
        (function($) {
            "use strict";
            var $form = $('#bookingWidgetForm');
            if (!$form.length) {
                return;
            }

            function updatePreview() {
                var price = parseFloat($form.find('.price-option:checked').data('price')) || 0;
                var partySize = parseInt($form.find('#partySizeInput').val(), 10) || 0;
                var total = price * partySize;
                $('#bookingPricePreview').text('{{ $general->cur_sym }}' + total.toFixed(2));
            }

            $form.on('change', '.price-option', updatePreview);
            $form.on('input', '#partySizeInput', updatePreview);
            updatePreview();
        })(jQuery);
    </script>

    <script>
        (function() {
            "use strict";
            var startDateInput = document.getElementById('bookingStartDate');
            var endDatePreview = document.getElementById('bookingEndDatePreview');
            if (!startDateInput || !endDatePreview) {
                return;
            }

            var durationNights = parseInt(startDateInput.getAttribute('data-duration-nights'), 10) || 0;

            startDateInput.addEventListener('change', function() {
                if (!this.value || durationNights <= 0) {
                    endDatePreview.classList.add('d-none');
                    return;
                }

                var endDate = new Date(this.value);
                endDate.setDate(endDate.getDate() + durationNights);

                var formatted = endDate.toLocaleDateString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });

                endDatePreview.textContent = "{{ __('Ends:') }} " + formatted;
                endDatePreview.classList.remove('d-none');
            });
        })();
    </script>

    <script>
        (function() {
            "use strict";
            document.body.classList.add('has-mobile-booking-cta');

            var ctaBtn = document.querySelector('.mobile-booking-cta__btn');
            if (!ctaBtn) {
                return;
            }
            ctaBtn.addEventListener('click', function(e) {
                var target = document.getElementById('bookingWidget');
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        })();
    </script>
@endpush
