@extends($activeTemplate . 'layouts.agency.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-6">
        <div class="tour-card radius--20 position-relative bg--white">
            <div class="tour-card__content">
                <h6 class="fw--500 mb-2">@lang('Commission Paid')</h6>
                <h4 class="fw--700">{{ $general->cur_sym }}{{ showAmount($totalPaid) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="tour-card radius--20 position-relative bg--white">
            <div class="tour-card__content">
                <h6 class="fw--500 mb-2">@lang('Commission Owed')</h6>
                <h4 class="fw--700">{{ $general->cur_sym }}{{ showAmount($totalOwed) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="tour-card radius--20 position-relative bg--white p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Tour')</th>
                            <th>@lang('Booking Amount')</th>
                            <th>@lang('Commission Amount')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $commission)
                            <tr>
                                <td>{{ $commission->tourBooking?->tour_package?->title }}</td>
                                <td>{{ $general->cur_sym }}{{ showAmount($commission->booking_amount) }}</td>
                                <td>{{ $general->cur_sym }}{{ showAmount($commission->commission_amount) }}</td>
                                <td>{{ showDateTime($commission->created_at) }}</td>
                                <td>
                                    @if ($commission->status == \App\Models\Commission::COLLECTED)
                                        <span class="badge badge--success">@lang('Paid')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Owed')</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">@lang('No commission records yet')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($commissions->hasPages())
                <div class="p-3">
                    {{ paginateLinks($commissions) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
