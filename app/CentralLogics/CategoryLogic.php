<?php
namespace App\CentralLogics;

use App\Models\Product;

class CategoryLogic {
    public static function products($category_id, $type, $name, $limit, $offset)
    {
        $limit = is_null($limit) ? null : $limit;
        $offset = is_null($offset) ? 1 : $offset;
        $key = explode(' ', $name);

        $productsQuery = Product::active()
            ->with(['branch_product', 'rating'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->branchProductAvailability()
            ->when(isset($name), function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
                $q->orWhereHas('tags',function($query) use ($key){
                    $query->where(function($q) use ($key){
                        foreach ($key as $value) {
                            $q->where('tag', 'like', "%{$value}%");
                        };
                    });
                });
            })
            ->when($category_id, function ($q) use ($category_id) {
                // Direct JSON query to filter by category_id within category_ids JSON array
                $q->whereJsonContains('category_ids', [['id' => $category_id]]);
            })
            ->latest();

        if (is_null($limit)) {
            // Fetch all products if limit is null
            $categoryProducts = $productsQuery->get();
            $totalSize = $categoryProducts->count();
        } else {
            // Apply pagination if limit is set
            $categoryProducts = $productsQuery->paginate($limit, ['*'], 'page', $offset);
            $totalSize = $categoryProducts->total();
        }

        return [
            'total_size' => $totalSize,
            'limit' => $limit,
            'offset' => $offset,
            'products' => is_null($limit) ? $categoryProducts : $categoryProducts->items(),
        ];
    }
}