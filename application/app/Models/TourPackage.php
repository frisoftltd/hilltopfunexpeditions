<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $casts = [
        'features' => 'object',
        'icons' => 'object',
        'highlights' => 'object',
        'destination_overview' => 'object',
        'exclusions' => 'object',
        'itinerary' => 'object',
    ];

    const INTENSITY_LABELS = [
        1 => 'Easy',
        2 => 'Moderate',
        3 => 'Average',
        4 => 'Challenging',
        5 => 'Demanding',
    ];

    public function getIntensityLabelAttribute()
    {
        return self::INTENSITY_LABELS[$this->intensity] ?? null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(admin::class, 'user_id', 'id');
    }

    public function TourPackagePrimaryImage()
    {
        return $this->hasOne(TourPackageImage::class, 'tour_package_id', 'id')->orderBy('id', 'asc');
    }

    public function tour_bookings()
    {
        return $this->hasMany(TourBooking::class, 'tour_package_id', 'id')->orderBy('id', 'asc');
    }

    public function packagePrices()
    {
        return $this->hasMany(PackagePrice::class);
    }

    public function getFromPriceAttribute()
    {
        return $this->packagePrices
            ->map(fn($packagePrice) => $packagePrice->final_price)
            ->sort()
            ->first();
    }

    public function tour_package_images()
    {
        return $this->hasMany(TourPackageImage::class, 'tour_package_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }



    public function scopeActive($query)
    {
        return $query->where('status',1);
    }

    public function scopeAllTour($query)
    {
        return $query;
    }

    public function scopePending()
    {
        return $this->where('status', 0);
    }

    public function scopeAdminAll($query)
    {
        return $query->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminApproved()
    {
        return $this->where('status', 1)->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminPending()
    {
        return $this->where('status', 0)->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminCanceled()
    {
        return $this->where('status', 2)->where('user_type', 'admin')->where('user_id', auth('admin')->id());
    }

    public function scopeAdminAgencyAll()
    {
        return $this->where('user_type', 'agency');
    }


    public function scopeAgencyAll()
    {
        return $this->where('user_type', 'agency')->where('user_id', auth('agency')->id());
    }


    public function scopeAgencyApproved()
    {
        return $this->where('status', 1)->where('user_type', 'agency')->where('user_id', auth('agency')->id());
    }

    public function scopeAgencyPending()
    {
        return $this->where('status', 0)->where('user_type', 'agency')->where('user_id', auth('agency')->id());
    }

    /**
     * Tours have no departures/seat caps and are always bookable once
     * live - status is a plain on/off switch, not a lifecycle. 1 = Active
     * (visible/bookable, see SiteController::tourPackageList()), anything
     * else = Inactive (hidden from search).
     */
    public function statusBadge($status)
    {
        if ($status == 1) {
            return '<span class="badge badge--success">' . trans('Active') . '</span>';
        }
        return '<span class="badge badge--warning">' . trans('Inactive') . '</span>';
    }
}
