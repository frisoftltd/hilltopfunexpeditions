@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="login-section position-relative py-100">
        <div class="row mx-0 justify-content-center">
            <div class="col-md-8 col-lg-7 col-xl-5">
                <div class="base--card custom--card text-center">
                    <h5 class="mb-3">@lang('Thank you!')</h5>

                    @if ($deposit && $deposit->tour_booking)
                        <p class="mb-2">
                            @lang('Your booking for') <strong>{{ __($deposit->tour_booking->tour_package?->title ?? 'Package deleted') }}</strong>
                        </p>
                        <p class="mb-4">@php echo $deposit->tour_booking->statusBadge($deposit->tour_booking->status) @endphp</p>
                    @else
                        <p class="mb-4">@lang('Your booking is confirmed.')</p>
                    @endif

                    <p class="text--black7 mb-4">@lang('We\'ve emailed you a link to set your password and log in - use it to view or manage this booking anytime.')</p>

                    <a href="{{ route('home') }}" class="btn btn--base btn--lg pills">@lang('Back to Home')</a>
                </div>
            </div>
        </div>
    </section>
@endsection
