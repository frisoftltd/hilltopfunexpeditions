@php
    $importantLinks = getContent('footer_important_links.element', false, null, true);
    $companyLinks = getContent('footer_company_links.element', false, null, true);
    $contact = getContent('contact_us.content', true);
    $socialIcons = getContent('social_icon.element', false);
    $policyPages = getContent('policy_pages.element', false, null, true);
@endphp
<!-- Footer Start Here -->
<footer class="footer-area overflow--hidden z--1">

    <div class="bg--element position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/whychoose-bg2.png') }}" alt="image">
    </div>

    <div class="bg--element-three position-absolute">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/dash-line.png') }}" alt="image">
    </div>


    <div class="cloud--group__one position-absolute">
        <img class="cloud-one position-relative left_image_bounce-1"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud1.png') }}" alt="image">
        <img class="cloud-two position-absolute left_image_bounce-2"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud2.png') }}" alt="image">
        <img class="cloud-three position-absolute left_image_bounce-1"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud3.png') }}" alt="image">
        <img class="cloud-four position-absolute left_image_bounce-1"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud6.png') }}" alt="image">
        <img class="cloud-five position-absolute left_image_bounce-2"
            src="{{ asset($activeTemplateTrue . 'images/shape/cloud7.png') }}" alt="image">
    </div>

    <div class="airplane--two">
        <div class="thumb--wrap">
            <img src="{{ asset($activeTemplateTrue . 'images/shape/ballon.png') }}" alt="image">
        </div>
    </div>


    <div class="footer-top py-100">
        <div class="container">
            <div class="row gy-4 justify-content-start">
                <div class="col-xl-4 col-sm-6">
                    <div class="footer-item">
                        <div class="footer-item--logo">
                            <a href="{{ route('home') }}" class="footer-logo-normal" id="footer-logo-normal">
                                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                    alt="">
                            </a>
                        </div>
                        <p class="footer-item--desc">{{ __($contact->data_values->short_details) }}</p>

                        <ul class="social-list z--9 position-relative">
                            @foreach ($socialIcons as $item)
                            <li>
                                <a href="{{ $item->data_values->url }}" class="social-list__link icon-wrapper">
                                    <div class="icon {{$loop->iteration == 2 ? '': ''}}">@php echo $item->data_values->social_icon; @endphp</div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item--title">@lang('Important Links')</h5>
                        <ul class="footer-menu">
                            @foreach ($importantLinks as $key => $item)
                                <li class="menu--item"><a href="{{ url('/') . $item->data_values->url }}"
                                        class="menu--link"><i
                                            class="fa-solid fa-arrow-right-long"></i>{{ __($item->data_values->title) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item--title">@lang('Company Link')</h5>
                        <ul class="footer-menu">
                            @foreach ($companyLinks as $key => $item)
                                <li class="menu--item">
                                    <a href="{{ url('/') . $item->data_values->url }}" class="menu--link"><i
                                            class="fa-solid fa-arrow-right-long"></i>{{ __($item->data_values->title) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>



                <div class="col-xl-4 col-sm-6">
                    <div class="footer-item">
                        <h5 class="footer-item--title">@lang('Stay Updated')</h5>
                        <p class="footer-item--desc">@lang('Join Our Travel Tribe – Get the Best Deals & Destination Ideas Delivered to Your Inbox!')</p>
                        <div class="subscribe-box mb-3">

                            <form action="{{ route('subscribe') }}" method="POST">
                                @csrf
                                <input class="form--control footer-input" type="email" name="email"
                                    placeholder="@lang('Email Address')">
                                <button class="sub-btn" type="submit"><i
                                        class="fa-regular fa-paper-plane"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- Footer Top End-->

    <!-- bottom Footer -->
    <div class="bottom-footer pt-4 pb-3">
        <div class="container">
            <div class="row text-center gy-2">
                <div class="col-lg-12">
                    <div class="bottom-footer-text d-flex flex-wrap gap--12 justify-content-center justify-content-sm-between align-items-center">
                        <div class="mb-0">@php echo $contact->data_values->website_footer; @endphp</div>
                        <div class="d-flex gap--12">
                            @foreach ($policyPages as $key => $item)
                                <a href="{{ route('policy.pages', [slug($item->data_values->title), $item->id]) }}">{{ __($item->data_values->title) }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</footer>
<!-- ==================== Footer End Here ==================== -->
<div class="scroll-top">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
            style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 197.514;">
        </path>
    </svg>
</div>
