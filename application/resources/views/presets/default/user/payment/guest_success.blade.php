@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="login-section position-relative py-100">
        <div class="row mx-0 justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="base--card custom--card text-center">
                    <h5 class="mb-3">@lang('Thank you!')</h5>
                    <p class="mb-4">@lang('Your booking is confirmed. We\'ve emailed you a confirmation with everything you need, including how to view or manage your booking online.')</p>
                    <a href="{{ route('home') }}" class="btn btn--base btn--lg pills">@lang('Back to Home')</a>
                </div>
            </div>
        </div>
    </section>
@endsection
