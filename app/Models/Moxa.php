<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MoxaAttribute;
use App\Models\{User, Category};

class Moxa extends Model
{
    use MoxaAttribute;

    protected $fillable = [
        'id','user_id',
        'name','location','floor','utility',
        'category_id', 'serial'
    ];

    protected $dates = [
        'created_at', 'updated_at'
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
}
