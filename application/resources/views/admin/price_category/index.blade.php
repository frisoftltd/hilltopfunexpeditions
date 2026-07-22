@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S/N')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Sort Order')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($priceCategories as $key => $priceCategory)
                                <tr>
                                    <td data-label="@lang('S/N')">{{ $key + 1 }}</td>
                                    <td data-label="@lang('Name')">{{ __($priceCategory->name) }}</td>
                                    <td data-label="@lang('Sort Order')">{{ $priceCategory->sort_order }}</td>
                                    <td data-label="@lang('Status')">
                                        @php
                                            echo $priceCategory->status == 1
                                                ? '<span class="badge badge--success">' . trans('Active') . '</span>'
                                                : '<span class="badge badge--danger">' . trans('Disabled') . '</span>';
                                        @endphp
                                    </td>
                                    <td data-label="Action">
                                        <button type="button" class="btn btn-sm btn--primary edit"
                                            data-id="{{ $priceCategory->id }}" data-name="{{ $priceCategory->name }}"
                                            data-sort_order="{{ $priceCategory->sort_order }}">
                                            <i class="las la-edit text--shadow"></i>
                                        </button>
                                        <button class="btn btn--danger btn-sm me-3 confirmationBtn"
                                            data-question="@lang('Are you sure to change this status?')"
                                            data-action="{{ route('admin.price.category.status.change', $priceCategory->id) }}">
                                            <i class="las la-sync-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($priceCategories->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($priceCategories) }}
                </div>
            @endif
        </div>
    </div>
</div>

<!--add modal-->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.price.category.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add Price Category')</h5>
                    <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>@lang('Name'):</label>
                                <input type="text" class="form-control" name="name" placeholder="@lang('e.g. Foreigner')"
                                    required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>@lang('Sort Order'):</label>
                                <input type="number" class="form-control" name="sort_order" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!--edit modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('admin.price.category.update') }}" method="POST">
            @csrf
            <input type="hidden" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Price Category')</h5>
                    <button type="button" class="close btn btn-outline--danger" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>@lang('Name'):</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>@lang('Sort Order'):</label>
                                <input type="number" class="form-control" name="sort_order">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                </div>
            </div>
        </form>
    </div>
</div>

<x-confirmation-modal></x-confirmation-modal>
@endsection

@push('breadcrumb-plugins')
<div class="d-flex flex-wrap justify-content-end">
    <button type="button" class="btn btn--primary addModal me-2" data-toggle="modal"><i class="fas fa-plus"></i>
        @lang('Add New')
    </button>
</div>
@endpush

@push('script')
    <script>
        'use strict';
        $('.addModal').on('click', function() {
            $('#addModal').modal('show');
        });

        var modal = $('#editModal');
        $('.edit').on('click', function() {
            var name = $(this).data('name');
            var sortOrder = $(this).data('sort_order');
            var id = $(this).data('id');

            modal.find('input[name=id]').val(id);
            modal.find('input[name=name]').val(name);
            modal.find('input[name=sort_order]').val(sortOrder);
            modal.modal('show');
        });
    </script>
@endpush
