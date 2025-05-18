<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private Product $product
    ) {}

    public function latestProducts(Request $request): JsonResponse
    {
        $products = ProductLogic::get_latest_products($request['limit'], $request['offset'], $request['product_type'], $request['name'], $request['category_ids'], $request['sort_by']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function popularProducts(Request $request): JsonResponse
    {
        $products = ProductLogic::get_popular_products(limit: $request['limit'], offset: $request['offset'], product_type: $request['product_type'], name: $request['name']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function setMenus(Request $request): JsonResponse
    {
        $setMenuProducts = $this->product->active()
            ->with(['rating', 'branch_product'])
            ->whereHas('branch_product.branch', function ($query) {
                $query->where('status', 1);
            })
            ->whereHas('branch_product', function ($q) {
                $q->where('is_available', 1);
            })
            ->where(['set_menu' => 1])
            ->latest()
            ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $products = [
            'total_size' => $setMenuProducts->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'products' => $setMenuProducts->items()
        ];

        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);

    }
}
