<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeparturePrice extends Model
{
    public function departure()
    {
        return $this->belongsTo(TourDeparture::class, 'tour_departure_id');
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class);
    }

    public function getFinalPriceAttribute()
    {
        return showTourPackageCalculateDiscount($this->price, $this->discount);
    }
}
