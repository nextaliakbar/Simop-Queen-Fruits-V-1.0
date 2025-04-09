@extends('layouts.admin.app')

@section('title','')

@push('css_or_js')
    <style>
        @media print {
            .non-printable {
                display: none;
            }

            .printable {
                display: block;
            }
        }

        .hr-style-2 {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0));
        }

        .hr-style-1 {
            overflow: visible;
            padding: 0;
            border: none;
            border-top: medium double #000000;
            text-align: center;
        }
        #printableAreaContent * {
            font-weight: normal !important;
        }
    </style>

    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 2px;
        }

    </style>
@endpush

@section('content')

    <div class="content container-fluid" style="color: black">
        <div class="row justify-content-center" id="printableArea">
            <div class="col-md-12">
                <div class="text-center">
                    <input type="button" class="btn btn-primary non-printable" onclick="printDiv('printableArea')"
                           value="Cetak"/>
                    <a href="{{url()->previous()}}" class="btn btn-danger non-printable">Kembali</a>
                </div>
                <hr class="non-printable">
            </div>
            <div class="col-5" id="printableAreaContent">
                <div class="text-center pt-4 mb-3">
                    <h2 style="line-height: 1">{{\App\Models\BusinessSetting::where(['key'=>'store_name'])->first()->value}}</h2>
                    <h5 style="font-size: 20px;font-weight: lighter;line-height: 1">
                        {{\App\Models\BusinessSetting::where(['key'=>'address'])->first()->value}}
                    </h5>
                    <h5 style="font-size: 16px;font-weight: lighter;line-height: 1">
                        No. Hp : {{\App\Models\BusinessSetting::where(['key'=>'phone'])->first()->value}}
                    </h5>
                </div>
                <hr class="text-dark hr-style-1">

                <div class="row mt-4">
                    <div class="col-6">
                        <h5>ID Pesanan: {{$order['id']}}</h5>
                    </div>
                    <div class="col-6">
                        <h5 style="font-weight: lighter">
                            <span class="font-weight-normal">{{date('d/M/Y h:m a',strtotime($order['created_at']))}}</span>
                        </h5>
                    </div>
                    <div class="col-12">
                            @if(isset($order->customer))
                                <h5>
                                    Nama Pelanggan : <span class="font-weight-normal">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</span>
                                </h5>
                                <h5>
                                    No. Hp : <span class="font-weight-normal">{{$order->customer['phone']}}</span>
                                </h5>
                                @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
                                <h5>
                                    Alamat : <span class="font-weight-normal">{{isset($address)?$address['address']:''}}</span>
                                </h5>
                            @endif
                    </div>
                </div>
                <h5 class="text-uppercase"></h5>
                <hr class="text-dark hr-style-2">
                <table class="table table-bordered mt-3">
                    <thead>
                    <tr>
                        <th style="width: 10%">Qty</th>
                        <th class="">Desk</th>
                        <th style="text-align:right; padding-right:4px">Harga</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php($subTotal=0)
                    @php($totalTax=0)
                    @php($totalDisOnPro=0)
                    @foreach($order->details as $detail)
                        @if($detail->product)
                            <tr>
                                <td class="">
                                    {{$detail['quantity']}}
                                </td>
                                <td class="">
                                    <span style="word-break: break-all;"> {{ Str::limit($detail->product['name'], 200) }}</span><br>
                                    @if (count(json_decode($detail['variations'], true)) > 0)
                                        <strong><u>Variasi : </u></strong>
                                        @foreach(json_decode($detail['variations'],true) as  $variation)
                                            @if ( isset($variation['name'])  && isset($variation['values']))
                                                <span class="d-block text-capitalize">
                                                    <strong>{{  $variation['name']}} - </strong>
                                                </span>
                                                @foreach ($variation['values'] as $value)
                                                    <span class="d-block text-capitalize">
                                                        {{ $value['label']}} :
                                                        <strong>Rp {{number_format( $value['optionPrice'])}}</strong>
                                                    </span>
                                                @endforeach
                                            @else
                                                @if (isset(json_decode($detail['variations'],true)[0]))
                                                    @foreach(json_decode($detail['variations'],true)[0] as $key1 =>$variation)
                                                        <div class="font-size-sm text-body">
                                                            <span>{{$key1}} :  </span>
                                                            <span class="font-weight-bold">{{$variation}}</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @break
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="font-size-sm text-body">
                                            <span>Harga : </span>
                                            <span
                                                class="font-weight-bold">Rp {{ number_format($detail->price) }}</span>
                                        </div>
                                    @endif

                                    Diskon : Rp {{ number_format($detail['discount_on_product']) }}
                                </td>
                                <td style="width: 28%;padding-right:4px; text-align:right">
                                    @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                                    Rp {{ number_format($amount) }}
                                </td>
                            </tr>
                            @php($subTotal+=$amount)
                            @php($totalTax+=($detail['tax_amount']*$detail['quantity']) )
                        @endif
                    @endforeach
                    </tbody>
                </table>


                <div class="row justify-content-md-end mb-3 m-0" style="width: 99%">
                    <div class="col-md-10 p-0">
                        <dl class="row text-right" style="color: black!important;">
                            <dt class="col-6">Harga:</dt>
                            <dd class="col-6">{{ number_format($subTotal) }}</dd>
                            <dt class="col-6">Pajak / PPN:</dt>
                            <dd class="col-6">{{ number_format($totalTax) }}</dd>
                            <dt class="col-6">Subtotal:</dt>
                            <dd class="col-6">
                                Rp {{ number_format($subTotal+$totalTax) }}</dd>
                            <dt class="col-6">Ekstra Diskon:</dt>
                            <dd class="col-6">
                                - Rp {{ number_format($order['extra_discount']) }}</dd>
                            <dt class="col-6">Biaya Pengiriman</dt>
                            <dd class="col-6">
                                @if($order['order_type']=='take_away')
                                    @php($del_c=0)
                                @else
                                    @php($del_c=$order['delivery_charge'])
                                @endif
                                Rp {{ number_format($del_c) }}
                                <hr>
                            </dd>

                            <dt class="col-6" style="font-size: 20px">Total : </dt>
                            <dd class="col-6" style="font-size: 20px">Rp {{ number_format($subTotal+$del_c+$totalTax-$order['coupon_discount_amount']-$order['extra_discount']) }}</dd>
                        </dl>
                    </div>
                </div>
                <hr class="text-dark hr-style-2">
                <h5 class="text-center pt-3">
                    """Terima Kasih"""
                </h5>
                <hr class="text-dark hr-style-2">
                <div class="text-center">Dibuat dengan penuh pengorbanan waktu dan tenaga</div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        "use strict";

        function printDiv(divName) {

            if($('html').attr('dir') === 'rtl') {
                $('html').attr('dir', 'ltr')
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                $('#printableAreaContent').attr('dir', 'rtl')
                window.print();
                document.body.innerHTML = originalContents;
                $('html').attr('dir', 'rtl')
            }else{
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }

        }
    </script>
@endpush
