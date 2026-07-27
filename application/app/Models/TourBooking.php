<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'owner_id', 'owner_type', 'price', 'discount',
        'tour_package_id', 'price_category_id',
        'start_date', 'party_size', 'status', 'phone', 'guest_signup',
        'agency_status', 'decline_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Agency::class, 'owner_id', 'id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'owner_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'owner_id', 'id');
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class);
    }

    public function tour_package()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class);
    }

    /**
     * Trip length lives on the package (duration_nights), not the booking,
     * so end_date is derived from the tourist's chosen start_date rather
     * than stored - same approach TourDeparture used to take.
     */
    public function getEndDateAttribute(): ?Carbon
    {
        $nights = $this->tour_package?->duration_nights;

        if (!$this->start_date || $nights === null) {
            return null;
        }

        return $this->start_date->copy()->addDays($nights);
    }

    public function scopeAdminAll($query)
    {
        return $query->where('owner_type','admin')->where('owner_id', auth('admin')->id());
    }


    public function scopeUserAll($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeAdminApproved($query)
    {
        return $query->where('status', 1)->where('owner_type','admin')->where('owner_id', auth('admin')->id());
    }

    public function scopeAdminPending()
    {
        return $this->where('status', 2)
        ->where('owner_type','admin')
        ->where('owner_id', auth('admin')->id());
    }

    public function scopeAdminCanceled()
    {
        return $this->where('status', 3)->where('owner_type','admin')->where('owner_id', auth('admin')->id());
    }

    public function scopeAgency()
    {
        return $this->where('owner_type','agency')->where('owner_id', auth('agency')->id());
    }

    public function scopeAgencyApproved($query)
    {
        return $query->where('status', 1)->where('owner_type','agency')->where('owner_id', auth('agency')->id());
    }

    public function scopeAgencyPending()
    {
        return $this->where('status', 2)->where('owner_type','agency')->where('owner_id', auth('agency')->id());
    }

    public function scopeAgencyCanceled()
    {
        return $this->where('status', 3)->where('owner_type','agency')->where('owner_id', auth('agency')->id());
    }

    public function scopeUserApproved($query)
    {
        return $query->where('status', 1)->where('user_id', auth()->id());
    }

    public function scopeUserPending()
    {
        return $this->where('status', 2)
        ->where('user_id', auth()->id());
    }

    public function scopeUserCanceled()
    {
        return $this->where('status', 3)->where('user_id', auth()->id());
    }


    /**
     * Real status vocabulary, as actually set by Gateway\PaymentController:
     * 0 = booking created, payment not completed yet (depositInsert());
     * 1 = paid (online gateway success, userDataUpdate());
     * 2 = pending confirmation (manual/bank-transfer submitted, manualDepositUpdate());
     * 3 = rejected by admin (Admin\DepositController).
     */
    public function statusBadge($status)
    {
        if ($status == 1) {
            return '<span class="badge badge--success">' . trans('Paid') . '</span>';
        } elseif ($status == 2) {
            return '<span class="badge badge--warning">' . trans('Pending Confirmation') . '</span>';
        } elseif ($status == 3) {
            return '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
        }
        return '<span class="badge badge--warning">' . trans('Awaiting Payment') . '</span>';
    }

    public function scopeStatusPaymentBadge()
    {
        if ($this->status == 1) {
            return '<span class="badge badge--success">' . trans('Paid') . '</span>';
        } elseif ($this->status == 2) {
            return '<span class="badge badge--warning">' . trans('Pending Confirmation') . '</span>';
        } elseif ($this->status == 3) {
            return '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
        }
        return '<span class="badge badge--warning">' . trans('Awaiting Payment') . '</span>';
    }

    /**
     * agency_status is the owning agency's own review layer, independent of
     * the payment status above: null = pending review, 1 = approved,
     * 2 = declined.
     */
    public function agencyStatusBadge()
    {
        if ($this->agency_status == 1) {
            return '<span class="badge badge--success">' . trans('Approved') . '</span>';
        } elseif ($this->agency_status == 2) {
            return '<span class="badge badge--danger">' . trans('Declined') . '</span>';
        }
        return '<span class="badge badge--warning">' . trans('Pending Review') . '</span>';
    }
}
