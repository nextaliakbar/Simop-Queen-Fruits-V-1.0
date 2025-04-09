<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    use HasFactory;

    protected $casts = [
        'order_id' => 'integer',
        'deliveryman_id' => 'integer',
        'time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
