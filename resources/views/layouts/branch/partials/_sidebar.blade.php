<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->
                    {{-- @php($restaurant_logo=\App\Models\BusinessSetting::where(['key'=>'logo'])->first()->value) --}}
                    <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo" style="object-fit: contain;"
                             src="{{asset('storage/store/logo_store_2.png')}}"
                             alt="Logo">
                        <img class="navbar-brand-logo-mini" style="object-fit: contain;"
                             onerror="this.src='{{asset('assets/admin/img/160x160/img2.jpg')}}'"
                             src="{{asset('storage/store/logo_store_2.png')}}" alt="Logo">
                    </a>
                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align" data-template="<div class=&quot;tooltip d-none d-sm-block&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><div class=&quot;tooltip-inner&quot;></div></div>" data-toggle="tooltip" data-placement="right" title="" data-original-title="Expand"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->

                    <div class="navbar-nav-wrap-content-left d-none d-xl-block">
                        <!-- Navbar Vertical Toggle -->
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip" data-placement="right" title="" data-original-title="Collapse"></i>
                            <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                        </button>
                        <!-- End Navbar Vertical Toggle -->
                    </div>
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="Cari Menu...">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('branch.dashboard')}}" title="dashboard">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Dashbord
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->

                        <!-- Pos Management -->
                        <li class="nav-item">
                            <small
                                class="nav-subtitle">Sistem Kasir</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/pos/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Kasir</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/pos*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/pos')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.pos.index')}}"
                                       title="kasir">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">Tambah Penjualan</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/pos/orders')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.pos.orders')}}" title="daftar-penjualan">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Daftar Penjualan
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where('branch_id', auth('branch')->id())->Pos()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Order Management -->
                        <li class="nav-item">
                            <small class="nav-subtitle" title="daftar-pesanan">Sistem Pesanan</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/verify-offline-payment*') ?'show active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('branch.verify-offline-payment', ['pending'])}}" title="verifikasi-pembayaran-offline">
                                <i class="tio-shopping-basket nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        Verifikasi Pembayaran
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/orders/list*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="pesanan">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Pesanan
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/orders/list*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Semua
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->where(['branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Tertunda
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'pending','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Dikonfirmasi
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'confirmed','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Diproses
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'processing','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['out_for_delivery'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Dalam Pengiriman
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'out_for_delivery','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Terkirim
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'delivered','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Dikembalikan
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'returned','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Gagal Terkirim
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'failed','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Dibatalkan
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->notSchedule()->where(['order_status'=>'canceled','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/schedule')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['schedule'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Penjadwalan
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->schedule()->where(['branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <!-- Product Management -->
                        <li class="nav-item">
                            <small class="nav-subtitle">Sistem Produk</small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/product*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    Produk
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/product*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('branch/product/list')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.product.list')}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            Daftar Produk
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- System Management -->
                        <li class="nav-item">
                            <small class="nav-subtitle">Pengaturan Sistem</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/business-settings*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.business-settings.index')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Pengaturan Bisnis</span>
                            </a>
                        </li>
                        <!--END SYSTEM SETTINGS -->
                        <li class="nav-item pt-10">
                            <div class=""></div>
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>
@push('script_2')
    <script>
        $(window).on('load' , function() {
            if($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        //Sidebar Menu Search
        var $rows = $('.navbar-vertical-content  .navbar-nav > li');
        $('#search-bar-input').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });

    </script>
@endpush

