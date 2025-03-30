<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryChargeSetup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        private Branch $branch,
        private DeliveryChargeSetup $delivery_charge_setup
    ) {}

    public function index(): Renderable
    {
        return view('admin-views.branch.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255|unique:branches',
            'email' => 'required|max:255|unique:branches',
            'password' => 'required|min:6|max:255',
            'preparation_time' => 'required',
            'image' => 'required|max:2048'
        ], [
            'name.required' => 'Nama cabang tidak boleh kosong',
            'name.unique' => 'Nama cabang sudah tersedia',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah tersedia',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'preparation.required' => 'Waktu persiapan tidak boleh kosong',
            'image.required' => 'Gambar cabang tidak boleh kosong',
        ]);

        if($request->has('image')) {
            $image_name = Helpers::upload('branch/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        if($request->has('cover_image')) {
            $cover_image_name = Helpers::upload('branch/', 'png', $request->file('cover_image'));
        } else {
            $cover_image_name = 'def.png';
        }

        $default_branch = $this->branch->find(1);
        $default_lat = $default_branch->latitude ?? '-8.200786388659255';
        $default_long = $default_branch->longitude ?? '113.53614807128906';
        $default_coverage = $default_branch->coverage ?? 50;

        $branch = $this->branch;
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->latitude = $request->latitude ?? $default_lat;
        $branch->longitude = $request->longitude ?? $default_long;
        $branch->coverage = $request->coverage ?? $default_coverage;
        $branch->address = $request->address;
        $branch->phone = $request->phone;
        $branch->password = bcrypt($request->password);
        $branch->preparation_time = $request->preparation_time;
        $branch->image = $image_name;
        $branch->cover_image = $cover_image_name;
        $branch->save();

        $branch_delivery_charge = $this->delivery_charge_setup;
        $branch_delivery_charge->branch_id = $branch->id;
        $branch_delivery_charge->delivery_charge_type = 'distance';
        $branch_delivery_charge->delivery_charge_per_kilometer = 7000;
        $branch_delivery_charge->minimum_delivery_charge = 7000;
        $branch_delivery_charge->minimum_distance_for_free_delivery = 10;
        $branch_delivery_charge->save();

        return redirect()->route('admin.branch.list')->with('branch-store', true);
    }

    public function list(Request $request): Renderable
    {
        $search = $request['search'];
        $query = $this->branch->with('delivery_charge_setup')
        ->when($search, function ($q) use ($search) {
            $key = explode(' ', $search);
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                ->orWhere('name', 'like', "%{$value}%");
            }
        });

        $query_param = ['search' => $request['search']];
        $branches = $query->orderBy('id', 'DESC')->paginate(Helpers::get_pagination())
        ->appends($query_param);

        return view('admin-views.branch.list', compact('branches', 'search'));
    }

    public function edit($id): Renderable
    {
        $branch = $this->branch->find($id);

        return view('admin-views.branch.edit', compact('branch'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255',
            'preparation_time' => 'required',
            'email' => ['required', 'unique:branches,email,' . $id .',id'],
            'image' => 'max:2048',
            'password' => 'nullable|min:6|max:255'
        ], [
            'name.required' => 'Nama cabang tidak boleh kosong',
            'preparation_time' => 'Waktu persiapan tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.unqie' => 'Email sudah tersedia'
        ]);

        $branch = $this->branch->find($id);
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->longitude = $request->longitude ? $request->longitude : $branch->longitude;
        $branch->latitude = $request->latitude ? $request->latitude : $branch->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : $branch->coverage;
        $branch->address = $request->address;
        $branch->image = $request->has('image') ? Helpers::update('branch/', $branch->image, 'png', $request->file('image')) : $branch->image;
        $branch->cover_image = $request->has('cover_image') ? Helpers::update('branch/', $branch->cover_image, 'png', $request->file('cover_image')) : $branch->cover_image;

        if($request['password'] != null) {
            $branch->password = bcrypt($request->password);
        }

        $branch->phone = $request->phone ?? '';
        $branch->preparation_time = $request->preparation_time;
        $branch->save();

        Toastr::success('Cabang bisnis berhasil diperbarui');
        return back();
    }

    public function status(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);
        $branch->status = $request->status;
        $branch->save();

        Toastr::success('Status cabang berhasil diperbarui');
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);

        if($branch && $branch->delete()) {
            $this->delivery_charge_setup->where(['branch_id' => $request->id])->delete();
            Helpers::delete('branch/' . $branch['image']);
            Helpers::delete('branch/' . $branch['cover_image']);

            Toastr::success('Cabang berhasil dihapus');
        } else {
            Toastr::error('Cabang gagal dihapus');
        }

        return back();
    }
}
