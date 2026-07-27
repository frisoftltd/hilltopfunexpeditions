@extends($activeTemplate . 'layouts.agency.master')
@section('content')
    <div class="row mb-3">
        <div class="col-lg-12">
            <a href="{{ route('agency.tour.package.booking.my.list') }}" class="btn btn-md btn--base pills">
                <i class="la la-arrow-left"></i> @lang('Back to Package List')
            </a>
        </div>
    </div>
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <ul class="custom--tabs buy-sell d-flex flex-wrap gap--4 z--1 mb-4" role="tablist">
                <li class="nav-item">
                    <a href="{{ route('agency.tour.package.booking.user.list', $id) }}"
                        class="btn nav-link pills {{ Route::is('agency.tour.package.booking.user.list') ? 'active' : '' }}">@lang('All')</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('agency.tour.package.booking.user.list.pending', $id) }}"
                        class="btn nav-link pills {{ Route::is('agency.tour.package.booking.user.list.pending') ? 'active' : '' }}">@lang('Pending')</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('agency.tour.package.booking.user.list.approved', $id) }}"
                        class="btn nav-link pills {{ Route::is('agency.tour.package.booking.user.list.approved') ? 'active' : '' }}">@lang('Approved')</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('agency.tour.package.booking.user.list.declined', $id) }}"
                        class="btn nav-link pills {{ Route::is('agency.tour.package.booking.user.list.declined') ? 'active' : '' }}">@lang('Declined')</a>
                </li>
            </ul>
            <form action="" method="GET">
                <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}"
                            placeholder="@lang('Search by user name')">
                        <button type="submit" class="input-group-text bg--base text-white border-0"><i
                                class="las la-search"></i></button>
                    </div>
                </div>
            </form>
            <div class="base--card radius--20 table-responsive">
                <table class="table table--responsive--lg booking-user-list-table">
                    <thead>
                        <tr>
                            <th>@lang('SI')</th>
                            <th>@lang('Full Name')</th>
                            <th>@lang('Price Category')</th>
                            <th>@lang('Party Size')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Phone')</th>
                            <th>@lang('Payment Status')</th>
                            <th>@lang('Review Status')</th>
                            <th>@lang('Action')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tourBookings as $item)
                            <tr>
                                <td data-label="@lang('SI')"><span>{{ $loop->iteration }}</span></td>

                                <td class="text-center col-truncate" data-label="@lang('Full Name')"
                                    title="{{ $item->user->fullname }}">
                                    {{ $item->user->fullname }}
                                </td>

                                <td class="text-center" data-label="@lang('Price Category')">
                                    {{ $item->priceCategory->name ?? '—' }}
                                </td>

                                <td class="text-center" data-label="@lang('Party Size')">
                                    {{ $item->party_size }}
                                </td>

                                <td class="text-center" data-label="@lang('Price')">
                                    {{ $general->cur_sym . $item->price }}
                                </td>

                                <td class="text-center col-truncate" data-label="@lang('Email')"
                                    title="{{ $item->user->email }}">
                                    {{ $item->user->email }}
                                </td>
                                <td data-label="@lang('Phone')">
                                    {{ $item->phone ?? $item->user->mobile }}
                                </td>


                                <td data-label="@lang('Payment Status')">
                                    @php
                                        echo ($item->statusPaymentBadge())
                                    @endphp
                                </td>
                                <td data-label="@lang('Review Status')">
                                    @php echo $item->agencyStatusBadge() @endphp
                                </td>
                                <td data-label="@lang('Action')">
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('User List')"href="{{ route('agency.tour.package.booking.details', $item->id) }}">
                                        <i class="la la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td data-label="@lang('Tour Table')" class="text-muted text-center" colspan="100%">
                                    {{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($tourBookings->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $tourBookings->links() }}
            </div>
        </div>
    @endif
@endsection
