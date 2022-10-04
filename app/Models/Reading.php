<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ReadingAttribute;
use App\Models\{Moxa};

class Reading extends Model
{
    // use ReadingAttribute;

    protected $fillable = [
        'moxa_id', 'total', 'datetime'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'datetime'
    ];

    public function moxa(){
        return $this->hasOne(Moxa::class, 'id', 'moxa_id');
    }
}
