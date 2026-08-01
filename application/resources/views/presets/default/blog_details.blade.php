@extends($activeTemplate . 'layouts.frontend')

@section('og_url', route('blog.details', ['slug' => slug($blog->data_values->title), 'id' => $blog->id]))
@section('og_title', $blog->data_values->title)
@if (!empty($blog->data_values->blog_image))
    @section('og_image', getImage(getFilePath('frontend') . '/blog/' . $blog->data_values->blog_image))
@endif
@if (!empty($blog->data_values->description))
    @section('og_description', strLimit(strip_tags($blog->data_values->description), 160))
@endif

@section('content')
    <section class="blog-details--section section--bg py-100">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-lg-8">
                    <div class="blog-details">

                        <div class="blog-item">
                            <div class="blog-item--thumb">
                                <img class="fit--img"
                                    src="{{ getImage(getFilePath('frontend') . '/blog' . '/' . ($blog->data_values->blog_image ?? 'default.jpg')) }}"
                                    alt="@lang('blog-img')">
                            </div>
                            <div class="blog-item--content pt-3">
                                <ul class="text-list d-flex gap--16">
                                    <li class="text-list__item">
                                        <div class="text-list__item-icon d-flex align-items-center gap--8">
                                            <i class="fas fa-calendar-alt"></i>
                                            <p> {{ showDateTime($blog->created_at, 'd M Y') }}</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="blog-details--content">
                            <h3 class="blog-details--title">{{ __($blog->data_values->title) }}</h3>
                          
                                <blockquote >{{ $blog->data_values->quote }}</blockquote>


                                @php
                                    echo $blog->data_values->description;
                                @endphp
                          

                            <x-share-buttons :share-title="$blog->data_values->title" />
                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
                    <!--  Blog Details Sidebar Start  -->
                    <div class="blog-sidebar-wrapper">




                            <div class="row justify-content-end mb-4">
                                <div class="col-lg-12 col-md-12">
                                    <form action="{{ route('blog') }}" method="GET">
                                        <div class="search--input  position-relative">
                                            <div class="input--group search--input d-flex flex-nowrap position-relative">
                                                <input type="text" class="form--control bg--white" id="searchValue"
                                                    value="{{ request()->search }}" placeholder="@lang('Search by title')"
                                                    name="search">
                                                <button class="search-btn"><i
                                                        class="fa-solid fa-magnifying-glass"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                        <div class="blog-sidebar base--card radius--12">
                            <h5 class="blog-sidebar--title fs--24 fw--600 d-inline-block position-relative">
                                @lang('Latests Topics')</h5>
                            @foreach ($latests as $item)
                                <div class="latest-blog d-flex flex-wrap">
                                    <div class="latest-blog--thumb">
                                        <a
                                            href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id]) }}">
                                            <img src="{{ getImage(getFilePath('frontend') . '/blog' . '/' . 'thumb_' . ($item->data_values->blog_image ?? 'default.jpg')) }}"
                                                alt="@lang('blog-image')"></a>
                                    </div>
                                    <div class="latest-blog--content">
                                        <a href="{{ route('blog.details', ['slug' => slug($item->data_values->title), 'id' => $item->id]) }}"
                                            class="latest-blog--title fs--16 fw--600 w-100">
                                            @if (strlen(__($item->data_values->title)) > 30)
                                                {{ substr(__($item->data_values->title), 0, 30) . '...' }}
                                            @else
                                                {{ __($item->data_values->title) }}
                                            @endif
                                        </a>

                                        <span class="latest-blog--date fs--14">{{ showDateTime($item->created_at) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Blog Details Sidebar End-->
                </div>
            </div>
        </div>
    </section>
@endsection
