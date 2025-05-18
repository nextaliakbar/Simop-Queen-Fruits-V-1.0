<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BusinessSetting;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function __construct(
        private Order $order,
        private Admin $admin,
        private BusinessSetting $business_setting
    ) {}

    public function settings()
    {
        return view('admin-views.settings');
    }

    public function settings_update(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'f_name' => 'required',
                'email' => [
                    'required', 'unique:admins,email,' . auth('admin')->id() . ',id'
                ],
                'phone' => 'required'
            ],
            [
                'f_name.required' => 'Nama depan tidak boleh kosong',
                'email.required' => 'Emmail tidak boleh kosong',
                'email.unique' => 'Email sudah tersedia',
                'phone.required' => 'No. hp tida boleh kosong'
            ]
            );

            $admin = $this->admin->find(auth('admin')->id());
            $admin->f_name = $request->f_name;
            $admin->l_name = $request->l_name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $admin->image = $request->has('image') ? Helpers::update('admin/', $admin->image, 'png', $request->file('image')) : $admin->image;
            $admin->save();

            Toastr::success('Informasi profil berhasil diperbarui');
            return back();
    }

    public function settings_password_update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ], [
            'password.required' => 'Passowrd tidak boleh kosong',
            'confirm_password.required' => 'Konfirmasi password tidak boleh kosong',
            'confirm_password.same' => 'Konfirmasi password harus sama dengan password'
        ]);

        $admin = $this->admin->find(auth('admin')->id());
        $admin->password = bcrypt($request['password']);
        $admin->save();
        
        Toastr::success('Password sukses diubah');

        return back();
    }

    public function store_data(): JsonResponse
    {
        $new_order = $this->order->where(['checked' => 0])->count();

        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $new_order]
        ]);
    }
}
