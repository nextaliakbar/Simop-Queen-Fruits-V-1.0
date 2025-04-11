<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BusinessSettingsController extends Controller
{
    public function index()
    {
        $branch = Branch::find(auth('branch')->id());
        return view('branch-views.business-settings.branch-index', compact('branch'));
    }

    public function update(Request $request): RedirectResponse
    {
        $branch = Branch::find(auth('branch')->id());
        $branch->name = $request->name;
        $branch->preparation_time = $request->preparation_time;
        $branch->save();

        Toastr::success('Pengaturan cabang berhasil diperbarui');
        return back();
    }
}
