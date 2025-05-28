<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionDurationTimeOrder extends Model
{
    use HasFactory;

    protected $table = 'prediction_duration_time_orders';

    protected $casts = [
        'id' => 'integer',
        'order_id' => 'integer',
        'delivery_person_age' => 'integer',
        'delivery_person_rating' => 'float',
        'distance' => 'double',
        'type_of_vehicle_electric_scooter' => 'integer',
        'type_of_vehicle_motorcycle' => 'integer',
        'type_of_vehicle_scooter' => 'integer',
        'prediction_duration_result' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
