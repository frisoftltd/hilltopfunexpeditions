<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    const COLLECTED = 0;
    const OWED = 1;

    protected $fillable = [
        'agency_id', 'tour_booking_id', 'booking_amount',
        'commission_rate', 'commission_amount', 'status', 'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function tourBooking()
    {
        return $this->belongsTo(TourBooking::class);
    }
}
