<div class="coupon__details-left">
    <div class="text-center">
        @if($coupon->discount_type != "amount")
            <h6 class="title" id="title">{{$coupon->discount}}% Dikson</h6>
        @else
            <h6 class="title" id="title">Rp {{number_format($coupon->discount)}} Diskon</h6>
        @endif
        <h6 class="subtitle">Kode : <span id="coupon_code">{{$coupon->code}}</span></h6>
        <div class="text-capitalize">
            <span>Diskon dalam </span>
            <strong id="discount_on">{{$coupon->discount_type}}</strong>
        </div>
    </div>
    <div class="coupon-info">
        <div class="coupon-info-item">
            <span>Minimal Pemesanan :</span>
            <strong id="min_purchase">Rp {{number_format($coupon->min_purchase)}}</strong>
        </div>
        @if($coupon->discount_type != "amount")
        <div class="coupon-info-item" id="max_discount_modal_div">
            <span>Maksimal Diskon : </span>
            <strong id="max_discount">Rp {{number_format($coupon->max_discount)}}</strong>
        </div>
        @endif
        <div class="coupon-info-item">
            <span>Tanggal Dimulai : </span>
            <span id="start_date">{{date_format($coupon->start_date, 'Y-m-d')}}</span>
        </div>
        <div class="coupon-info-item">
            <span>Tanggal Berkahir : </span>
            <span id="expire_date">{{date_format($coupon->expire_date, 'Y-m-d')}}</span>
        </div>
    </div>
</div>
<div class="coupon__details-right">
    <div class="coupon">
        <div class="d-flex">
            @if($coupon->discount_type != "amount")
                <h2 class="" id="">{{$coupon->discount}}%</h2>
            @else
                <h2 class="" id="">Rp {{number_format($coupon->discount)}}</h2>
            @endif
        </div>
    </div>
</div>
