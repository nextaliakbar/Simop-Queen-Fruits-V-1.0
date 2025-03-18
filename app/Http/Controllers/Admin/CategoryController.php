<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private Category $category
    ) {}

    public function index(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];
        if($request->has('search')) {
            $key = explode(' ',$request['search']);

            $categories = $this->category->where('position', 0)->where(function($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $categories = $this->category->where('position', 0);
        }

        $categories = $categories->orderBY('priority', 'ASC')->paginate(Helpers::get_pagination())->appends($query_param);
        return view('admin-views.category.index', compact('categories', 'search'));
    }

    public function sub_index(Request $request): Renderable
    {
        $search = $request['search'];
        $query_param = ['search' => $search];

        $categories = $this->category->with(['parent'])
        ->when($request['search'], function($query) use ($search){
            $query->orWhere('name', 'like', "%{$search}%");
        })->where(['position' => 1])
        ->latest()
        ->paginate(Helpers::get_pagination())
        ->appends($query_param);

        return view('admin-views.category.sub-index', compact('categories', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required'
        ], ['name.required' => 'Nama kategori tidak boleh kosonng']);

        if($request->has('type')) {
            $request->validate([
                'parent_id' => 'required',
            ],['parent_id.required' => 'Pilih salah satu kategori']);
        }

        if(strlen($request->name) > 255) {
            toastr::error('Nama kategori terlalu panjang');
            return back();
        }

        $category_exist = $this->category->where('name', $request->name)->where('parent_id', $request->parent_id ?? 0)->first();
        if(isset($category_exist)) {
            Toastr::error($request->parent_id == null ? 'Kategori' : 'Sub Kategori' . 'sudah tersedia');
            return back();
        }

        if(!empty($request->file('image'))) {
            $image_name = Helpers::upload('category/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        if(!empty($request->file('banner_image'))) {
            $banner_image_name = Helpers::upload('category/banner/', 'png', $request->file('banner_image'));
        } else {
            $banner_image_name = 'def.png';
        }

        $category = $this->category;
        $category->name =$request->name;
        $category->image = $image_name;
        $category->banner_image = $banner_image_name;
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->position = $request->position;
        $category->save();

        Toastr::success('Kategori produk berhasil ditambahkan');
        return back();
    }

    public function edit($id): Renderable
    {
        $category = $this->category->find($id);
        return view('admin-views.category.edit', compact('category'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required'
        ],['name.required' => 'Nama kategori tidak boleh kosong']);

        if(strlen($request->name) > 255) {
            toastr::error('Nama kategori terlalu panjang');
            return back();
        }

        $category = $this->category->find($id);
        $category->name = $request->name;
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, 'png', $request->file('image')) : $category->image;
        $category->banner_image = $request->has('banner_image')? Helpers::update('category/banner/', $category->banner_image, 'png', $request->file('banner_image')) : $category->banner_image;
        $category->save();
        $message = $category->parent_id == 0 ? 'Kategori produk berhasil diperbarui' : 'Sub kategori produk berhasil diperbarui';
        Toastr::success($message);
        return back();
    }   

    public function status(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->status = $request->status;
        $category->save();
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        if($category->childes->count() == 0) {
            $category->delete();
            Helpers::delete('category/' . $category['image'], 'category/banner/' . $category['banner_image']);
            Toastr::success($category->parent_id == 0 ? 'Kategori produk berhasil dihapus' : 'Sub kategori produk berhasil dihapus');
        } else {
            Toastr::warning('Silahkan hapus sub kategori produk terlebih dahulu');
        };

        return back();
    }

    public function priority(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->priority = $request->priority;
        $category->save();

        Toastr::success('Nomor urut prioritas di perbarui');
        return back();
    }
}
