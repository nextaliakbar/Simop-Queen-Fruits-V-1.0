<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Branch extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'coverage' => 'integer',
        'status' => 'integer',
        'branch_promotion_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'preparation_time' => 'integer'
    ];

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img2.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('branch/' . $image)) {
            $path = asset('storage/branch/' . $image);
        }

        return $path;
    }

    public function getCoverImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img2.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('branch/' . $image)) {
            $path = asset('storage/branch/' . $image);
        }

        return $path;
    }

    public function delivery_charge_setup()
    {
        return $this->hasOne(DeliveryChargeSetup::class, 'branch_id', 'id');
    }
}
