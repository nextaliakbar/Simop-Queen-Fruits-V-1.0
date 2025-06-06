<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'f_name',
        'l_name',
        'phone',
        'email',
        'password',
        'is_active',
        'user_type',

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
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class, 'user_id');
    }

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img1.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('profile/' . $image)) {
            $path = asset('storage/profile/' . $image);   
        }

        return $path;
    }
}
