<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private Review $review,
        private Product $product
    ) {}

    public function list(Request $request): Renderable
    {
        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $products = $this->product->where(function($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();

            $reviews = $this->review->whereIn('product_id', $products)
            ->with(['product', 'customer'])
            ->latest()
            ->paginate(Helpers::get_pagination());
        } else {
            $reviews = $this->review->with(['product', 'customer'])->latest()->paginate(Helpers::get_pagination());
        }

        return view('admin-views.reviews.list', compact('reviews'));
    }
}
