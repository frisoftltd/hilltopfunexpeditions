@extends($activeTemplate . 'layouts.agency.master')
@section('content')
    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <div class="base--card radius--20 filter--wrap mb-4 px-3">
                <form action="" method="GET">
                    <div class="d-flex flex-wrap gap-4">
                        <div class="flex-grow-1">
                            <label class="form--label">@lang('Title')</label>
                            <input type="text" name="search" class="form--control " value="{{request()->search}}" placeholder="@lang('Search by title')">
                        </div>

                        <div class="flex-grow-1">
                            <label class="form--label">@lang('Category')</label>
                            <select name="category_id" class="form--control form--select form-select ">
                                <option value="">@lang('Select Category')</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{request()->category_id ==  $category->id ? 'selected':''}}>{{ __($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex-grow-1 align-self-end">
                            <button type="submit" class="btn btn--lg btn--base w-100"><i class="fas fa-check"></i>
                                @lang('Filter')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <div class="col-lg-12">
            <div class="base--card radius--20 table-responsive">
                <table class="table table--responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('Tour Title')</th>
                            <th>@lang('Image')</th>
                            <th>@lang('Category')</th>
                            <th>@lang('From Price')</th>
                            <th>@lang('Confirmed')</th>
                            <th>@lang('Pending')</th>
                            <th>@lang('Country')</th>
                            <th>@lang('Tour Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tourPackages as $item)
                            <tr>
                                <td data-label="@lang('Title')">{{ __($item->title) }}</td>

                                <td data-label="@lang('tourPackageImage')">
                                    <img src="{{ getImage(getFilePath('tourPackageImage') . '/' . $item->TourPackagePrimaryImage->image) }}"
                                        alt="@lang('image')" class="rounded img-thumb" style="width: 60px;height:60px;">
                                </td>

                                <td data-label="@lang('Category')">{{ __($item->category->name) }}</td>
                                <td data-label="@lang('From Price')">
                                    @if ($item->from_price !== null)
                                        {{ $general->cur_sym }}{{ showAmount($item->from_price) }}
                                    @else
                                        &mdash;
                                    @endif
                                </td>
                                <td data-label="@lang('Confirmed')">{{ $item->confirmed_bookings_count }}</td>
                                <td data-label="@lang('Pending')">{{ $item->pending_bookings_count }}</td>
                                <td data-label="@lang('Country')">{{ $item->country }}</td>

                                <td data-label="@lang('Tour Status')">
                                    @php
                                        echo $item->statusBadge($item->status);
                                    @endphp
                                </td>
                                <td data-label="@lang('Action')">
                                    <div class="d-flex align-items-center flex-nowrap gap-2">
                                        <a href="{{ route('agency.tour.package.edit', $item->id) }}"
                                            class="btn btn--base btn--sm pills"><i
                                                class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('agency.tour.package.status.change', $item->id) }}"
                                            method="POST" class="d-inline-block">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn--sm pills status-toggle-btn {{ $item->status == 1 ? 'btn--warning' : 'btn--success' }}"
                                                title="@lang('Toggle Active/Inactive')">
                                                {{ $item->status == 1 ? __('Deactivate') : __('Activate') }}
                                            </button>
                                        </form>
                                        <button title="@lang('Delete')" type="button"
                                            class="btn btn--sm pills btn--danger confirmationBtn"
                                            data-question="@lang('Are you sure to delete this tour package?')"
                                            data-action="{{ route('agency.tour.package.delete', $item->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" data-label="@lang('Gellery Table')" colspan="100%">
                                    {{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($tourPackages->hasPages())
        <div class="row mx-xxl-5 mx-lg-0 my-4">
            <div class="col-lg-12 justify-content-end d-flex">
                {{ $tourPackages->links() }}
            </div>
        </div>
    @endif

    <x-confirmation-modal></x-confirmation-modal>
@endsection

