<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function __construct(
        private Coupon $coupon
    ) {}

    public function index(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if($request->has('search')) {
            $key = explode(' ', $search);
            $coupons = $this->coupon->where(function($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                    ->orWhere('code', 'like', "%{$value}%");
                }
            });

            $query_param['search'] = $search;
        } else {
            $coupons = $this->coupon;
        }

        $coupons = $coupons->latest()->paginate(Helpers::get_pagination())->appends($query_param);
        return view('admin-views.coupon.index', compact('coupons', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required',
            'title' => 'required|max:255',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required|max:9',
            'min_purchase' => 'required|max:9',
            'max_discount' => 'required|max:9',
        ], [
            'code.required' => 'Kode diskon tidak boleh kosong',
            'title.required' => 'Nama diskon tidak boleh kosong',
            'title.max' => 'Nama diskon terlalu panjang',
            'start_date.required' => 'Isi tanggal mulai terlebih dahulu',
            'expire_date.required' => 'Isi tanggal berakhir terlebih dahulu',
            'discount.required' => 'Isi diskon terlebih dahulu'
        ]);

        if($request->discount_type == 'precent' && (int) $request->discount > 100) {
            Toastr::error('Diskon tidak boleh lebih dari 100%');
            return back();
        }

        $this->coupon->insert([
            'title' => $request->title,
            'code' => $request->code,
            'coupon_limit' => $request->limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
            'status' => 1,
        ]);

        Toastr::success('Diskon berhasil ditambahkan');
        return back();
    }

    public function edit($id): Renderable
    {
        $coupon = $this->coupon->where('id', $id)->first();
        return view('admin-views.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'code' => 'required',
            'title' => 'required|max:255',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required|max:9',
            'min_purchase' => 'required|max:9',
            'max_discount' => 'required|max:9',
        ], [
            'code.required' => 'Kode diskon tidak boleh kosong',
            'title.required' => 'Nama diskon tidak boleh kosong',
            'title.max' => 'Nama diskon terlalu panjang',
            'start_date.required' => 'Isi tanggal mulai terlebih dahulu',
            'expire_date.required' => 'Isi tanggal berakhir terlebih dahulu',
            'discount.required' => 'Isi diskon terlebih dahulu'
        ]);

        if($request->discount_type == 'precent' && (int) $request->discount > 100) {
            Toastr::error('Diskon tidak boleh lebih dari 100%');
            return back();
        }

        $this->coupon->where('id', $id)->update([
            'title' => $request->title,
            'code' => $request->code,
            'coupon_limit' => $request->limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
        ]);

        Toastr::success('Diskon berhasil diperbarui');
        return redirect()->route('admin.coupon.add-new');
    }
    
    public function status(Request $request): RedirectResponse
    {
        $coupon = $this->coupon->find($request->id);
        $coupon->status = $request->status;
        $coupon->save();

        Toastr::success('Status diskon berhasil diperbarui');
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->coupon->find( $request->id)->delete();
        Toastr::success('Diskon berhasil dihapus');
        return back();
    }

    public function generate_coupon_code(): JsonResponse
    {
        return response()->json(Str::random(10));
    }

    public function coupon_details(Request $request): JsonResponse
    {
        $coupon = $this->coupon->findOrFail($request->id);
        return response()->json([
            'success' => 1, 
            'view' => view('admin-views.coupon.partials._coupon-view', compact('coupon'))->render()
        ]);
    }
}
