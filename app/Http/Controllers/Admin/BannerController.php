<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\CentralLogics\Helpers;

class BannerController extends Controller
{
    public function __construct(
        private Banner $banner,
        private Product $product,
        private Category $category,
    ){}

    public function list(Request $request) 
    {
        $search = $request->search;
        $query_param = ['search' => $search];

        $banners = $this->banner->when($search, function($query) use ($search, $query_param){
            $keywords = explode(' ', $search);
            foreach($keywords as $keyword) {
                $query->orWhere('title', 'LIKE', "%$keyword%")
                ->orWhere('id', 'LIKE', "%$keyword%");
            }
        })->latest()->paginate(Helpers::get_pagination())
        ->appends($query_param);

        return view('admin-views.banner.list', compact('banners', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required',
            'item_type' => 'required'
        ], [
            'title.required' => 'Judul tidak boleh kosong',
            'title.ax' => 'Judul terlalu panjang',
            'image.required' => 'Gambar banner tidak boleh kosong',
            'item_type.required' => 'Silahkan pilih jenis banner'
        ]);

        $banner = $this->banner;
        $banner->title = $request->title;

        if($request['item_type'] == 'product') {
            $banner->product_id = $request->product_id;
        } elseif($request['item_type'] == 'category') {
            $banner->category_id = $request->category_id;
        }

        $banner->image = Helpers::upload('banner/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
        $banner->save();

        Toastr::success('Banner berhasil ditambahkan');
        return redirect('admin/banner/list');
    }

    public function status(Request $request): RedirectResponse
    {
        $banner = $this->banner->find($request->id);
        $banner->status = $request->status;
        $banner->save();

        return back();
    }

    public function edit($id): Renderable
    {
        $products = $this->product->orderBy('name')->get();
        $banner = $this->banner->find($id);
        $categories = $this->category->where(['parent_id' => 0])->orderBy('name')->get();
    
        return view('admin-views.banner.edit', compact('banner', 'products', 'categories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'item_type' => 'required'
        ], [
            'title.required' => 'Judul tidak boleh kosong',
            'title.ax' => 'Judul terlalu panjang',
        ]);

        $banner = $this->banner->find($id);
        $banner->title = $request->title;

        if($request['item_type'] == 'product') {
            $banner->product_id = $request->product_id;
            $banner->category_id = null;
        } elseif($request['item_type'] == 'category') {
            $banner->category_id = $request->category_id;
            $banner->product_id = null;
        }

        $banner->image = $request->has('image') ? Helpers::update('banner/', $banner->image, $request->file('image')->getClientOriginalExtension(), $request->file('image')) : $banner->image;
        $banner->save();

        Toastr::success('Banner berhasil diperbarui');
        return redirect('admin/banner/list');
    }

    public function delete(Request $request): RedirectResponse
    {
        $banner = $this->banner->find($request->id);
        Helpers::delete('banner/' . $banner['image']);
        $banner->delete();

        Toastr::success('Banner berhasil dihapus');
        return back();
    }
}
