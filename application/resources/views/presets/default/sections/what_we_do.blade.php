@php
    $whatWeDoContent = getContent('what_we_do.content', true);
    $travelerItems = $whatWeDoContent->data_values->traveler_items ?? [];
    $operatorItems = $whatWeDoContent->data_values->operator_items ?? [];
@endphp
<section class="what-we-do--section py-100 position-relative section--bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($whatWeDoContent->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">{{ __($whatWeDoContent->data_values->heading) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="base--card radius--16 h--100 p-4">
                    <h5 class="fw--700 mb-3">@lang('For Travelers')</h5>
                    <ul class="highlight__key d-flex flex-column gap--12">
                        @foreach ($travelerItems as $item)
                            <li class="d-flex gap--8">
                                <span class="text--base">
                                    <i class="fa-solid fa-circle-right"></i>
                                </span>
                                <p>{{ __($item) }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="base--card radius--16 h--100 p-4">
                    <h5 class="fw--700 mb-3">@lang('For Tour Operators')</h5>
                    <ul class="highlight__key d-flex flex-column gap--12">
                        @foreach ($operatorItems as $item)
                            <li class="d-flex gap--8">
                                <span class="text--base">
                                    <i class="fa-solid fa-circle-right"></i>
                                </span>
                                <p>{{ __($item) }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
