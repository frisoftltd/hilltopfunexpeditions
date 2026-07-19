@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row gy-4 pb-4" id="sortable-container" class="sortable-container">
        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard2">
            <a class="d-block" href="{{ route('user.tour.package.booking.my.list') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('All Bookings')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_tour_package'] }}</h6>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard3">
            <a class="d-block" href="{{ route('user.tour.package.booking.canceled') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Pending Bookings')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_pending_tour_package'] }}</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard4">
            <a class="d-block" href="{{ route('user.tour.package.booking.approved') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Approved Bookings')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-plane"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_approved_tour_package'] }}</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard6">
            <a class="d-block" href="{{ route('user.get.wishlist') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Wishlists')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['wishlists'] }}</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('ticket') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Total Ticket')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_support_ticker'] }}</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('ticket.open') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Open Ticket')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_open_support_ticker'] }}</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('ticket.open') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Active Ticket')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_active_support_ticker'] }}</h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('user.transactions') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Total Transactions')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_transaction'] }}</h6>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row gy-4 pb-4">
        <div class="col-lg-6">
            <div class="base--card radius--20">
                <h6>@lang('Monthly Tour Booking Chart')</h6>
                <div id="tourPackageChart"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="base--card radius--20">
                <h6>@lang('Monthly Payments Chart')</h6>
                <div id="depositChart"></div>
            </div>
        </div>
    </div>
    <div class="row gy-4 pb-4">
        <div class="col-lg-12">
            <div class="base--card radius--20">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('SI')</th>
                            <th>@lang('Tour Package Title')</th>
                            <th>@lang('Tour Date')</th>
                            <th>@lang('Stay Day & Nights')</th>
                            <th>@lang('Total Price')</th>
                            <th>@lang('Discount')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myBookings as $item)
                            <tr>
                                <td data-label="@lang('SI')"><span class="">{{ $loop->iteration }}</span>
                                </td>

                                <td class="text-center" data-label="@lang('Tour Package Title')">
                                    {{ __($item->tour_package->title) }}
                                </td>
                                <td class="text-center" data-label="@lang('Tour Date')">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ showDateTime($item->tour_package->tour_start) }}
                                </td>
                                <td class="text-center" data-label="@lang('Stay Day & Nights')">
                                    {{ __($item->tour_package->day_nights) }}
                                </td>
                                <td class="text-center" data-label="@lang('Price')">
                                    {{ $general->cur_sym }}{{ showAmount($item->price) }}
                                </td>

                                <td class="text-center" data-label="@lang('Discount')">
                                    {{ $general->cur_sym }}{{ showAmount($item->discount) }}
                                </td>

                                <td class="text-center" data-label="@lang('Status')">
                                    @php echo $item->statusBadge($item->status) @endphp
                                </td>
                                <td data-label="@lang('Action')">
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')"
                                        href="{{ route('tour.package.details', [$item->tour_package->id, slug($item->tour_package->title)]) }}">
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
    @if ($myBookings->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $myBookings->links() }}
            </div>
        </div>
    @endif
@endsection


@push('script-lib')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>
@endpush

@push('script')
    <script>
        // tourPackageChart
        (function() {
            "use strict";
            var options = {
                chart: {
                    type: 'area',
                    stacked: false,
                    height: '310px'
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%'
                    }
                },
               colors: ['#39bff9', '#39bff9a6'],
                series: [{
                    name: '@lang('Tour Package')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode($tourPackageChart['values']); ?>')
                }],
                fill: {
                    opacity: [0.85, 1],
                },
                labels: JSON.parse('<?php echo json_encode($tourPackageChart['labels']); ?>'),
                markers: {
                    size: 0
                },
                xaxis: {
                    type: 'text'
                },
                yaxis: {
                    min: 0
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return "$ " + y.toFixed(0);
                            }
                            return y;

                        }
                    }
                },
                legend: {
                    labels: {
                        useSeriesColors: true
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#tourPackageChart"),
                options
            );
            chart.render();
        })();

        (function() {
            "use strict";
            var options = {
                chart: {
                    type: 'area',
                    stacked: false,
                    height: '310px'
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%'
                    }
                },
                colors: ['#39bff9', '#39bff9a6'],
                series: [{
                    name: '@lang('Payments Chart')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode($depositsChart['values']); ?>')
                }],
                fill: {
                    opacity: [0.85, 1],
                },
                labels: JSON.parse('<?php echo json_encode($depositsChart['labels']); ?>'),
                markers: {
                    size: 0
                },
                xaxis: {
                    type: 'text'
                },
                yaxis: {
                    min: 0
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(y) {
                            if (typeof y !== "undefined") {
                                return "$ " + y.toFixed(0);
                            }
                            return y;

                        }
                    }
                },
                legend: {
                    labels: {
                        useSeriesColors: true
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#depositChart"),
                options
            );
            chart.render();
        })();
    </script>
@endpush
