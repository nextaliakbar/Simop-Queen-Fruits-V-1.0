<div class="modal-header p-2">
    <h4 class="modal-title product-title"></h4>
    <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="d-flex flex-wrap gap-3">
        <div class="d-flex align-items-center justify-content-center active">
            <img class="img-responsive rounded" width="160"
                 src="{{$product['imageFullPath']}}"
                 data-zoom="{{$product['imageFullPath']}}"
                 alt="product">
            <div class="cz-image-zoom-pane"></div>
        </div>

        <?php
            $pb = json_decode($product->product_by_branch, true);
            $discountData = [];
            if(isset($pb[0])){
                $price = $pb[0]['price'];
                $discountData =[
                    'discount_type' => $pb[0]['discount_type'],
                    'discount' => $pb[0]['discount']
                ];
            }else{
                $price = $product['price'];
                $discountType = $product['discount_type'];
                $discount = $product['discount'];
                $discountData =[
                    'discount_type' => $product['discount_type'],
                    'discount' => $product['discount']
                ];
            }
        ?>
        <div class="details">
            <div class="break-all">
                <a href="#" class="d-block h3 mb-2 product-title">{{ Str::limit($product->name, 100) }}</a>
            </div>

            <div class="mb-2 text-dark d-flex align-items-baseline gap-2">
                <h3 class="font-weight-normal text-accent mb-0">
                    Rp {{number_format(($price -App\CentralLogics\Helpers::discount_calculate($discountData, $price))) }}
                </h3>
                @if($discountData['discount'] > 0)
                    <strike class="fz-12">
                        Rp {{number_format($price) }}
                    </strike>
                @endif
            </div>

            @if($discountData['discount'] > 0)
                <div class="mb-3 text-dark">
                    <strong>{{translate('Discount : ')}}</strong>
                    <strong
                        id="set-discount-amount">Rp {{number_format(\App\CentralLogics\Helpers::discount_calculate($discountData, $price)) }}</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="row pt-2">
        <div class="col-12">
            <?php
            $cart = false;
            if (session()->has('cart')) {
                foreach (session()->get('cart') as $key => $cartItem) {
                    if (is_array($cartItem) && $cartItem['id'] == $product['id']) {
                        $cart = $cartItem;
                    }
                }
            }

            ?>
            <h3 class="mt-3">Deskripsi</h3>
            <div class="d-block text-break text-dark __descripiton-txt __not-first-hidden">
                <div>
                    <p>
                        {!! $product->description !!}
                    </p>
                </div>
                <div class="show-more text-info text-center">
                    <span class="">Lihat Lebih Banyak</span>
                </div>
            </div>
            <form id="add-to-cart-form" class="mb-2">
                @csrf
                <input type="hidden" name="id" value="{{ $product->id }}">
                @if (isset($product->branch_products) && count($product->branch_products))
                    @foreach($product->branch_products as $branch_product)
                        @foreach ($branch_product->variations as $key => $choice)
                            @if (isset($choice->price) == false)
                                <div class="h3 p-0 pt-2">
                                    {{ $choice['name'] }}
                                    <small class="text-muted custom-text-size12">
                                        ({{ ($choice['required'] == 'on')  ?  'Wajib' : 'Opsional' }})
                                    </small>
                                </div>
                                @if ($choice['min'] != 0 && $choice['max'] != 0)
                                    <small class="d-block mb-3">
                                        Silahkan pilih minimal {{ $choice['min'] }} hingga {{ $choice['max'] }} maksimal opsi
                                    </small>
                                @endif

                                <div>
                                    <input type="hidden"  name="variations[{{ $key }}][min]" value="{{ $choice['min'] }}" >
                                    <input type="hidden"  name="variations[{{ $key }}][max]" value="{{ $choice['max'] }}" >
                                    <input type="hidden"  name="variations[{{ $key }}][required]" value="{{ $choice['required'] }}" >
                                    <input type="hidden" name="variations[{{ $key }}][name]" value="{{ $choice['name'] }}">
                                    @foreach ($choice['values'] as $k => $option)
                                        <div class="form-check form--check d-flex pr-5 mr-6">
                                            <input class="form-check-input" type="{{ ($choice['type'] == "multi") ? "checkbox" : "radio"}}" id="choice-option-{{ $key }}-{{ $k }}"
                                                   name="variations[{{ $key }}][values][label][]" value="{{ $option['label'] }}" autocomplete="off">

                                            <label class="form-check-label"
                                                   for="choice-option-{{ $key }}-{{ $k }}">{{ Str::limit($option['label'], 20, '...') }}</label>
                                            <span class="ml-auto">{{number_format($option['optionPrice']) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                            @endif
                        @endforeach
                    @endforeach

                @endif

                <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
                    <h3 class="product-description-label mt-2 mb-0">Kuantitas:</h3>

                    <div class="product-quantity d-flex align-items-center">
                        <div class="product-quantity-group d-flex align-items-center">
                            <button class="btn btn-number text-dark p-2" type="button"
                                    data-type="minus" data-field="quantity"
                                    disabled="disabled">
                                    <i class="tio-remove font-weight-bold"></i>
                            </button>
                            <input type="text" name="quantity"
                                   class="form-control input-number text-center cart-qty-field"
                                   placeholder="1" value="1" min="1" max="130">
                            <button class="btn btn-number text-dark p-2" type="button" data-type="plus"
                                    data-field="quantity">
                                    <i class="tio-add font-weight-bold"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters mt-4 text-dark" id="chosen_price_div">
                    <div class="col-2">
                        <div class="product-description-label">Total Harga:</div>
                    </div>
                    <div class="col-10">
                        <div class="product-price">
                            <strong id="chosen_price"></strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                    <button class="btn btn-primary px-md-5 add-to-cart-button" type="button">
                        <i class="tio-shopping-cart"></i>
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    "use strict";

    cartQuantityInitialize();
    getVariantPrice();

    $('#add-to-cart-form input').on('change', function () {
        getVariantPrice();
    });

    $('.show-more span').on('click', function(){
        $('.__descripiton-txt').toggleClass('__not-first-hidden')
        if($(this).hasClass('active')) {
            $('.show-more span').text('Lihat Lebih Banyak')
            $(this).removeClass('active')
        }else {
            $('.show-more span').text('Lihat Lebih Sedikit')
            $(this).addClass('active')
        }
    })

    $('.addon-chek').change(function() {
        addon_quantity_input_toggle($(this));
    });

    $('.decrease-quantity').click(function() {
        var input = $(this).closest('.addon-quantity-input').find('.addon-quantity');
        input.val(parseInt(input.val()) - 1);
        getVariantPrice();
    });

    $('.increase-quantity').click(function() {
        var input = $(this).closest('.addon-quantity-input').find('.addon-quantity');
        input.val(parseInt(input.val()) + 1);
        getVariantPrice();
    });

    $('.add-to-cart-button').click(function() {
        addToCart();
    });
</script>

