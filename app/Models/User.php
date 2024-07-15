<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Sanctum\HasApiTokens;

use App\Traits\UserAttribute;

use Hash;

class User extends Authenticatable
{
    use UserAttribute, SoftDeletes, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role', 'name', 'email', 'address', 'contact', 
        'password', 'username', 'avatar', 'admin_id',
        'login_link'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
}