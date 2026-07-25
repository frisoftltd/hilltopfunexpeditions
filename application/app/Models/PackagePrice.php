<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagePrice extends Model
{
    protected $fillable = ['tour_package_id', 'price_category_id', 'price', 'discount'];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
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
