@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="explore-section section--bg py-100">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-12 mb-3">
                    <h4 class="mb-0">@lang('Tour Operators')</h4>
                </div>

                @forelse ($agencies as $agency)
                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6">
                        <a href="{{ route('operator.profile', $agency->username) }}" class="d-block">
                            <div class="tour-card radius--20 position-relative bg--white overflow-hidden">
                                <div class="tour-card__thumb">
                                    <img class="fit--img"
                                        src="{{ getImage(getFilePath('coverImage') . '/' . $agency->cover_image, getFileSize('coverImage')) }}"
                                        alt="@lang('Cover Image')">
                                </div>

                                <div class="tour-card__content text-center">
                                    <div class="thumb--wrap radius--50 overflow-hidden mx-auto mb-2" style="width: 70px; height: 70px; margin-top: -45px;">
                                        <img class="fit--img radius--50"
                                            src="{{ getImage(getFilePath('agencyProfile') . '/' . $agency->image, getFileSize('agencyProfile')) }}"
                                            alt="@lang('Image')">
                                    </div>

                                    <div class="d-flex align-items-center justify-content-center gap--8">
                                        <h6 class="tour-card__title fs--20 fw--600 mb-0">{{ $agency->fullname }}</h6>
                                        @if ($agency->kv == 1)
                                            <i class="fas fa-circle-check text--base" title="@lang('KYC Verified')"></i>
                                        @endif
                                    </div>

                                    @php
                                        $locationParts = array_filter([$agency->address?->city, $agency->address?->country]);
                                        $location = implode(', ', $locationParts);
                                    @endphp
                                    @if ($location)
                                        <p class="fs--14"><i class="fa-regular fa-compass"></i> {{ $location }}</p>
                                    @endif

                                    <p class="fs--14">@lang('Member since') {{ showDateTime($agency->created_at, 'M Y') }}</p>

                                    @if ($agency->bio)
                                        <p class="fs--14">{{ strLimit($agency->bio, 80) }}</p>
                                    @endif

                                    <p class="fs--14 mb-0"><i class="fa-solid fa-plane"></i> {{ $agency->tour_packages_count }} @lang('Packages')</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="text-center">@lang('No operators found')</p>
                @endforelse
            </div>

            @if ($agencies->hasPages())
                <div class="row mt-4">
                    <div class="col-lg-12 justify-content-end d-flex">
                        {{ $agencies->links() }}
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
