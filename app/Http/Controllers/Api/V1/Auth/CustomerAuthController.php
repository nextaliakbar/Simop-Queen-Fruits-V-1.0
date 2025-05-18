<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\LoginSetup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class CustomerAuthController extends Controller
{
    public function __construct(
        private User $user,
        private BusinessSetting $business_setting,
        private LoginSetup $login_setup
    ) {}

    public function registration(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6'
        ], [
            'f_name.required' => 'Nama depan tidak boleh kosong',
            'l_name.required' => 'Nama belakang tidak boleh kosong'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $temporary_token = Str::random(40);
        
        $user = $this->user->create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'temporary_token' => $temporary_token,
        ]);

        $token = $user->createToken('StoreCustomerAuthToken')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'password' => 'required|min:6',
            'type' => 'required|in:phone,email'
        ]);

        $user_id = $request['email_or_phone'];
        $type = $request['type'];

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = $this->user->where('is_active', 1)
        ->where(function($query) use ($user_id) {
            $query->where(['email' => $user_id])->orWhere('phone', $user_id);
        })->first();

        $max_login_hit = 5;
        $temp_block_time = 600;

        if(isset($user)) {
            // dd("Test 1");
            if(isset($user->temp_block_time) && Carbon::parse($user->time_block_time)->DiffInSeconds() <= $temp_block_time) {
                $time = $temp_block_time - Carbon::parse($user->temp_block_time)->DiffInSeconds();
            }

            $data = [
                'email' => $user->email,
                'password' => $request->password,
                'user_type' => null
            ];

            if(auth()->attempt($data)) {
                $temporary_token = Str::random(40);

                $token = auth()->user()->createToken('StoreCustomerAuth')->accessToken;
            
                return response()->json(['token' => $token, 'status' => true], 200);
            }
        }
        // dd("Testt 2");

        return response()->json(['errors' => ['code' => 'auth-001','message' => 'Invalid credentials']], 401);
    }
}
