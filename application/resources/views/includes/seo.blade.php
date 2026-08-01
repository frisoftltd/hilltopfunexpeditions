@if($seo)
    <meta name="title" Content="{{ $general->siteName(__($pageTitle)) }}">
    <meta name="description" content="{{ $seo->description }}">
    <meta name="keywords" content="{{ implode(',',$seo->keywords) }}">
    <link rel="shortcut icon" href="{{ getImage(getFilePath('logoIcon') .'/favicon.png') }}" type="image/x-icon">

    {{--<!-- Apple Stuff -->--}}
    <link rel="apple-touch-icon" href="{{ getImage(getFilePath('logoIcon') .'/logo.png') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ $general->siteName($pageTitle) }}">
    {{--<!-- Google / Search Engine Tags -->--}}
    <meta itemprop="name" content="{{ $general->siteName($pageTitle) }}">
    <meta itemprop="description" content="{{ $general->seo_description }}">
    <meta itemprop="image" content="{{ getImage(getFilePath('seo') .'/'. $seo->image) }}">
    {{--<!-- Facebook Meta Tags -->--}}
    @php
        // Falls back to the site-wide defaults for any page that doesn't
        // @section() one of these - individual pages (operator profile,
        // tour package details, blog details) override them so link
        // previews on Facebook/LinkedIn/Twitter show that page's own
        // title/image/description instead of the homepage's.
        $ogImage = $__env->yieldContent('og_image', getImage(getFilePath('seo') . '/' . $seo->image));
    @endphp
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', $seo->social_title)">
    <meta property="og:description" content="@yield('og_description', $seo->social_description)">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:type" content="image/{{ pathinfo($ogImage, PATHINFO_EXTENSION) }}">
    @php $socialImageSize = explode('x', getFileSize('seo')) @endphp
    <meta property="og:image:width" content="{{ $socialImageSize[0] }}">
    <meta property="og:image:height" content="{{ $socialImageSize[1] }}">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    {{--<!-- Twitter Meta Tags -->--}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', $seo->social_title)">
    <meta name="twitter:description" content="@yield('og_description', $seo->social_description)">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
