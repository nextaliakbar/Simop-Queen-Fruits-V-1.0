<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomRoleController extends Controller
{
    public function __construct(
        private AdminRole $admin_role,
        private Admin $admin
    ) {}

    public function create(Request $request): Renderable
    {
        $search = $request['search'];
        $roles = $this->admin_role->whereNotIn('id', [1])
        ->when($search, function ($query) use ($search) {
            $params = explode(' ', $search);
            foreach ($params as $param) {
                $query->where('name', 'like', "%" . $param ."%");
            }
        })->latest()->get();

        return view('admin-views.custom-role.create', compact('roles', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:admin_roles'
        ], [
            'name.required' => 'Nama peran tidak boleh kosong',
            'name.unique' => 'Nama peran sudah tersedia'
        ]);

        if($request['modules'] == null) {
            Toastr::error('Pilih setidaknya satu izin peran');
            return back();
        }

        $admin_role = $this->admin_role;
        $admin_role->name = $request->name;
        $admin_role->module_access = json_encode($request['modules']);
        $admin_role->status = 1;
        $admin_role->save();

        Toastr::success('Izin peran berhasil ditambahkan');
        return back();
    }

    public function change_status($id, Request $request): JsonResponse
    {
        $role_exist = $this->admin->where('admin_role_id', $id)->first();

        if($role_exist) {
            return response()->json('Karyawan ditugaskan pada peran ini. Status gagal diperbarui', 409);
        } else {
            $action = $this->admin_role->where('id', $id)->update(['status' => $request['status']]);

            if($action) {
                return response()->json('Status berhasil diperbarui', 200);
            } else {
                return response()->json('Status gagal diperbarui', 500);
            }
        }
    }

    public function edit($id): Renderable
    {
        $role = $this->admin_role->where(['id' => $id])->first();
        return view('admin-views.custom-role.edit', compact('role'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required'
        ], [
            'name.required' => 'Nama tidak boleh kosong'
        ]);

        $admin_roles = $this->admin_role->where(['id' => $id])->first();
        $admin_roles->name = $request->name;
        $admin_roles->module_access = json_encode($request['modules']);
        $admin_roles->status = 1;
        $admin_roles->save();

        Toastr::success('Izin peran berhasil diperbarui');
        return redirect('admin/custom-role/create');
    }

    public function delete(Request $request): RedirectResponse
    {
        $role_exist = $this->admin->where('admin_role_id', $request->id)->first();

        if($role_exist) {
            Toastr::warning('Izin peran ini sedang ditugaskan untuk karyawan');
        } else {
            $action = $this->admin_role->destroy($request->id);

            if($action) {
                Toastr::success('Izin peran berhasil dihapus');
            } else {
                Toastr::error('Izin peran gagal dihapus');
            }
        }

        return back();
    }
}
