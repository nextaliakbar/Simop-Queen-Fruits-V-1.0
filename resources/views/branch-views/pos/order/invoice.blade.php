<div class="print-area-content" id="printableAreaContent">
    <div class="text-center pt-4 mb-3 w-100">
        <h2 class="custom-title">{{App\Models\Branch::find(auth('branch')->id() ?? 1)->name}}</h2>
        <h5 class="custom-h5">
            {{App\Models\Branch::find(auth('branch')->id() ?? 1)->address}}
        </h5>
        <h5 class="custom-phone">
            No. Hp
            : {{App\Models\Branch::find(auth('branch')->id() ?? 1)->phone}}
        </h5>
    </div>

    <span>--------------------------------------------</span>
    <div class="row mt-3">
        <div class="col-6">
            <h5>ID Pesanan : {{$order['id']}}</h5>
        </div>
        <div class="col-6">
            <h5 style="font-weight: lighter">
                {{date('d/M/Y h:i a',strtotime($order['created_at']))}}
            </h5>
        </div>
        @if($order->customer)
            <div class="col-12">
                <h5>Nama Pelanggan : {{$order->customer['f_name'].' '.$order->customer['l_name']}}</h5>
                <h5>No. Hp : {{$order->customer['phone']}}</h5>
            </div>
        @endif
    </div>
    <h5 class="text-uppercase"></h5>
    <span>--------------------------------------------</span>
    <table class="table table-bordered mt-3 custom-table">
        <thead>
        <tr>
            <th class="custom-qty">Qty</th>
            <th class="">Deskripsi</th>
            <th class="custom-price">Harga</th>
        </tr>
        </thead>

        <tbody>
        @php($itemPrice=0)
        @php($totalTax=0)
        @php($totalDisOnPro=0)
        @foreach($order->details as $detail)
            @if($detail->product)
                <tr>
                    <td>
                        {{$detail['quantity']}}
                    </td>
                    <td>
                        <span class="custom-span"> {{ Str::limit($detail->product['name'], 200) }}</span><br>
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
                                    class="font-weight-bold">Rp {{number_format($detail->price) }}</span>
                            </div>
                        @endif
                        Diskon : Rp {{ number_format($detail['discount_on_product']*$detail['quantity']) }}
                    </td>
                    <td class="custom-td">
                        @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                        Rp {{ number_format($amount) }}
                    </td>
                </tr>
                @php($itemPrice+=$amount)
                @php($totalTax+=$detail['tax_amount']*$detail['quantity'])
            @endif
        @endforeach
        </tbody>
    </table>
    <span>--------------------------------------------</span>
    <div class="row justify-content-md-end">
        <div class="col-md-9 col-lg-9">
            <dl class="row text-right custom-dl">
                <dt class="col-8">Harga Produk:</dt>
                <dd class="col-4">Rp {{number_format($itemPrice)}}</dd>
                <dt class="col-8">Pajak / PPN:</dt>
                <dd class="col-4">{{number_format($totalTax)}}</dd>
                    <hr>
                </dd>

                <dt class="col-8">Subtotal:</dt>
                @php($subtotal = $itemPrice + $totalTax)
                <dd class="col-4">{{ number_format($subtotal) }}</dd>
                <dt class="col-8">Extra Diskon:</dt>
                <dd class="col-4">
                    -Rp {{ number_format($order['extra_discount']) }}</dd>

                <dt class="col-8">Biaya Pengiriman</dt>
                <dd class="col-4">
                    @if($order['order_type']=='take_away')
                        @php($deliveryCharge=0)
                    @else
                        @php($deliveryCharge=$order['delivery_charge'])
                    @endif
                    Rp {{ number_format($deliveryCharge) }}
                    <hr>
                </dd>

                <dt class="col-6 custom-text-size">Total:</dt>
                <dd class="col-6 custom-text-size">{{ number_format($subtotal-$order['coupon_discount_amount']-$order['extra_discount']+$deliveryCharge) }}</dd>
            </dl>
        </div>
    </div>
    <div class="d-flex flex-row justify-content-between border-top">
        <span>Sumber Pembayaran: {{ $order->payment_method}}</span>
    </div>
    <span>--------------------------------------------</span>
    <h5 class="text-center pt-3">
        """Terima Kasihh"""
    </h5>
    <span>--------------------------------------------</span>
</div>
