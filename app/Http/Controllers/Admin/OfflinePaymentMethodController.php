<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\OfflinePaymentMethod;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfflinePaymentMethodController extends Controller
{
    public function __construct(
        private OfflinePaymentMethod $offline_payment_method,
        private Order $order
    ) {}

    public function list(Request $request)
    {
        $search = $request['search'];

        $methods = $this->offline_payment_method
        ->when($request->has('search'), function ($query) use ($search) {
            $key = explode(' ', $search['search']);

            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('method_name', 'like', "%{$value}%");
                }
            });
        })->latest()->paginate(Helpers::get_pagination());

        return view('admin-views.business-settings.offline-payment.list', compact('methods', 'search'));
    }

    public function add()
    {
        return view('admin-views.business-settings.offline-payment.add');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'method_name' => 'required',
            'field_name' => 'required|array',
            'field_data' => 'required|array',
            'information_name' => 'required|array',
            'information_placeholder' => 'required|array'
        ], [
            'method_name.required' => 'Nama metode tidak boleh kosong',
            'field_name.required' => 'Nama bidang tidak boleh kosong',
            'field_data.required' => 'Data bidang tidak boleh kosong',
            'information_name.required' => 'Isi setidaknya 1 kebutuhan informasi dari pelanggan',
            'information_placeholder.required' => 'Isi setidaknya 1 kebutuhan informasi dari pelanggan'
        ]);

        $method_fields = [];
        foreach ($request->field_name as $key => $field_name) {
            $method_fields[] = [
                'field_name' => $request->field_name[$key],
                'field_data' => $request->field_data[$key]
            ];
        }

        $method_informations = [];
        foreach ($request->information_name as $key => $information_name) {
            $method_informations[] = [
                'information_name' => $request->information_name[$key],
                'information_placeholder' => $request->information_placeholder[$key],
                'information_required' => isset($request['information_required']) && isset($request['information_required'][$key])? 1 : 0
            ];
        }

        $method = $this->offline_payment_method;
        $method->method_name = $request->method_name;
        $method->method_fields = $method_fields;
        $method->payment_note = $request->payment_note;
        $method->method_informations = $method_informations;

        $method->save();

        Toastr::success('Metode pembayaran berhasil ditambahkan');
        return redirect()->route('admin.business-settings.web-app.third-party.offline-payment.list');
    }

    public function status(Request $request): RedirectResponse
    {
        $method = $this->offline_payment_method->find($request->id);
        $method->status = $request->status;
        $method->save();

        Toastr::success('Status Berhasil Diperbarui');
        return back();
    }

    public function edit($id)
    {
        $method = $this->offline_payment_method->find($id);
        return view('admin-views.business-settings.offline-payment.edit', compact('method'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'method_name' => 'required',
            'field_name' => 'required|array',
            'field_data' => 'required|array',
            'information_name' => 'required|array',
            'information_placeholder' => 'required|array'
        ], [
            'method_name.required' => 'Nama metode tidak boleh kosong',
            'field_name.required' => 'Nama bidang tidak boleh kosong',
            'field_data.required' => 'Data bidang tidak boleh kosong',
            'information_name.required' => 'Isi setidaknya 1 kebutuhan informasi dari pelanggan',
            'information_placeholder.required' => 'Isi setidaknya 1 kebutuhan informasi dari pelanggan'
        ]);

        $method_fields = [];
        foreach($request->field_name as $key => $field_name) {
            $method_fields[] = [
                'field_name' => $request->field_name[$key],
                'field_data' => $request->field_data[$key]
            ];
        }

        $method_informations = [];
        foreach($request->information_name as $key => $information_name) {
            $method_informations[] = [
                'information_name' => $request->information_name[$key],
                'information_placeholder' => $request->information_placeholder[$key],
                'information_required' => isset($request['information_required']) && isset($request['information_required'][$key]) ? 1 : 0
            ];
        }

        $method = $this->offline_payment_method->find($id);
        $method->method_name = $request->method_name;
        $method->method_fields = $method_fields;
        $method->payment_note = $request->payment_note;
        $method->method_informations = $method_informations;
        $method->save();

        Toastr::success('Metode pembayaran berhasil diperbarui');
        return redirect()->route('admin.business-settings.web-app.third-party.offline-payment.list');
    }

    public function delete(Request $request): RedirectResponse
    {
        $method = $this->offline_payment_method->find($request->id);
        $method->delete();
        return back();
    }

    public function offline_payment_list(Request $request, $status)
    {
        $search = $request['search'];
        $status_mapping = [
            'pending' => 0,
            'denied' => 2
        ];

        $status = $status_mapping[$status];

        $orders = $this->order->with(['offline_payment'])
        ->where(['payment_method' => 'offline_payment'])
        ->whereHas('offline_payment', function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->when($request->has('search'), function ($query) use ($request) {
            $keys = explode(' ', $request['search']);
            return $query->where(function ($query) use ($keys) {
                foreach($keys as $key) {
                    $query->where('id', 'like', "%{$key}%")
                    ->orWhere('order_status', 'like', "%{$key}%")
                    ->orWhere('payment_status', 'like', "%{$key}%");
                }
            });
        })
        ->latest()->paginate(Helpers::get_pagination());

        return view('admin-views.order.offline-payment.list', compact('orders', 'search'));
    }

    public function quick_view_details(Request $request): JsonResponse
    {
        $order = $this->order->find($request->id);

        return response()->json([
            'view' => view('admin-views.order.offline-payment.details-quick-view', compact('order'))->render()
        ]);
    }
}
