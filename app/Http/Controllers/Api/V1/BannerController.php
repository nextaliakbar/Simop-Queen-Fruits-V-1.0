<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(
        private Banner $banner
    ) {}

    public function getBanners(): JsonResponse
    {
        $banners = $this->banner->with(['product.rating','product.branch_product'])->active()->get();
        foreach($banners as $banner){
            $banner['product'] = isset($banner['product']) ? Helpers::product_data_formatting($banner['product']) : null;
        }

        return response()->json($banners, 200);
    }
}
