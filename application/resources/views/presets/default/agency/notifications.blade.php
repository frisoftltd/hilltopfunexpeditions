@extends($activeTemplate.'layouts.agency.master')
@section('content')

<div class="row gy-4 mb-4">
    <div class="col-lg-12">
        <div class="text-end mb-3">
            <a href="{{ route('agency.notifications.readAll') }}" class="btn btn--base pills">@lang('Mark All as Read')</a>
        </div>
        <div class="base--card radius--20">
            @forelse($notifications as $notification)
                <a class="d-block p-3 border-bottom {{ $notification->read_status == 0 ? 'fw-bold' : '' }}"
                    href="{{ route('agency.notification.read', $notification->id) }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ __($notification->title) }}</span>
                        <small class="text--black7">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </a>
            @empty
                <p class="text-center mb-0 py-4">{{ __($emptyMessage) }}</p>
            @endforelse
        </div>
        @if ($notifications->hasPages())
            <div class="mt-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
