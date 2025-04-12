<div class="table-responsive pos-cart-table border">
    <table class="table table-align-middle mb-0">
        <thead class="text-dark bg-light">
            <tr>
                <th class="text-capitalize border-0 min-w-120">Produk</th>
                <th class="text-capitalize border-0">Kuantitas</th>
                <th class="text-capitalize border-0">Harga</th>
                <th class="text-capitalize border-0">Hapus</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $subtotal = 0;
            $discount = 0;
            $discountType = 'amount';
            $discountOnProduct = 0;
            $totalTax = 0;
        ?>
        @if(session()->has('cart') && count( session()->get('cart')) > 0)
            <?php
                $cart = session()->get('cart');
                if(isset($cart['discount']))
                {
                    $discount = $cart['discount'];
                    $discountType = $cart['discount_type'] ?? null;
                }
            ?>
            @foreach(session()->get('cart') as $key => $cartItem)
            @if(is_array($cartItem))
                <?php
                $productSubtotal = ($cartItem['price'])*$cartItem['quantity'];
                $discountOnProduct += ($cartItem['discount']*$cartItem['quantity']);
                $subtotal += $productSubtotal;
                $product = \App\Models\Product::find($cartItem['id']);
                $totalTax +=App\CentralLogics\Helpers::tax_calculate($product, $cartItem['price'])*$cartItem['quantity'];

                ?>
                <tr>
                    <td>
                        <div class="media align-items-center gap-10">
                            <img class="avatar avatar-sm" src="{{asset('storage/product')}}/{{$cartItem['image']}}" 
                            onerror="this.src={{asset('assets/admin/img/160x160/img2.jpg')}}" alt="{{$cartItem['name']}} image">
                            <div class="media-body">
                                <h5 class="text-hover-primary mb-0">{{Str::limit($cartItem['name'], 10)}}</h5>
                                <small>{{Str::limit($cartItem['variant'], 20)}}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="number" class="form-control qty" data-key="{{$key}}" value="{{$cartItem['quantity']}}" min="1" onkeyup="updateQuantity(event)">
                    </td>
                    <td>
                        <div class="">
                            Rp {{number_format($productSubtotal) }}
                        </div>
                    </td>
                    <td class="justify-content-center gap-2">
                        <a href="javascript:removeFromCart({{$key}})" class="btn btn-sm btn-outline-danger square-btn form-control">
                            <i class="tio-delete"></i>
                        </a>
                    </td>
                </tr>
            @endif
            @endforeach
        @endif
        </tbody>
    </table>
</div>

<?php
    $total = $subtotal;
    $discountAmount = !is_null($discountType) ? (($discountType=='percent' && $discount>0)?(($total * $discount)/100):$discount) : $discount;
    $discountAmount += $discountOnProduct;
    $total -= $discountAmount;

    $extraDiscount = session()->get('cart')['extra_discount'] ?? 0;
    $extraDiscountType = session()->get('cart')['extra_discount_type'] ?? 'amount';
    if($extraDiscountType == 'percent' && $extraDiscount > 0){
        $extraDiscount = ($subtotal * $extraDiscount) / 100;
    }
    if($extraDiscount) {
        $total -= $extraDiscount;
    }

    $deliveryCharge = 0;
    if (session()->get('order_type') == 'home_delivery'){
        $distance = 0;
        if (session()->has('address')){
            $address = session()->get('address');
            $distance = $address['distance'];
        }
        $deliveryCharge = App\CentralLogics\Helpers::get_delivery_charge( auth('branch')->id(),  $distance);
    }else{
        $deliveryCharge = 0;
    }
?>
<div class="pos-data-table p-3">
    <dl class="row">
        <dt  class="col-6">Subtotal : </dt>
        <dd class="col-6 text-right">Rp {{number_format($subtotal) }}</dd>

        <dt  class="col-6">Diskon Produk :</dt>
        <dd class="col-6 text-right">- Rp {{number_format(round($discountAmount,2)) }}</dd>

        <dt  class="col-6">Extra Diskon :</dt>
        <dd class="col-6 text-right">
            <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#add-discount">
                <i class="tio-edit"></i>
            </button>
            - Rp {{number_format($extraDiscount) }}
        </dd>

        <dt  class="col-6">Pajak/PPN : </dt>
        <dd class="col-6 text-right">Rp {{number_format(round($totalTax,2)) }}</dd>

        <dt  class="col-6">Biaya Pengiriman :</dt>
        <dd class="col-6 text-right"> Rp {{number_format(round($deliveryCharge,2)) }}</dd>

        <dt  class="col-6 border-top font-weight-bold pt-2">Total : </dt>
        <dd class="col-6 text-right border-top font-weight-bold pt-2">Rp {{number_format(round($total+$totalTax+$deliveryCharge, 2)) }}</dd>
    </dl>

    <form action="{{route('branch.pos.order')}}" id='order_place' method="post">
        @csrf

        <div class="pt-4 mb-4">
            <div class="text-dark d-flex mb-2">Sumber Pembayaran :</div>
            <ul class="list-unstyled option-buttons">
                <li id="cash_payment_li" style="display: {{ session('order_type') != 'home_delivery' ?  'block' : 'none' }}">
                    <input type="radio" id="cash" value="cash" name="type" hidden="" {{ session('order_type') != 'home_delivery' ?  'checked' : '' }}>
                    <label for="cash" class="btn btn-bordered px-4 mb-0">Tunai</label>
                </li>
                <li id="card_payment_li">
                    <input type="radio" value="card" id="card" name="type" hidden="" {{ session('order_type') == 'home_delivery' ?  'checked' : '' }}>
                    <label for="card" class="btn btn-bordered px-4 mb-0">Non Tunai</label>
                </li>
            </ul>
        </div>

        <div class="row mt-4 gy-2">
            <div class="col-md-6">
                <a href="#" class="btn btn-outline-danger btn--danger btn-block empty-cart-button">
                    <i class="fa fa-times-circle"></i> Batalkan Pesanan
                </a>
            </div>
            <div class="col-md-6">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-shopping-bag"></i>
                        Simpan Pesanan
                    </button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="add-discount" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perbarui Diskon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('branch.pos.discount')}}" method="post" class="row mb-0">
                    @csrf
                    <div class="form-group col-sm-6">
                        <label class="text-dark">Diskon</label>
                        <input type="number" class="form-control" name="discount" value="{{ session()->get('cart')['extra_discount'] ?? 0 }}" min="0" step="0.1">
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="text-dark">Jenis Diskon</label>
                        <select name="type" class="form-control">
                            <option
                                value="amount" {{$extraDiscountType=='amount'?'selected':''}}>Diskon Langsung (Rp)
                            </option>
                            <option
                                value="percent" {{$extraDiscountType=='percent'?'selected':''}}>Persentase (%)
                            </option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end col-sm-12">
                        <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    "use strict";

    $('.empty-cart-button').click(function(event) {
        event.preventDefault();
        emptyCart();
    });
</script>
