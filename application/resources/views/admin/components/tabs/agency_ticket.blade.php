<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agency.ticket.pending') ? 'active' : '' }}"
                    href="{{route('admin.agency.ticket.pending')}}">@lang('Pending')
                    @if($pendingTicketCount)
                    <span class="badge rounded-pill bg--white text-muted">{{$pendingTicketCount}}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agency.ticket.closed') ? 'active' : '' }}"
                    href="{{route('admin.agency.ticket.closed')}}">@lang('Closed')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agency.ticket.answered') ? 'active' : '' }}"
                    href="{{route('admin.agency.ticket.answered')}}">@lang('Answered')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::routeIs('admin.agency.ticket') ? 'active' : '' }}"
                    href="{{route('admin.agency.ticket')}}">@lang('All')
                </a>
            </li>
        </ul>
    </div>
</div>