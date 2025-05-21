<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProductController extends Controller
{
    public function __construct(
        private Product $product
    ) {}

    public function latest_products(Request $request): JsonResponse
    {
        $products = ProductLogic::get_latest_products($request['limit'], $request['offset'], $request['product_type'], $request['name'], $request['category_ids'], $request['sort_by']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function popular_products(Request $request): JsonResponse
    {
        $products = ProductLogic::get_popular_products(limit: $request['limit'], offset: $request['offset'], product_type: $request['product_type'], name: $request['name']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function import_products(Request $request): JsonResponse
    {
        $local_products = $this->product->active()
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->whereHas('branch_product', function ($q) {
                $q->where('is_available', 1);
            })
            ->where(['local_product' => 0])
            ->latest()
            ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $products = [
            'total_size' => $local_products->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'products' => $local_products->items()
        ];

        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);

    }

    public function search_recommended(Request $request): JsonResponse
    {
        $branch_id = Config::get('branch_id') ?? 1;
        $order_details_product_ids = OrderDetail::whereHas('order', function($query) use ($branch_id) {
            $query->where('branch_id', $branch_id);
        })->pluck('product_id')->unique();

        $products = Product::whereIn('id', $order_details_product_ids)
        ->orderBy('popularity_count', 'DESC')->get();

        $categoriy_ids = $products->flatMap(function($product) {
            $category_data = json_decode($product->category_ids, true);
            return collect($category_data)->filter(function($category) {
                return $category['position'] == 1;
            })->pluck('id');
        })->unique();

        $category_list = Category::whereIn('id', $categoriy_ids)->select('id', 'name', 'image', 'banner_image')->get();
    
        if($category_list->count() < 8) {
            $additional_categorires = Category::whereNotIn('id', $categoriy_ids)
            ->inRandomOrder()
            ->limit(8 - $category_list->count())
            ->select('id', 'name', 'image', 'banner_image')
            ->get();
            $category_list = $category_list->merge($additional_categorires);
        }

        $category_list = $category_list->take(8);

        return response()->json(['categories' => $category_list], 200);
    }

    public function recommended_products(Request $request): JsonResponse
    {
        $products = ProductLogic::get_recommended_products($request['limit'], $request['offset'], $request['name']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        
        return response()->json($products, 200);
    }
}
