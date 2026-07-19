<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.booking.index') ? 'active' : '' }}"
                    href="{{ route('admin.tour.package.booking.index') }}">@lang('Tours by Admin')</a>
            </li>
    
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.booking.agency.index') ? 'active' : '' }}"
                    href="{{ route('admin.tour.package.booking.agency.index') }}">@lang('Agency Tours')
                    
                </a>
            </li>

         
        </ul>
    </div>
</div>
