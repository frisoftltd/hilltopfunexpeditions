@extends($activeTemplate.'layouts.user.master')
@section('content')
<div class="row gy-4 mb-4">
    <div class="col-lg-12">

            <form action="" method="GET">
                <div class="mb-3 d-flex justify-content-end w-25 ms-auto">
                    <div class="input-group">
                        <input type="text" name="search" class="form--control form-control bg--white" value="{{ request()->search }}"
                            placeholder="@lang('Search by transactions')">
                        <button type="submit" class="input-group-text bg--base text-white border-0">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </div>
            </form>
       
    </div>
</div>
<div class="row gy-4 mb-4">
    <div class="col-lg-12">
       <div class="base--card radius--20">
        <table class="table table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('Trx')</th>
                    <th>@lang('Transacted')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Detail')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td data-label="@lang('Trx')" class="fw--500">#{{ $trx->trx }}</td>
                    <td data-label="@lang('Transacted')"> {{ showDateTime($trx->created_at) }}</td>
                    <td data-label="@lang('Amount')" class="budget">
                        <span
                            class="fw-bold text--base">
                             {{showAmount($trx->amount)}} {{ $general->cur_text}}
                        </span>
                    </td>
                    
                    <td data-label="@lang('Detail')">{{ __($trx->details) }}</td>
                </tr>
                @empty
                <tr>
                    <td class="text-muted text-center" data-label="@lang('Transactions Table')" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>
@if($transactions->hasPages())
    <div class="row mx-xxl-5 mx-lg-0 my-4">
        <div class="col-lg-12 justify-content-end d-flex">
            {{ $transactions->links() }}
        </div>
    </div>
@endif
@endsection