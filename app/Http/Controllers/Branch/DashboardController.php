<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private Order $order,
        private Branch $branch
    ) {}

    public function dashboard(): Renderable
    {
        $data = self::order_stats_data();

        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $earning = [];
        $earning_data = $this->order->where([
            'order_status' => 'delivered',
            'branch_id' => auth('branch')->id()
        ])->select(DB::raw('IFNULL(sum(order_amount),0) as sums'),
        DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
        ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
        ->groupBy('year', 'month')->get()->toArray();

        for($i = 1; $i <= 12; $i++) {
            $earning[$i] = 0;
            foreach($earning_data as $match) {
                if($match['month'] == $i) {
                    $earning[$i] = number_format($match['sums'],0, '.', '');
                }
            }
        }

        $order_statistics_chart = [];
        $order_statistics_chart_data = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
        ->select(DB::raw('(count(id)) as total'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
        ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
        ->groupBy('year', 'month')->get()->toArray();

        for($i = 1; $i <= 12; $i++) {
            $order_statistics_chart[$i] = 0;
            foreach($order_statistics_chart_data as $match) {
                if($match['month'] == $i) {
                    $order_statistics_chart[$i] = $match['total'];
                }
            }
        }

        $donut = [];
        $donut_data = $this->order->where('branch_id', auth('branch')->id())->get();
        $donut['pending'] = $donut_data->where('order_status', 'pending')->count();
        $donut['ongoing'] = $donut_data->whereIn('order_status', ['confirmed', 'processing', 'out_for_delivery'])->count();
        $donut['delivered'] = $donut_data->where('order_status', 'delivered')->count();
        $donut['canceled'] = $donut_data->where('order_status', 'canceled')->count();
        $donut['returned'] = $donut_data->where('order_status', 'returned')->count();
        $donut['failed'] = $donut_data->where('order_status', 'failed')->count();

        $data['recent_orders'] = $this->order->latest()
        ->where('branch_id', auth('branch')->id())
        ->take(5)->get();

        return view('branch-views.dashboard', compact('data','order_statistics_chart', 'earning', 'donut'));
    }

    private function order_stats_data(): array
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        $pending = $this->order
        ->where(['order_status' => 'pending', 'branch_id' => auth('branch')->id()])
        ->notSchedule()
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $confirmed = $this->order
        ->where(['order_status' => 'confirmed', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $processing = $this->order
        ->where(['order_status' => 'processing', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $out_for_delivery = $this->order
        ->where(['order_status' => 'out_for_delivery', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $canceled = $this->order
        ->where(['order_status' => 'canceled', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $delivered = $this->order
        ->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $all = $this->order
        ->where(['branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $returned = $this->order
        ->where(['order_status' => 'returned', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();
        
        $failed = $this->order
        ->where(['order_status' => 'failed', 'branch_id' => auth('branch')->id()])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        return [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'processing' => $processing,
            'out_for_delivery' => $out_for_delivery,
            'canceled' => $canceled,
            'delivered' => $delivered,
            'all' => $all,
            'returned' => $returned,
            'failed' => $failed  
        ];
    }

    public function order_statistics(Request $request): JsonResponse
    {
        $date_type = $request->type;

        $order_data = [];

        if($date_type == 'yearOrder') {
            $number = 12;
            $from = Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
            ->select(DB::raw('(count(id)) as total'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupBy('year', 'month')->get()->toArray();

            for($i = 1; $i <= $number; $i++) {
                $order_data[$i] = 0;
                foreach($orders as $match) {
                    if($match['month'] == $i) {
                        $order_data[$i] = $match['total'];
                    }
                }
            }

            $key_range = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        } elseif($date_type == 'MonthOrder') {
            $from = date('Y-m-01');
            $to = date('Y-M-t');
            $number = date('d', strtotime($to));
            $key_range = range(1, $number);

            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
            ->select(DB::raw('(count(id)) as total'), DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day'))
            ->whereBetween('created_at', [Carbon::parse(now())->startOfMonth(), Carbon::parse(now())->endOfMonth()])
            ->groupBy('created_at')->get()->toArray();     
            
            for($i = 1; $i <= $number; $i++) {
                $order_data[$i] = 0;
                foreach($orders as $match) {
                    if($match['day'] == $i) {
                        $order_data[$i] = $match['total'];
                    }
                }
            }
        } elseif($date_type == 'WeekOrder') {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);

            $from = Carbon::now()->startOfWeek();
            $to = Carbon::now()->endOfWeek();
            
            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
            ->whereBetween('created_at', [$from, $to])->get();

            $date_range = CarbonPeriod::create($from, $to)->toArray();
            $key_range = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $order_data = [];
            foreach($date_range as $date) {
                $order_data[] = $orders->whereBetween('created_at', [$date, Carbon::parse($date)->endOfDay()])->count();
            }
        }

        $label = $key_range;
        $final_order_data = $order_data;

        return response()->json([
            'orders_label' => $label,
            'orders' => array_values($final_order_data)
        ]);
    }

    public function earning_statistics(Request $request): JsonResponse
    {
        $date_type = $request->type;

        $earning_data = [];
        if($date_type == 'yearEarn') {
            $earning = $this->order->where([
                'order_status' => 'delivered', 'branch_id' => auth('branch')->id()
            ])->select(DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupBy('year', 'month')->get()->toArray();

            for($i = 1; $i <= 12; $i++) {
                $earning_data[$i] = 0;
                foreach($earning as $match) {
                    if($match['month'] == $i) {
                        $earning_data[$i] = number_format($match['sums'], 0, '.', '');
                    }
                }
            }

            $key_range = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        } elseif($date_type == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-M-t');
            $number = date('d', strtotime($to));
            $key_range = range(1, $number);

            $earning = $this->order->where([
                'order_status' => 'delivered', 'branch_id' => auth('branch')->id()
            ])->select(DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day'))
            ->whereBetween('created_at', [Carbon::parse(now())->startOfMonth(), Carbon::parse(now())->endOfMonth()])
            ->groupBy('created_at')->get()->toArray();     
            
            for($i = 1; $i <= $number; $i++) {
                $earning_data[$i] = 0;
                foreach($earning as $match) {
                    if($match['day'] == $i) {
                        $earning_data[$i] = $match['sums'];
                    }
                }
            }

        } elseif($date_type == 'WeekEarn') {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);

            $from = Carbon::now()->startOfWeek();
            $to = Carbon::now()->endOfWeek();
            
            $orders = $this->order->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])
            ->whereBetween('created_at', [$from, $to])->get();

            $date_range = CarbonPeriod::create($from, $to)->toArray();
            $key_range = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $earning_data = [];
            foreach($date_range as $date) {
                $earning_data[] = $orders->whereBetween('created_at', [$date, Carbon::parse($date)->endOfDay()])->sum('order_amount');
            }
        }

        $label = $key_range;
        $final_earning_data = $earning_data;

        return response()->json([
            'earning_label' => $label,
            'earning' => array_values($final_earning_data)
        ]);
    }

    public function order_stats(Request $request): JsonResponse
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::order_stats_data();

        return response()->json([
            'view' => view('branch-views.partials._dashboard-order-stats', compact('data'))->render()
        ]);
    }
}
