<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function agency(){
    	return $this->belongsTo(Agency::class);
    }
}
