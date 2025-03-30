<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchPromotion;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchPromotionController extends Controller
{
    public function __construct(
        private Branch $branch,
        private BranchPromotion $branch_promotion
    ) {}

    public function status(Request $request): RedirectResponse
    {
        $branch = $this->branch($request->id);
        $branch->branch_promotion_status = $request->status;
        $branch->save();

        Toastr::success('Status kampanye promosi cabang berhasil diperbarui');
        return back();
    }
}
