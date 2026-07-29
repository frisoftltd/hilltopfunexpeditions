<?php

namespace App\Http\Controllers\Agency;

use App\Constants\QuoteRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;

class QuoteRequestController extends Controller
{
    public function index()
    {
        $pageTitle = 'Quote Requests';
        $items = QuoteRequest::with('tourPackage', 'user')
            ->where('agency_id', auth('agency')->id())
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        return view($this->activeTemplate . 'agency.quote_requests.index', compact('pageTitle', 'items'));
    }

    public function view($id)
    {
        $pageTitle = 'Quote Request Details';
        $item = QuoteRequest::with('tourPackage', 'user')
            ->where('agency_id', auth('agency')->id())
            ->findOrFail($id);

        if ($item->status == QuoteRequestStatus::PENDING) {
            $item->status = QuoteRequestStatus::VIEWED;
            $item->save();
        }

        return view($this->activeTemplate . 'agency.quote_requests.view', compact('pageTitle', 'item'));
    }

    public function markResponded($id)
    {
        $item = QuoteRequest::where('agency_id', auth('agency')->id())->findOrFail($id);
        $item->status = QuoteRequestStatus::RESPONDED;
        $item->save();

        $notify[] = ['success', 'Marked as responded.'];
        return back()->withNotify($notify);
    }
}
