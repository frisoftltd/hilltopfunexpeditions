@php
    $topDestinationContent = getContent('top_destination.content', true);
    $topDestinations = App\Models\Location::where('status', 1)->limit(5)->orderBy('id', 'asc')->get();
@endphp
<section class="location--section py-100 position-relative">
    <div class="bg--element position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/shape-1.png') }}" alt="image">
    </div>
    <div class="bg--element-two position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/whychoose-bg2.png') }}" alt="image">
    </div>

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($topDestinationContent->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s"> {{ __($topDestinationContent->data_values->heading) }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s"> {{ __($topDestinationContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="location--card__wrap d-flex gap--20 flex-wrap justify-content-center">
                    @foreach ($topDestinations ?? [] as $item)
                        <a href="{{ route('browse') }}?lati={{ $item->latitude }}&longi={{ $item->longitude }}"
                            class="location__card radius--20 overflow-hidden position-relative {{ $loop->iteration == 3 ? 'active' : '' }}">
                            <div class="location__card-thumb position-relative w--100 h--100">
                                <img class="fit--img" src="{{ getImage(getFilePath('location') . '/' . $item->image) }}"
                                    alt="image">
                            </div>

                            <div class="location__card-content position-absolute w--100 d-flex justify-content-between align-items-center" >
                                <div class="content">
                                    <h6 class="title text--white fs--24 mb-0">
                                        <i class="fa-solid fa-location-dot"></i>
                                        {{ __($item->name) }}
                                    </h6>
                                    <p class="text--white custom-location">{{ $item->location }}</p>
                                </div>
                                <span class="btn circle"><i class="fa-solid fa-arrow-up-long"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
