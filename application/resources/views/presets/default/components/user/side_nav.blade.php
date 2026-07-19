<div class="sidebar-menu">
    <span class="sidebar-menu__close"><i class="las la-times"></i></span>
    <div class="logo-wrapper px-3">
        <a href="{{route('home')}}" class="normal-logo" id="normal-logo">  <img width="150" src="{{ getImage(getFilePath('logoIcon') . '/logo.png', '?' . time()) }}" alt="{{ config('app.name') }}">
        </a>
    </div>

    <ul class="sidebar-menu-list">
        <li class="sidebar-menu-list__item">
            <a href="{{route('user.home')}}" class="sidebar-menu-list__link {{ Route::is('user.home') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-border-all"></i></span>
                <span class="text">@lang('Dashboard')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('user.tour.package.booking.my.list')||isActiveRoute('user.tour.package.booking.pending') ||   isActiveRoute('user.tour.package.booking.approved')|| isActiveRoute('user.tour.package.booking.cancel') || isActiveRoute('user.deposit.history') || isActiveRoute('user.tour.package.booking.details') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-cart-shopping"></i></span>
                <span class="text">@lang('Tour Bookings')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('user.tour.package.booking.my.list')||isActiveRoute('user.tour.package.booking.pending') || isActiveRoute('user.tour.package.booking.approved')|| isActiveRoute('user.tour.package.booking.cancel') || isActiveRoute('user.deposit.history') || isActiveRoute('user.tour.package.booking.details') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.my.list')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.my.list') ? 'active' : '' }}">@lang('My Bookings')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.pending')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.pending') ? 'active' : '' }}">@lang('Processing')</a>
                    </li>

                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.approved')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.approved') ? 'active' : '' }}">@lang('Approved')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.tour.package.booking.canceled')}}" class="sidebar-submenu-list__link {{ Route::is('user.tour.package.booking.canceled') ? 'active' : '' }}">@lang('Canceled')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{route('user.deposit.history')}}" class="sidebar-submenu-list__link {{ Route::is('user.deposit.history') ? 'active' : '' }}">@lang('Payment Log')</a>
                    </li>
                </ul>
            </div>
        </li>


        <li class="sidebar-menu-list__item">
            <a href="{{route('user.get.wishlist')}}" class="sidebar-menu-list__link {{ Route::is('user.get.wishlist') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-heart"></i></span>
                <span class="text">@lang('Wishlists')</span>
            </a>
        </li>

        <li class="sidebar-menu-list__item">
            <a href="{{route('user.transactions')}}" class="sidebar-menu-list__link {{ Route::is('user.transactions') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-arrow-right-arrow-left"></i></span>
                <span class="text">@lang('Transaction')</span>
            </a>
        </li>
    
        <li class="sidebar-menu-list__item has-dropdown {{ isActiveRoute('ticket')||isActiveRoute('ticket.open') ? 'active' : '' }}">
            <a href="javascript:void(0)" class="sidebar-menu-list__link">
                <span class="icon"><i class="fa-solid fa-headset"></i></span>
                <span class="text">@lang('Support Tickets')</span>
            </a>
            <div class="sidebar-submenu {{ isActiveRoute('ticket')||isActiveRoute('ticket.open') ? 'd-block' : '' }}">
                <ul class="sidebar-submenu-list">
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('ticket') }}" class="sidebar-submenu-list__link {{ Route::is('ticket') ? 'active' : '' }}">@lang('My Tickets')</a>
                    </li>
                    <li class="sidebar-submenu-list__item">
                        <a href="{{ route('ticket.open') }}" class="sidebar-submenu-list__link {{ Route::is('ticket.open') ? 'active' : '' }}">@lang('New Ticket')</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>