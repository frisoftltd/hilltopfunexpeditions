@php
    $languages = App\Models\Language::all();
    $pages = App\Models\Page::where('tempname', $activeTemplate)->get();
    $currentLang = $languages->firstWhere('code', session('lang', 'en'));

@endphp


<!-- Header Start -->
<div class="header-main-area">
    <div class="header" id="header">
        <div class="container position-relative">
            <div class="header-wrapper">
                <!-- ham menu -->
                <i class="fa-sharp fa-solid fa-bars-staggered ham__menu" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"></i>

                <!-- logo -->
                <div class="header-menu-wrapper align-items-center d-flex gap--32">
                    <div class="logo-wrapper">
                        <a href="{{ route('home') }}" class="normal-logo" id="normal-logo">
                            <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                                alt="{{ config('app.name') }}" width="150" height="35">
                        </a>
                    </div>
                </div>
                <!-- / logo -->

                <div class="menu--wrap d-flex align-items-center gap--72">
                    <div class="menu-list-wrapper">
                        <ul class="main-menu">
                            @foreach ($pages as $page)
                                <li>
                                    <a class="{{ Request::url() == url($page->slug) ? 'active' : '' }}"
                                        href="{{ route('pages', [$page->slug]) }}">
                                        {{ __($page->name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <ul class="login-lng d-flex align-items-center gap-4">
                    <li class="language">
                        <div class="language-box">
                          <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img class="flag--img" src="{{ getImage(getFilePath('language') . '/' . $currentLang->icon, getFileSize('language')) }}" alt="@lang('Icon')">{{ __($currentLang->name) }}
                          </button>
                          <ul class="dropdown-menu lng--dropdown">
                            @foreach ($languages as $language)
                              <li>
                                <a class="dropdown-item lang-change @if (Session::get('lang') === $language->code) selected @endif" href="javascript:void(0)"
                                   data-lang="{{ $language->code }}">
                                  <img class="flag--img" src="{{ getImage(getFilePath('language') . '/' . $language->icon, getFileSize('language')) }}" alt="@lang('Icon')">
                                  {{ __($language->name) }}
                                </a>
                              </li>
                            @endforeach
                          </ul>
                        </div>
                      </li>

                    <li class="loin-btn--wrap">
                        @auth
                            <a class="btn btn--base btn--lg w--100 pills" href="{{ route('user.home') }}">@lang('Dashboard') <i
                                    class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        @endauth
                        @auth('agency')
                            <a class="btn btn--base btn--lg w--100 pills" href="{{ route('agency.home') }}">@lang('Dashboard') <i
                                    class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        @endauth
                        @if (!(auth()->id() || agencyId()))
                            <a class="btn btn--base btn--lg w--100 pills" href="{{ route('user.login') }}">@lang('Sign In') <i
                                    class="fa-solid fa-arrow-right-to-bracket"></i>
                            </a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Header section End -->

<!-- Sidebar mobile menu wrap Start-->
<div class="offcanvas offcanvas-start text-bg-light" tabindex="-1" id="offcanvasExample">
    <div class="offcanvas-header">
        <div class="logo">
            <div class="align-items-center d-flex">
                <div class="logo-wrapper">
                    <a href="{{ route('home') }}" class="normal-logo" id="offcanvas-logo-normal">
                        <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}"
                            alt="{{ config('app.name') }}" width="150" height="35">
                    </a>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="user-info">
            @auth
                <div class="user-thumb">
                    <a href="{{ route('user.home') }}">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image) }}" alt="user-thumb">
                    </a>
                </div>
                <a href="{{ route('user.home') }}">
                    <h4>{{ __(auth()->user()->username) }}</h4>
                </a>
            @endauth
            @auth('agency')

                <div class="user-thumb">
                    <a href="{{ route('agency.home') }}">
                        <img src="{{ getImage(getFilePath('agencyProfile') . '/' . auth('agency')->user()->image) }}" alt="user-thumb">
                    </a>
                </div>
                <a href="{{ route('agency.home') }}">
                    <h4>{{ auth('agency')->user()->username }}</h4>
                </a>
            @endauth
        </div>
        <ul class="side-Nav">
            @foreach ($pages as $page)
                <li>
                    <a class="{{ Request::url() == url($page->slug) ? 'active' : '' }}"
                        href="{{ route('pages', [$page->slug]) }}" aria-current="page">
                        {{ __($page->name) }}
                    </a>
                </li>
            @endforeach
            <li>
                @auth
                    <a class="login-btn" href="{{ route('user.home') }}">@lang('Dashboard') <i
                            class="fa-solid fa-arrow-right-to-bracket"></i>
                    </a>
                @endauth
                @auth('agency')
                    <a class="login-btn" href="{{ route('agency.home') }}">@lang('Dashboard') <i
                            class="fa-solid fa-arrow-right-to-bracket"></i>
                    </a>
                @endauth
                @if (!(auth()->id() || agencyId()))
                    <a class="login-btn" href="{{ route('user.login') }}">@lang('SignUp') <i
                            class="fa-solid fa-arrow-right-to-bracket"></i>
                    </a>
                @endif
            </li>
        </ul>
    </div>
</div>
