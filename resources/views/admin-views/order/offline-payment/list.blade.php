@extends('layouts.admin.app')

@section('title', 'Verifikasi Pembayaran Offline')

@section('content')
    <div class="content container-fluid">
        <div>
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                    <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/all_orders.png')}}" alt="">
                    <span class="page-header-title">
                    Verifikasi Pembayaran Offline
                </span>
                </h2>
                <span class="badge badge-soft-dark rounded-50 fz-14">{{ $orders->total() }}</span>
            </div>
            <ul class="nav nav-tabs border-0 my-2">
                <li class="nav-item">
                    <a class="nav-link {{Request::is('admin/verify-offline-payment/pending')?'active':''}}"  href="{{route('admin.verify-offline-payment', ['pending'])}}">Pesanan Tertunda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{Request::is('admin/verify-offline-payment/denied')?'active':''}}"  href="{{route('admin.verify-offline-payment', ['denied'])}}">Pesanan Tertolak</a>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control"
                                       placeholder="Cari berdasarkan id, status pesanan atau referensi transaksi" aria-label="Search"
                                       value="{{$search}}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="py-4">
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th>No.</th>
                            <th>ID Pesanan</th>
                            <th>Tanggal Pengiriman</th>
                            <th>Info Pelanggan</th>
                            <th>Total Jumlah</th>
                            <th>Metode Pembayaran</th>
                            <th>Status Verifikasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td>{{$orders->firstitem()+$key}}</td>
                                <td>
                                    <a class="text-dark" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['delivery_date']))}}</div>
                                    <div>{{date('h:i A',strtotime($order['delivery_time']))}}</div>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <h6 class="text-capitalize mb-1">
                                            <a class="text-dark" href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                        </h6>
                                        <a class="text-dark fz-12" href="tel:{{$order->customer->phone}}">{{$order->customer->phone}}</a>
                                    @else
                                        <span class="text-capitalize text-muted">
                                        Pelanggan Tidak Tersedia
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div>Rp {{number_format($order['order_amount'] + $order['delivery_charge']) }}</div>
                                </td>

                                <td>
                                        <?php
                                        $paymentInfo = json_decode($order->offline_payment?->payment_info, true);
                                        ?>
                                    {{ $paymentInfo['payment_name'] }}
                                </td>
                                <td class="text-capitalize">
                                    @if($order->offline_payment?->status == 0)
                                        <span class="badge badge-soft-info">
                                            Tertunda
                                        </span>
                                    @elseif($order->offline_payment?->status == 2)
                                        <span class="badge badge-soft-danger">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <button class="btn btn-primary" type="button" id="offline_details"
                                                onclick="get_offline_payment(this)" data-id="{{ $order['id'] }}"
                                                data-target="" data-toggle="modal">
                                            Verifikasi Pembayaran
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="table-responsive mt-4 px-3">
                <div class="d-flex justify-content-lg-end">
                    {!!$orders->links()!!}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="quick-view" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered coupon-details modal-lg" role="document">
            <div class="modal-content" id="quick-view-modal">
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        function get_offline_payment(t){
            let id = $(t).data('id')

            $.ajax({
                type: 'GET',
                url: '{{route('admin.offline-modal-view')}}',
                data: {
                    id: id
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    $('#quick-view').modal('show');
                    $('#quick-view-modal').empty().html(data.view);
                }
            });
        }

        function verify_offline_payment(order_id, status) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/verify-offline-payment/'+ order_id+ '/' + status,
                success: function (data) {
                    location.reload();
                    if(data.status == true) {
                        toastr.success('Status verifikasi pembayaran berubah', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else{
                        toastr.error('Status verifikasi pembayaran tidak berubah', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }

                },
                error: function () {
                }
            });
        }


    </script>

@endpush
