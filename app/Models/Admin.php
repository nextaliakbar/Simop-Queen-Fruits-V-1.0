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
}
