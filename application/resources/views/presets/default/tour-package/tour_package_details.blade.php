@php
    $imageCount = $tourPackage->tour_package_images->count();
    $colOne = $imageCount == 1 ? '12' : '6';
    $colTwo = $imageCount == 2 ? '6' : '6';
@endphp


@extends($activeTemplate . 'layouts.frontend')
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
                            <ul class="d-flex gap--20">
                                <li>
                                    <span class="text--black7"><i class="fa-solid fa-user-group"></i>
                                        {{ $tourPackage->activeDepartures->sum('seats_total') }}</span>
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
                                        {{ $tourPackage->activeDepartures->sum('seats_booked') }}</span>
                                </li>

                            </ul>
                        </div>
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
                                        <div class="col-lg-6">
                                            <div class="details__key-item">
                                                <div class="d-flex align-items-center justify-content-start gap--12 mb-2">
                                                    <div class="icon--wrap d-flex align-items-center justify-content-center">
                                                        <i class="fa-solid fa-users"></i>
                                                    </div>
                                                    <p>@lang('Person')</p>
                                                </div>
                                                <div class="content--wrap">
                                                    <h6 class="mb-0 fw--500 text--black7">{{ $tourPackage->activeDepartures->sum('seats_total') }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
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

                                <div class="product--details__info mb-4 section--bg p-4 radius--12">
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
                                                <div class="review-card">
                                                    <div class="user-info">
                                                        <div class="thumb-wrap">
                                                            <img class="fit--img"
                                                                src="{{ getImage(getFilePath('userProfile') . '/' . $item->user->image, getFileSize('userProfile')) }}"
                                                                alt="..">
                                                        </div>
                                                        <div class="user-name">
                                                            <div class="d-flex align-items-center gap--8">
                                                                <h1 class="name fs--20 fw--600 mb-0">
                                                                    {{ $item->user->fullname }}
                                                                </h1>
                                                                <p class="fs--14">
                                                                    {{ showDateTime($item->created_at, 'd M') }}
                                                                </p>
                                                            </div>
                                                            <ul class="rating-wrap">
                                                                @php echo calculateIndividualRating($item->star) @endphp
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="content">

                                                        <div class="discription">@php
                                                            echo $item->review;
                                                        @endphp</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <h5 class="text-center no-review">@lang('No Reviews')</h5>
                                        @endforelse

                                        <div class="row mt-4">
                                            <form action="{{ route('user.review.submit') }}" method="POST">
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
                    <div class="product--info__wrap  position-sticky">
                        <h6 class="fs--20 fw--600 mb-3">@lang('Departures')</h6>
                        @forelse ($tourPackage->activeDepartures as $departure)
                            <div class="bg--white radius--20 p-4 mb-4">
                                <form method="POST" action="{{ route('user.tour.package.booking.now') }}">
                                    @csrf
                                    <input type="hidden" value="{{ $tourPackage->id }}" name="tour_package_id">
                                    <input type="hidden" value="{{ $departure->id }}" name="tour_departure_id">

                                    <div class="product--info__item d-flex flex-column gap--20">
                                        <div>
                                            <p class="mb-1"><i class="fa-solid fa-calendar-days"></i> @lang('From - To')</p>
                                            <h6 class="price fs--18 fw--500 mb-0 text--black7">
                                                {{ $departure->start_date->format('M d, Y') }}
                                                @if ($departure->end_date)
                                                    - {{ $departure->end_date->format('M d, Y') }}
                                                @endif
                                            </h6>
                                        </div>
                                        <div>
                                            <p class="mb-1"><i class="fa-solid fa-chair"></i> @lang('Seats Left')</p>
                                            <h6 class="price fs--18 fw--500 mb-0 text--black7">{{ $departure->seats_available }}</h6>
                                        </div>
                                    </div>

                                    @if ($departure->departurePrices->isEmpty())
                                        <p class="text-danger mt-3 mb-0">@lang('Pricing for this departure is not set yet.')</p>
                                    @else
                                        <div class="product--info__item">
                                            <label class="mb-2 form--label">@lang('Price Category')</label>
                                            @foreach ($departure->departurePrices as $dp)
                                                <div class="form--check mb-2">
                                                    <input class="form-check-input" type="radio" name="price_category_id"
                                                        id="priceCategory{{ $departure->id }}_{{ $dp->price_category_id }}"
                                                        value="{{ $dp->price_category_id }}"
                                                        {{ $loop->first ? 'checked' : '' }} required>
                                                    <label class="form-check-label"
                                                        for="priceCategory{{ $departure->id }}_{{ $dp->price_category_id }}">
                                                        {{ $dp->priceCategory->name }} —
                                                        {{ $general->cur_sym }}{{ showAmount($dp->final_price) }}
                                                        @if ($dp->discount)
                                                            <del class="text--black7 fs--14">{{ $general->cur_sym }}{{ $dp->price }}</del>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="product--info__item border-0 pb-2 mb-0">
                                            <div class="form-group">
                                                <label class="mb-2 form--label">@lang('Travelers')</label>
                                                <input class="form--control" type="number" min="1"
                                                    max="{{ $departure->seats_available }}" value="1" step="1"
                                                    name="seat" required>
                                            </div>
                                        </div>

                                        <div class="product--info__item">
                                            <button class="btn btn--base btn--lg w--100 pills" type="submit">@lang('Book Now')
                                                <i class="fa-solid fa-arrow-right-long"></i></button>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        @empty
                            <div class="bg--white radius--20 p-4 mb-4 text-center">
                                <p class="mb-0">@lang('No upcoming departures for this tour yet. Check back soon!')</p>
                            </div>
                        @endforelse
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
@endpush
