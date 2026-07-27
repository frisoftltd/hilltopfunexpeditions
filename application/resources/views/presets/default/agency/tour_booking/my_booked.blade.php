@extends($activeTemplate . 'layouts.agency.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <form action="" method="GET">
                <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}"
                            placeholder="@lang('Search by tour package title.')">
                        <button type="submit" class="input-group-text bg--base text-white border-0"><i
                                class="las la-search"></i></button>
                    </div>
                </div>
            </form>
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('SI')</th>
                            <th>@lang('Tour Package Title')</th>
                            <th>@lang('Next Booking')</th>
                            <th>@lang('Bookings')</th>
                            <th>@lang('Tour Status')</th>
                            <th>@lang('Booking Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingTourPackages as $item)
                            <tr>
                                <td data-label="@lang('SI')"><span>{{ $loop->iteration }}</span></td>



                                <td class="text-center" data-label="@lang('Tour Package Title')">
                                    {{ __($item->title)}}
                                </td>
                                <td class="text-center" data-label="@lang('Next Booking')">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $item->tour_bookings->where('status', 1)->where('start_date', '>=', now())->sortBy('start_date')->first()?->start_date?->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="text-center" data-label="@lang('Bookings')">
                                    {{ $item->tour_bookings->where('status', '!=', 3)->sum('party_size') }}
                                </td>

                                <td class="text-center" data-label="@lang('Tour Status')">
                                    @php echo ($item->statusBadge($item->status)) @endphp
                                </td>
                                <td class="text-center" data-label="@lang('Status')">
                                    @php echo ($item->tourPositionBadge()) @endphp
                                </td>

                                <td data-label="@lang('Action')">
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Bookings for this Package')"
                                        href="{{ route('agency.tour.package.booking.user.list', $item->id) }}">
                                        <i class="la la-eye"></i>
                                    </a>
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Edit Package')"
                                        href="{{ route('agency.tour.package.edit', $item->id) }}">
                                        <i class="fas fa-edit"></i>
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

    @if ($bookingTourPackages->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $bookingTourPackages->links() }}
            </div>
        </div>
    @endif

@endsection
