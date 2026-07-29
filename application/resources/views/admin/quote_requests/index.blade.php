@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light">
                        <thead>
                            <tr>
                                <th>@lang('Package')</th>
                                <th>@lang('Requester')</th>
                                <th>@lang('Agency')</th>
                                <th>@lang('Dates')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ __(strLimit($item->tourPackage->title ?? '—', 30)) }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $item->name }}</span><br>
                                    <span class="text--muted">{{ $item->email }}</span>
                                </td>
                                <td>
                                    {{ $item->agency?->fullname ?? '—' }}
                                </td>
                                <td>
                                    {{ showDateTime($item->start_date, 'M d') }} – {{ showDateTime($item->end_date, 'M d, Y') }}
                                </td>
                                <td>
                                    @php echo $item->statusBadge(); @endphp
                                </td>
                                <td>
                                    <a title="@lang('Details')" href="{{ route('admin.quote.requests.view', $item->id) }}"
                                        class="btn btn-sm btn--primary ms-1">
                                        <i class="las la-eye text--shadow"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($items->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($items) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>
@endsection
