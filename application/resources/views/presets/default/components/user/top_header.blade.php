@php
  $user = auth()->user();
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
          <a href="javascript:void(0)" class="u-info dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="user-thumb">
                <img src="{{ getImage(getFilePath('userProfile').'/'.$user->image,getFileSize('userProfile')) }}" alt="@lang('image')">
              </div>
              <div class="user--name d-flex align-items-center gap-2">
                  <i class="fa-solid fa-circle-chevron-down"></i>
              </div>
          </a>

          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('user.profile.setting') }}"><i class="fa-regular fa-user"></i> @lang('Profile')</a></li>
            <li><a class="dropdown-item" href="{{ route('user.change.password') }}"><i class="fa-solid fa-key"></i> @lang('Password')</a></li>
            <li><a class="dropdown-item" href="{{ route('user.twofactor') }}"><i class="fa-solid fa-user-ninja"></i> @lang('2FA Security')</a></li>
            <li><a class="dropdown-item" href="{{route('user.logout')}}"><i class="fa-solid fa-arrow-right-from-bracket"></i> @lang('Logout')</a> </li>
        </ul>
      </div>
    </div>
</div>
