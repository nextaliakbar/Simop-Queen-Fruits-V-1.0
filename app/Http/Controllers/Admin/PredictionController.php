<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\PredictionDurationTimeOrder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function __construct(
        private PredictionDurationTimeOrder $predict
    ){}

    public function list(Request $request): Renderable
    {
        $query_param = [];
        $search = $request['search'];

        if($request->has('search')) {
            $key = explode(' ', $search);
            $query = $this->predict->where(function ($q) use ($key) {
                foreach($key as $value) {
                    $q->orWhere('order_id', 'like', "%{$value}%");
                }
            });

            $query_param = ['search' => $search];
        } else {
            $query = $this->predict;
        }
        
        $predicts = $query->orderBy('id', 'DESC')
        ->paginate(Helpers::get_pagination())
        ->appends($query_param);

        return view('admin-views.predict.index', compact('predicts', 'search'));
    }
}
