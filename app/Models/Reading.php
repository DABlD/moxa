<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ReadingAttribute;
use App\Models\{Device};

class Reading extends Model
{
    // use ReadingAttribute;

    protected $fillable = [
        'moxa_id', 'total', 'datetime'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'datetime'
    ];

    public function device(){
        return $this->hasOne(Device::class, 'id', 'moxa_id');
    }
}
