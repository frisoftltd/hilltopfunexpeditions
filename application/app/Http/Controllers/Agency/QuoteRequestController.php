<?php

namespace App\Http\Controllers\Agency;

use App\Constants\QuoteMessageSender;
use App\Constants\QuoteRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestMessage;
use Illuminate\Http\Request;

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
        $item = QuoteRequest::with('tourPackage', 'user', 'messages')
            ->where('agency_id', auth('agency')->id())
            ->findOrFail($id);

        if ($item->status == QuoteRequestStatus::PENDING) {
            $item->status = QuoteRequestStatus::VIEWED;
            $item->save();
        }

        return view($this->activeTemplate . 'agency.quote_requests.view', compact('pageTitle', 'item'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $item = QuoteRequest::with('tourPackage', 'user')
            ->where('agency_id', auth('agency')->id())
            ->findOrFail($id);

        $message = new QuoteRequestMessage();
        $message->quote_request_id = $item->id;
        $message->message = $request->message;
        $message->sender_type = QuoteMessageSender::AGENCY;
        $message->sender_id = auth('agency')->id();
        $message->save();

        $item->status = QuoteRequestStatus::RESPONDED;
        $item->save();

        $recipient = $item->user ?: ['email' => $item->email, 'name' => $item->name];
        notify($recipient, 'QUOTE_REQUEST_REPLY_TO_TRAVELER', [
            'tour_title' => $item->tourPackage->title ?? '',
            'reply_message' => $request->message,
        ]);

        $notify[] = ['success', 'Reply sent.'];
        return back()->withNotify($notify);
    }
}
