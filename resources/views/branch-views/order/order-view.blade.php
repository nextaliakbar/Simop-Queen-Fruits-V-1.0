@extends('layouts.branch.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/order_details.png')}}" alt="">
                <span class="page-header-title">
                    Detail Pesanan
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{$order->details->count()}}</span>
        </div>
        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card mb-3 mb-lg-5">
                    <div class="px-card py-3">
                        <div class="row gy-2">
                            <div class="col-sm-6 d-flex flex-column justify-content-between">
                                <div>
                                    <h2 class="page-header-title h1 mb-3">Pesanan #{{$order['id']}}</h2>
                                    <h5 class="text-capitalize">
                                        <i class="tio-shop"></i>
                                        Cabang :
                                        <label class="badge-soft-info px-2 rounded">
                                            {{$order->branch?$order->branch->name:'Cabang dihapus!'}}
                                        </label>
                                    </h5>

                                    <div class="">
                                        Tanggal dan Waktu Pesanan: <i class="tio-date-range"></i>{{date('d M Y',strtotime($order['created_at']))}} {{ date(config('time_format'), strtotime($order['created_at'])) }}
                                    </div>
                                </div>

                                <div>
                                    <h5>Catatan Pesanan : {{$order['order_note']}}</h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-right">
                                    <div class="d-flex flex-wrap gap-2 justify-content-sm-end">
                                        @if($order['order_type']!='take_away' && $order['order_type'] != 'pos')

                                            @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                                            @if($googleMapStatus)
                                                <div class="hs-unfold ml-1">
                                                    @if($order['order_status']=='out_for_delivery')
                                                        @php($origin=\App\Models\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->first())
                                                        @php($current=\App\Models\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->latest()->first())
                                                        @if(isset($origin))
                                                            <a class="btn btn-outline-primary" target="_blank"
                                                               title="Lokasi terkahir kurir" data-toggle="tooltip" data-placement="top"
                                                               href="https://www.google.com/maps/dir/?api=1&origin={{$origin['latitude']}},{{$origin['longitude']}}&destination={{$current['latitude']}},{{$current['longitude']}}">
                                                                <i class="tio-map"></i> Lihat lokasi di map
                                                            </a>
                                                        @else
                                                            <a class="btn btn-outline-primary" href="javascript:" data-toggle="tooltip"
                                                               data-placement="top" title="Tunggu untuk lokasi... ">
                                                                <i class="tio-map"></i> Lihat lokasi di map
                                                            </a>
                                                        @endif
                                                    @else
                                                        <a class="btn btn-outline-dark last-location-view" href="javascript:"
                                                           data-toggle="tooltip" data-placement="top"
                                                           title="Hanya tersedia saat pesanan sedang dikirim">
                                                            <i class="tio-map"></i> Lihat lokasi di map
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif

                                        @endif
                                        <a class="btn btn-info" href={{route('branch.orders.generate-invoice',[$order['id']])}}>
                                            <i class="tio-print"></i> Cetak Bukti Pembayaran
                                        </a>
                                    </div>

                                    <div class="d-flex gap-3 justify-content-sm-end my-3">
                                        <div class="text-dark font-weight-semibold">Status :</div>
                                        @if($order['order_status']=='pending')
                                            <span class="badge-soft-info px-2 rounded text-capitalize">Tertunda</span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge-soft-info px-2 rounded text-capitalize">Dikonfirmasi</span>
                                        @elseif($order['order_status']=='processing')
                                            <span class="badge-soft-warning px-2 rounded text-capitalize">Diproses</span>
                                        @elseif($order['order_status']=='out_for_delivery')
                                            <span class="badge-soft-warning px-2 rounded text-capitalize">Dalam Pengiriman</span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge-soft-success px-2 rounded text-capitalize">Terkirim</span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge-soft-danger px-2 rounded text-capitalize">Gagal Dikirim</span>
                                        @else
                                            <span class="badge-soft-danger px-2 rounded text-capitalize">{{str_replace('_',' ',$order['order_status'])}}</span>
                                        @endif
                                    </div>


                                    <div class="text-capitalize d-flex gap-3 justify-content-sm-end mb-3">
                                        <span>Sumber Pembayaran :</span>
                                        <span class="text-dark">{{str_replace('_',' ',$order['payment_method'] == 'cash' ? 'Tunai' : 'Non Tunai')}}</span>
                                    </div>

                                    @if(!in_array($order['payment_method'], ['offline_payment', 'card']))
                                        @if($order['transaction_reference']==null && $order['order_type']!='pos')
                                            <div class="d-flex gap-3 justify-content-sm-end align-items-center mb-3">
                                                Kode Referensi :
                                                <button class="btn btn-outline-primary px-3 py-1" data-toggle="modal"
                                                        data-target=".bd-example-modal-sm">
                                                    Tambah
                                                </button>
                                            </div>
                                        @elseif($order['order_type']!='pos')
                                            <div class="d-flex gap-3 justify-content-sm-end align-items-center mb-3">
                                                Kode Referensi
                                                : {{$order['transaction_reference']}}
                                            </div>
                                        @endif
                                    @endif


                                    <div class="d-flex gap-3 justify-content-sm-end mb-3">
                                        <div>Status Pembayaran :</div>
                                        @if($order['payment_status']=='paid')
                                            <span class="badge-soft-success px-2 rounded text-capitalize">Terbayar</span>
                                        @else
                                            <span class="badge-soft-danger px-2 rounded text-capitalize">Belum Terbayar</span>
                                        @endif
                                    </div>

                                    <div class="d-flex gap-3 justify-content-sm-end mb-3 text-capitalize">
                                        Jenis Pesanan
                                        : <label class="badge-soft-info px-2 rounded">
                                            {{str_replace('_',' ',$order['order_type'] == 'pos' ? 'Kasir' : 'Pengiriman')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="py-4 table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>No.</th>
                                <th>Detail Pesanan</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Pajak</th>
                                <th class="text-right">Total Harga</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr>
                            </tr>
                            @php($subTotal=0)
                            @php($totalTax=0)
                            @php($totalDisOnPro=0)
                            @foreach($order->details as $detail)
                                @php($productDetails = json_decode($detail['product_details'], true))
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="media gap-3 w-max-content">

                                            <img class="img-fluid avatar avatar-lg"
                                                 src="{{ $detail->product?->imageFullPath ?? asset('assets/admin/img/160x160/img2.jpg') }}"
                                                 alt="Image Description">

                                            <div class="media-body text-dark fz-12">
                                                <h6 class="text-capitalize">{{$productDetails['name']}}</h6>
                                                <div class="d-flex gap-2">
                                                    @if (isset($detail['variations']))
                                                        @foreach(json_decode($detail['variations'],true) as  $variation)
                                                            @if (isset($variation['name'])  && isset($variation['values']))
                                                                <span class="d-block text-capitalize">
                                                                <strong>{{  $variation['name']}} -</strong>
                                                            </span>
                                                                @foreach ($variation['values'] as $value)

                                                                    <span class="d-block text-capitalize">
                                                                     {{ $value['label']}} :
                                                                    <strong>Rp {{number_format( $value['optionPrice'])}}</strong>
                                                                </span>
                                                                @endforeach
                                                            @else
                                                                @if (isset(json_decode($detail['variations'],true)[0]))
                                                                    <strong><u> Variasi : </u></strong>
                                                                    @foreach(json_decode($detail['variations'],true)[0] as $key1 =>$variation)
                                                                        <div class="font-size-sm text-body">
                                                                            <span>{{$key1}} :  </span>
                                                                            <span class="font-weight-bold">{{$variation}}</span>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="font-size-sm text-body">
                                                            <span class="text-dark">Harga  : Rp {{number_format($detail['price'])}}</span>
                                                        </div>
                                                    @endif

                                                    <div class="d-flex gap-2">
                                                        <span class="">Qty :  </span>
                                                        <span>{{$detail['quantity']}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php($amount=$detail['price']*$detail['quantity'])
                                        Rp {{number_format($amount)}}
                                    </td>
                                    <td>
                                        @php($totDiscount = $detail['discount_on_product']*$detail['quantity'])
                                        Rp {{number_format($totDiscount)}}
                                    </td>
                                    <td>
                                        @php($productTax = $detail['tax_amount']*$detail['quantity'])
                                        Rp {{number_format($productTax)}}
                                    </td>
                                    <td class="text-right">{{number_format($amount-$totDiscount + $productTax)}}</td>
                                </tr>
                                @php($totalDisOnPro += $totDiscount)
                                @php($subTotal += $amount)
                                @php($totalTax += $productTax)

                            @endforeach
                            </tbody>
                        </table>
                    </div>


                    <div class="card-body pt-0">
                        <hr>
                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row">
                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            Harga Produk<span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">Rp {{ number_format($subTotal) }}</dd>

                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>Pajak / PPN</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">Rp {{ number_format($totalTax) }}</dd>
                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>Diskon Produk</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">Rp {{ number_format($totalDisOnPro) }}</dd>

                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>
                                        Subtotal</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        Rp {{ number_format($subTotal =$subTotal+$totalTax-$totalDisOnPro) }}</dd>
                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>Ekstra Diskon </span>
                                        <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        - Rp {{ number_format($order['extra_discount']) }}</dd>
                                    <dt class="col-6">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>
                                                Biaya Pengiriman</span>
                                            <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 text-dark text-right">
                                        @if($order['order_type']=='take_away')
                                            @php($del_c=0)
                                        @else
                                            @php($del_c=$order['delivery_charge'])
                                        @endif
                                        Rp {{ number_format($del_c) }}
                                    </dd>

                                    <dt class="col-6 border-top pt-2 fz-16 font-weight-bold">
                                        <div class="d-flex max-w220 ml-auto">
                                            <span>Total</span>
                                        <span>:</span>
                                        </div>
                                    </dt>
                                    <dd class="col-6 border-top pt-2 fz-16 font-weight-bold text-dark text-right">Rp {{ number_format($subTotal - $order['extra_discount'] + $del_c) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="col-lg-4">
                    @if($order['order_type'] != 'pos')
                    <div class="card mb-3">
                        <div class="card-body text-capitalize d-flex flex-column gap-4">
                            <h4 class="mb-0 text-center">Pengaturan Pesanan</h4>

                            @if(isset($order->offline_payment))
                                <div class="card mt-3">
                                    <div class="card-body text-center">
                                        @if($order->offline_payment?->status == 1)
                                            <h4 class="">Pembayaran Terverifikasi</h4>
                                        @else
                                            <h4 class="">Verifikasi Pembayaran</h4>
                                            <p class="text-danger">Mohon untuk melakukan verifikasi pembayaran sebelum mengkonfirmasi pesanan</p>
                                            <div class="mt-3">
                                                <button class="btn btn-primary" type="button"
                                                        data-id="{{ $order['id'] }}"
                                                        data-target="#payment_verify_modal" data-toggle="modal">Verifikasi Pembayaran
                                                </button>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endif

                            @if($order['order_type'] != 'pos')
                                <div class="hs-unfold w-100">
                                    <label class="font-weight-bold text-dark fz-14">Ubah Status Pesanan</label>
                                    <div class="dropdown">
                                        <button class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                @if ($order['order_status'] == 'pending')
                                                    Tertunda    
                                                @elseif ($order['order_status'] == 'confirmed')
                                                    Dikonfirmasi
                                                @elseif ($order['order_status'] == 'processing')
                                                    Diproses
                                                @elseif ($order['order_status'] == 'out_for_delivery')
                                                    Dalam Pengiriman
                                                @elseif ($order['order_status'] == 'delivered')
                                                    Terkirim
                                                @elseif ($order['order_status'] == 'returned')
                                                    Dikembalikan
                                                @elseif ($order['order_status'] == 'failed')
                                                    Gagal
                                                @elseif ($order['order_status'] == 'canceled')
                                                    Dibatalkan
                                                @endif
                                        </button>
                                        <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                            @if($order['payment_method'] == 'offline_payment' && $order->offline_payment?->status != 1)
                                                <a class="dropdown-item offline-payment-order-alert"
                                                    href="javascript:">Tertunda</a>

                                                <a class="dropdown-item offline-payment-order-alert"
                                                   href="javascript:">Dikonfirmasi</a>

                                                <a class="dropdown-item offline-payment-order-alert"
                                                    href="javascript:">Diproses</a>

                                                <a class="dropdown-item offline-payment-order-alert"
                                                    href="javascript:">Dalam Pengiriman</a>

                                                <a class="dropdown-item offline-payment-order-alert"
                                                    href="javascript:">Terkirim</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'returned'])}}" data-message="Ubah status menjadi dikembalikan?"
                                                    href="javascript:">Dikembalikan</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'failed'])}}" data-message="Ubah status menjadi gagal?"
                                                    href="javascript:">Gagal</a>

                                                <a class="dropdown-item route-alert"
                                                   data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'canceled'])}}" data-message="Ubah status menjadi dibatalkan?"
                                                   href="javascript:">Dibatalkan</a>
                                            @else

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'pending'])}}" data-message="Ubah status menjadi tertunda?"
                                                    href="javascript:">Tertunda</a>

                                                <a class="dropdown-item route-alert"
                                                   data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'confirmed'])}}" data-message="Ubah status menjadi dikonfirmasi?"
                                                   href="javascript:">Dikonfirmasi</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'processing'])}}" data-message="Ubah status menjadi diproses?"
                                                    href="javascript:">Diproses</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'out_for_delivery'])}}" data-message="Ubah status menjadi dalam pengiriman?"
                                                    href="javascript:">Dalam Pengiriman</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'delivered'])}}" data-message="Ubah status menjadi terkirim?"
                                                    href="javascript:">Terkirim</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'returned'])}}" data-message="Ubah status menjadi dikembalikan?"
                                                    href="javascript:">Dikembalikan</a>

                                                <a class="dropdown-item route-alert"
                                                    data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'failed'])}}" data-message="Ubah status menjadi gagal?"
                                                    href="javascript:">Gagal</a>

                                                <a class="dropdown-item route-alert"
                                                   data-route="{{route('branch.orders.status',['id'=>$order['id'],'order_status'=>'canceled'])}}" data-message="Ubah status menjadi dibatalkan?"
                                                   href="javascript:">Dibatalkan</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center gap-10 form-control">
                                        <span class="title-color">Status Pembayaran</span>
                                        @if($order['payment_method'] == 'offline_payment' && $order->offline_payment?->status != 1)
                                            <label class="switcher payment-status-text">
                                                <input class="switcher_input offline-payment-status-alert" type="checkbox" name="payment_status" value="1" id="payment_status_switch"
                                                    {{$order->payment_status == 'paid' ?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        @else
                                            <label class="switcher payment-status-text">
                                                <input class="switcher_input change-payment-status" type="checkbox" name="payment_status" value="1"
                                                       data-id="{{ $order['id'] }}"
                                                       data-status="{{ $order->payment_status == 'paid' ?'unpaid':'paid' }}"
                                                    {{$order->payment_status == 'paid' ?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($order->customer)
                                <div>
                                    <label class="font-weight-bold text-dark fz-14">Tanggal & Waktu Pesanan {{$order['delivery_date'] > \Carbon\Carbon::now()->format('Y-m-d')? 'Dijadwalkan' : ''}}</label>
                                    <div class="d-flex gap-2 flex-wrap flex-xxl-nowrap">
                                        <input name="delivery_date" type="date" class="form-control delivery-date" value="{{$order['delivery_date'] ?? ''}}">
                                        <input name="delivery_time" type="time" class="form-control delivery-time" value="{{$order['delivery_time'] ?? ''}}">
                                    </div>

                                </div>
                                @if($order['order_type']!='take_away' && $order['order_type'] != 'pos' && !$order['delivery_man_id'])

                                    <a href="#" class="btn btn-primary btn-block d-flex gap-1 justify-content-center align-items-center" data-toggle="modal" data-target="#assignDeliveryMan">
                                        <img width="17" src="{{asset('assets/admin/img/icons/assain_delivery_man.png')}}" alt="">
                                        Tetapkan Kurir
                                    </a>
                                @endif
                            @endif
                            <div>
                                @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && ($order['order_status'] != DELIVERED && $order['order_status'] != RETURNED && $order['order_status'] != CANCELED && $order['order_status'] != FAILED && $order['order_status'] != COMPLETED))
                                    <label class="font-weight-bold text-dark fz-14">Waktu Persiapan Pesanan</label>
                                    <div class="form-control justify-content-between">
                                        <span class="ml-2 ml-sm-3 ">
                                        <i class="tio-timer d-none" id="timer-icon"></i>
                                        <span id="counter" class="text-info"></span>
                                        <i class="tio-edit p-2 d-none li-pointer" id="edit-icon" data-toggle="modal" data-target="#counter-change" data-whatever="@mdo"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>


                            @if($order->delivery_man_id)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4 class="mb-4 d-flex gap-2">
                                    <span class="card-header-icon">
                                        <i class="tio-user text-dark"></i>
                                    </span>
                                            <span>Kurir</span>
                                            <a  href="#"  data-toggle="modal" data-target="#assignDeliveryMan"
                                                class="text--base cursor-pointer ml-auto">
                                                Ubah
                                            </a>
                                        </h4>
                                        <div class="media flex-wrap gap-3">
                                            <a>
                                                <img class="avatar avatar-lg rounded-circle" src="{{$order->delivery_man?->imageFullPath }}" alt="Image">
                                            </a>
                                            <div class="media-body d-flex flex-column gap-1">
                                                <a target="" href="#" class="text-dark"><span>{{$order->delivery_man['f_name'].' '.$order->delivery_man['l_name'] ?? ''}}</span></a>
                                                <span class="text-dark"> <span>{{$order->delivery_man['orders_count']}}</span> Pesanan</span>
                                                <span class="text-dark break-all">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a href="tel:{{$order->delivery_man['phone']}}" class="text-dark">{{$order->delivery_man['phone'] ?? ''}}</a>
                                        </span>
                                                <span class="text-dark break-all">
                                            <i class="tio-email mr-2"></i>
                                            <a href="mailto:{{$order->delivery_man['email']}}" class="text-dark">{{$order->delivery_man['email'] ?? ''}}</a>
                                        </span>
                                            </div>
                                        </div>
                                        <hr class="w-100">
                                        @if($order['order_status']=='out_for_delivery')
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5>Lokasi Terakhir</h5>
                                            </div>
                                            @php($origin=\App\Models\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->first())
                                            @php($current=\App\Models\DeliveryHistory::where(['deliveryman_id'=>$order['delivery_man_id'],'order_id'=>$order['id']])->latest()->first())
                                            @if(isset($origin))
                                                <a target="_blank" class="text-dark"
                                                   title="Delivery Boy Last Location" data-toggle="tooltip" data-placement="top"
                                                   href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$current['latitude']}}+{{$current['longitude']}}">
                                                    <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{$current['location']?? ''}}
                                                </a>
                                            @else
                                                <a href="javascript:" data-toggle="tooltip" class="text-dark"
                                                   data-placement="top" title="Menunggu lokasi ...">
                                                    <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Menunggu lokasi ...
                                                </a>
                                            @endif
                                        @else
                                            <a href="javascript:" class="text-dark last-location-view"
                                               data-toggle="tooltip" data-placement="top"
                                               title="Hanya tersedia ketika pesanan dalam pengiriman">
                                                <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Hanya tersedia ketika pesanan dalam pengiriman
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($order['order_type']!='take_away' && $order['order_type'] != 'pos')
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-4 d-flex gap-2 justify-content-between">
                                        <h4 class="mb-0 d-flex gap-2">
                                            <i class="tio-user text-dark"></i>
                                            Informasi Pengiriman
                                        </h4>

                                        <div class="edit-btn cursor-pointer" data-toggle="modal" data-target="#deliveryInfoModal">
                                            <i class="tio-edit"></i>
                                        </div>
                                    </div>
                                    <div class="delivery--information-single flex-column">
                                        @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
                                        <div class="d-flex">
                                            <div class="name">Nama</div>
                                            <div class="info">{{ $address? $address['contact_person_name']: '' }}</div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="name">Kontak</div>
                                            <a href="tel:{{ $address? $address['contact_person_number']: '' }}" class="info">{{ $address? $address['contact_person_number']: '' }}</a>
                                        </div>
                                        <div class="d-flex">
                                            <div class="name">Alamat</div>
                                            <div class="info">{{$address['address'] ?? ''}}</div>
                                        </div>
                                        @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                                        @if($googleMapStatus)
                                            @if(isset($address['address']) && isset($address['latitude']) && isset($address['longitude']))
                                                <hr class="w-100">
                                                <div class="d-flex align-items-center gap-3">
                                                    <a target="_blank" class="text-dark"
                                                       href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                                        <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        {{$address['address']}}
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                    @endif

                        @if($order->offline_payment)
                            @php($payment = json_decode($order->offline_payment?->payment_info, true))

                            <div class="card mt-2">
                                <div class="card-body">
                                    <h5 class="form-label mb-3">
                                        <span class="card-header-icon"><i class="tio-shopping-basket"></i></span>
                                        <span>Informasi Pembayaran Offline</span>
                                    </h5>
                                    <div class="offline-payment--information-single flex-column mt-3">
                                        <div class="d-flex">
                                            <span class="name">Catatan Pembayaran</span>
                                            <span class="info">{{ $payment['payment_note'] }}</span>
                                        </div>
                                        @foreach($payment['method_information'] as $infos)
                                            @foreach($infos as $info_key => $info)
                                                <div class="d-flex">
                                                    <span class="name">{{ $info_key }}</span>
                                                    <span class="info">{{ $info }}</span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif


                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <i class="tio-user text-dark"></i>
                                Informasi Pelanggan
                            </h4>
                                @if($order->customer)
                                    <div class="media flex-wrap gap-3">
                                        <a target="#" class="">
                                            <img class="avatar avatar-lg rounded-circle" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'" 
                                            src="{{$order->customer?->imageFullPath}}" alt="Image">
                                        </a>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <a target="#" class="text-dark"><strong>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong></a>
                                            <span class="text-dark">{{$order->customer['orders_count']}} Pesanan</span>
                                            <span class="text-dark">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a class="text-dark break-all" href="tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                        </span>
                                            <span class="text-dark">
                                            <i class="tio-email mr-2"></i>
                                            <a class="text-dark break-all" href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                                        </span>
                                        </div>
                                    </div>
                                @endif
                                @if($order->user_id != null && !isset($order->customer))
                                    <div class="media flex-wrap gap-3 align-items-center">
                                        <a target="#" class="" >
                                            <img class="avatar avatar-lg rounded-circle" src="{{asset('assets/admin/img/160x160/img1.jpg')}}" alt="">
                                        </a>
                                        <div class="media-body d-flex flex-column gap-1">
                                            <a target="#"  class="text-dark text-capitalize"><strong>Pelanggan Tidak Tersedia</strong></a>
                                        </div>
                                    </div>
                                @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <i class="tio-user text-dark"></i>
                                Informasi Cabang
                            </h4>
                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img class="avatar avatar-lg rounded-circle" src="{{ $order->branch?->imageFullPath}}" alt="Image">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    @if(isset($order->branch))
                                        <span class="text-dark"><span>{{$order->branch?->name}}</span></span>
                                        <span class="text-dark"> <span>{{$order->branch['orders_count']}}</span> Pesanan Dilakukan</span>
                                        @if($order->branch['phone'])
                                            <span class="text-dark break-all">
                                                <i class="tio-call-talking-quiet mr-2"></i>
                                                <a class="text-dark" href="tel:{{$order->branch?->phone}}">{{$order->branch?->phone}}</a>
                                            </span>
                                        @endif
                                        <span class="text-dark break-all">
                                        <i class="tio-email mr-2"></i>
                                        <a class="text-dark" href="mailto:{{$order->branch?->email}}">{{$order->branch->email}}</a>
                                    </span>
                                    @else
                                        <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                            Cabang Dihapus
                                        </span>
                                    @endif

                                </div>
                            </div>
                            @if(isset($order->branch))
                                <hr class="w-100">
                                <div class="d-flex align-items-center text-dark gap-3">
                                    <img width="13" src="{{asset('assets/admin/img/icons/location.png')}}" alt="">
                                    <a target="_blank" class="text-dark"
                                       href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$order->branch['latitude']}}+{{$order->branch['longitude']}}">
                                        {{$order->branch['address']}}<br>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
    </div>

    <div class="modal fade" id="assignDeliveryMan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title fs-5" id="assignDeliveryManLabel">Tetapkan Kurir</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @foreach(\App\Models\DeliveryMan::where(['is_active'=> 1])->whereIn('branch_id', [0, auth('branch')->id()])->get() as $deliveryMan)
                            <li class="list-group-item d-flex flex-wrap align-items-center gap-3 justify-content-between">
                                <div class="media align-items-center gap-2 flex-wrap">
                                    <div class="avatar">
                                        <img class="img-fit rounded-circle" loading="lazy" decoding="async"
                                         src="{{$deliveryMan->imageFullPath}}" alt="kurir">
                                    </div>
                                    <span>{{$deliveryMan['f_name'].' '.$deliveryMan['l_name']}}</span>
                                </div>
                                <a id="{{$deliveryMan->id}}" class="btn btn-primary btn-sm assign-deliveryman">Tetapkan</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4"
                        id="mySmallModalLabel">Tambah Kode Referensi</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                            aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{route('branch.orders.add-payment-ref-code',[$order['id']])}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" name="transaction_reference" class="form-control"
                                   placeholder="Contoh : xxxxxx" required>
                        </div>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deliveryInfoModal" id="deliveryInfoModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">Perbarui Informasi Pesanan</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>
                @if($order['delivery_address_id'])
                    <form action="{{route('branch.orders.update-shipping',[$order['delivery_address_id']])}}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{$order->user_id}}">
                        <input type="hidden" name="order_id" value="{{$order->id}}">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Jenis</label>
                                        <input type="text" name="address_type" class="form-control"
                                               placeholder="Contoh : Rumah" value="{{ $address['address_type'] ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="">Nama
                                            <span class="input-label-secondary text-danger">*</span></label>
                                        <input type="text" class="form-control" name="contact_person_name"
                                               placeholder="Contoh : John Doe" value="{{ $address['contact_person_name'] ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="">No. Hp
                                            <span class="input-label-secondary text-danger">*</span></label>
                                        <input type="text" class="form-control" name="contact_person_number"
                                               placeholder="Contoh : 081234xxxxx" value="{{ $address['contact_person_number']?? '' }}" required>
                                    </div>
                                </div>

                                @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                                @if($googleMapStatus)
                                    @if($order?->branch?->delivery_charge_setup?->delivery_charge_type == 'distance')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label" for="">Latitude
                                                    <span class="input-label-secondary text-danger">*</span></label>
                                                <input type="text" class="form-control" name="latitude"
                                                       placeholder="Contoh : -8.200786388659255" value="{{ $address['latitude'] ?? '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="input-label" for="">Longitude<span
                                                        class="input-label-secondary text-danger">*</span></label>
                                                <input type="text" class="form-control" name="longitude"
                                                       placeholder="Contoh : -8.200786388659255" value="{{ $address['longitude'] ?? '' }}" required>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Alamat<span class="input-label-secondary text-danger">*</span></label>
                                        <textarea class="form-control" name="address" cols="30" rows="3" placeholder="Contoh : Jl. PB SudirmanKec. Patrang, Kabupaten Jember" required>{{ $address['address'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>

    @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && ($order['order_status'] != DELIVERED && $order['order_status'] != RETURNED && $order['order_status'] != CANCELED && $order['order_status'] != FAILED && $order['order_status'] != COMPLETED))
        <div class="modal fade" id="counter-change" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title custom-text-size" id="exampleModalLabel">Butuh waktu untuk menyiapkan pesanan?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('branch.orders.increase-preparation-time', ['id' => $order->id])}}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group text-center">
                                <input type="number" min="0" name="extra_minute" id="extra_minute" class="form-control" placeholder="Contoh : 20" required>
                            </div>
                            <div class="form-group flex-between predefined-time-input">
                                <div class="badge text-info shadow li-pointer" data-time="10">10 Menit</div>
                                <div class="badge text-info shadow li-pointer" data-time="20">20 Menit</div>
                                <div class="badge text-info shadow li-pointer" data-time="30">30 Menit</div>
                                <div class="badge text-info shadow li-pointer" data-time="40">40 Menit</div>
                                <div class="badge text-info shadow li-pointer" data-time="50">50 Menit</div>
                                <div class="badge text-info shadow li-pointer" data-time="60">60 Menit</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($order->offline_payment)
        <div class="modal fade" id="payment_verify_modal">
            <div class="modal-dialog modal-lg offline-details">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h4 class="modal-title pb-2">Verifikasi Pembayaran</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="card">
                        <div class="modal-body mx-2">
                            <p class="text-danger">Harap Periksa & Verifikasi informasi pembayaran apakah benar atau tidak sebelum mengkonfirmasi pesanan.</p>
                            <h5>Informasi Pelanggan</h5>

                            <div class="card-body">
                                <p>Nama : {{ $order->customer ? $order->customer->f_name.' '. $order->customer->l_name: ''}} </p>
                                <p>Kontak : {{ $order->customer ? $order->customer->phone: ''}}</p>
                            </div>

                            <h5>Informasi Pembayaran</h5>
                            @php($payment = json_decode($order->offline_payment?->payment_info, true))
                            <div class="row card-body">
                                <div class="col-md-6">
                                    <p>Sumber Pembayaran : {{ $payment['payment_name'] == 'cash' ? 'Tunai' : 'Non Tunai' }}</p>
                                    @foreach($payment['method_fields'] as $fields)
                                        @foreach($fields as $field_key => $field)
                                            <p>{{ $field_key }} : {{ $field }}</p>
                                        @endforeach
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <p>Catatan Pembayaran : {{ $payment['payment_note'] }}</p>
                                    @foreach($payment['method_information'] as $infos)
                                        @foreach($infos as $info_key => $info)
                                            <p>{{ $info_key }} : {{ $info }}</p>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-center my-2 mx-3">
                        @if($order->offline_payment?->status == 0)
                            <a type="reset" class="btn btn-secondary verify-offline-payment" data-status="2">Pembayaran Tidak Diterima</a>
                        @endif
                        <a type="submit" class="btn btn-primary verify-offline-payment" data-status="1">Ya, Pembayaran Diterima</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script_2')
    <script>
        "use strict";

        $('.assign-deliveryman').click(function() {
            var deliveryManId = $(this).attr('id');
            addDeliveryMan(deliveryManId);
        });

        $('.change-payment-status').on('click', function(){
            let id = $(this).data('id');
            let status = $(this).data('status');
            let paymentStatusRoute = "{{ route('branch.orders.payment-status') }}";
            location.href = paymentStatusRoute + '?id=' + encodeURIComponent(id) + '&payment_status=' + encodeURIComponent(status);
        });

        $('.last-location-view').click(function (){
            last_location_view();
        })

        $('.delivery-date, .delivery-time').on('change', function() {
            changeDeliveryTimeDate(this);
        });

        $('.predefined-time-input .badge').click(function() {
            var time = $(this).data('time');
            predefined_time_input(time);
        });

        $('.verify-offline-payment').click(function() {
            var status = $(this).data('status');
            verify_offline_payment(status);
        });

        $('.offline-payment-status-alert').on('click', function () {
            Swal.fire({
                title: 'Pembayaran tidak terverifikasi',
                text: 'Anda tidak dapat mengubah status pembayaran offline yang belum diverifikasi',
                type: 'question',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: 'default',
                confirmButtonColor: '#01684b',
                cancelButtonText: 'Tutup',
                confirmButtonText: '',
                reverseButtons: true
            }).then((result) => {
                $('#payment_status_switch').prop('checked', false);
            })
        })

        $('.offline-payment-order-alert').on('click', function () {
            Swal.fire({
                title: 'Pembayaran tidak terverifikasi',
                text: 'Anda tidak dapat mengubah status pesanan ke status ini. Harap Periksa & Verifikasi informasi pembayaran apakah sudah benar atau belum. Anda hanya dapat mengubah status pesanan menjadi gagal atau batal jika pembayaran belum diverifikasi.',
                type: 'question',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: 'default',
                confirmButtonColor: '#01684b',
                cancelButtonText: 'Tutup',
                confirmButtonText: 'Proses',
                reverseButtons: true
            }).then((result) => {

            })
        })

        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/branch/orders/add-delivery-man/{{$order['id']}}/' + id,
                data: $('#product_form').serialize(),
                success: function (data) {
                    if(data.status == true) {
                        toastr.success('Kurir berhasil ditetapkan atau diubah', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.reload();
                        }, 2000)
                    }else{
                        toastr.error('Kurir tidak dapat ditetapkan atau diubah pada status ini', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText)
                    toastr.error('Tambahkan data yang valid', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Hanya tersedia ketika pesanan dalam pengiriman', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        function predefined_time_input(min) {
            document.getElementById("extra_minute").value = min;
        }

        function changeDeliveryTimeDate(t) {
            let name = t.name
            let value = t.value
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/branch/orders/ajax-change-delivery-time-date/{{$order['id']}}?' + t.name + '=' + t.value,
                data: {
                    name : name,
                    value : value
                },
                success: function (data) {
                    console.log(data)
                    if(data.status == true && name == 'delivery_date') {
                        toastr.success('Tanggal pengiriman berhasil diubah', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else if(data.status == true && name == 'delivery_time'){
                        toastr.success('Waktu pengiriman berhasil diubah', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else {
                        toastr.error('{Pesanan tidak valid', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    location.reload();
                },
                error: function () {
                    toastr.error('Tambahkan data yang valid', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
            });
        }

        function verify_offline_payment(status) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/branch/orders/verify-offline-payment/{{$order['id']}}/' + status,
                success: function (data) {
                    //console.log(data);
                    location.reload();
                    if(data.status == true) {
                        toastr.success('Status verifikasi pembayaran offline berubah', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else{
                        toastr.error('Status verifikasi pembayaran offline tidak berubah', {
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
    @if($order['order_type'] != 'pos' && $order['order_type'] != 'take_away' && ($order['order_status'] != DELIVERED && $order['order_status'] != RETURNED && $order['order_status'] != CANCELED && $order['order_status'] != FAILED && $order['order_status'] != COMPLETED))
        <script>
            "use strict";

            const expire_time = "{{ $order['remaining_time'] }}";
            var countDownDate = new Date(expire_time).getTime();
            const time_zone = "{{ \App\CentralLogics\Helpers::get_business_settings('time_zone') ?? 'Asia/Jakarta' }}";

            var x = setInterval(function() {
                var now = new Date(new Date().toLocaleString("en-US", {timeZone: time_zone})).getTime();

                var distance = countDownDate - now;

                var days = Math.trunc(distance / (1000 * 60 * 60 * 24));
                var hours = Math.trunc((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.trunc((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.trunc((distance % (1000 * 60)) / 1000);


                document.getElementById("timer-icon").classList.remove("d-none");
                document.getElementById("edit-icon").classList.remove("d-none");
                var $text = (distance < 0) ? "Lebih" : "Kiri";
                document.getElementById("counter").innerHTML = Math.abs(days) + "d " + Math.abs(hours) + "h " + Math.abs(minutes) + "m " + Math.abs(seconds) + "s " + $text;
                if (distance < 0) {
                    var element = document.getElementById('counter');
                    element.classList.add('text-danger');
                }
            }, 1000);


            $(document).ready(function() {
                const $areaDropdown = $('#areaDropdown');
                const $deliveryChargeInput = $('#deliveryChargeInput');

                $areaDropdown.change(function() {
                    const selectedOption = $(this).find('option:selected');
                    const charge = selectedOption.data('charge');
                    $deliveryChargeInput.val(charge);
                });
            });


        </script>
    @endif
@endpush
