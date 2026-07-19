<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.active') ? 'active' : '' }}"
                href="{{route('admin.agencies.active')}}">@lang('Active')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.all') ? 'active' : '' }}"
                    href="{{route('admin.agencies.all')}}">@lang('All')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.banned') ? 'active' : '' }}"
                    href="{{route('admin.agencies.banned')}}">@lang('Banned')
                    @if($bannedAgenciesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$bannedAgenciesCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.email.unverified') ? 'active' : '' }}"
                    href="{{route('admin.agencies.email.unverified')}}">@lang('Email Unverified')
                    @if($emailUnverifiedAgenciesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$emailUnverifiedAgenciesCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.mobile.unverified') ? 'active' : '' }}"
                    href="{{route('admin.agencies.mobile.unverified')}}">@lang('Mobile Unverified')
                    @if($mobileUnverifiedAgenciesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$mobileUnverifiedAgenciesCount}}</span>
                    @endif
                </a>
            </li>
    

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.kyc.unverified') ? 'active' : '' }}"
                    href="{{route('admin.agencies.kyc.unverified')}}">@lang('Kyc Unverified')
                    @if($kycUnverifiedAgenciesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$kycUnverifiedAgenciesCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.kyc.pending') ? 'active' : '' }}"
                    href="{{route('admin.agencies.kyc.pending')}}">@lang('Kyc Pending')
                    @if($kycPendingAgenciesCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$kycPendingAgenciesCount}}</span>
                    @endif
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.with.balance') ? 'active' : '' }}"
                    href="{{route('admin.agencies.with.balance')}}">@lang('With Balance')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.subscriber.index') ? 'active' : '' }}"
                    href="{{route('admin.subscriber.index')}}">@lang('Subscribers')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agencies.notification.all') ? 'active' : '' }}"
                    href="{{route('admin.agencies.notification.all')}}">@lang('Notification to agencies')
                </a>
            </li>
        </ul>
    </div>
</div>