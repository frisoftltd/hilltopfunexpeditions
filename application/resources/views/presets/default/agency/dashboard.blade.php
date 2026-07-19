@extends($activeTemplate . 'layouts.agency.master')
@section('content')
    <div class="row gy-4 mb-3 align-items-center">
        <div class="col-lg-12">
            @if (auth('agency')->user()->kv == 0)
            <div class="alert alert-warning radius--20">
                <div class="kyc-noty d-flex justify-content-between align-items-center" role="alert">
                    <h5 class="alert-heading mb-0">@lang('KYC Verification required')</h5>
                    <hr>
                    <p class="mb-0">
                        <a href="{{ route('agency.kyc.form') }}" class="btn btn--base btn--md pills">
                            @lang('Click Here to Verify')</a>
                        </p>
                    </div>
                    </div>
                    @elseif(auth('agency')->user()->kv == 2)
                    <div class="alert alert-warning radius--20">
                    <div class="kyc-noty kyc-noty-pending d-flex justify-content-between align-items-center" role="alert">
                        <h5 class="alert-heading mb-0">@lang('KYC Verification pending')</h5>
                        <hr>
                        <p class="mb-0"> <a href="{{ route('agency.kyc.data') }}"
                                class="btn btn--base btn--md pills">@lang('See KYC Data')</a></p>
                    </div>
                    </div>
                @endif
            </div>
        </div>


    <div class="row gy-4 pb-4" id="sortable-container" class="sortable-container">

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard6">
            <a class="d-block" href="javascript:void(0)">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Balance')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-money-bill"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ showAmount($widget['balance']) }}</h6>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard2">
            <a class="d-block" href="{{ route('agency.tour.package.my.list') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Total Tour Package')</h6>
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
            <a class="d-block" href="{{ route('agency.tour.package.active') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Pending Tour Package')</h6>
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
            <a class="d-block" href="{{ route('agency.tour.package.pending') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Active Tour Package')</h6>
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

        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('agency.tour.package.booking.my.list') }}">
                <div class="wizard-card d-flex flex-column">
                    <div class="content-wrap d-flex align-items-center justify-content-between gap--12">
                        <h6 class="title fw--400 fs--16 mb-0 ">@lang('Total Booking')</h6>
                        <div
                            class="icon-wrap d-flex justify-content-center align-items-center position-relative overflow-hidden z--1">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                    </div>
                    <div class="amount-wrap">
                        <h6 class="amount mb-2 fs--24">{{ $widget['total_bookings'] }}</h6>
                    </div>
                </div>
            </a>
        </div>



        <div class="col-xxl-3 col-xl-4 col-lg-6 col-sm-6" draggable="true" id="wizard7">
            <a class="d-block" href="{{ route('agency.ticket') }}">
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
            <a class="d-block" href="{{ route('agency.ticket.open') }}">
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
            <a class="d-block" href="{{ route('agency.transactions') }}">
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
                <h6>@lang('Monthly Tour Booked Chart')</h6>
                <div id="tourPackageChart"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="base--card radius--20">
                <h6>@lang('Monthly Withdrawals Chart')</h6>
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
                            <th>@lang('Image')</th>
                            <th>@lang('Tour Package Title')</th>
                            <th>@lang('Tour Date')</th>
                            <th>@lang('Tour End')</th>
                            <th>@lang('Total seats')</th>
                            <th>@lang('Available seats')</th>
                            <th>@lang('Tour Status')</th>
                            <th>@lang('Booking Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myBooked as $item)
                            <tr>
                                <td data-label="@lang('SI')"><span>{{ $loop->iteration }}</span>
                                </td>

                                <td data-label="@lang('tourPackageImage')">
                                    <img src="{{ getImage(getFilePath('tourPackageImage') . '/' . $item->TourPackagePrimaryImage->image) }}"
                                        alt="@lang('image')" class="rounded img-thumb"
                                        style="width: 60px;height:60px;">
                                </td>

                                <td class="text-center" data-label="@lang('Tour Package Title')">
                                    {{ __($item->title) }}
                                </td>
                                <td class="text-center" data-label="@lang('Tour Date')">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ showDateTime($item->tour_start) }}
                                </td>
                                <td class="text-center" data-label="@lang('Tour Date')">
                                    {{ showDateTime($item->tour_end) }}
                                </td>
                                <td class="text-center" data-label="@lang('Total seats')">
                                    {{ $item->person_capability }}
                                </td>
                                <td class="text-center" data-label="@lang('Available seats')">
                                    {{ $item->person_capability - $item->booking_person }}
                                </td>

                                <td class="text-center" data-label="@lang('Tour Status')">
                                    @php echo ($item->statusBadge($item->status)) @endphp
                                </td>
                                <td class="text-center" data-label="@lang('Status')">
                                    @php echo ($item->tourPositionBadge()) @endphp
                                </td>

                                <td data-label="@lang('Action')">
                                    <a class="btn btn-md btn--base detailBtn action--btn" title="@lang('Details')"
                                        href="{{ route('tour.package.details', [$item->id, slug($item->title)]) }}">
                                        <i class="la la-eye"></i>
                                    </a>

                                    <a class="btn btn-md btn--base detailBtn action--btn"
                                        title="@lang('User List')"href="{{ route('agency.tour.package.booking.user.list', $item->id) }}">
                                        <i class="la la-users"></i>
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
    @if ($myBooked->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $myBooked->links() }}
            </div>
        </div>
    @endif
    </div>
@endsection


@push('script')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>

    <script>
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
                colors: ['#00adad', '#67BAA7'],
                series: [{
                    name: '@lang('Withdrawals')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode(collect($withdrawalsChart['values'])->map(fn($val) => (float) $val)); ?>')
                }],
                fill: {
                    opacity: [0.85, 1],
                },
                labels: JSON.parse('<?php echo json_encode($withdrawalsChart['labels']); ?>'),
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
                    },
                    markers: {
                        customHTML: [
                            function() {
                                return ''
                            },
                            function() {
                                return ''
                            }
                        ]
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#depositChart"),
                options
            );
            chart.render();
        })();

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
                colors: ['#00adad', '#67BAA7'],
                series: [{
                    name: '@lang('Tour Package')',
                    type: 'area',
                    data: JSON.parse('<?php echo json_encode(collect($tourPackageChart['values'])->map(fn($val) => (float) $val)); ?>')
                }],
                labels: JSON.parse('<?php echo json_encode($tourPackageChart['labels']); ?>') ?? ['No Data']),
                markers: {
                    size: 0,
                    showNullDataPoints: false
                },
                fill: {
                    opacity: [0.85, 1],
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
                    },
                    markers: {
                        customHTML: [
                            function() {
                                return ''
                            },
                            function() {
                                return ''
                            }
                        ]
                    }
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#tourPackageChart"),
                options
            );
            chart.render();
        })();
    </script>
@endpush
