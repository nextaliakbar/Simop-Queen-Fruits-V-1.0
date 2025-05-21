<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct(
        private User $user,
        private Order $order,
        private CustomerAddress $customer_address
    ) {}

    public function info(Request $request): JsonResponse
    {
        $user = $this->user
            ->withCount(['orders'])
            ->where(['id' => $request->user()->id])
            ->first();

        return response()->json($user, 200);
    }

    public function address_list(Request $request): JsonResponse
    {
        $user_id= auth('api')->user()->id;

        $addresses = $this->customer_address
            ->where('user_id', $user_id)
            ->latest()
            ->get();

        return response()->json($addresses, 200);
    }

    public function add_address(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = auth('api')->user()->id;

        if($request->has('is_default')) {
            $this->customer_address
            ->where(['user_id' => $user_id, 'is_default' => 1])
            ->update(['is_default' => 0]);
        }

        $address = $this->customer_address;
        $address->user_id = $user_id;
        $address->contact_person_name = $request->contact_person_name;
        $address->contact_person_number = $request->contact_person_number;
        $address->address_type = $request->address_type;
        $address->address = $request->address;
        $address->latitude = $request->latitude;
        $address->longitude = $request->longitude;
        $address->is_default = $request->has('is_default') ? 1 : 0;
        $address->save();

        return response()->json([
            'message' => 'Alamah berhasil ditambahkan'
        ], 200);
    }

    public function last_ordered_address(): JsonResponse
    {
        if(!auth('api')->user()) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized'
            ], 200);
        }

        $user_id = auth('api')->user()->id;

        $default_address = $this->customer_address
            ->where(['user_id' => $user_id, 'is_default' => 1])
            ->first();

        if(isset($default_address)) {
            return response()->json($default_address, 200);
        }

        $order = $this->order->where('user_id', $user_id)
        ->whereNotNull('delivery_address_id')
        ->orderBy('id', 'DESC')
        ->with('customer_delivery_address')
        ->first();

        if(isset($order) && $order->customer_delivery_address) {
            return response()->json($order->customer_delivery_address, 200);
        }

        $last_added_address = $this->customer_address
        ->where('user_id', $user_id)->first();

        if(isset($last_added_address)) {
            return response()->json($last_added_address, 200);
        }

        return response()->json(null, 200);
    }
}
