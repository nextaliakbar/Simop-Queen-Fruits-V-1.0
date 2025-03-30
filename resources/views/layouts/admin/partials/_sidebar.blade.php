<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">
                    <!-- Logo -->
                    {{-- @php($restaurant_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value) --}}
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
{{--                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['dashboard_management']))--}}
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.dashboard')}}" title="dashboard">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        Dashboard
                                    </span>
                            </a>
                        </li>
{{--                        @endif--}}
                        <!-- End Dashboards -->

{{--
                        <!-- Pos Management -->
                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['pos_management']))

                            <!-- POS -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/pos/*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-shopping nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Kasir</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/pos*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/pos')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.pos.index')}}"
                                           title="kasir">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Penjualan Baru</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/pos/orders')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.pos.orders')}}" title="daftar-penjualan">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                Daftar Penjualan
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::Pos()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End POS -->
                        @endif

                        <!-- Order Management -->
                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['order_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">Manajemen Pesanan</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/verify-offline-payment*') ?'show active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                   href="{{route('admin.verify-offline-payment', ['pending'])}}" title="verifikasi-pembayaran-offline">
                                    <i class="tio-shopping-basket nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        Verifikasi Pembayaran Offline
                                    </span>
                                </a>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders/list/*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-shopping-cart nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        Pesanan
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                        <a class="nav-link" href="{{route('admin.orders.list',['all'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                <span>Semua</span>
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['pending'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                <span>Tertunda</span>
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'pending'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['confirmed'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                Terkonfirmasi
                                                    <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'confirmed'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['processing'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    Diproses
                                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'processing'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['out_for_delivery'])}}"
                                           title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    Dalam Pengiriman
                                                    <span class="badge badge-soft-warning badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'out_for_delivery'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['delivered'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    Terkirim
                                                    <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'delivered'])->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['returned'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                    Dikembalian
                                                    <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'returned'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['failed'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                Gagal Terkirim
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'failed'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['canceled'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                Dibatalkan
                                                    <span class="badge badge-soft-dark badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where(['order_status'=>'canceled'])->notSchedule()->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/orders/list/schedule')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.orders.list',['schedule'])}}" title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate sidebar--badge-container">
                                                Penjadwalan
                                                    <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{\App\Model\Order::notPos()->notDineIn()->where('delivery_date','>',\Carbon\Carbon::now()->format('Y-m-d'))->count()}}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- End Pages -->
                        @endif
--}}

                        <!-- Product Management -->
                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['product_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">Manajemen Produk</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>


                            <!-- Pages -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/category*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                >
                                    <i class="tio-category nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Pengaturan Kategori</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/category*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/category/add')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.category.add')}}"
                                           title="tambah-kategori-produk">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Kategori Produk</span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/category/add-sub-category')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.category.add-sub-category')}}"
                                           title="tambah-sub-kategori-produk">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Sub Kategori Produk</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                            <!-- End Pages -->

                            <!-- Pages -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product*') || Request::is('admin/attribute*') || Request::is('admin/reviews/list')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                >
                                    <i class="tio-premium-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Pengaturan Produk</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/product*') || Request::is('admin/attribute*') || Request::is('admin/reviews*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/product/add-new') ?'active':'' }}">
                                        <a class="nav-link " href="{{route('admin.product.add-new')}}" title="tambah-produk">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Tambah Produk</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/product/list') || Request::is('admin/product/edit*') ?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.product.list')}}" title="daftar-produk">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Daftar Produk</span>
                                        </a>
                                    </li>
                                    <!-- REVIEWS -->
                                    <li class="navbar-vertical-aside-has-menu {{Request::is('admin/reviews*')?'active':''}}">
                                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.reviews.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        Review Produk
                                    </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            <!-- End Pages -->

                        <!-- Promotion Management -->
                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['promotion_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">Manajemen Promosi</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- BANNER -->
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banner*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.banner.list')}}">
                                    <i class="tio-image nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Banner</span>
                                </a>
                            </li>
                        @endif

                        <!-- User Management -->
                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            <li class="nav-item {{(Request::is('admin/employee*') || Request::is('admin/custom-role*'))?'scroll-here':''}}">
                                <small class="nav-subtitle">Manajemen Pengguna</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/transaction') || Request::is('admin/customer/list') || Request::is('admin/customer/view*') || Request::is('admin/customer/settings')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-poi-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        Pelanggan
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/customer/transaction') || Request::is('admin/customer/list')  || Request::is('admin/customer/view*')  || Request::is('admin/customer/settings')?'block':''}}; top: 831.076px;">
                                    <li class="nav-item {{Request::is('admin/customer/list') || Request::is('admin/customer/view*') ? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('admin.customer.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Daftar Pelanggan</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/delivery-man*')? 'active' : ''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                    <i class="tio-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            Kurir
                                        </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display:  {{Request::is('admin/delivery-man*')? 'block' : ''}}; top: 831.076px;">
                                    <li class="nav-item {{Request::is('admin/delivery-man/list')? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('admin.delivery-man.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Daftar Kurir</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/delivery-man/add') ?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.delivery-man.add')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Tambah Kurir</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/delivery-man/reviews/list')? 'active' : ''}}">
                                        <a class="nav-link" href="{{route('admin.delivery-man.reviews.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Review Kurir</span>
                                        </a>
                                    </li>

                                </ul>
                            </li>
                        @endif

                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['user_management']))
                            @if(auth('admin')->user()->admin_role_id == 1)
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/custom-role*') || Request::is('admin/employee*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="Employees">
                                        <i class="tio-incognito nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            Pengaturan Karyawan
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub " style="display: {{Request::is('admin/custom-role*') || Request::is('admin/employee*')?'block':''}}">

                                        <li class="nav-item {{Request::is('admin/custom-role*')? 'active': ''}}">
                                            <a class="nav-link" href="{{route('admin.custom-role.create')}}" title="pengaturan-peran-karyawan">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    Peran Karyawan</span>
                                            </a>
                                        </li>

                                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/employee*')?'active':''}}">
                                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="pengaturan-karyawan">
                                                <span class="tio-user mr-2"></span>
                                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                                    Karyawan
                                                </span>
                                            </a>
                                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/employee*')?'block':''}}">
                                                <li class="nav-item {{Request::is('admin/employee/add-new')?'active':''}}">
                                                    <a class="nav-link " href="{{route('admin.employee.add-new')}}" title="tambah-karyawan">
                                                        <span class="tio-circle nav-indicator-icon"></span>
                                                        <span class="text-truncate">Tambah Karyawan</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item {{Request::is('admin/employee/list')?'active':''}}">
                                                    <a class="nav-link" href="{{route('admin.employee.list')}}" title="daftar-karyawan">
                                                        <span class="tio-circle nav-indicator-icon"></span>
                                                        <span class="text-truncate">Daftar Karyawan</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                            @endif

                        @endif

                        <!-- System Management -->
                        @if(\App\CentralLogics\Helpers::module_permission_check(MANAGEMENT_SECTION['system_management']))
                        <li class="nav-item">
                            <small class="nav-subtitle">Pengaturan Sistem</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <!-- Business_Setup -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/restaurant*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.business-settings.store.store-setup')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Pengturan Bisnis</span>
                            </a>
                        </li>
                        <!-- END Business_Setup -->

                        <!--BRANCH SETUP -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/branch*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-shop nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            Cabang Bisnis
                                        </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/branch*')?'block':'none'}}">
                                    <li class="nav-item {{Request::is('admin/branch/add-new')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.branch.add-new')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Tambah Cabang</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/branch/list')?'active':''}}">
                                        <a class="nav-link" href="{{route('admin.branch.list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">Daftar Cabang</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <!--END BRANCH SETUP -->
                        @endif
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


{{--<script>
    $(document).ready(function () {
        $('.navbar-vertical-content').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 'slow');
    });
</script>--}}

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

