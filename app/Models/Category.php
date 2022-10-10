<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{Site, Moxa};
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\CategoryAttribute;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'admin_id', 'site_id'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function site(){
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }

    // category_id = building_id
    public function moxas(){
        return $this->hasMany(Moxa::class, 'category_id', 'id');
    }
}