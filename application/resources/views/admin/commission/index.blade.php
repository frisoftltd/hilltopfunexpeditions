@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4 mb-4">
    <div class="col-sm-4">
        <div class="card prod-p-card background-pattern-white bg--primary">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5 text-white">@lang('Total Commission Collected')</h6>
                        <h3 class="m-b-0 text-white">{{ $general->cur_sym }}{{ showAmount($totalCollected) }}</h3>
                    </div>
                    <div class="col-auto">
                        <i class="dashboard-widget__icon fas fa-hand-holding-usd text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card prod-p-card background-pattern">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5">@lang('Total Commission Owed')</h6>
                        <h3 class="m-b-0">{{ $general->cur_sym }}{{ showAmount($totalOwed) }}</h3>
                    </div>
                    <div class="col-auto">
                        <i class="dashboard-widget__icon fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card prod-p-card background-pattern-white bg--primary">
            <div class="card-body">
                <div class="row align-items-center m-b-0">
                    <div class="col">
                        <h6 class="m-b-5 text-white">@lang('Grand Total Earned')</h6>
                        <h3 class="m-b-0 text-white">{{ $general->cur_sym }}{{ showAmount($grandTotal) }}</h3>
                    </div>
                    <div class="col-auto">
                        <i class="dashboard-widget__icon fas fa-coins text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 mb-4">
            <div class="card-header">
                <h5>@lang('Per-Agency Breakdown')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Agency')</th>
                                <th>@lang('Collected')</th>
                                <th>@lang('Owed')</th>
                                <th>@lang('Total')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agencyBreakdown as $agency)
                                <tr>
                                    <td>{{ $agency->fullname }}</td>
                                    <td>{{ $general->cur_sym }}{{ showAmount($agency->collected_amount ?? 0) }}</td>
                                    <td>{{ $general->cur_sym }}{{ showAmount($agency->owed_amount ?? 0) }}</td>
                                    <td><strong>{{ $general->cur_sym }}{{ showAmount(($agency->collected_amount ?? 0) + ($agency->owed_amount ?? 0)) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No commission records yet')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($agencyBreakdown->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($agencyBreakdown) }}
                </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5>@lang('Commission Owed')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Agency')</th>
                                <th>@lang('Tour')</th>
                                <th>@lang('Booking Amount')</th>
                                <th>@lang('Commission Amount')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($owedCommissions as $commission)
                                <tr>
                                    <td>{{ $commission->agency?->fullname }}</td>
                                    <td>{{ $commission->tourBooking?->tour_package?->title }}</td>
                                    <td>{{ $general->cur_sym }}{{ showAmount($commission->booking_amount) }}</td>
                                    <td>{{ $general->cur_sym }}{{ showAmount($commission->commission_amount) }}</td>
                                    <td>{{ showDateTime($commission->created_at) }}</td>
                                    <td>
                                        <button type="button" class="btn btn--sm btn--success confirmationBtn"
                                            title="@lang('Mark as Paid')"
                                            data-question="@lang('Mark this commission as paid?')"
                                            data-action="{{ route('admin.commission.mark.paid', $commission->id) }}">
                                            <i class="la la-check-circle"></i> @lang('Mark as Paid')
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('Nothing owed right now')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($owedCommissions->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($owedCommissions) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
