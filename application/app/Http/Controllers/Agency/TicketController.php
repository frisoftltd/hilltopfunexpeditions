<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Traits\SupportTicketManager;

class TicketController extends Controller
{
    use SupportTicketManager;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
        $this->layout = 'frontend';

        $this->middleware(function ($request, $next) {
            $this->user = agency();
            if ($this->user) {
                $this->layout = 'master';
            }
            return $next($request);
        });

        $this->redirectLink = 'agency.ticket.view';
        $this->userType     = 'agency';
        $this->column       = 'agency_id';
    }
}
