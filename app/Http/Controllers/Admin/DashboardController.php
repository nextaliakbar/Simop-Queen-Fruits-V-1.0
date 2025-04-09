<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private Order $order,
        private OrderDetail $order_detail,
        private Admin $admin,
        private Review $review,
        private User $user,
        private Product $product,
        private Category $category,
        private Branch $branch
    ) {}

    public function dashboard(): Renderable
    {
        $top_sell = $this->order_detail->with(['product'])
        ->whereHas('order', function ($query) {
            $query->where('order_status', 'delivered');
        })
        ->select('product_id', DB::raw('SUM(quantity) as count'))
        ->groupBy('product_id')
        ->orderBy('count', 'desc')
        ->take(6)
        ->get();

        $most_rated_products = $this->review->with(['product'])
        ->select(['product_id', DB::raw('AVG(rating) as rating_average'), DB::raw('COUNT(rating) as total')])
        ->groupBy('product_id')
        ->orderBy('total', 'desc')
        ->take(7)
        ->get();

        $top_customer = $this->order->with(['customer'])
        ->select('user_id', DB::raw('COUNT(user_id) as count'))
        ->groupBy('user_id')
        ->orderBy('count', 'desc')
        ->take(6)
        ->get();

        $data = self::order_stats_data();

        $data['customer'] = $this->user->count();
        $data['product'] = $this->product->count();
        $data['order'] = $this->order->count();
        $data['category'] = $this->category->where('parent_id', 0)->count();
        $data['branch'] = $this->branch->count();

        $data['top_sell'] = $top_sell;
        $data['most_rated_products'] = $most_rated_products;
        $data['top_customer'] = $top_customer;

        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->endOfYear()->format('Y-m-d');

        $earning = [];
        $earning_data = $this->order->where([
            'order_status' => 'delivered'
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
        $order_statistics_chart_data = $this->order->where(['order_status' => 'delivered'])
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
        $donut_data = $this->order->all();
        $donut['pending'] = $donut_data->where('order_status', 'pending')->count();
        $donut['ongoing'] = $donut_data->whereIn('order_status', ['confirmed', 'processing', 'out_for_delivery'])->count();
        $donut['delivered'] = $donut_data->where('order_status', 'delivered')->count();
        $donut['canceled'] = $donut_data->where('order_status', 'canceled')->count();
        $donut['returned'] = $donut_data->where('order_status', 'returned')->count();
        $donut['failed'] = $donut_data->where('order_status', 'failed')->count();

        $data['recent_orders'] = $this->order->latest()->take(5)->get();
        
        return view('admin-views.dashboard', compact('data', 'earning', 'order_statistics_chart', 'donut'));
    }

    public function order_stats_data(): array
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        $pending = $this->order
        ->where(['order_status' => 'pending'])
        ->notSchedule()
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $confirmed = $this->order
        ->where(['order_status' => 'confirmed'])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $processing = $this->order
        ->where(['order_status' => 'processing'])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $out_for_delivery = $this->order
        ->where(['order_status' => 'out_for_delivery'])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $canceled = $this->order
        ->where(['order_status' => 'canceled'])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $delivered = $this->order
        ->where(['order_status' => 'delivered'])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $all = $this->order
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();

        $returned = $this->order
        ->where(['order_status' => 'returned'])
        ->when($today, function($query) {
            return $query->whereDate('created_at', Carbon::today());
        })
        ->when($this_month, function($query) {
            return $query->whereMonth('created_at', Carbon::now());
        })
        ->count();
        
        $failed = $this->order
        ->where(['order_status' => 'failed'])
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

            $orders = $this->order->where(['order_status' => 'delivered'])
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

            $orders = $this->order->where(['order_status' => 'delivered'])
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
            
            $orders = $this->order->where(['order_status' => 'delivered'])
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
                'order_status' => 'delivered'
            ])->select(DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
            ->whereBetween('created_at', [Carbon::parse(now())->startOfYear(), Carbon::parse(now())->endOfYear()])
            ->groupBy('year', 'month')->get()->toArray();

            for($i = 1; $i <= 12; $i++) {
                $earning_data[$i] = 0;
                foreach($earning as $match) {
                    if($match['month'] == $i) {
                        $earning_data[$i] = number_format($match['sums']);
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
                'order_status' => 'delivered'
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
            
            $orders = $this->order->where(['order_status' => 'delivered'])
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
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }
}
