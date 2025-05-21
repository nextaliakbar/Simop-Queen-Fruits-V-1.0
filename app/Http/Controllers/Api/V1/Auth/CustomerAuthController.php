<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\LoginSetup;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterval;
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
            'l_name.required' => 'Nama belakang tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'password' => 'password tidak boleh kosong'
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
        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = $this->user->where('is_active', 1)
        ->where(function($query) use ($user_id) {
            $query->where(['email' => $user_id])->orWhere('phone', $user_id);
        })->first();

        $max_login_hit = Helpers::get_business_settings('maximum_login_hit') ?? 5;
        $temp_block_time = Helpers::get_business_settings('temporary_login_block_time') ?? 600;

        if(isset($user)) {
            if(isset($user->temp_block_time) && Carbon::parse($user->time_block_time)->DiffInSeconds() <= $temp_block_time) {
                $time = $temp_block_time - Carbon::parse($user->temp_block_time)->DiffInSeconds();
                $errors = [];
                $errors[] = [
                    'code' => 'login_block_time',
                    'message' => 'Coba lagi setelah ' . CarbonInterval::seconds($time)->cascade()->forHumans()
                ];

                return response()->json(['errors' => $errors], 403);
            }

            $data = [
                'email' => $user->email,
                'password' => $request->password,
                'user_type' => null
            ];

            if(auth()->attempt($data)) {
                $token = auth()->user()->createToken('StoreCustomerAuth')->accessToken;
                $user->login_hit_count = 0;
                $user->is_temp_blocked = 0;
                $user->temp_block_time = null;
                $user->save();
            
                return response()->json(['token' => $token, 'status' => true], 200);
            } else {
                if(isset($user->temp_block_time) && Carbon::parse($user->time_block_time)->DiffInSeconds() <= $temp_block_time) {
                    $time = $temp_block_time - Carbon::parse($user->temp_block_time)->DiffInSeconds();
                    $errors = [];
                    $errors[] = [
                        'code' => 'login_block_time',
                        'message' => 'Coba lagi setelah ' . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];

                    return response()->json(['errors' => $errors], 403);
                }

                if($user->is_temp_blocked_time == 1 && Carbon::parse($user->temp_block_time)->DiffInSeconds() >= $temp_block_time) {
                    $user->login_hit_count = 0;
                    $user->is_temp_blocked = 0;
                    $user->temp_block_time = null;
                    $user->updated_at = now();
                    $user->save();
                }

                if($user->login_hit_count >= $max_login_hit && $user->is_temp_blocked == 0) {
                    $user->is_temp_blocked = 1;
                    $user->temp_block_time = now();
                    $user->updated_at = now();
                    $user->save();

                    $time = $temp_block_time - Carbon::parse($user->temp_block_time)->diffInSeconds();
                    $errors = [];
                    $errors[] = [
                        'code' => 'login_temp_blocked',
                        'message' => 'Terlalu banyak percobaan. Coba lagi setelah ' . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];

                    return response()->json(['errors' => $errors], 403);
                }
            }

            $user->login_hit_count += 1;
            $user->temp_block_time = null;
            $user->updated_at = now();
            $user->save();
        }
        $errors = [];
        $errors[] = [
            'code' => 'auth-001',
            'message' => 'Akun tidak ditemukan'
        ];
        return response()->json(['errors' => $errors], 401);
    }
}
