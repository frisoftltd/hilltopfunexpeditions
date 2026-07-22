<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceCategory extends Model
{
    public function departurePrices()
    {
        return $this->hasMany(DeparturePrice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
