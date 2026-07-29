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

    public function statusBadge()
    {
        if ($this->status == QuoteRequestStatus::RESPONDED) {
            return '<span class="badge badge--success">' . trans('Responded') . '</span>';
        }
        return '<span class="badge badge--warning">' . trans('New') . '</span>';
    }
}
