<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductByBranch;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(
        private Product $product,
        private ProductByBranch $product_by_branch
    ){}

    public function list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if($request->has('search')) {
            $key = explode(' ', $search);
            $query = $this->product->where(function($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('name', 'like', "%{$value}%");
                }
            });

            $query_param = ['search' => $search];
        } else {
            $query = $this->product;
        }

        $products = $query->with(['product_by_branch', 'sub_branch_product'])->orderBy('id', 'DESC')
        ->paginate(Helpers::get_pagination());

        return view('branch-views.product.list', compact('products', 'search'));
    }

    public function set_price($id)
    {
        $product = $this->product->with(['product_by_branch', 'sub_branch_product'])->find($id);
        $main_branch_product = $this->product_by_branch->where(['product_id' => $id, 'branch_id' => 1])->first();
    
        return view('branch-views.product.set-price', compact('product', 'main_branch_product'));
    }

    public function update_price(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required',
            'stock_type' => 'required|in:unlimited,fixed',
            'product_stok' => 'required_if:stock_type,fixed'
        ], [
            'price.required' => 'Harga produk tidak boleh kosong',
            'produck_stok.required_if' => 'Stok produk tidak boleh kosong'
        ]);

        $discount = 0;
        if($request['discount_type'] != 'nothing') {
            if($request['discount_type'] == 'precent') {
                $discount = ($request['price'] / 100) * $request['discount'];
            } else {
                $discount = $request['discount'];
            }
        }

        if($request['price'] <= $discount) {
            $validator->getMessageBag()->add('unit_price', 'Diskon tidak boleh lebih besar atau sama dengan harga');
        }

        if($request['price'] <= $discount || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $price = $request['price'];
        $variations = [];

        if(isset($request->options)) {
            foreach(array_values($request->options) as $key => $option) {
                $temp_variation['name'] = $option['name'];
                $temp_variation['type'] = $option['type'];
                $temp_variation['min'] = $option['min'] ?? 0;
                $temp_variation['max'] = $option['max'] ?? 0;
                $temp_variation['required'] = $option['required'] ?? 'off';
                
                if($option['min'] > 0 && $option['min'] >= $option['max']) {
                    $validator->getMessageBag()->add('name', 'Nilai maksimal tidak boleh lebih kecil atau sama dari nilai minimal');
                    return response()->json([
                        'errors' => Helpers::error_processor($validator)
                    ]);
                }
                
                if(!isset($option['values'])) {
                    $validator->getMessageBag()->add('name', 'Silahakan tambahkan opsi untuk ' . $option['name']);
                    return response()->json([
                        'errors' => Helpers::error_processor($validator)
                    ]);
                }

                if($option['max'] > count($option['values'])) {
                    $validator->getMessageBag()->add('name', 'Silahakan tambahkan opsi lain atau ubah nilai maksimal untuk ' . $option['name']);
                    return response()->json([
                        'errors' => Helpers::error_processor($validator)
                    ]);
                }

                $temp_value = [];

                foreach(array_values($option['values']) as $value) {
                    if(isset($value['label'])) {
                        $temp_option['label'] = $value['label'];
                    }
                    $temp_option['optionPrice'] = $value['optionPrice'];
                    $temp_value[] = $temp_option;
                }

                $temp_variation['values'] = $temp_value;
                $variations[] = $temp_variation;
            }
        }

        $branch_product = [
            'product_id' => $id,
            'price' => $price,
            'discount_type' => $request['discount_type'] != 'nothing' ? $request['discount_type'] : null,
            'discount' => $discount,
            'branch_id' => auth('branch')->id(),
            'is_available' => 1,
            'variations' => $variations,
            'stock_type' => $request->stock_type,
            'stock' => $request->product_stock ?? 0
        ];

        $update_product = $this->product_by_branch->updateOrCreate([
            'product_id' => $branch_product['product_id'],
            'branch_id' => auth('branch')->id()
        ], $branch_product);

        if($update_product->wasChanged('stock_type') || $update_product->wasChanged('stock')) {
            $update_product->sold_quantity = 0;
            $update_product->save();
        }

        if(auth('branch')->id() == 1) {
            $product = $this->product->find($branch_product['product_id']);
            if($product) {
                $product->price = $request['price'];
                $product->discount_type = $request['discount_type'] != 'nothing' ? $request['discount_type'] : null;
                $product->discount = $discount;
                $product->variations = json_encode($variations);
                $product->update();
            }
        }

        return response()->json([], 200);
    }

    public function status(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);
        $branch_product = $this->product_by_branch->where(['product_id' => $product->id, 'branch_id' => auth('branch')->id()])->first();
        $main_branch_product = $this->product_by_branch->where(['product_id' => $product->id, 'branch_id' => 1])->first();

        if(isset($branch_product)) {
            $data = [
                'price' => $branch_product->price,
                'discount_type' => $branch_product->discount_type ?? null,
                'discount' => $branch_product->discount,
                'product_id' => $product->id,
                'is_available' => $request->status,
                'stock_type' => $branch_product->stock_type,
                'stock' => $branch_product->stock
            ];

            $this->product_by_branch->updateOrCreate([
                'product_id' => $data['product_id'],
                'branch_id' => auth('branch')->id()
            ], $data);
        } else {
            $variations = json_decode($product->variations, true);

            $data = [];

            if(count($variations) > 0) {
                foreach ($variations as $variation) {
                    
                    if(isset($variation['price'])) {
                        return response()->json(['variation_message' => 'Silahkan perbarui variasi terlebih dahulu']);
                    }

                    $var[] = $variation;
                    $data = [
                        'product_id' => $product->id,
                        'price' => $product->price,
                        'discount_type' => $product->discount_type ?? null,
                        'discount' => $product->discount,
                        'branch_id' => auth('branch')->id(),
                        'is_available' => $request->status,
                        'variations' => $var,
                        'stock_type' => $main_branch_product->stock_type ?? 'unlimited',
                        'stock' => $main_branch_product->stock ?? 0
                    ];
                }
            } else {
                $data = [
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'discount_type' => $product->discount_type ?? null,
                    'discount' => $product->discount,
                    'branch_id' => auth('branch')->id(),
                    'is_available' => $request->status,
                    'variations' => [],
                    'stock_type' => $main_branch_product->stock_type ?? 'unlimited',
                    'stock' => $main_branch_product->stock ?? 0
                ];
            }

            $this->product_by_branch->updateOrCreate([
                'product_id' => $product->id,
                'branch_id' => auth('branch')->id()
            ], $data);
        }

        return response()->json(['success_message' => 'Status diperbarui'], 200);
    }
}
