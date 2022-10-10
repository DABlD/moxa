<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Category};
use App\Traits\SiteAttribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SiteAttribute, SoftDeletes;

    protected $fillable = [
        'name', 'site_location', 'admin_id'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function buildings(){
        return $this->hasMany(Category::class, 'site_id', 'id');
    }
}
