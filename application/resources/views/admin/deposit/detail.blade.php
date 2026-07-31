@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30 justify-content-center">
    <div class="col-xl-4 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Payment Via') {{ __($deposit->gateway->name) }}</h5>
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Date')
                        <span class="fw-bold">{{ showDateTime($deposit->created_at) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Transaction Number')
                        <span class="fw-bold">{{ $deposit->trx }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Username')
                        <span class="fw-bold">
                            <a href="{{ route('admin.users.detail', $deposit->user_id) }}">{{ $deposit->user->username
                                }}</a>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Method')
                        <span class="fw-bold">{{ __($deposit->gateway->name) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Amount')
                        <span class="fw-bold">{{ showAmount($deposit->amount ) }} {{ __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Charge')
                        <span class="fw-bold">{{ showAmount($deposit->charge ) }} {{ __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('After Charge')
                        <span class="fw-bold">{{ showAmount($deposit->amount+$deposit->charge ) }} {{
                            __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Rate')
                        <span class="fw-bold">1 {{__($general->cur_text)}}
                            = {{ showAmount($deposit->rate) }} {{__($deposit->baseCurrency())}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payable')
                        <span class="fw-bold">{{ showAmount($deposit->final_amo ) }}
                            {{__($deposit->method_currency)}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Status')
                        @php echo $deposit->statusBadge @endphp
                    </li>
                    @if($deposit->admin_feedback)
                    <li class="list-group-item">
                        <strong>@lang('Admin Response')</strong>
                        <br>
                        <p>{{__($deposit->admin_feedback)}}</p>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @if($details || $deposit->status == 2)
    <div class="col-xl-8 col-md-6 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <h5 class="card-title mb-50 border-bottom pb-2">@lang('Payment Info')</h5>
                @if($details != null)
                @foreach(json_decode($details) as $val)
                @if($deposit->method_code >= 1000)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>{{__($val->name)}}</h6>
                        @if($val->type == 'checkbox')
                        {{ implode(',',$val->value) }}
                        @elseif($val->type == 'file')
                        @if($val->value)
                        @php
                            $attachmentExtension = strtolower(pathinfo($val->value, PATHINFO_EXTENSION));
                            $attachmentPath = encrypt(getFilePath('verify').'/'.$val->value);
                            $isPreviewable = in_array($attachmentExtension, ['jpg', 'jpeg', 'png', 'pdf']);
                        @endphp
                        @if($isPreviewable)
                        <button type="button" class="btn btn-sm btn--primary me-3" data-bs-toggle="modal"
                            data-bs-target="#attachmentModal{{ $loop->index }}">
                            <i class="fa fa-file"></i> @lang('Attachment')
                        </button>
                        <div class="modal fade" id="attachmentModal{{ $loop->index }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">@lang('Attachment')</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        @if($attachmentExtension == 'pdf')
                                        <iframe src="{{ route('admin.download.attachment', ['file_hash' => $attachmentPath, 'inline' => 1]) }}"
                                            style="width: 100%; height: 85vh; border: 0;"></iframe>
                                        @else
                                        <img src="{{ route('admin.download.attachment', ['file_hash' => $attachmentPath, 'inline' => 1]) }}"
                                            class="img-fluid" alt="@lang('Attachment')">
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('admin.download.attachment', $attachmentPath) }}"
                                            class="btn btn--primary"><i class="fa fa-download"></i> @lang('Download')</a>
                                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('admin.download.attachment', $attachmentPath) }}"
                            class="me-3"><i class="fa fa-file"></i> @lang('Attachment') </a>
                        @endif
                        @else
                        @lang('No File')
                        @endif
                        @else
                        <p>{{__($val->value)}}</p>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
                @if($deposit->method_code < 1000) @include('admin.deposit.gateway_data',['details'=>
                    json_decode($details)])
                    @endif
                    @endif
                    @if($deposit->status == 2)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button class="btn btn--success ms-1 confirmationBtn"
                                data-action="{{ route('admin.deposit.approve', $deposit->id) }}"
                                data-question="@lang('Are you sure to approve this transaction?')"><i
                                    class="fas fa-check"></i>
                                @lang('Approve')
                            </button>

                            <button class="btn btn--danger ms-1 rejectBtn" data-id="{{ $deposit->id }}"
                                data-info="{{$details}}"
                                data-amount="{{ showAmount($deposit->amount)}} {{ __($general->cur_text) }}"
                                data-username="{{ $deposit->user->username }}"><i class="fas fa-ban"></i>
                                @lang('Reject')
                            </button>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Reject Payment Confirmation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.deposit.reject')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span
                            class="fw-bold withdraw-amount text-success"></span> @lang('payment of') <span
                            class="fw-bold withdraw-user"></span>?</p>

                    <div class="form-group">
                        <label class="fw-bold mt-2">@lang('Reason for Rejection')</label>
                        <textarea name="message" maxlength="255" class="form-control" rows="5"
                            required>{{ old('message') }}</textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-global">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-confirmation-modal></x-confirmation-modal>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";

        $('.rejectBtn').on('click', function () {
            var modal = $('#rejectModal');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('.withdraw-amount').text($(this).data('amount'));
            modal.find('.withdraw-user').text($(this).data('username'));
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush