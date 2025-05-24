<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function __construct(
        private Branch $branch
    ){}

    public function settings(): Renderable
    {
        return view('branch-views.settings');
    }

    public function settings_basic_info_update(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'phone.required' => 'No. hp tidak boleh kosong'
        ]);

        $branch = $this->branch->find(auth('branch')->id());
        $branch->name = $request->input('name');
        $branch->image = $request->has('image') ? Helpers::update('branch/', $branch->image, $request->file('image')->getClientOriginalExtension(), $request->file('image')) : $branch->image;
        $branch->phone = $request->input('phone');

        $branch->save();

        Toastr::success('Cabang berhasil diperbarui');
        return back();
    }

    public function settings_password_info_update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ], [
            'password.required' => 'Passowrd tidak boleh kosong',
            'confirm_password.required' => 'Konfirmasi password tidak boleh kosong',
            'confirm_password.same' => 'Konfirmasi password harus sama dengan password'
        ]);

        $branch = $this->branch->find(auth('branch')->id());
        $branch->password = bcrypt($request->input('password'));
        $branch->save();

        Toastr::success('Password sukses diubah');
        return back();
    }
}
