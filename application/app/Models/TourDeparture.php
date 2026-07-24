<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TourDeparture extends Model
{
    protected $fillable = ['tour_package_id', 'start_date', 'seats_total', 'seats_booked', 'status'];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function departurePrices()
    {
        return $this->hasMany(DeparturePrice::class);
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class);
    }

    /**
     * Trip length lives on the package (duration_nights), not the departure,
     * so end_date is derived rather than stored.
     */
    public function getEndDateAttribute(): ?Carbon
    {
        $nights = $this->tourPackage?->duration_nights;

        if (!$this->start_date || $nights === null) {
            return null;
        }

        return $this->start_date->copy()->addDays($nights);
    }

    public function getSeatsAvailableAttribute(): int
    {
        return max(0, $this->seats_total - $this->seats_booked);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }
}
