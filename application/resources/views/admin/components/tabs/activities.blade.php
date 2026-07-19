<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.report.login.history') ? 'active' : '' }}"
                    href="{{route('admin.report.login.history')}}">@lang('User')</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agency.report.login.history') ? 'active' : '' }}"
                    href="{{route('admin.agency.report.login.history')}}">@lang('Agency')
                </a>
            </li>
        </ul>
    </div>
</div>
