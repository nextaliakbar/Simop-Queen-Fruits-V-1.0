<div class="pos-product-item card quick-view-trigger" data-product-id="{{$product->id}}">
    <div class="pos-product-item_thumb">
        <img src="{{$product['imageFullPath']}}" class="img-fit" alt="product">
    </div>

    <div class="pos-product-item_content clickable">
        <div class="pos-product-item_title">
            {{ Str::limit($product['name'], 15) }}
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
        <div class="pos-product-item_price">
            Rp {{number_format($price-App\CentralLogics\Helpers::discount_calculate($discountData, $price)) }}
        </div>
    </div>
</div>
