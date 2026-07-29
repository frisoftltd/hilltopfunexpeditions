@php
  $user = agency();
@endphp
<div class="row mb-4 mx-0">
    <div class="dashboard-header d-flex justify-content-between align-items-center radius--20">
        <div class="navigator-text d-flex justify-content-center align-items-center">
            <div class="dashboard-body__bar">
                <span class="dashboard-body__bar-icon"><i class="las la-bars"></i></span>
            </div>
            <h6>{{__($pageTitle)}}</h6>
        </div>
        <div class="user-info--wrap d-flex align-items-center gap-2">
          <div class="dropdown">
            <a href="javascript:void(0)" class="position-relative dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bell fs--20"></i>
                @if ($agencyNotificationCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $agencyNotificationCount }}
                    </span>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <strong>@lang('Notifications')</strong>
                    @if ($agencyNotificationCount > 0)
                        <a href="{{ route('agency.notifications.readAll') }}" class="fs--14">@lang('Mark all read')</a>
                    @endif
                </li>
                @forelse ($agencyNotifications as $notification)
                    <li>
                        <a class="dropdown-item" href="{{ route('agency.notification.read', $notification->id) }}">
                            <div>{{ __($notification->title) }}</div>
                            <small class="text--black7">{{ $notification->created_at->diffForHumans() }}</small>
                        </a>
                    </li>
                @empty
                    <li class="px-3 py-2 text--black7">@lang('No unread notifications')</li>
                @endforelse
                <li class="px-3 py-2 border-top text-center">
                    <a href="{{ route('agency.notifications') }}">@lang('View all notifications')</a>
                </li>
            </ul>
          </div>

          <a href="javascript:void(0)" class="u-info dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-thumb">
                <img src="{{ getImage(getFilePath('agencyProfile').'/'.$user->image,getFileSize('agencyProfile')) }}" alt="@lang('image')">
              </div>
              <div class="user--name d-flex align-items-center gap-2">
                  <i class="fa-solid fa-circle-chevron-down"></i>
              </div>
          </a>

          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('agency.profile.setting') }}"><i class="fa-regular fa-user"></i> @lang('Profile')</a></li>
            <li><a class="dropdown-item" href="{{ route('agency.change.password') }}"><i class="fa-solid fa-key"></i> @lang('Password')</a></li>
            <li><a class="dropdown-item" href="{{ route('agency.twofactor') }}"><i class="fa-solid fa-user-ninja"></i> @lang('2FA Security')</a></li>
            <li><a class="dropdown-item" href="{{route('agency.logout')}}"><i class="fa-solid fa-arrow-right-from-bracket"></i> @lang('Logout')</a> </li>
        </ul>
      </div>
    </div>
</div>
