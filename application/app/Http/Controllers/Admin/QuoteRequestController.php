<?php

namespace App\Http\Controllers\Admin;

use App\Constants\QuoteRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;

class QuoteRequestController extends Controller
{
    public function index()
    {
        $pageTitle = 'Quote Requests';
        $items = QuoteRequest::with('tourPackage', 'user', 'agency')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.quote_requests.index', compact('pageTitle', 'items'));
    }

    public function view($id)
    {
        $pageTitle = 'Quote Request Details';
        $item = QuoteRequest::with('tourPackage', 'user', 'agency')->findOrFail($id);

        if ($item->status == QuoteRequestStatus::PENDING) {
            $item->status = QuoteRequestStatus::VIEWED;
            $item->save();
        }

        return view('admin.quote_requests.view', compact('pageTitle', 'item'));
    }

    public function markResponded($id)
    {
        $item = QuoteRequest::findOrFail($id);
        $item->status = QuoteRequestStatus::RESPONDED;
        $item->save();

        $notify[] = ['success', 'Marked as responded.'];
        return back()->withNotify($notify);
    }
}
