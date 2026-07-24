<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeparturePrice extends Model
{
    protected $fillable = ['tour_departure_id', 'price_category_id', 'price', 'discount'];

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
