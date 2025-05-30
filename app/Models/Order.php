<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

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

    public function deliveryman_review()
    {
        return $this->hasOne(DMReview::class, 'order_id');
    }

    public function prediction_duration_time_order(): HasOne
    {
        return $this->hasOne(PredictionDurationTimeOrder::class, 'order_id','id' );
    }

    public function scopeByMonthYear($query, $month, $year)
    {
        return $query->whereMonth('created_at', $month)
                     ->whereYear('created_at', $year);
    }

    public static function get_expenses_chart($user_id, $branch_id, $month, $year)
    {
        $isFebruary = $month == 2;
    
    if ($isFebruary) {
        // Februari: 4 periode (per 7 hari)
        return self::selectRaw("
            period_calc as period_number,
            CASE 
                WHEN period_calc = 1 THEN 'Periode 1 (1-7)'
                WHEN period_calc = 2 THEN 'Periode 2 (8-14)'
                WHEN period_calc = 3 THEN 'Periode 3 (15-21)'
                ELSE 'Periode 4 (22-28/29)'
            END as period_label,
            SUM(order_amount) as total_amount,
            COUNT(*) as total_orders,
            AVG(order_amount) as avg_amount
        ")
        ->fromSub(function($query) use ($user_id, $branch_id, $month, $year) {
            $query->selectRaw("
                *,
                CASE 
                    WHEN DAY(created_at) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(created_at) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(created_at) BETWEEN 15 AND 21 THEN 3
                    ELSE 4
                END as period_calc
            ")
            ->from('orders')
            ->where('user_id', $user_id)
            ->where('branch_id', $branch_id)
            ->where('order_status', 'delivered')
            ->whereRaw('MONTH(created_at) = ?', [$month])
            ->whereRaw('YEAR(created_at) = ?', [$year]);
        }, 'sub')
        ->groupBy('period_calc')
        ->orderBy('period_number')
        ->get();
    } else {
        // Bulan lain: 3 periode (per 10 hari)
        return self::selectRaw("
            period_calc as period_number,
            CASE 
                WHEN period_calc = 1 THEN 'Periode 1 (1-10)'
                WHEN period_calc = 2 THEN 'Periode 2 (11-20)'
                ELSE 'Periode 3 (21-31)'
            END as period_label,
            SUM(order_amount) as total_amount,
            COUNT(*) as total_orders,
            AVG(order_amount) as avg_amount
        ")
        ->fromSub(function($query) use ($user_id, $branch_id, $month, $year) {
            $query->selectRaw("
                *,
                CASE 
                    WHEN DAY(created_at) BETWEEN 1 AND 10 THEN 1
                    WHEN DAY(created_at) BETWEEN 11 AND 20 THEN 2
                    ELSE 3
                END as period_calc
            ")
            ->from('orders')
            ->where('user_id', $user_id)
            ->where('branch_id', $branch_id)
            ->where('order_status', 'delivered')
            ->whereRaw('MONTH(created_at) = ?', [$month])
            ->whereRaw('YEAR(created_at) = ?', [$year]);
        }, 'sub')
        ->groupBy('period_calc')
        ->orderBy('period_number')
        ->get();
        }
    }
}
