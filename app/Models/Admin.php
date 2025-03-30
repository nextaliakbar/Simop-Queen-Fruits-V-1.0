<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['admin_role_id'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class, 'admin_role_id');
    }

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;

        $path = asset('assets/admin/img/160x160/img1.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('admin/' . $image)) {
            $path = asset('storage/admin/'. $image);
        }

        return $path;
    }

    public function getIdentityImageFullPathAttribute()
    {
        $value = $this->identity_image ?? [];
        $image_url_array = is_array($value) ? $value : json_decode($value, true);
        if(is_array($image_url_array)) {
            foreach ($image_url_array as $key => $item) {
                if(Storage::disk('public')->exists('admin/' . $item)) {
                    $image_url_array[$key] = asset('storage/admin/' . $item);
                } else {
                    $image_url_array[$key] = asset('assets/admin/img/400x400/img2.jpg');
                }
            }
        }

        return $image_url_array;
    }
}
