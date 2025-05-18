<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function __construct(
        private User $user,
        private CustomerAddress $customerAddress
    ) {}

    public function info(Request $request): JsonResponse
    {
        $user = $this->user
            ->withCount(['orders', 'wishlist'])
            ->where(['id' => $request->user()->id])
            ->first();

        return response()->json($user, 200);
    }

    public function addressList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'guest_id' => auth('api')->user() ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = (bool)auth('api')->user() ? auth('api')->user()->id : $request['guest_id'];
        $userType = (bool)auth('api')->user() ? 0 : 1;

        $addresses = $this->customerAddress
            ->where(['user_id' => $userId, 'is_guest' => $userType])
            ->latest()
            ->get();

        return response()->json($addresses, 200);
    }
}
