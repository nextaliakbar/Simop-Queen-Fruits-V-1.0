<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $casts = [
        'order_amount' => 'float',
        'total_tax_amount' => 'float',
        'delivery_address_id' => 'integer',
        'delivery_man_id' => 'integer',
        'delivery_charge_' => 'float',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'delivery_address' => 'array'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function delivery_man(): BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id')->withCount('orders');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withCount('orders');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id')->withCount('orders');
    }

    public function customer_delivery_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function delivery_address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function scopePos($query)
    {
        return $query->where('order_type', '=', 'pos');
    }

    public function scopeNotPos($query) 
    {
        return $query->where('order_type', '!=', 'pos');
    }

    public function scopeSchedule($query)
    {
        return $query->whereDate('delivery_date', '>', Carbon::now()->format('Y-m-d'));
    }

    public function scopeNotSchedule($query)
    {
        return $query->whereDate('delivery_date', '<=', Carbon::now()->format('Y-m-d'));
    }

    public function offline_payment(): HasOne
    {
        return $this->hasOne(OfflinePayment::class, 'order_id');
    }
}
