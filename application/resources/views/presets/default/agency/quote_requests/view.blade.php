@extends($activeTemplate.'layouts.agency.master')
@section('content')

<div class="row gy-4">
    <div class="col-lg-8">
        <div class="base--card radius--20">
            <h5 class="mb-3">@lang('Quote Request Details')</h5>
            <ul class="list-group mb-3 gap--12">
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Tour Package'):
                    <span class="fw--500">{{ $item->tourPackage->title ?? '—' }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Full Name'):
                    <span class="fw--500">{{ $item->name }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Email'):
                    <span class="fw--500">{{ $item->email }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Phone'):
                    <span class="fw--500">{{ $item->phone ?? '—' }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Party Size'):
                    <span class="fw--500">{{ $item->party_size }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Start Date'):
                    <span class="fw--500">{{ showDateTime($item->start_date, 'd-m-Y') }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('End Date'):
                    <span class="fw--500">{{ showDateTime($item->end_date, 'd-m-Y') }}</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Status'):
                    <span class="fw--500">@php echo $item->statusBadge(); @endphp</span>
                </li>
                <li class="list-group-item p-0 border-0 d-flex justify-content-between align-items-center">
                    @lang('Submitted'):
                    <span class="fw--500">{{ showDateTime($item->created_at) }}</span>
                </li>
            </ul>

            @if ($item->message)
                <h6 class="mb-2">@lang('Message')</h6>
                <p class="mb-0">{{ $item->message }}</p>
            @endif
        </div>
    </div>
</div>

@endsection
