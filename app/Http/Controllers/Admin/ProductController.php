<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductByBranch;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;
use App\CentralLogics\Helpers;
use App\Models\Review;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(
        private Category $category,
        private Product $product,
        private ProductByBranch $product_by_branch
    ) {}

    public function index(Request $request): Renderable
    {
        $categories = $this->category->where(['position' => 0])->get();
        return view('admin-views.product.index', compact('categories'));
    }

    public function list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search')) {
            $key = explode(' ', $search);
            $query = $this->product->where(function ($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $search];
        } else {
            $query = $this->product;
        }

        $products = $query->with('main_branch_product')
        ->orderBy('id', 'DESC')
        ->paginate(Helpers::get_pagination())
        ->appends($query_param);

        return view('admin-views.product.list', compact('products', 'search'));
    }

    public function get_categories(Request $request): JsonResponse
    {
        $categories = $this->category->where(['parent_id' => $request->parent_id])->get();
        $response = 'option value="' . 0 . '" disable selected>---Pilih---</option>';

        foreach($categories as $row) {
            if($row->id == $request->sub_category) {
                $response .= '<option value="' . $row->id . '"selected>' . $row->name . '</option>';
            } else {
                $response .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }

        return response()->json(['options'=> $response]);
    }

    public function store(Request $request): JsonResponse
    {
        
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:products',
            'image' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric',
            'product_type' => 'required',
            'stock_type' => 'required|in:unlimited,fixed',
            'product_stock' => 'required_if:stock_type,fixed'
        ],[
            'name.required' => 'Nama produk tidak boleh kosong',
            'name.unique' => 'Nama produk sudah tersedia',
            'image.required' => 'Gambar produk tidak boleh kosong',
            'category_id.required' => 'Silahkan pilih salah satu kategori',
            'product_type.required' => 'Silahkan pilih salah satu tipe produk',
            'product_stock.required_if' => 'Stok produk harus diisi minimal 1'
        ]);
         
        if(in_array(request('stock_type'), ['fixed'])) {
            if($request->product_stock < 1) {
                $validator->getMessageBag()->add('product_stock', 'Stok produk harus diisi minimal 1');
                return response()->json([
                    'errors' => Helpers::error_processor($validator)
                ]);
            }
        }

        if(!is_null($request['discount_type'])) {

            if($request['discount_type'] == 'percent') {
                $discount = ($request['price'] / 100) * $request['discount'];
            } else {
                $discount = $request['discount'];
            }

            if($request['price'] <= $discount) {
                $validator->getMessageBag()->add('unit_price', 'Diskon tidak boleh melebihi atau sama dengan harga asli');
                return response()->json([
                    'errors' => Helpers::error_processor($validator)
                ]);
            }
        }

        $tags_ids = [];
        if($request->tags != null) {
            $tags = explode(',', $request->tags);
        }
        
        if(isset($tags)) {
            foreach($tags as $key => $value) {
                $tag = Tag::firstOrNew(['tag' => $value]);
                $tag->save();
                $tags_ids[] = $tag->id;
            }
        }

        $product = $this->product;
        $product->name = $request->name;

        $category = [];
        if($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1
            ];
        }

        if($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = strip_tags($request->description);

        $choice_options = [];
        $product->choice_options = json_encode($choice_options);

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

        if($validator->fails()) {
            return response()->json([
                'errors' => Helpers::error_processor($validator)
            ]);
        }

        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->local_product = $request->product_type;
        $product->image = Helpers::upload('product/', 'png', $request->file('image'));
        $product->available_time_starts = $request->available_time_starts;
        $product->available_time_ends = $request->available_time_ends;
        $product->tax_type = !is_null($request['tax_type']) ? ($request['tax_type'] != 'nothing' ? $request['tax_type'] : null) : null;
        $product->tax = !is_null($request['tax_type']) ? ($request['tax_type'] != 'nothing' ? $request['tax'] : 0) : 0;
        $product->discount_type = !is_null($request['discount_type']) ? ($request['discount_type'] != 'nothing' ? $request['discount_type'] : null) : null;
        $product->discount = !is_null($request['discount_type']) ? ($request['discount_type'] != 'nothing' ? $request['discount'] : 0) : 0;

        $product->status = $request->status == 'on' ? 1 : 0;
        $product->is_recommended = $request->is_recommended == 'on' ? 1 : 0;
        $product->save();

        $product->tags()->sync($tags_ids);

        $main_branch_product = $this->product_by_branch;
        $main_branch_product->product_id = $product->id;
        $main_branch_product->price = $request->price;
        $main_branch_product->discount_type = $request->discount_type;
        $main_branch_product->discount = $request->discount ?? 0;
        $main_branch_product->branch_id = 1;
        $main_branch_product->is_available = 1;
        $main_branch_product->variations = $variations;
        $main_branch_product->stock_type = $request->stock_type;
        $main_branch_product->stock = $request->product_stock ?? 0;
        $main_branch_product->save();
        

        return response()->json([], 200);
    }

    public function view($id)
    {
        $product = $this->product->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $product->id])->latest()->paginate(Helpers::get_pagination());        
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    public function status(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();

        return back();
    }

    public function recommended(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->is_recommended = $request->status;
        $product->save();

        return back();
    }

    public function edit($id)
    {
        $product = $this->product->withoutGlobalScopes()->with(['main_branch_product'])->find($id);
        $product_category = json_decode($product->category_ids);
        $categories = $this->category->where(['parent_id' => 0])->get();

        return view('admin-views.product.edit', compact('product', 'product_category', 'categories'));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products,name,' . $id,
            'category_id' => 'required',
            'price' => 'required|numeric',
            'product_type' => 'required',
            'stock_type' => 'required|in:unlimited,fixed',
            'product_stock' => 'required_if:stock_type,fixed'
        ], [
            'name.required' => 'Nama produk tidak boleh kosong',
            'name.unique' => 'Nama produk sudah tersedia',
            'category_id.required' => 'Silahkan pilih salah satu kategori',
            'price.required' => 'Harga produk tidak boleh kosong',
            'product_type.required' => 'Silahkan pilih salah satu tipe produk',
        ]);

        if(in_array(request('stock_type'), ['fixed'])) {
            if($request->product_stock < 1) {
                $validator->getMessageBag()->add('product_stock', 'Stok produk harus diisi minimal 1');
                return response()->json([
                    'errors' => Helpers::error_processor($validator)
                ]);
            }
        }

        if(!is_null($request['discount_type'])) {

            if($request['discount_type'] == 'percent') {
                $discount = ($request['price'] / 100) * $request['discount'];
            } else {
                $discount = $request['discount'];
            }

            if($request['price'] <= $discount) {
                $validator->getMessageBag()->add('unit_price', 'Diskon tidak boleh melebihi atau sama dengan harga asli');
                return response()->json([
                    'errors' => Helpers::error_processor($validator)
                ]);
            }
        }

        $tags_ids = [];
        if($request->tags != null) {
            $tags = explode(',', $request->tags);
        }
        
        if(isset($tags)) {
            foreach($tags as $key => $value) {
                $tag = Tag::firstOrNew(['tag' => $value]);
                $tag->save();
                $tags_ids[] = $tag->id;
            }
        }

        $product = $this->product->find($id);
        $product->name = $request->name;

        $category = [];

        if($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1
            ];
        }

        if($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2
            ];
        }

        if($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = strip_tags($request->description);
    
        $choice_options = [];
        $product->choice_options = json_encode($choice_options);

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

        if($validator->fails()) {
            return response()->json([
                'errors' => Helpers::error_processor($validator)
            ]);
        }

        $product->variations = json_encode($variations);

        // branch variation update
        $branch_products = $this->product_by_branch->where(['product_id' => $id])->get();

        foreach($branch_products as $branch_product) {
            $mapped = array_map(function ($variation) use ($branch_product){
                $variation_array = [];
                $variation_array['name'] = $variation['name'];
                $variation_array['type'] = $variation['type'];
                $variation_array['min'] = $variation['min'];
                $variation_array['max'] = $variation['max'];
                $variation_array['required'] = $variation['required'];
                $variation_array['values'] = array_map(function ($value) use ($branch_product, $variation){
                    $option_array = [];
                    $option_array['label'] = $value['label'];

                    $price = $value['optionPrice'];

                    foreach($branch_product['variations'] as $branch_variation) {
                        if($branch_variation['name'] == $variation['name']) {
                            foreach($branch_variation['values'] as $branch_value) {
                                if($branch_value['label'] == $value['label']) {
                                    $price = $branch_value['optionPrice'];
                                }
                            }
                        };   
                    }

                    $option_array['optionPrice'] = $price;
                    
                    return $option_array;
                }, $variation['values']);

                return $variation_array;
            }, $variations);

            $data = ['variations' => $mapped];

            $this->product_by_branch->whereIn('product_id', [$id])->update($data);
        }

        $product->price = $request->price;
        $product->local_product = $request->product_type;
        $product->image = $request->has('image') ? Helpers::update('product/', 'png', $request->file('image')) : $product->image;
        $product->available_time_starts = $request->available_time_starts;
        $product->available_time_ends = $request->available_time_ends;
        $product->tax_type = $request['tax_type'] != 'nothing' ? $request['tax_type'] : null;
        $product->tax = $request['tax_type'] != 'nothing' ? $request['tax'] : 0;
        $product->discount_type = $request['discount_type'] != 'nothing' ? $request['discount_type'] : null;
        $product->discount = $request['discount_type'] != 'nothing' ? $request['discount'] : 0;
        $product->status = $request->status == 'on' ? 1 : 0;
        $product->is_recommended = $request->is_recommended == 'on' ? 1 : 0;
        $product->save();

        $product->tags()->sync($tags_ids);

        $updated_product = $this->product_by_branch->updateOrCreate([
            'product_id' => $product->id,
            'branch_id' => 1
        ], [
            'product_id' => $product->id,
            'price' => $request->price,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount ?? 0,
            'branch_id' => 1,
            'is_available' => 1,
            'variations' => $variations,
            'stock_type' => $request->stock_type,
            'stock' => $request->product_stock ?? 0
        ]);

        if($updated_product->wasChanged('stock_type') || $updated_product->wasChanged('stock')) {
            $updated_product->sold_quantity = 0;
            $updated_product->save();
        }

        return response()->json([], 200);
    }

    public function delete(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        Helpers::delete('product/' . $product['image']);
        $product->delete();    
    
        Toastr::success('Produk berhasil dihapus');
        return back();
    }
}
