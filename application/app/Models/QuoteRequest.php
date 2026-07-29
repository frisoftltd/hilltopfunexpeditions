<?php

namespace App\Models;

use App\Constants\QuoteRequestStatus;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    /**
     * badge--primary/success/warning chosen because they're the only
     * badge classes defined in both admin.css and the frontend theme's
     * main.css - this badge renders in both the admin and agency
     * inboxes. badge--secondary/dark exist in only one of the two (see
     * TourBooking::statusBadge()'s CANCELLED_BY_TRAVELER badge for the
     * same constraint).
     */
    public function statusBadge()
    {
        if ($this->status == QuoteRequestStatus::RESPONDED) {
            return '<span class="badge badge--success">' . trans('Responded') . '</span>';
        }
        if ($this->status == QuoteRequestStatus::VIEWED) {
            return '<span class="badge badge--primary">' . trans('Viewed') . '</span>';
        }
        return '<span class="badge badge--warning">' . trans('New') . '</span>';
    }
}
