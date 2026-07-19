@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4 mb-4">
        <div class="col-xl-6">
            <div class="row gy-4">

                    <div class="col-sm-12">
                        <div class="card p-3 rounded-3">
                            <div class="row g-0">
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-plane"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.tour.package.index') }}">
                                            </a>
                                            <h5>{{ $widget['total_tour_package'] }}</h5>
                                            <span>@lang('Tour Packages')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-users"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.agencies.all') }}"></a>
                                            <h5>{{ $widget['total_agencies'] }}</h5>
                                            <span>@lang('Total Agencies')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4 ">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-user-check"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.agencies.active') }}"></a>
                                            <h5>{{ $widget['verified_agencies'] }}</h5>
                                            <span>@lang('Active Agencies')</span>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-credit-card"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.withdraw.pending') }}"></a>
                                            <h5>{{ $withdrawals['total_withdraw_pending'] }}</h5>
                                            <span>@lang('Pending Withdrawals')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-ticket-alt"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.agency.ticket') }}"></a>
                                            <h5>{{ $widget['tickets_agencies'] }}</h5>
                                            <span>@lang('Agency Tickets')</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-envelope"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.agencies.email.unverified') }}"></a>
                                            <h5>{{ $widget['email_unverified_agencies'] }}</h5>
                                            <span>@lang('Email Unverified')</span>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="card p-3 rounded-3">
                            <div class="row g-0">

                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-calendar-alt"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.tour.package.booking.index') }}">
                                            </a>
                                            <h5>{{ $widget['total_tour_bookings'] }}</h5>
                                            <span>@lang('Total Tours')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-users"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.users.all') }}"></a>
                                            <h5>{{ $widget['total_users'] }}</h5>
                                            <span>@lang('Total Users')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4 ">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-user-check"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.users.active') }}"></a>
                                            <h5>{{ $widget['verified_users'] }}</h5>
                                            <span>@lang('Active Users')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-envelope"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.users.email.unverified') }}"></a>
                                            <h5>{{ $widget['email_unverified_users'] }}</h5>
                                            <span>@lang('Email Unverified')</span>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-ticket-alt"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.ticket') }}"></a>
                                            <h5>{{ $widget['tickets_users'] }}</h5>
                                            <span>@lang('Total Tickets')</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 col-xl-6 col-xxl-4">
                                    <div class="dashboard-widget">
                                        <div class="dashboard-widget__icon">
                                            <i class="dashboard-card-icon las la-spinner"></i>
                                        </div>
                                        <div class="dashboard-widget__content">
                                            <a title="@lang('View all')" class="dashboard-widget-link"
                                                href="{{ route('admin.deposit.pending') }}"></a>
                                            <h5>{{ $deposit['total_deposit_pending'] }}</h5>
                                            <span>@lang('Pending Deposits')</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

            </div>

        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Monthly Payments & Withdraw Report') (@lang('This year'))</h5>
                    <div id="account-chart"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-12">
            <div class="row gy-4">
                <div class="col-sm-3">
                    <a href="{{ route('admin.deposit.list') }}">
                        <div class="card prod-p-card  background-pattern-white bg--primary">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">@lang('Total Payments')</h6>
                                        <h3 class="m-b-0 text-white">
                                            {{ $general->cur_sym }}{{ showAmount($deposit['total_deposit_amount']) }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-hand-holding-usd text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.deposit.list') }}">
                        <div class="card prod-p-card background-pattern">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">@lang('Payments Charge')</h6>
                                        <h3 class="m-b-0">
                                            {{ $general->cur_sym }}{{ showAmount($deposit['total_deposit_charge']) }}</h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-percentage"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.withdraw.log') }}">
                        <div class="card prod-p-card background-pattern-white bg--primary">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white">@lang('Total Withdrawal')</h6>
                                        <h3 class="m-b-0 text-white">
                                            {{ $general->cur_sym }}{{ showAmount($withdrawals['total_withdraw_amount']) }}
                                        </h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon lar la-credit-card text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ route('admin.withdraw.approved') }}">
                        <div class="card prod-p-card background-pattern">
                            <div class="card-body">
                                <div class="row align-items-center m-b-0">
                                    <div class="col">
                                        <h6 class="m-b-5">@lang('Withdrawal Charge')</h6>
                                        <h3 class="m-b-0">
                                            {{ $general->cur_sym }}{{ showAmount($withdrawals['total_withdraw_charge']) }}
                                        </h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="dashboard-widget__icon fas fa-percentage"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>

    </div>
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="row gy-4 mb-4">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Daily Logins Users') (@lang('Last 10 days'))</h5>
                            <div id="login-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">@lang('Daily Logins Agencies') (@lang('Last 10 days'))</h5>
                            <div id="agency-login-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/apexcharts.min.js') }}"></script>

    <script>
        "use strict";
        // [ account-chart ] start
        (function() {
            var options = {
                chart: {
                    type: 'area',
                    stacked: false,
                    height: '268px'
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
                colors: ['#39bff9', '#39bff9'],
                series: [{
                    name: '@lang('Withdrawals')',
                    type: 'column',
                    data: JSON.parse('<?php echo json_encode($withdrawalsChart['values']); ?>')
                }, {
                    name: '@lang('Deposits')',
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
                document.querySelector("#account-chart"),
                options
            );
            chart.render();
        })();

        // [ User login-chart ] start
        (function() {
            var options = {
                series: [{
                    name: "User Count",
                    data: JSON.parse('<?php echo json_encode($userLogins['values']); ?>')
                }],
                chart: {
                    height: '310px',
                    type: 'area',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: ['#39bff9'],
                labels: JSON.parse('<?php echo json_encode($userLogins['labels']); ?>'),
                xaxis: {
                    type: 'date',
                },
                yaxis: {
                    opposite: true
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart = new ApexCharts(document.querySelector("#login-chart"), options);
            chart.render();
        })();

        // [ Agency login-chart ] start
        (function() {
            var options = {
                series: [{
                    name: "Agency Count",
                    data: JSON.parse('<?php echo json_encode($agencyLogins['values']); ?>')
                }],
                chart: {
                    height: '310px',
                    type: 'area',
                    zoom: {
                        enabled: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                colors: ['#39bff9'],
                labels: JSON.parse('<?php echo json_encode($agencyLogins['labels']); ?>'),
                xaxis: {
                    type: 'date',
                },
                yaxis: {
                    opposite: true
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart = new ApexCharts(document.querySelector("#agency-login-chart"), options);
            chart.render();
        })();
    </script>
@endpush
