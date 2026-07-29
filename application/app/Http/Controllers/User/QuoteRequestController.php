<?php

namespace App\Http\Controllers\User;

use App\Constants\QuoteMessageSender;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestMessage;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function index()
    {
        $pageTitle = 'My Quote Requests';
        $items = QuoteRequest::with('tourPackage')
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        return view($this->activeTemplate . 'user.quote_requests.index', compact('pageTitle', 'items'));
    }

    public function view($id)
    {
        $pageTitle = 'Quote Request Details';
        $item = QuoteRequest::with('tourPackage', 'agency', 'messages')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view($this->activeTemplate . 'user.quote_requests.view', compact('pageTitle', 'item'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $item = QuoteRequest::with('tourPackage', 'agency')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $message = new QuoteRequestMessage();
        $message->quote_request_id = $item->id;
        $message->message = $request->message;
        $message->sender_type = QuoteMessageSender::TRAVELER;
        $message->sender_id = auth()->id();
        $message->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->id();
        $adminNotification->title = 'Traveler replied on a quote request for ' . ($item->tourPackage->title ?? '');
        $adminNotification->click_url = urlPath('admin.quote.requests.view', $item->id);
        $adminNotification->save();

        if ($item->agency_id && $item->agency) {
            notify($item->agency, 'QUOTE_REQUEST_REPLY_TO_OWNER', [
                'tour_title' => $item->tourPackage->title ?? '',
                'reply_message' => $request->message,
            ]);
        }

        $notify[] = ['success', 'Reply sent.'];
        return back()->withNotify($notify);
    }
}
