<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Category, User};
use App\Traits\SiteAttribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SiteAttribute, SoftDeletes;

    protected $fillable = [
        'name', 'site_location', 'admin_id', 'user_id'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function buildings(){
        return $this->hasMany(Category::class, 'site_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
