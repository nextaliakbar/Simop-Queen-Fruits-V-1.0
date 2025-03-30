<div class="mt-5 mb-5">
    <div class="inline-page-menu my-4">
        <ul class="list-unstyled">
            <li class="{{Request::is('admin/business-settings/store/store-setup')? 'active': ''}}"><a href="{{route('admin.business-settings.store.store-setup')}}">Pengaturan Bisnis</a></li>
            <li class="{{Request::is('admin/business-settings/store/main-branch-setup')? 'active' : ''}}"><a href="{{route('admin.business-settings.store.main-branch-setup')}}">Cabang Utama</a></li>
            <li class="{{Request::is('admin/business-settings/store/time-schedule')? 'active' : ''}}"><a href="{{route('admin.business-settings.store.time-schedule-index')}}">Jadwal Waktu Toko</a></li>
            <li class="{{Request::is('admin/business-settings/store/delivery-fee-setup')? 'active' : ''}}"><a href="{{route('admin.business-settings.store.delivery-fee-setup')}}">Biaya Pengiriman</a></li>
            <li class="{{Request::is('admin/business-settings/store/order-index')? 'active' : ''}}"><a href="{{route('admin.business-settings.store.order-index')}}">Pesanan</a></li>
            <li class="{{Request::is('admin/business-settings/web-app/third-party/offline-payment/list')? 'active' : ''}}">
                <a href="{{route('admin.business-settings.web-app.third-party.offline-payment.list')}}">Metode Pembayaran Offline</a>
            </li>
        </ul>
    </div>
</div>
