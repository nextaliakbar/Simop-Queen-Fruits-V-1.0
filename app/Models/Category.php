<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $cast = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'status' => 'integer',
        'priority' => 'integer'
    ];

    public function childes(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getImageFullPathAttribute()
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img2.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('category/' . $image)) {
            $path = asset('storage/category/' . $image);
        }

        return $path;
    }

    public function getBannerImageFullPathAttribute()
    {
        $image = $this->banner_image ?? null;
        $path = asset('assets/admin/img/900x400/img1.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('category/banner/' . $image)) {
            $path = asset('storage/category/banner/' . $image);
        }

        return $path;
    }
}
