<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $casts = [
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'discount' => 'float',
        'status' => 'integer',
        'start_date' => 'date',
        'expire_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
