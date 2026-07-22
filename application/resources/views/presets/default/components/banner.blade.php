{{-- ===== HILLTOP HERO SLIDER (managed via Admin > Sections > Banner) ===== --}}
@php
    $sliderElements = getContent('banner.element', false, null, true);
    $sliderElements = $sliderElements->filter(function($sl){ return !empty($sl->data_values) && !empty($sl->data_values->banner_image);})->values();
    $bc = getContent('banner.content', true);
    $bannerContent = getContent('banner.content', true);
    $heroLocations = App\Models\Location::where('status', 1)->get();
    $languages = App\Models\Language::all();
    $currentLang = $languages->firstWhere('code', session('lang', 'en'));
    $galleryElements = getContent('gallery.element', false, '6', true);
    $galleryElements = $galleryElements->filter(function($g){ return !empty($g->data_values) && !empty($g->data_values->banner_image); })->values();

    \Carbon\Carbon::setlocale(session('lang', 'en'));
    $monthOptions = collect(range(0, 11))->map(function ($i) {
        $date = now()->addMonthsNoOverflow($i);
        return ['value' => $date->format('Y-m'), 'label' => $date->translatedFormat('F Y')];
    });
@endphp

<div class="hilltop-hero-wrap">
    <div class="hilltop-css-slider">
        @foreach($sliderElements as $i => $sl)
        <div class="hilltop-css-slide hilltop-css-slide-{{ $i }}"
             style="background-image:url('{{ asset('assets/images/frontend/banner/' . $sl->data_values->banner_image) }}');">
        </div>
        @endforeach
    </div>
    <div class="hilltop-hero-content">
        <div class="hilltop-hero-text">
            <h6>{{ __($bc->data_values->title) }}</h6>
            <h1>{{ __($bc->data_values->heading) }}</h1>
            <p>{{ __($bc->data_values->sub_heading) }}</p>
        </div>
        <div class="hilltop-hero-search">
            <form action="{{ route('browse') }}" method="GET">
                <div class="hilltop-search-inner">
                    <div class="hs-field">
                        <i class="fa-solid fa-location-dot"></i>
                        <select name="destination" class="from-select3">
                            <option value="">@lang('Destination')</option>
                            @foreach($heroLocations as $loc)
                                <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hs-field">
                        <i class="fa-regular fa-calendar-days"></i>
                        <select name="month" class="from-select3">
                            <option value="">@lang('Anytime')</option>
                            @foreach($monthOptions as $opt)
                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hs-field">
                        <i class="fa-regular fa-user"></i>
                        <input type="number" min="1" name="travelers" value="2">
                    </div>
                    <button type="submit" class="hs-btn">
                        <i class="fa-solid fa-magnifying-glass"></i> @lang('Search')
                    </button>
                </div>
            </form>
        </div>
    </div>
    <button class="hilltop-arr hilltop-arr-prev" onclick="hilltopPrev()">&#8249;</button>
    <button class="hilltop-arr hilltop-arr-next" onclick="hilltopNext()">&#8250;</button>
</div>

@push('script')
<script>
(function(){
    var slides = document.querySelectorAll('.hilltop-css-slide');
    var total = slides.length;
    var current = 0;
    var timer;
    function show(n) {
        slides[current].classList.remove('active');
        current = (n + total) % total;
        slides[current].classList.add('active');
    }
    function autoPlay() { timer = setInterval(function(){ show(current + 1); }, 5000); }
    function reset() { clearInterval(timer); autoPlay(); }
    slides[0].classList.add('active');
    autoPlay();
    window.hilltopNext = function(){ show(current + 1); reset(); };
    window.hilltopPrev = function(){ show(current - 1); reset(); };
})();
</script>
@endpush

