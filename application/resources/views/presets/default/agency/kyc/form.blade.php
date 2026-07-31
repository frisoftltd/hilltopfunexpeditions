@extends($activeTemplate.'layouts.agency.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="base--card radius--20">
            @if ($form)
            <form action="{{route('agency.kyc.submit')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row gy-3">
                    <div class="col-sm-12">
                        <x-custom-form identifier="act" identifierValue="agency_kyc"></x-custom-form>
                    </div>

                    <div class="col-sm-12 text-end">
                        <button type="submit" class="btn btn--base btn--lg pills">@lang('Save')</button>
                    </div>
                </div>
            </form>
            @else
            <h5 class="text-center mt-3">@lang('KYC verification is not yet configured. Please contact support.')</h5>
            @endif
        </div>
    </div>
</div>
@endsection
