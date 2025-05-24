<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private Admin $admin,
        private AdminRole $admin_role
    ) {}

    public function index(): Renderable
    {
        $roles = $this->admin_role->whereNotIn('id', [1])->get();
        return view('admin-views.employee.add-new', compact('roles'));   
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required',
            'phone' => 'required',
            'identity_image' => 'required',
            'identity_type' => 'required',
            'identity_number' => 'required',
            'confirm_password' => 'same:password'
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'role_id.required' => 'Pilih salah satu izin peran',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah tersedia',
            'password.required' => 'Password tidak boleh kosong',
            'phone.required' => 'No. hp tidak boleh kosong',
            'identity_image.required' => 'Foto identitas tidak boleh kosong',
            'identity_type.required' => 'Jenis identitas tidak boleh kosong',
            'identity_number.required' => 'Nomor identitas tidak boleh kosong',
            'confirm_password.same' => 'Konfirmasi password tidak valid'
        ]);

        if($request->role_id == 1) {
            Toastr::warning('Akses ditolak');
            return back();
        }

        $identity_image_names = [];
        if(!empty($request->file('identity_image'))) {
            foreach($request->identity_image as $img) {
                $identity_image_names[] = Helpers::upload('admin/', $img->getClientOriginalExtension(), $img);
            }

            $identity_image = json_encode($identity_image_names);
        } else {
            $identity_image = json_encode([]);
        }

        $admins = $this->admin;
        $admins->f_name = $request->name;
        $admins->phone = $request->phone;
        $admins->email = $request->email;
        $admins->admin_role_id = $request->role_id;
        $admins->identity_number = $request->identity_number;
        $admins->identity_type = $request->identity_type;
        $admins->identity_image = $identity_image;
        $admins->password = bcrypt($request->password);
        $admins->status = 1;
        $admins->image = Helpers::upload('admin/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
        $admins->created_at = now();
        $admins->updated_at = now();
        $admins->save();

        Toastr::success('Karyawan berhasil ditambahkan');
        return redirect()->route('admin.employee.list');
    }

    public function list(Request $request): Renderable
    {
        $search = $request['search'];
        $key = explode(' ', $request['search']);

        $query = $this->admin->with(['role'])
        ->when($search != null, function ($query) use ($key) {
            $query->whereNotIn('id', [1])->where(function ($query) use ($key){
                foreach($key as $value) {
                    $query->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%");
                } 
            });
        }, function ($query) {
            $query->whereNotIn('id', [1]);
        });

        $employees = $query->paginate(Helpers::get_pagination());
        return view('admin-views.employee.list', compact('employees', 'search'));
    }

    public function edit($id): Renderable
    {
        $employee = $this->admin->where(['id' => $id])->first();
        $roles = $this->admin_role->whereNotIn('id', [1])->get();

        return view('admin-views.employee.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
            'phone' => 'required',
            'identity_type' => 'required',
            'identity_number' => 'required'
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'role_id.required' => 'Pilih salah satu izin peran',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah tersedia',
            'phone.required' => 'No. hp tidak boleh kosong',
            'identity_type.required' => 'Jenis identitas tidak boleh kosong',
            'identity_number.required' => 'Nomor identitas tidak boleh kosong',
        ]);

        if($request->role_id == 1) {
            Toastr::warning('Akses ditolak');
            return back();
        }

        $employee = $this->admin->find($id);
        $identity_image = $employee['identity_image'];

        if($request['password'] != null) {
            $request->validate([
                'confirm_password' => 'same:password'
            ], [
                'confirm_password.same' => 'Konfirmasi password tidak valid'
            ]);

            if(strlen($request['password']) < 6) {
                Toastr::warning('Panjang password minimal harus 6 karakter');
                return back();
            }

            $password = bcrypt($request['password']);
        } else {
            $password = $employee['password'];
        }

        if($request->has('image')) {
            $employee['image'] = Helpers::update('admin/' . $employee['image'], $request->file('image')->getClientOriginalExtension(), $request->file('image'));
        }

        $identity_image_names = [];

        if(!empty($request->file('identity_image'))) {
            foreach($request->identity_image as $img) {
                $identity_image_names[] = Helpers::upload('admin/', $img->getClientOriginalExtension(), $img);
            }

            $identity_image = json_encode($identity_image_names);
        }

        $admins = $this->admin->find($id);
        $admins->f_name = $request->name;
        $admins->phone = $request->phone;
        $admins->email = $request->email;
        $admins->admin_role_id = $request->role_id;
        $admins->password = $password;
        $admins->image = $employee['image'];
        $admins->identity_number = $request->identity_number;
        $admins->identity_type = $request->identity_type;
        $admins->identity_image = $identity_image;
        $admins->updated_at = now();
        $admins->save();

        Toastr::success('Karyawan berhasil diperbarui');
        return redirect()->route('admin.employee.list');
    }

    public function status(Request $request): RedirectResponse
    {
        $employee = $this->admin->find($request->id);
        $employee->status = $request->status;
        $employee->save();
        Toastr::success('Status berhasil diubah');
        return back();
    }

    public function delete(Request $request): RedirectResponse
    {
        if($request->id == 1) {
            Toastr::warning('Master admin tidak dapat dihapus');
        } else {
            $employee = $this->admin->find($request->id);
            Helpers::delete('admin/' .$employee->image);

            foreach(json_decode($employee->identity_image) as $img) {
                Helpers::delete('admin/'. $img);
            }

            $employee->delete();

            Toastr::success('Karyawan berhasil dihapus');
        }

        return back();
    }
}
