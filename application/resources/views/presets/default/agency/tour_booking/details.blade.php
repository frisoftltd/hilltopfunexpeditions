@extends($activeTemplate . 'layouts.agency.master')
@section('content')
<div class="row mb-3">
    <div class="col-lg-12 d-flex gap--12">
        <a href="{{ route('agency.tour.package.booking.user.list', $bookingDetails->tour_package_id) }}"
            class="btn btn-md btn--base pills">
            <i class="la la-arrow-left"></i> @lang('Back to List')
        </a>
        <a href="{{ route('agency.tour.package.edit', $bookingDetails->tour_package_id) }}"
            class="btn btn-md btn--base pills">
            <i class="fas fa-edit"></i> @lang('Edit Package')
        </a>
    </div>
</div>
<div class="row justify-content-center gy-4">
    <div class="col-lg-6">

        <div class="tour-card radius--20 position-relative bg--white">
            <div class="tour-card__thumb">
                <a href="">
                    <img class="fit--img"
                        src="{{ getImage(getFilePath('tourPackageImage') . '/' . $bookingDetails->tour_package?->TourPackagePrimaryImage?->image) }}"
                        alt="Tour Image">
                </a>
            </div>

            <div class="tour-card__content">
                <ul class="list-group border-0 gap--12">
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Package Title'):
                        <span class="fw--500">{{ __($bookingDetails?->tour_package?->title ?? 'Package deleted') }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Category'):
                        <span class="fw--500">{{ __($bookingDetails?->tour_package?->category->name) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Owner'):
                        <span class="fw--500">{{ __(ucfirst($bookingDetails?->owner_type)) }}</span>
                    </li>

                    @if ($bookingDetails?->owner_type == 'agency')
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Owner Email'):
                            <span class="fw--500">{{ __(ucfirst($bookingDetails?->agency->email)) }}</span>
                        </li>

                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Owner Phone'):
                            <span class="fw--500">{{ __(ucfirst($bookingDetails?->agency->mobile)) }}</span>
                        </li>
                    @else
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Owner Email'):
                            <span class="fw--500">{{ __(ucfirst($bookingDetails?->admin->email)) }}</span>
                        </li>
                    @endif
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Party Size'):
                        <span class="fw--500">{{ __($bookingDetails?->party_size) }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Price'):
                        <span
                            class="fw--500 badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails?->price) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Discount'):
                        <span
                            class="fw--500 badge badge--success">{{ $bookingDetails?->discount }}%</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Start Date'):
                        <span
                            class="fw--500">{{ $bookingDetails?->start_date ? showDateTime($bookingDetails->start_date, 'd-m-Y') : '—' }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('End Date'):
                        <span
                            class="fw--500">{{ $bookingDetails?->end_date ? showDateTime($bookingDetails->end_date, 'd-m-Y') : '—' }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Stay Day/Nights'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package?->day_nights }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('City'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package?->city }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('State'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package?->state }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Zip Code'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package?->zip_code }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Country'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package?->country }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Address'):
                        <span class="fw--500">{{ $bookingDetails?->tour_package?->address }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Tour Status'):

                        @php
                            echo $bookingDetails?->tour_package?->statusBadge(
                                $bookingDetails?->tour_package?->status,
                            );
                        @endphp
                    </li>
                </ul>
            </div>
        </div>
    </div>


        <div class="col-lg-6">
            <div class="tour-card radius--20 position-relative bg--white">
                <h5 class="mb-20">@lang('Booking Details')</h5>
                <ul class="list-group mb-3 gap--12">
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Full Name'):
                        <span class="fw--500">{{ $bookingDetails->user?->fullname }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Email'):
                        <span class="fw--500">{{ $bookingDetails->user?->email }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Phone'):
                        <span class="fw--500">{{ $bookingDetails->phone ?? $bookingDetails->user?->mobile }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Price Category'):
                        <span class="fw--500">{{ $bookingDetails->priceCategory->name ?? '—' }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Party Size'):
                        <span class="fw--500">{{ $bookingDetails->party_size }}</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Total Price'):
                        <span
                            class="fw--500 badge badge--success">{{ $general->cur_sym }}{{ showAmount($bookingDetails->price) }}</span>
                    </li>
                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Get Discount'):
                        <span class="fw--500 badge badge--success">{{ $bookingDetails->discount ?? 0 }}%</span>
                    </li>
                    @if ($bookingDetails?->start_date)
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Tour Start Date'):
                            <span
                                class="fw--500">{{ showDateTime($bookingDetails?->start_date, 'd-m-Y') }}</span>
                        </li>
                    @endif

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Payment Status'):
                        <span class="fw--500">@php
                            echo $bookingDetails->statusPaymentBadge();
                        @endphp</span>
                    </li>

                    <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                        @lang('Review Status'):
                        <span class="fw--500">@php echo $bookingDetails->agencyStatusBadge() @endphp</span>
                    </li>

                    @if ($bookingDetails->agency_status == 2 && $bookingDetails->decline_reason)
                        <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                            @lang('Decline Reason'):
                            <span class="fw--500">{{ $bookingDetails->decline_reason }}</span>
                        </li>
                    @endif
                </ul>

                @if (is_null($bookingDetails->agency_status))
                    @if (in_array($bookingDetails->status, [App\Constants\BookingStatus::PAID, App\Constants\BookingStatus::RESERVED]))
                        <form action="{{ route('agency.tour.package.booking.approve', $bookingDetails->id) }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn--success w--100 pills" style="color: #ffffff !important;">@lang('Approve')</button>
                        </form>
                    @else
                        <div class="mb-3">
                            <button type="button" class="btn btn--success w--100 pills" disabled>@lang('Approve')</button>
                            <p class="fs--14 mt-1 mb-0">@lang('Approval available once payment is confirmed.')</p>
                        </div>
                    @endif
                    <form action="{{ route('agency.tour.package.booking.decline', $bookingDetails->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="form--label">@lang('Decline Reason (optional)')</label>
                            <textarea name="reason" class="form--control form-control" rows="2" maxlength="1000"
                                placeholder="@lang('Let the tourist know why, if you want to')"></textarea>
                        </div>
                        <button type="submit" class="btn btn--danger w--100 pills">@lang('Decline')</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
