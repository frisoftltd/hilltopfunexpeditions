<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.index') ? 'active' : '' }}"
                    href="{{route('admin.tour.package.index')}}">@lang('All')
                    @if($allTourPackages)
                    <span class="badge rounded-pill bg--white text-muted">{{$allTourPackages}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.my.list') ? 'active' : '' }}"
                    href="{{route('admin.tour.package.my.list')}}">@lang('My list')
                    @if($myTourPackages)
                    <span class="badge rounded-pill bg--white text-muted">{{$myTourPackages}}</span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.all.agency') ? 'active' : '' }}"
                    href="{{route('admin.tour.package.all.agency')}}">@lang('Agencies Tour')
                    @if($allAgencyTourPackages)
                    <span class="badge rounded-pill bg--white text-muted">{{$allAgencyTourPackages}}</span>
                    @endif
                </a>
            </li>
          

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.active') ? 'active' : '' }}"
                    href="{{route('admin.tour.package.active')}}">@lang('Active')

                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.tour.package.pending') ? 'active' : '' }}"
                    href="{{route('admin.tour.package.pending')}}">@lang('Inactive')
                </a>
            </li>
        </ul>
    </div>
</div>