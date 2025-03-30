<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Validator;

class TimeScheduleController extends Controller
{
    public function __construct(
        private TimeSchedule $time_schedule
    ) {}

    public function time_schedule_index(): Renderable
    {
        $schedules = $this->time_schedule->get();

        return view('admin-views.business-settings.time-schedule-index', compact('schedules'));
    }

    public function add_schedule(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ], [
            'start_time.required' => 'Waktu mulai tidak boleh kosong',
            'end_time.required' => 'Waktu akhir tidak boleh kosong',
            'end_time.after' => 'Silahkan tentukan waktu mulai dulu'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $temp = $this->time_schedule->where('day', $request->day)
        ->where(function($q) use ($request) {
            return $q->where(function($query) use ($request) {
                return $query->where('opening_time', '<=', $request->start_time)->where('closing_time', '>=', $request->start_time);
            })->orWhere(function($query) use ($request) {
                return $query->where('opening_time', '<=', $request->end_time)->where('closing_time', '>=', $request->end_time);
            });
        })->first();

        if(isset($temp)) {
            return response()->json([
                'errors' => [
                    'code' => 'time',
                    'message' => 'Terdapat jadwal yang tumpang tindih'
                ]
            ]);
        }

        $this->time_schedule->insert([
            'day' => $request->day,
            'opening_time' => $request->start_time,
            'closing_time' => $request->end_time
        ]);

        $schedules = $this->time_schedule->get();
        return response()->json(['view' => view('admin-views.business-settings.partials._schedule', compact('schedules'))->render()]);
    }

    public function remove_schedule(Request $request): JsonResponse
    {
        $schedule = $this->time_schedule->find($request['schedule_id']);

        if(!$schedule) {
            return response()->json([], 404);
        }

        $restaurant = $schedule->restaurant;

        $schedule->delete();

        $schedules = $this->time_schedule->get();

        return response()->json([
            'view' => view('admin-views.business-settings.partials._schedule', compact('schedules'))->render()
        ]);
    }
}
