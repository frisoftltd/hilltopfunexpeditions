@extends($activeTemplate . 'layouts.agency.master')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="base--card radius--20">
                @if ($agency->kyc_data)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('Field Name')</th>
                                    <th>@lang('Value')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($agency->kyc_data as $val)
                                    @continue(!$val->value)
                                    <tr>
                                        <td>{{ __($val->name) }}</td>
                                        <td>
                                            @if ($val->type == 'checkbox')
                                                {{ implode(', ', $val->value) }}
                                            @elseif ($val->type == 'file')
                                                <a href="{{ route('agency.attachment.download', encrypt(getFilePath('verify') . '/' . $val->value)) }}">
                                                    <i class="fas fa-download"></i> @lang('Download Attachment')
                                                </a>
                                            @else
                                                {{ __($val->value) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <h5 class="text-center mt-3">@lang('KYC data not found')</h5>
                @endif
            </div>
        </div>
    </div>
@endsection
