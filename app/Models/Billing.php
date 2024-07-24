<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BillingAttribute;
use App\Models\{User, Device};

class Billing extends Model
{
    use BillingAttribute;

    protected $fillable = [
        "user_id","moxa_id","reading","rate","total","status", 'mop', 'invoice', 'refno', 'date_paid'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at', 'from', 'to', 'date_paid'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function device(){
        return $this->hasOne(Device::class, 'id', 'moxa_id');
    }
}