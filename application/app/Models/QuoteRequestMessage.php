<?php

namespace App\Models;

use App\Constants\QuoteMessageSender;
use Illuminate\Database\Eloquent\Model;

class QuoteRequestMessage extends Model
{
    public function quoteRequest()
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    /**
     * Best-effort display name for whoever sent this message - sender_id
     * is loose (no FK, matches the ownership-column convention used
     * elsewhere), so this degrades to null rather than throwing if the
     * account no longer exists.
     */
    public function senderName(): ?string
    {
        if (!$this->sender_id) {
            return null;
        }

        $model = match ($this->sender_type) {
            QuoteMessageSender::ADMIN => Admin::find($this->sender_id),
            QuoteMessageSender::AGENCY => Agency::find($this->sender_id),
            QuoteMessageSender::TRAVELER => User::find($this->sender_id),
            default => null,
        };

        return $model->fullname ?? $model->name ?? null;
    }
}
