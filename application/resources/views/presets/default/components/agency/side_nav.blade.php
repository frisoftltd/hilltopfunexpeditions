<div class="sidebar-menu">
    <span class="sidebar-menu__close"><i class="las la-times"></i></span>
    <div class="logo-wrapper px-3">
        <a href="{{route('home')}}" class="normal-logo" id="normal-logo">  <img width="150" src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="{{ config('app.name') }}">
        </a>
    </div>

    <ul class="sidebar-menu-list">

        <li class="sidebar-menu-list__item">
            <a href="{{route('agency.home')}}" class="sidebar-menu-list__link {{ Route::is('agency.home') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-gauge-high"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>



        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('agency.tour.package.my.list') ||isActiveRoute('agency.tour.package.create')||isActiveRoute('agency.tour.package.pending') || isActiveRoute('agency.tour.package.active') || isActiveRoute('agency.tour.package.running') || isActiveRoute('agency.tour.package.expired') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-plane"></i></span>
                <span class="text">@lang('Tour Package')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('agency.tour.package.my.list') ||isActiveRoute('agency.tour.package.create')||isActiveRoute('agency.tour.package.pending') ||isActiveRoute('agency.tour.package.active')  ||isActiveRoute('agency.tour.package.running') ||isActiveRoute('agency.tour.package.expired') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.tour.package.my.list') }}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.my.list') ? 'active' : '' }}">@lang('All List')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.tour.package.create') }}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.create') ? 'active' : '' }}">@lang('Create Tour')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.tour.package.active') }}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.active') ? 'active' : '' }}">@lang('Active')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.tour.package.running') }}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.running') ? 'active' : '' }}">@lang('Running')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.tour.package.expired') }}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.expired') ? 'active' : '' }}">@lang('Expired')</a>
                    </li>
                </ul>
            </div>
        </li>





        <li class="sidebar-menu-list__item">
            <a href="{{route('agency.tour.package.booking.my.list')}}" class="sidebar-menu-list__link {{ Route::is('agency.tour.package.booking.my.list') ? 'active' : '' }}">
                <span class="icon">

                    <i class="fa-solid fa-calendar-days"></i>
                </span>
                <span class="text">@lang('Tour Bookings')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('agency.kyc.form')||isActiveRoute('agency.kyc.data')  ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-shield-halved"></i></span>
                <span class="text">@lang('KYC')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('agency.kyc.form')||isActiveRoute('agency.kyc.data')  ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    @if (auth('agency')->user()-> kv != 1)

                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('agency.kyc.form')}}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.booking.my.list') ? 'active' : '' }}">@lang('KYC Form')</a>
                    </li>
                    @endif
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('agency.kyc.data')}}" class="sidebar-submenu-list__link {{ Route::is('agency.tour.package.booking.pending') ? 'active' : '' }}">@lang('KYC Data')</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('agency.withdraw') || isActiveRoute('agency.withdraw.history') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-money-check-dollar"></i></span>
                <span class="text">@lang('Withdraw')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('agency.withdraw') || isActiveRoute('agency.withdraw.history') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.withdraw') }}" class="sidebar-submenu-list__link {{ Route::is('agency.withdraw') ? 'active' : '' }}">@lang('Withdraw Now')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.withdraw.history') }}" class="sidebar-submenu-list__link {{ Route::is('agency.withdraw.history') ? 'active' : '' }}">@lang('Withdraw Log')</a>
                    </li>
                </ul>
            </div>
        </li>



        <li class="sidebar-menu-list__item">
            <a href="{{route('agency.transactions')}}" class="sidebar-menu-list__link {{ Route::is('agency.transactions') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-arrow-right-arrow-left"></i></span>
                <span class="text">@lang('Transaction')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('agency.ticket')||isActiveRoute('agency.ticket.open') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-headset"></i></span>
                <span class="text">@lang('Support Tickets')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('agency.ticket')||isActiveRoute('agency.ticket.open') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.ticket') }}" class="sidebar-submenu-list__link {{ Route::is('agency.ticket') ? 'active' : '' }}">@lang('My Tickets')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('agency.ticket.open') }}" class="sidebar-submenu-list__link {{ Route::is('agency.ticket.open') ? 'active' : '' }}">@lang('New Ticket')</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
</div>
