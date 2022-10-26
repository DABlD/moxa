<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MoxaAttribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{User, Category, Site};

class Device extends Model
{
    use MoxaAttribute, SoftDeletes;

    protected $fillable = [
        'id','user_id',
        'name','location','floor','utility',
        'category_id', 'serial', 'inDashboard'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function admin(){
        return $this->belongsTo(User::class, 'id', 'admin_id');
    }

    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function site(){
        return $this->hasOne(Site::class, 'id', 'site_id');
    }
}
