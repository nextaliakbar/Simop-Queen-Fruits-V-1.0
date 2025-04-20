<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'local_product' => 'integer',
        'popularity_count' => 'integer',
        'is_recommended' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function main_branch_product(): HasOne
    {
        return $this->hasOne(ProductByBranch::class);
    }

    public function branch_products(): HasMany
    {
        return $this->hasMany(ProductByBranch::class)->where(['branch_id' => session()->get('branch_id') ?? 1]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function rating(): HasMany
    {
        return $this->hasMany(Review::class)
        ->select(DB::raw('avg(rating) average, product_id'))
        ->groupBy('product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function product_by_branch(): HasMany
    {
        return $this->hasMany(ProductByBranch::class)->where(['branch_id' => auth('branch')->id()]);
    }

    public function sub_branch_product(): HasOne
    {
        return $this->hasOne(ProductByBranch::class)->where(['branch_id' => auth('branch')->id()]);
    }

    public function getImageFullPathAttribute(): string
    {
        $image = $this->image?? null;
        $path = asset('assets/admin/img/160x160/img2.jpg');

        if(!is_null($image) && Storage::disk('public')->exists('product/' . $image)) {
            $path = asset('storage/product/' . $image);
        }

        return $path;
    }
}
