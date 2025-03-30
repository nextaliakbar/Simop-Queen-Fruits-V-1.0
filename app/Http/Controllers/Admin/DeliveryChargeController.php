<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryChargeSetup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeliveryChargeController extends Controller
{
    public function __construct(
        private DeliveryChargeSetup $delivery_charge_setup,
        private Branch $branch
    ) {}

    public function delivery_fee_setup(Request $request)
    {
        $search = $request->input('search');

        $branches = $this->branch->with(['delivery_charge_setup'])->get();
        
        return view('admin-views.business-settings.store.delivery-fee', compact('branches'));
    }

    public function store_km_wise_delivery_charge(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => 'required',
            'delivery_charge_per_kilometer' => 'required|numeric|min:0|max:99999999',
            'minimum_distance_for_free_delivery' => 'required|numeric|min:0|max:99999999',
        ]);

        $this->delivery_charge_setup->updateOrCreate([
            'branch_id' => $request['branch_id']
        ], [
            'branch_id' => $request['branch_id'],
            'delivery_charge_per_kilometer' => $request['delivery_charge_per_kilometer'],
            'minimum_delivery_charge' => $request['minimum_delivery_charge'],
            'minimum_distance_for_free_delivery' => $request['minimum_distance_for_free_delivery']
        ]);

        Toastr::success('Biaya pengiriman berhasil diatur');
        return back();
    }

    public function check_distance_based_delivery(): JsonResponse
    {
        $has_distance_based_delivery = $this->delivery_charge_setup->where('delivery_charge_type', 'distance')->exists();
        return response()->json(['hasDistanceBasedDelivery' => $has_distance_based_delivery]);
    }
}
