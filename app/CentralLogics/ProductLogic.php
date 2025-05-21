<?php

namespace App\CentralLogics;

use App\Models\Product;

class ProductLogic {

    public static function get_latest_products($limit, $offset, $product_type, $name, $category_ids, $sort_by)
    {
        $limit = is_null($limit) ? 10 : $limit;
        $offset = is_null($offset) ? 1 : $offset;

        $key = explode(' ', $name);
        $paginator = Product::active()
            ->with(['branch_product', 'rating'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }})
            ->when(isset($category_ids), function ($query) use ($category_ids) {
                return $query->whereJsonContains('category_ids', ['id'=>$category_ids]);
            })
            ->when($sort_by == 'popular', function ($query){
                return $query->orderBy('popularity_count', 'desc');
            })
            ->latest() // default sorting
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items(),
        ];
    }

    public static function get_popular_products($limit, $offset, $product_type, $name)
    {
        $limit = is_null($limit) ? null : $limit;
        $offset = is_null($offset) ? 1 : $offset;
        $key = explode(' ', $name);

        $paginator = Product::active()
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->when(isset($product_type) && ($product_type == 'veg' || $product_type == 'non_veg'), function ($query) use ($product_type) {
                return $query->productType(($product_type == 'veg') ? 'veg' : 'non_veg');
            })
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('tags',function($query) use ($key){
                        $query->where(function($q) use ($key){
                            foreach ($key as $value) {
                                $q->where('tag', 'like', "%{$value}%");
                            };
                        });
                    });
                });
            })
            ->orderBy('popularity_count', 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_recommended_products($limit, $offset, $name)
    {
        $limit = is_null($limit) ? null : $limit;
        $offset = is_null($offset) ? 1 : $offset;
        $key = explode(' ', $name);

        $paginator = Product::active()
            ->with(['branch_product', 'rating'])
            ->where('is_recommended', 1)
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                    $q->orWhereHas('tags',function($query) use ($key){
                        $query->where(function($q) use ($key){
                            foreach ($key as $value) {
                                $q->where('tag', 'like', "%{$value}%");
                            };
                        });
                    });
                });
            })
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

            return [
                'total_size' => $paginator->total(),
                'limit' => $limit,
                'offset' => $offset,
                'products' => $paginator->items()
            ];
    }
}