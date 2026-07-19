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
    ];

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

    public function scopeRunning()
    {
        return $this->where('status', 2);
    }
    
    public function scopeExpired()
    {
        return $this->where('status', 3);
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

    public function statusBadge($status)
    {
        $html = '';
        if ($this->status == 1) {
            $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
        } elseif ($this->status == 2) {
            $html = '<span class="badge badge--success">' . trans('Running') . '</span>';
        } elseif ($this->status == 3) {
            $html = '<span class="badge badge--danger">' . trans('Expired') . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        }
        return $html;
    }


    public function statusTourPositionBadge($status)
    {
        $html = '';
        if ($this->person_capability <= $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('House Full') . '</span>';
        } elseif ($this->person_capability > $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Seats Available') . '</span>';
        }

        return $html;
    }

    public function tourPositionBadge()
    {
        $html = '';
        if ($this->person_capability <= $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->person_capability > $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Seats Available') . '</span>';
        }
        return $html;
    }

    public function adminTourPositionBadge()
    {
        $html = '';
        if ($this->person_capability <= $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        } elseif ($this->person_capability > $this->booking_person) {
            $html = '<span class="badge badge--success">' . trans('Seats Available') . '</span>';
        }
        return $html;
    }
}
