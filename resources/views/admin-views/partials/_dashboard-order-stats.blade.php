
<div class="col-sm-6 col-lg-3">
    <a href="{{route('admin.orders.list',['pending'])}}" class="dashboard--card">
        <h5 class="dashboard--card__subtitle">Tertunda</h5>
        <h2 class="dashboard--card__title">{{$data['pending']}}</h2>
        <img width="30" src="{{asset('assets/admin/img/icons/pending.png')}}" class="dashboard--card__img" alt="">
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a href="{{route('admin.orders.list',['confirmed'])}}" class="dashboard--card">
        <h5 class="dashboard--card__subtitle">Dikonfirmasi</h5>
        <h2 class="dashboard--card__title">{{$data['confirmed']}}</h2>
        <img width="30" src="{{asset('assets/admin/img/icons/confirmed.png')}}" class="dashboard--card__img" alt="">
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a href="{{route('admin.orders.list',['processing'])}}" class="dashboard--card">
        <h5 class="dashboard--card__subtitle">Diproses</h5>
        <h2 class="dashboard--card__title">{{$data['processing']}}</h2>
        <img width="30" src="{{asset('assets/admin/img/icons/packaging.png')}}" class="dashboard--card__img" alt="">
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a href="{{route('admin.orders.list',['out_for_delivery'])}}" class="dashboard--card">
        <h5 class="dashboard--card__subtitle">Dalam Pengiriman</h5>
        <h2 class="dashboard--card__title">{{$data['out_for_delivery']}}</h2>
        <img width="30" src="{{asset('assets/admin/img/icons/out_for_delivery.png')}}" class="dashboard--card__img" alt="">
    </a>
</div>

<div class="col-sm-6 col-lg-3">
    <a class="order-stats order-stats_pending" href="{{route('admin.orders.list',['delivered'])}}">
        <div class="order-stats__content">
            <img width="20" src="{{asset('assets/admin/img/icons/delivered.png')}}" class="order-stats__img" alt="">
            <h6 class="order-stats__subtitle">Terkirim</h6>
        </div>
        <span class="order-stats__title">
            {{$data['delivered']}}
        </span>
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a class="order-stats order-stats_canceled" href="{{route('admin.orders.list',['canceled'])}}">
        <div class="order-stats__content">
            <img width="20" src="{{asset('assets/admin/img/icons/canceled.png')}}" class="order-stats__img" alt="">
            <h6 class="order-stats__subtitle">Dibatalkan</h6>
        </div>
        <span class="order-stats__title">
            {{$data['canceled']}}
        </span>
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a class="order-stats order-stats_returned" href="{{route('admin.orders.list',['returned'])}}">
        <div class="order-stats__content">
            <img width="20" src="{{asset('assets/admin/img/icons/returned.png')}}" class="order-stats__img" alt="">
            <h6 class="order-stats__subtitle">Dikembalikan</h6>
        </div>
        <span class="order-stats__title">
            {{$data['returned']}}
        </span>
    </a>
</div>
<div class="col-sm-6 col-lg-3">
    <a class="order-stats order-stats_failed" href="{{route('admin.orders.list',['failed'])}}">
        <div class="order-stats__content">
            <img width="20" src="{{asset('assets/admin/img/icons/failed_to_deliver.png')}}" class="order-stats__img" alt="">
            <h6 class="order-stats__subtitle">Gagal Terkirim</h6>
        </div>
        <span class="order-stats__title">
            {{$data['failed']}}
        </span>
    </a>
</div>
