@extends($activeTemplate.'layouts.agency.master')
@section('content')

<div class="row gy-4 mb-4">
    <div class="col-lg-12">
       <div class="base--card radius--20">
        <table class="table table--responsive--lg">
            <thead>
            <tr>
                <th>@lang('Package')</th>
                <th>@lang('Requester')</th>
                <th>@lang('Dates')</th>
                <th>@lang('Status')</th>
                <th>@lang('Action')</th>
            </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td data-label="@lang('Package')">{{ __($item->tourPackage->title ?? '—') }}</td>
                        <td data-label="@lang('Requester')">
                            {{ $item->name }}<br>
                            <span class="fs--14 text--black7">{{ $item->email }}</span>
                        </td>
                        <td data-label="@lang('Dates')">
                            {{ showDateTime($item->start_date, 'M d') }} – {{ showDateTime($item->end_date, 'M d, Y') }}
                        </td>
                        <td data-label="@lang('Status')">
                            @php echo $item->statusBadge(); @endphp
                        </td>
                        <td data-label="@lang('Action')">
                            <a href="{{ route('agency.quote.requests.view', $item->id) }}" class="btn btn--base btn-md action--btn">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" data-label="@lang('Package')" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
       </div>
    </div>
</div>

@if ($items->hasPages())
<div class="row mx-xxl-5 mx-lg-0 my-4">
    <div class="col-lg-12 justify-content-end d-flex">
        {{ $items->links() }}
    </div>
</div>
@endif

@endsection
