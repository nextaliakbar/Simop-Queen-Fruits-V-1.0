<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MapApiController extends Controller
{
    public function geocode_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required' 
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $api_key = Helpers::get_business_settings('google_map_api_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->lat . ',' . $request->lng . '&key=' . $api_key);

        return $response->json();
    }

    public function distance_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin_lat' => 'required',
            'origin_lng' => 'required',
            'destination_lat' => 'required',
            'destination_lng' => 'required'
        ]);

        if($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $api_key = Helpers::get_business_settings('google_map_api_key');
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $request['origin_lat'] . ',' . $request['origin_lng'] . '&destinations=' . $request['destination_lat'] . ',' . $request['destination_lng'] . '&key=' . $api_key);

        return $response->json();
    }
}
