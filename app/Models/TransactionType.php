<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Traits\TransactionTypeAttribute;

class TransactionType extends Model
{
    use TransactionTypeAttribute, SoftDeletes;

    protected $fillable = [
        'type', 'operator', 'inDashboard', 'canDelete', 'admin_id', 'demand', 'rate', 'classification', 'late_interest'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];
}
