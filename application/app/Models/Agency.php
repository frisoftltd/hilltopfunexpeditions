<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agency extends Authenticatable
{
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime',
        'languages' => 'array',
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function artwork()
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * Inverse of TourPackage::agency(). user_id is shared with the admin
     * table (not a dedicated FK), so this must also filter user_type -
     * same scoping used everywhere else a package is matched to its agency.
     */
    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class, 'user_id')->where('user_type', 'agency');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function fullname(): Attribute {
        return new Attribute(
            get: fn() => $this->firstname || $this->lastname ? $this->firstname . ' ' . $this->lastname : '@'.$this->username,
        );
    }


    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', 0);
    }

    public function scopeKycUnverified()
    {
        return $this->where('kv', 0);
    }

    public function scopeKycPending()
    {
        return $this->where('kv', 2);
    }

    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', 1);
    }

    public function scopeWithBalance()
    {
        return $this->where('balance','>', 0);
    }

}
