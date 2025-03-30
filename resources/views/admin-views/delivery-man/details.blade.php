@extends('layouts.admin.app')

@section('title', 'Detail Kurir')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{ asset('assets/admin/img/icons/takeaway.png') }}"
                    alt="">
                <span class="page-header-title">
                    Detail Kurir
                </span>
            </h2>
        </div>

        <div class="card">
            <div class="card-top border-bottom py-3 p-20">
                <h2 class="page-header-title text-title">
                    Detail Profil
                </h2>
            </div>
            <div class="row p-20">
                <div class="col-lg-4">
                    <div class="media gap-3 mb-30 mb-lg-0">
                        <div class="avatar">
                            <img width="60" class="img-fit rounded-circle" src="{{$deliveryman->imageFullPath}}" alt="deliveryman">
                        </div>

                        <div class="media-body">
                            <h3 class="fz-22 text-title mb-0">{{ $deliveryman->f_name }} {{ $deliveryman->l_name }}</h3>
                            <div class="mb-3">
                                Bergabung: <span
                                    class="font-weight-medium">{{ $deliveryman->created_at->format('d M Y') }}</span>
                            </div>
                            <a class="btn btn-primary px-6"
                                href="{{ route('admin.delivery-man.edit', [$deliveryman['id']]) }}">Edit</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="single-details d-flex gap-20 flex-column flex-sm-row mb-4 mb-lg-0">
                        <div class="border-line d-none d-lg-block"></div>
                        <div class="flex-grow-1">
                            <h5 class="fz-16 text-title opacity-lg mb-3">Informasi Kontak</h5>
                            <div class="mb-2">{{ $deliveryman->email }}</div>
                            <div>{{$deliveryman->phone}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="single-details-slider owl-carousel owl-theme">
                        @foreach ($deliveryman->identityImageFullPath as $identification_image)
                            <div class="item">
                                <div class="single-details d-flex gap-20 flex-column flex-sm-row">
                                    <div class="border-line d-none d-lg-block"></div>
                                    <div class="d-flex flex-grow-1 gap-20 justify-content-between flex-column flex-sm-row">
                                        <div>
                                            <h5 class="fz-16 text-title opacity-lg mb-3">Informasi Identitas
                                            </h5>
                                            <div class="mb-2">
                                                Jenis Identitas:
                                                <span class="font-weight-medium">{{ $deliveryman->identity_type }}</span>
                                            </div>
                                            <div>
                                                Nomor Identitas:
                                                <span class="font-weight-medium">{{ $deliveryman->identity_number }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <img class="rounded-10 max-w200 max-h100" width="200" height="100"
                                                src="{{ $identification_image }}" alt="Identity Image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="details-slider-btn">
                        <button class="prev-btn rounded-circle">
                            <i class="tio-back-ui"></i>
                        </button>
                        <button class="next-btn rounded-circle">
                            <i class="tio-next-ui"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-top px-card border-bottom p-20">
                <form action="{{ url()->current() }}" method="GET" id="searchForm">
                    <div class="d-flex gap-4 justify-content-between align-items-end flex-wrap">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <div class="form-group flex-grow-1 mb-0">
                            <label class="input-label">Tanggal Pesan</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" name="date" id="js-daterangepicker-predefined" class="form-control"
                                    placeholder="Pilih Tanggal" value="{{ request()->get('date') }}" autocomplete="off">
                            </div>
                            {{--                            <div class="js-daterangepicker-predefined-preview"></div> --}}
                        </div>
                        <div class="form-group flex-grow-1 mb-0">
                            <label class="input-label">Cabang</label>
                            <select class="custom-select text-title" name="branch_id" id="branch_id">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch->id == $branch_id ? 'selected' : '' }}>
                                        {{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary px-6">Filter</button>
                    </div>
                </form>
            </div>

            <div>
                <div class="d-flex flex-wrap gap-10 mb-30 mt-30 px-20">
                    <div class="flex-grow-1">
                        <div class="resturant-card dashboard--card border-0 shadow-none bg-FF5B7F-light">
                            <div class="mr-4">
                                <h4 class="title">{{ $pending_orders }}</h4>
                                <span class="subtitle">Tertunda</span>
                            </div>
                            <div class="resturant-icon round-bg bg-FF5B7F">
                                <img class="" width="16"
                                    src="{{ asset('assets/admin/img/modal/deliveryman-report/pending.svg') }}"
                                    alt="">
                            </div>

                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="resturant-card dashboard--card border-0 shadow-none bg-BF83FF-light">
                            <div class="mr-4">
                                <h4 class="title">{{ $out_for_delivery_orders }}</h4>
                                <span class="subtitle">Dalam Pengiriman</span>
                            </div>
                            <div class="resturant-icon round-bg bg-BF83FF">
                                <img class="" width="16"
                                    src="{{ asset('assets/admin/img/modal/deliveryman-report/out-for-delivery.svg') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="resturant-card dashboard--card border-0 shadow-none bg-3CD856-light">
                            <div class="mr-4">
                                <h4 class="title">{{ $completed_orders }}</h4>
                                <span class="subtitle">Selesai</span>
                            </div>
                            <div class="resturant-icon round-bg bg-3CD856">
                                <img class="" width="16"
                                    src="{{ asset('assets/admin/img/modal/deliveryman-report/completed.svg') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="resturant-card dashboard--card border-0 shadow-none bg-53B65A-light">
                            <div class="mr-4">
                                <h4 class="title">Rp {{ number_format($total_order_amount) }}</h4>
                                <span class="subtitle">Jumlah Pesanan</span>
                            </div>
                            <div class="resturant-icon round-bg bg-53B65A">
                                <img class="" width="16"
                                    src="{{ asset('assets/admin/img/modal/deliveryman-report/earned.svg') }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center px-card mb-3">
                    <div class="col-md-4 col-lg-6">
                        <h5 class="d-flex gap-1">
                            Daftar Pesanan
                            <span class="badge badge-soft-dark rounded-50 fz-12 ml-1">{{ $orders->total() }}</span>
                        </h5>
                    </div>
                    <div class="col-md-8 col-lg-6">
                        <div class="d-flex gap-3 flex-wrap justify-content-end">
                            <form action="{{ url()->current() }}" method="GET" class="flex-grow-1">
                                <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                                <input type="hidden" name="date" value="{{ request()->get('date') }}"
                                    id="hiddenDate">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                        placeholder="Cari berdasarkan id pesanan" aria-label="Search" value="{{ $search }}"
                                        autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive datatable-custom">
                    <table
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>No. </th>
                                <th>ID Pesanan</th>
                                <th>Info Pelanggan</th>
                                <th>Tanggal Pesanan</th>
                                <th class="text-center">Total Item</th>
                                <th class="text-center">Jumlah Pesanan</th>
                                <th>Metode Pembayaran</th>
                                <th>Cabang</th>
                                <th class="text-center">Status Pesanan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                            @foreach ($orders as $key => $order)
                                <tr class="">
                                    <td>{{ $orders->firstitem() + $key }}</td>
                                    <td>
                                        <a class="text-dark"
                                            href="{{ route('admin.orders.details', ['id' => $order['id']]) }}">{{ $order['id'] }}</a>
                                    </td>
                                    <td>
                                        @if ($order->customer)
                                            <h6 class="text-capitalize mb-1">
                                                <a class="text-dark"
                                                    href="{{ route('admin.customer.view', [$order['user_id']]) }}">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</a>
                                            </h6>
                                            <a class="text-dark fz-12"
                                                href="tel:{{ $order->customer->phone }}">{{ $order->customer->phone }}</a>
                                        @else
                                            <span class="text-capitalize text-muted">
                                                Pelanggan tidak tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('d M Y,') }}<br>
                                        {{ $order->created_at->format('h:i A') }}
                                    </td>
                                    <td class="text-center">{{ $order->details_count }}</td>
                                    <td class="text-center">
                                        Rp {{ number_format($order->order_amount + $order->delivery_charge) }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge-soft-success px-2 py-1 rounded">{{ str_replace('_', ' ', $order['payment_method']) }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge-soft-info px-2 py-1 rounded">{{ $order->branch ? $order->branch->name : 'Branch deleted!' }}</span>
                                    </td>
                                    <td class="text-capitalize">
                                        @if ($order['order_status'] == 'pending')
                                            <span
                                                class="badge-soft-info px-2 py-1 rounded">Tertunda</span>
                                        @elseif($order['order_status'] == 'confirmed')
                                            <span
                                                class="badge-soft-info px-2 py-1 rounded">Dikonfirmasi</span>
                                        @elseif($order['order_status'] == 'processing')
                                            <span
                                                class="badge-soft-warning px-2 py-1 rounded">Diproses</span>
                                        @elseif($order['order_status'] == 'out_for_delivery')
                                            <span
                                                class="badge-soft-warning px-2 py-1 rounded">Dalam Pengiriman</span>
                                        @elseif($order['order_status'] == 'delivered')
                                            <span
                                                class="badge-soft-success px-2 py-1 rounded">Terkirim</span>
                                        @elseif($order['order_status'] == 'failed')
                                            <span
                                                class="badge-soft-danger px-2 py-1 rounded">Gagal Dikirim</span>
                                        @else
                                            <span
                                                class="badge-soft-danger px-2 py-1 rounded">{{ str_replace('_', ' ', $order['order_status']) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-success btn-sm square-btn"
                                                href="{{ route('admin.orders.details', ['id' => $order['id']]) }}">
                                                <i class="tio-visible"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="table-responsive px-3 mt-3">
                    <div class="d-flex justify-content-end">
                        {!! $orders->links() !!}
                    </div>
                </div>
                @if (count($orders) == 0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}"
                            alt="image">
                        <p class="mb-0">Data tidak tersedia</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        $(document).on('ready', function() {
            var start = "{{ request()->get('date') ? explode(' - ', request()->get('date'))[0] : null }}";
            var end = "{{ request()->get('date') ? explode(' - ', request()->get('date'))[1] : null }}";

            start = start ? moment(start, 'D MMM, YYYY') : null;
            end = end ? moment(end, 'D MMM, YYYY') : null;

            function cb(start, end) {
                $('#js-daterangepicker-predefined').val(start.format('D MMM, YYYY') + ' - ' + end.format(
                    'D MMM, YYYY'));
                $('#js-daterangepicker-predefined-preview').html(start.format('D MMM') + ' - ' + end.format(
                    'D MMM, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                autoUpdateInput: false,
                startDate: start || moment(),
                endDate: end || moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                locale: {
                    format: 'D MMM, YYYY'
                }
            });

            //cb(start, end);

            // Update the input field and preview when a range is selected
            $('#js-daterangepicker-predefined').on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate);
            });

            // Clear the input and preview on cancel
            $('#js-daterangepicker-predefined').on('cancel.daterangepicker', function() {
                $(this).val('');
                $('.js-daterangepicker-predefined-preview').html('');
            });


            if (start && end) {
                cb(start, end);
            }


            $('#searchForm').on('submit', function() {
                var selectedDate = $('#js-daterangepicker-predefined').val();
                $('#hiddenDate').val(selectedDate);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var owl = $(".single-details-slider").owlCarousel({
                loop: false,
                margin: 10,
                dots: false,
                nav: false,
                items: 1,
                smartSpeed: 600,
                // rtl: true
            });

            updateNavButtons({
                item: {
                    index: 0,
                    count: owl.find(".owl-item").length
                }
            });

            $(".next-btn").click(function() {
                owl.trigger("next.owl.carousel");
            });

            $(".prev-btn").click(function() {
                owl.trigger("prev.owl.carousel");
            });

            owl.on("changed.owl.carousel", function(event) {
                updateNavButtons(event);
            });

            function updateNavButtons(event) {
                var totalItems = event.item.count;
                var currentIndex = event.item.index;

                if (currentIndex === 0) {
                    $(".prev-btn").hide();
                } else {
                    $(".prev-btn").show();
                }

                if (currentIndex === totalItems - 1) {
                    $(".next-btn").hide();
                } else {
                    $(".next-btn").show();
                }
            }
        });
    </script>
@endpush