<!-- Hero Section -->
<section class="hero">
    <div class="hero--element__wrap">
        <div class="airplane--two">
            <div class="thumb--wrap position-absolute">
                <img src="{{ asset($activeTemplateTrue . 'images/shape/airplane4.png') }}" alt="shape-image">
            </div>
        </div>
        <div class="map--wrap">
            <div class="thumb--wrap">
                <svg class="map" viewBox="0 0 706 461" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.2315 120.821C20.2315 120.821 19.3915 121.661 18.5415 120.821C18.5415 122.501 16.8615 123.351 16.8615 125.881C12.6515 126.721 10.9615 130.091 6.75151 130.931C7.59151 135.141 5.91151 137.671 1.70151 137.671C2.54151 141.041 5.91151 144.411 9.28151 145.251C7.60151 151.151 14.3415 158.731 4.23151 149.461C-6.71849 157.891 7.60151 157.891 8.44151 162.941C10.1215 161.251 12.6515 161.251 13.5015 161.251C12.6615 164.621 10.1315 167.151 6.76151 167.151C6.76151 169.681 5.92151 168.831 7.60151 171.361C4.23151 176.411 14.3415 184.001 17.7115 182.311C21.0815 186.521 13.5015 195.791 10.1315 197.481C10.9715 198.321 12.6615 198.321 14.3415 199.161C16.0315 193.261 21.0815 190.731 25.2915 185.681C27.8215 182.311 27.8215 177.251 32.0315 173.881C32.0315 176.411 33.7215 174.721 32.0315 178.091C34.5615 178.091 36.2415 178.091 38.7715 176.411C38.7715 177.251 38.7715 177.251 39.6115 178.091C43.8215 171.351 46.3515 178.091 51.4015 176.411C51.4015 178.091 52.2415 179.781 52.2415 181.471C58.9815 180.631 64.8815 189.891 66.5615 194.951C67.4015 194.111 69.0915 194.951 69.9315 194.111C70.7715 198.321 77.5115 210.121 80.8815 209.281C83.4115 213.491 80.8815 217.701 85.9415 217.701C85.9415 217.701 87.6215 220.231 86.7815 220.231C83.4115 222.761 84.2515 237.921 87.6215 240.451C88.4615 242.981 90.1515 244.661 92.6715 246.351C91.8315 252.251 99.4115 261.521 101.942 265.731C106.152 272.471 110.372 277.531 115.422 282.581C114.582 273.311 105.312 266.571 103.622 258.151C107.832 260.681 117.942 273.321 120.472 277.531C122.162 280.061 123.002 284.271 124.682 287.641C126.372 288.481 132.262 291.011 134.792 292.701C135.632 291.861 137.322 291.021 138.162 290.171C140.692 295.221 144.902 291.011 148.272 295.221C149.112 295.221 149.112 294.381 149.962 293.541C151.652 298.591 155.862 297.751 158.392 301.121C159.232 301.121 160.072 301.121 160.072 301.121C162.602 304.491 165.972 309.551 171.862 308.701C171.862 306.171 172.702 307.021 173.552 305.331C175.232 307.861 176.922 308.701 178.602 310.381C180.292 306.171 176.072 304.481 173.552 301.951C172.712 302.791 171.022 303.641 171.022 304.481C168.492 301.951 167.652 298.581 167.652 295.211C166.812 296.051 165.962 296.891 165.122 296.891C163.432 294.361 161.752 291.841 158.382 290.991C159.222 288.461 158.382 285.091 159.222 283.411C157.532 284.251 155.852 284.251 154.172 286.781C153.332 285.941 152.482 285.101 151.642 284.251C144.902 295.201 128.052 271.611 142.372 265.711C142.372 266.551 142.372 266.551 143.212 267.401C145.742 263.191 150.792 266.561 155.852 264.871C155.852 261.501 156.692 260.651 160.062 259.811C160.062 261.491 160.902 262.341 160.902 264.021C160.902 264.021 162.582 262.341 162.582 263.181C167.642 265.711 164.272 271.601 170.162 274.971C176.062 271.601 165.952 261.491 174.372 257.281C177.742 257.281 182.802 248.861 180.272 245.481C182.802 241.271 186.172 240.431 188.692 235.371C190.382 232.001 187.852 226.941 194.592 225.261C195.432 226.941 197.122 227.791 197.962 229.471C201.332 228.631 201.331 224.421 199.652 221.051C200.492 221.051 202.182 221.051 203.022 221.051C203.022 221.891 203.022 222.731 203.862 223.581C208.072 224.421 215.662 224.421 210.602 217.681C208.072 220.211 199.652 216.001 196.282 215.151C197.972 207.571 204.712 210.101 208.082 213.471C211.452 211.791 213.132 207.571 216.502 205.891C216.502 210.101 211.452 224.431 221.552 217.691C221.552 219.371 224.082 221.061 224.082 222.751C233.352 218.541 224.082 217.691 223.242 215.171C220.712 210.121 220.712 205.901 218.182 201.691C218.182 200.851 215.652 201.691 214.812 200.851C214.812 200.011 215.652 196.641 214.812 195.801C213.972 194.961 209.752 192.431 208.912 191.591C206.382 188.221 206.382 183.171 202.172 182.321C200.492 179.791 199.642 176.421 200.492 173.051C193.752 172.211 197.122 179.791 192.072 178.951C187.022 178.111 188.702 167.161 182.802 167.161C173.532 165.471 180.272 188.221 180.272 194.961C179.432 194.961 177.742 194.961 176.902 194.961C176.902 197.491 177.742 200.011 177.742 202.541C177.742 205.071 176.062 208.441 176.062 210.121C171.012 210.961 168.482 206.751 170.162 202.541C163.422 201.701 168.472 192.431 159.212 194.121C156.682 189.061 148.262 185.691 143.202 180.641C144.892 175.581 143.202 170.531 147.412 164.631C152.472 159.581 156.682 159.581 156.682 151.151C157.522 151.151 159.212 152.001 160.052 152.841C160.892 151.151 161.732 151.151 162.582 150.311C162.582 146.941 162.582 147.781 164.272 145.251C164.272 146.091 165.112 147.781 165.112 148.621C166.802 147.781 167.642 146.091 168.482 144.411C167.642 143.571 166.802 143.571 165.952 142.731C170.162 138.521 168.482 130.091 162.582 128.411C162.582 134.311 165.112 138.521 160.052 141.891C157.522 140.211 155.842 139.361 153.312 139.361C154.152 136.831 152.472 134.301 153.312 132.621C151.622 131.781 150.782 131.781 149.102 131.781C148.262 128.411 146.572 119.141 143.202 118.301C143.202 115.771 144.042 113.251 144.892 111.561C148.262 111.561 151.632 109.871 150.792 105.661C139.842 100.601 143.212 120.831 140.682 125.881C143.212 130.091 147.422 135.991 144.052 141.041C141.522 137.671 140.682 132.611 136.472 130.931C135.632 134.301 136.472 137.671 139.002 140.201C135.632 143.571 125.522 141.881 123.832 136.831C122.142 136.831 119.622 135.991 117.932 135.991C117.932 137.671 117.092 140.201 117.092 141.891C112.882 140.211 110.352 145.261 105.292 141.891C105.292 141.051 105.292 141.051 106.132 140.211C100.232 141.891 97.7115 134.311 92.6515 132.631C86.7515 130.951 82.5415 133.471 77.4915 130.101C76.6515 130.101 76.6515 130.941 75.8115 131.791C74.9715 130.951 74.9715 130.951 73.2815 130.101L72.4415 131.791C67.3815 133.471 62.3315 133.471 58.1215 136.001C57.2815 134.311 56.4415 133.471 55.5915 132.631C51.3815 130.951 45.4815 130.951 41.2715 130.951C40.4315 130.951 39.5815 129.271 38.7415 129.271C37.9015 129.271 37.9015 130.111 37.0515 130.951C36.2115 127.581 33.6815 126.741 30.3115 127.581C30.3115 126.741 30.3115 126.741 30.3115 125.901C29.4715 125.901 29.4715 126.741 28.6315 126.741C28.6315 126.741 28.6315 125.901 27.7915 125.901C26.9515 125.901 26.1115 126.741 25.2615 126.741C23.6015 125.031 20.2315 122.501 20.2315 120.821Z" fill="#D7F2FE" />
                </svg>
            </div>
        </div>
        <div class="tree--wrap__one">
            <div class="thumb--wrap">
                <img src="{{ asset($activeTemplateTrue . 'images/shape/tree4.png') }}" alt="shape-image">
            </div>
        </div>
        <div class="fly--image__wrap">
            <img class="fly--thumb" src="{{ asset($activeTemplateTrue . 'images/shape/flyimg1.png') }}" alt="image">
            <img class="fly--thumb" src="{{ asset($activeTemplateTrue . 'images/shape/flyimg2.png') }}" alt="image">
            <img class="fly--thumb" src="{{ asset($activeTemplateTrue . 'images/shape/flyimg3.png') }}" alt="image">
            <img class="fly--thumb" src="{{ asset($activeTemplateTrue . 'images/shape/flyimg4.png') }}" alt="image">
            <img class="fly--thumb" src="{{ asset($activeTemplateTrue . 'images/shape/flyimg5.png') }}" alt="image">
            <img class="fly--thumb" src="{{ asset($activeTemplateTrue . 'images/shape/flyimg6.png') }}" alt="image">
        </div>
    </div>

    <div class="hero-content--wrap">
        <div class="container position-relative">
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-8 d-flex align-items-center justify-content-center">
                    <div class="hero--content position-relative d-flex flex-column justify-content-center">
                        <h6 class="third--font text-center text--base fw--900 fs--30">
                            {{ __($bannerContent->data_values->title) }}
                        </h6>
                        <h1 class="title text-center fw--600 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">
                            {{ __($bannerContent->data_values->heading) }}
                        </h1>
                        <p class="subtitle text-center fs--18 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.3s">
                            {{ __($bannerContent->data_values->sub_heading) }}
                        </p>
                    </div>
                </div>

                <div class="col-lg-11">
                    <form action="{{route('browse')}}" method="GET">
                        <div class="banner--filter__wrap d-flex gap--16">
                            <div class="banner--filter__inputs">
                                <div class="form-group position-relative pills">
                                    <span class="icon--wrap position-absolute fs--18"><i class="fa-solid fa-location-dot"></i></span>
                                    <select class="form--control pills from-select3" name="destination">
                                        <option value="">@lang('Destination')</option>
                                        @foreach($heroLocations as $loc)
                                            <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group position-relative">
                                    <span class="icon--wrap position-absolute fs--18"><i class="fa-regular fa-calendar-days"></i></span>
                                    <select class="form--control pills from-select3" name="month">
                                        <option value="">@lang('Anytime')</option>
                                        @foreach($monthOptions as $opt)
                                            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group position-relative d-none d-md-block">
                                    <span class="icon--wrap position-absolute fs--18"><i class="fa-regular fa-user"></i></span>
                                    <input class="form--control pills" type="number" min="1" name="travelers" value="2">
                                </div>
                            </div>
                            <div class="banner--filter__btn flex-shrink-0">
                                <button class="btn btn--base btn--lg pills">
                                    <i class="fa-solid fa-magnifying-glass"></i> @lang('Search')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                @if($galleryElements->count() >= 6)
                <div class="col-lg-12">
                    <div class="gallery--grid__wrap image--popup-group">
                        <div class="row g-3 justify-content-center">
                            <div class="col-xl-2 col-lg-3 col-md-6">
                                <div class="gallery--grid__thumb full">
                                    <a class="d-flex h-100 w-100 position-relative" href="{{ getFilePath('galleryImage') . '/' . $galleryElements[0]->data_values->banner_image }}">
                                        <img class="fit--img" src="{{ getFilePath('galleryImage') . '/' . $galleryElements[0]->data_values->banner_image }}" alt="">
                                        <span class="gallery--overlay position-absolute"><i class="fa-solid fa-eye"></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="row g-3">
                                    <div class="col-lg-12">
                                        <div class="gallery--grid__thumb small">
                                            <a class="d-flex h-100 w-100 position-relative" href="{{ getFilePath('galleryImage') . '/' . $galleryElements[1]->data_values->banner_image }}">
                                                <img class="fit--img" src="{{ getFilePath('galleryImage') . '/' . $galleryElements[1]->data_values->banner_image }}" alt="">
                                                <span class="gallery--overlay position-absolute"><i class="fa-solid fa-eye"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="gallery--grid__thumb small">
                                            <a class="d-flex h-100 w-100 position-relative" href="{{ getFilePath('galleryImage') . '/' . $galleryElements[2]->data_values->banner_image }}">
                                                <img class="fit--img" src="{{ getFilePath('galleryImage') . '/' . $galleryElements[2]->data_values->banner_image }}" alt="">
                                                <span class="gallery--overlay position-absolute"><i class="fa-solid fa-eye"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-5 col-md-12">
                                <div class="row g-3">
                                    <div class="col-lg-12 col-md-6">
                                        <div class="gallery--grid__thumb small">
                                            <a class="d-flex h-100 w-100 position-relative" href="{{ getFilePath('galleryImage') . '/' . $galleryElements[3]->data_values->banner_image }}">
                                                <img class="fit--img" src="{{ getFilePath('galleryImage') . '/' . $galleryElements[3]->data_values->banner_image }}" alt="">
                                                <span class="gallery--overlay position-absolute"><i class="fa-solid fa-eye"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-6">
                                        <div class="row g-3">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="gallery--grid__thumb small">
                                                    <a class="d-flex h-100 w-100 position-relative" href="{{ getFilePath('galleryImage') . '/' . $galleryElements[4]->data_values->banner_image }}">
                                                        <img class="fit--img" src="{{ getFilePath('galleryImage') . '/' . $galleryElements[4]->data_values->banner_image }}" alt="">
                                                        <span class="gallery--overlay position-absolute"><i class="fa-solid fa-eye"></i></span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="gallery--grid__thumb small">
                                                    <a class="d-flex h-100 w-100 position-relative" href="{{ getFilePath('galleryImage') . '/' . $galleryElements[5]->data_values->banner_image }}">
                                                        <img class="fit--img" src="{{ getFilePath('galleryImage') . '/' . $galleryElements[5]->data_values->banner_image }}" alt="">
                                                        <span class="gallery--overlay position-absolute"><i class="fa-solid fa-eye"></i></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>
@push('script-lib')
    <script src="{{ asset('assets/admin/js/datepicker.en.js') }}"></script>
@endpush
