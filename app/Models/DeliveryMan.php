<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class DeliveryMan extends Authenticatable
{
    use Notifiable;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_man_id');
    }

    public function getImageFullPathAttribute():string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img1.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('delivery-man/' . $image)) {
            $path = asset('storage/delivery-man/' . $image);   
        }

        return $path;
    }

    public function getIdentityImageFullPathAttribute()
    {
        $images = $this->identity_image ?? [];
        $image_url_array = is_array($images) ? $images : json_decode($images, true);

        if(is_array($image_url_array)) {
            foreach($image_url_array as $key => $value) {
                if(Storage::disk('public')->exists('delivery-man/' . $value)) {
                    $image_url_array[$key] = asset('storage/delivery-man/' . $value);
                } else {
                    $image_url_array[$key] = asset('assets/admin/img/160x160/img1.jpg');
                }
            }
        }

        return $image_url_array;
    }
}
