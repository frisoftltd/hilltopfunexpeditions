{{--
    Shared across three contexts (admin, agency, traveler quote-request
    detail views) - correcting the copy-paste-per-audience pattern found
    for support tickets. Deliberately built from plain Bootstrap classes
    only (card/form-control/btn/bg-light etc.), not this theme's
    base--card/form--control/btn--base classes - admin.css and the
    frontend theme's main.css define --primary/--base completely
    differently (hex vs HSL-triplet), so nothing here can safely assume
    either theme's custom variables/classes are present in both places.
--}}
@props(['quoteRequest', 'replyUrl', 'senderType'])

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3">@lang('Messages')</h5>

        <form action="{{ $replyUrl }}" method="POST" class="mb-4">
            @csrf
            <div class="form-group mb-2">
                <textarea class="form-control" name="message" rows="3" placeholder="@lang('Write a reply...')" required></textarea>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-sm">@lang('Send Reply')</button>
            </div>
        </form>

        @forelse ($quoteRequest->messages as $message)
            <div class="d-flex {{ $message->sender_type == $senderType ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                <div class="p-3 rounded {{ $message->sender_type == $senderType ? 'bg-primary bg-opacity-10' : 'bg-light' }}" style="max-width: 80%;">
                    <div class="d-flex justify-content-between align-items-center mb-1 gap-3">
                        <strong>{{ $message->senderName() ?? ucfirst($message->sender_type) }}</strong>
                        <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0">{{ $message->message }}</p>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">@lang('No messages yet.')</p>
        @endforelse
    </div>
</div>
