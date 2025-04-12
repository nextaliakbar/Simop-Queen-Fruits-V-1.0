@extends('layouts.branch.app')

@section('title', 'Daftar Pesanan')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/all_orders.png')}}" alt="">
                <span class="page-header-title">
                    @if ($status == 'all')
                        Semua Pesanan    
                    @elseif ($status == 'pending')
                        Pesanan Tertunda
                    @elseif ($status == 'confirmed')
                        Pesanan Dikonfirmasi
                    @elseif ($status == 'processing')
                        Pesanan Diproses
                    @elseif ($status == 'out_for_delivery')
                        Pesanan Dalam Pengiriman
                    @elseif ($status == 'delivered')
                        Pesanan Terkirim
                    @elseif ($status == 'returned')
                        Pesanan Dikembalikan
                    @elseif ($status == 'failed')
                        Pesanan Gagal Terkirim
                    @elseif ($status == 'canceled')
                        Pesanan Dibatalkan
                    @elseif ($status == 'schedule')
                        Penjadwalan Pesanan
                    @endif
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{ $orders->total() }}</span>
        </div>

        <div class="card">
            <div class="card">
                <div class="card-body">
                    <form action="#" id="form-data" method="GET">
                        <input type="hidden" name="search" value="{{$search}}">
                        <div class="row gy-3 gx-2 align-items-end">
                            <div class="col-12 pb-0">
                                <h4 class="mb-0">Pilih Rentang Tanggal</h4>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">Dari</label>
                                    <input type="date" name="from" value="{{ $from }}" id="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">Sampai</label>
                                    <input type="date" value="{{ $to }}" name="to" id="to_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 d-flex gap-2">
                                <a href="{{route('branch.orders.list',['all'])}}" class="btn btn-secondary flex-grow-1">Atur Ulang</a>
                                <button type="submit" class="btn btn-primary text-nowrap flex-grow-1">Tampilkan Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($status == 'all')
                <div class="px-4 mt-4">
                    <div class="row g-2">
                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'pending'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/pending.png')}}" alt="" class="oder--card-icon">
                                        <span>Tertunda</span>
                                    </h6>
                                    <span class="card-title text-0661CB">
                                    {{$order_count['pending']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'confirmed'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/confirmed.png')}}" alt="" class="oder--card-icon">
                                        <span>Dikonfirmasi</span>
                                    </h6>
                                    <span class="card-title text-107980">
                                {{$order_count['confirmed']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'processing'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/packaging.png')}}" alt="" class="oder--card-icon">
                                        <span>Diproses</span>
                                    </h6>
                                    <span class="card-title text-danger">
                                {{$order_count['processing']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'out_for_delivery'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/out_for_delivery.png')}}" alt="" class="oder--card-icon">
                                        <span>Dalam Pengiriman</span>
                                    </h6>
                                    <span class="card-title text-00B2BE">
                                {{$order_count['out_for_delivery']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'delivered'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/delivered.png')}}" alt="" class="oder--card-icon">
                                        <span>Terkirim</span>
                                    </h6>
                                    <span class="card-title text-success">
                                {{$order_count['delivered']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'canceled'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/canceled.png')}}" alt="" class="oder--card-icon">
                                        <span>Dibatalkan</span>
                                    </h6>
                                    <span class="card-title text-danger">
                                {{$order_count['canceled']}}
                            </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'returned'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/returned.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>Dikembalikan</span>
                                    </h6>
                                    <span class="card-title text-warning">
                                {{$order_count['returned']}}
                            </span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <a class="order--card h-100" href="{{route('branch.orders.list', ['status' => 'failed'])}}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                        <img src="{{asset('assets/admin/img/icons/failed_to_deliver.png')}}" alt="dashboard" class="oder--card-icon">
                                        <span>Gagal Terkirim</span>
                                    </h6>
                                    <span class="card-title text-danger">
                                {{$order_count['failed']}}
                            </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="Cari berdasarkan id pesanan, status pesanan atau referensi transaksi" aria-label="Search"
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
                                <th>No. </th>
                                <th>ID Pesanan</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Info Pelanggan</th>
                                <th>Cabang</th>
                                <th>Total Jumlah</th>
                                <th>Status Pesanan</th>
                                <th>Jenis Pesanan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td>{{$orders->firstitem()+$key}}</td>
                                <td>
                                    <a class="text-dark" href="{{route('branch.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['delivery_date']))}}</div>
                                    <div>{{date('h:i A',strtotime($order['delivery_time']))}}</div>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <h6 class="text-capitalize mb-1">
                                            <a class="text-dark" href="{{route('branch.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                        </h6>
                                        <a class="text-dark fz-12" href="tel:{{$order->customer->phone}}">{{$order->customer->phone}}</a>
                                    @else
                                        <span class="text-capitalize text-muted">
                                        Pelanggan Tidak Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-soft-info px-2 py-1 rounded">{{$order->branch?$order->branch->name:'Branch deleted!'}}</span>
                                </td>
                                <td>
                                    <div>Rp {{ number_format($order['order_amount'] + $order['delivery_charge']) }}</div>
                                    @if($order->payment_status=='paid')
                                        <span class="text-success">Terbayar</span>
                                    @else
                                        <span class="text-danger">Belum Terbayar</span>
                                    @endif
                                </td>
                                <td class="text-capitalize">
                                    @if($order['order_status']=='pending')
                                        <span class="badge-soft-info px-2 py-1 rounded">Tertunda</span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge-soft-info px-2 py-1 rounded">Dikonfirmasi</span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge-soft-warning px-2 py-1 rounded">Diproses</span>
                                    @elseif($order['order_status']=='out_for_delivery')
                                        <span class="badge-soft-warning px-2 py-1 rounded">Dalam Pengiriman</span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge-soft-success px-2 py-1 rounded">Terkirim</span>
                                    @elseif($order['order_status']=='failed')
                                        <span class="badge-soft-danger px-2 py-1 rounded">Gagal Terkirim</span>
                                    @else
                                        <span class="badge-soft-danger px-2 py-1 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-soft-success px-2 py-1 rounded">{{$order['order_type'] == 'take_away' ? 'Ambil Ditempat' : 'Pengiriman'}}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-sm btn-outline-primary square-btn" href="{{route('branch.orders.details',['id'=>$order['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a href="{{route('branch.orders.generate-invoice',[$order['id']])}}" class="btn btn-sm btn-outline-success square-btn" target="_blank">
                                            <i class="tio-print"></i>
                                        </a>
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
@endsection
