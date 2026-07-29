@php
    $locationParts = array_filter([$agency->address?->city, $agency->address?->country]);
    $location = implode(', ', $locationParts);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="profile--section py-100">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <div class="base--card radius--20 text-center operator-sidebar position-sticky">
                        <div class="thumb--wrap radius--50 overflow-hidden mx-auto mb-3">
                            <img class="fit--img radius--50"
                                src="{{ getImage(getFilePath('agencyProfile') . '/' . $agency->image, getFileSize('agencyProfile')) }}"
                                alt="@lang('Image')">
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap--8">
                            <h6 class="name fs--24 fw--700 mb-0">{{ $agency->fullname }}</h6>
                            @if ($agency->kv == 1)
                                <i class="fas fa-circle-check text--base" title="@lang('KYC Verified')"></i>
                            @endif
                        </div>

                        @if ($agency->tagline)
                            <p class="text--base fw--600 mb-2">{{ $agency->tagline }}</p>
                        @endif

                        @if ($reviewCount > 0)
                            <div class="d-flex align-items-center justify-content-center gap--8 mb-2">
                                <ul class="rating-wrap d-flex mb-0">
                                    @php echo calculateIndividualRating($averageRating) @endphp
                                </ul>
                                <span class="fs--14">{{ number_format($averageRating, 1) }} ({{ $reviewCount }} @lang('reviews'))</span>
                            </div>
                        @else
                            <p class="fs--14">@lang('No reviews yet')</p>
                        @endif

                        @if ($location)
                            <p class="fs--14 mb-1"><i class="fa-regular fa-compass"></i> {{ $location }}</p>
                        @endif
                        @if (!empty($agency->languages))
                            <p class="fs--14 mb-1"><i class="fa-solid fa-language"></i> @lang('Languages Spoken'): {{ implode(', ', $agency->languages) }}</p>
                        @endif
                        <p class="fs--14">@lang('Member since') {{ showDateTime($agency->created_at, 'M Y') }}</p>

                        <button type="button" class="btn btn--base btn--lg w--100 pills mt-2"
                            data-bs-toggle="modal" data-bs-target="#packagesModal">
                            @lang('Book Me')
                        </button>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="profile--banner radius--16 overflow-hidden mb-4">
                        <img class="fit--img w--100"
                            src="{{ getImage(getFilePath('coverImage') . '/' . $agency->cover_image, getFileSize('coverImage')) }}"
                            alt="@lang('Cover Image')">
                    </div>

                    <div class="base--card radius--20 mb-4">
                        <h6 class="fw--700 mb-3">@lang('Hi there! Nice to meet you')</h6>
                        @if ($agency->bio)
                            <div id="bioText" class="bio-clamp mb-2">{{ $agency->bio }}</div>
                            <a href="javascript:void(0)" id="bioToggle" class="text--base fw--600 d-none">@lang('Read more')</a>
                        @else
                            <p class="mb-0">@lang("This operator hasn't added a bio yet.")</p>
                        @endif
                    </div>

                    <div class="base--card radius--20 mb-4">
                        <div class="auth--badge d-flex align-items-center gap--8 flex-wrap">
                            <h5 class="social-share--title mb-0 me-sm-3 me-1 d-inline-block">@lang('Share This'):</h5>
                            <ul class="social-list d-flex gap--12 mb-0">
                                <li class="social-list--item">
                                    <a href="https://www.facebook.com/share.php?u={{ Request::url() }}&title={{ slug($agency->fullname) }}"
                                        class="social-list__link d-flex justify-content-center align-items-center" target="_blank">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li class="social-list--item">
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ Request::url() }}&title={{ slug($agency->fullname) }}&source=behands"
                                        class="social-list__link d-flex justify-content-center align-items-center" target="_blank">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </li>
                                <li class="social-list--item">
                                    <a href="https://twitter.com/intent/tweet?status={{ slug($agency->fullname) }}+{{ Request::url() }}"
                                        class="social-list__link d-flex justify-content-center align-items-center" target="_blank">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>
                                <li class="social-list--item">
                                    <button type="button" id="copyProfileLink" class="social-list__link d-flex justify-content-center align-items-center border-0" title="@lang('Copy Link')">
                                        <i class="fa-solid fa-link"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div id="packages" class="mb-4">
                        <h5 class="mb-3">@lang('Book one of my offers')</h5>
                        <div class="row gy-4">
                            @include($activeTemplate . 'components.single_tour_package')
                        </div>
                    </div>

                    <div>
                        <h5 class="mb-3">@lang('Reviews')</h5>
                        <div class="row gy-4">
                            @forelse ($reviews as $item)
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
                                            <div class="discription">@php echo $item->review; @endphp</div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <h5 class="text-center no-review">@lang('No Reviews')</h5>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="packagesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Book one of my offers')</h5>
                    <button type="button" class="close btn btn--danger" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row gy-4">
                        @include($activeTemplate . 'components.single_tour_package')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .operator-sidebar {
            top: 100px;
        }

        .bio-clamp {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
        }

        .bio-clamp.expanded {
            -webkit-line-clamp: unset;
            display: block;
        }

        @media (max-width: 991px) {
            .operator-sidebar {
                position: static;
            }
        }

        @media (min-width: 1200px) {
            #packagesModal .modal-dialog {
                max-width: 90%;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        (function() {
            "use strict";

            const copyBtn = document.getElementById('copyProfileLink');
            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    navigator.clipboard.writeText(window.location.href).then(function() {
                        copyBtn.querySelector('i').classList.remove('fa-link');
                        copyBtn.querySelector('i').classList.add('fa-check');
                        setTimeout(function() {
                            copyBtn.querySelector('i').classList.remove('fa-check');
                            copyBtn.querySelector('i').classList.add('fa-link');
                        }, 2000);
                    });
                });
            }

            const bioText = document.getElementById('bioText');
            const bioToggle = document.getElementById('bioToggle');
            if (bioText && bioToggle) {
                if (bioText.scrollHeight > bioText.clientHeight + 2) {
                    bioToggle.classList.remove('d-none');
                }
                bioToggle.addEventListener('click', function() {
                    const expanded = bioText.classList.toggle('expanded');
                    bioToggle.textContent = expanded ? "{{ __('Read less') }}" : "{{ __('Read more') }}";
                });
            }
        })();
    </script>
@endpush
