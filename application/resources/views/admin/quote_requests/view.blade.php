@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-8">
        <div class="card b-radius--10">
            <div class="card-body">
                <h5 class="mb-3">@lang('Quote Request Details')</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Tour Package'):
                        <span class="fw-bold">{{ $item->tourPackage->title ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Agency'):
                        <span class="fw-bold">{{ $item->agency?->fullname ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Full Name'):
                        <span class="fw-bold">{{ $item->name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Email'):
                        <span class="fw-bold">{{ $item->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Phone'):
                        <span class="fw-bold">{{ $item->phone ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Party Size'):
                        <span class="fw-bold">{{ $item->party_size }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Start Date'):
                        <span class="fw-bold">{{ showDateTime($item->start_date, 'd-m-Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('End Date'):
                        <span class="fw-bold">{{ showDateTime($item->end_date, 'd-m-Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Status'):
                        @php echo $item->statusBadge(); @endphp
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Submitted'):
                        <span class="fw-bold">{{ showDateTime($item->created_at) }}</span>
                    </li>
                </ul>

                @if ($item->message)
                    <h6 class="mb-2">@lang('Message')</h6>
                    <p class="mb-0">{{ $item->message }}</p>
                @endif
            </div>
        </div>

        <x-message-thread :quote-request="$item" :reply-url="route('admin.quote.requests.reply', $item->id)" sender-type="admin" />
    </div>
</div>
@endsection
