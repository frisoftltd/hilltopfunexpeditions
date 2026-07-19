@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonialElements = getContent('testimonial.element', false, 8, true);
@endphp
<section class="testimonial-section py-100 position-relative">
    <div class="bg--element position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="image">
    </div>

    <div class="bg--element-two position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/element-10.png') }}" alt="image">
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-content mb-50">
                    <div class="title-wrap">
                        <h6 class="heading third--font text-center fs--32 fw--700 text--base mb-0">
                            {{ __($testimonialContent->data_values->title) }}</h6>
                        <h2 class="title text-center mb-3 fs--40 fw--800 wow animate__animated animate__fadeInUp splite-text"
                            data-splitting data-wow-delay="0.2s">{{ __($testimonialContent->data_values->heading) }}
                        </h2>
                        <p class="subtitle wow animate__animated animate__fadeInUp text-center fs-16 fw--400"
                            data-wow-delay="0.3s">{{ __($testimonialContent->data_values->sub_heading) }}</p>
                    </div>
                </div>
            </div>
        </div>



        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="row testimonial-slider">
                    @foreach ($testimonialElements ?? [] as $item)
                        <div
                            class="testimonial-card position-relative section--bg radius--20 d-flex justify-content-center">
                            <div
                                class="item--wrap d-flex flex-column justify-content-center align-items-center position-relative">
                                <div class="quote--icon mb-3">
                                    <img src="{{ asset($activeTemplateTrue . 'images/shape/quote1.png') }}"
                                        alt="image">
                                </div>
                                <div class="content-wrap">
                                    <p class="description text-center fw--500">
                                        @if (strlen(__($item->data_values->description)) > 150)
                                            {{ substr(__($item->data_values->description), 0, 150) . '...' }}
                                        @else
                                            {{ __($item->data_values->description) }}
                                        @endif
                                    </p>
                                </div>
                                <div class="d-flex justify-content-center align-items-center w--100">
                                    <div class="d-flex flex-column justify-content-center align-items-center gap--12">
                                        <div class="user--thumb position-relative d-flex">
                                            <img class="fit--img"
                                                src="{{ getImage(getFilePath('testimonial') . '/' . $item->data_values->client_image) }}"
                                                alt="image">
                                        </div>
                                        <div
                                            class="user--info d-flex flex-column justify-content-center align-items-center">
                                            <h6 class="fs--22 text-center fw--600 mb-0">
                                                {{ __($item->data_values->name) }}</h6>
                                            <p class="mb-1 text-center fs--14">
                                                {{ __($item->data_values->designation) }}</p>
                                            <div class="d-flex gap--8 align-items-center">
                                                <ul class="star--wrap d-flex ">
                                                    <li>
                                                        @php echo showRatings($item->data_values->star_count); @endphp
                                                    </li>
                                                </ul>
                                                <p class="avg">{{ $item->data_values->star_count }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
