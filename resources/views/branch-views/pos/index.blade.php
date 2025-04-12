@extends('layouts.branch.app')

@section('title', 'Penjualan Baru')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="loading" class="d--none">
                    <div class="loading-inner">
                        <img width="200" src="{{asset('assets/admin/img/loader.gif')}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <section class="section-content padding-y-sm bg-default mt-3">
            <div class="container-fluid">
                <div class="row gy-3 gx-2">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="pos-title">
                                <h4 class="mb-0">Daftar Produk</h4>
                            </div>

                            <div class="d-flex flex-wrap flex-md-nowrap justify-content-between gap-3 gap-xl-4 px-4 py-4">
                                <div class="w-100 mr-xl-2">
                                    <select name="category" class="form-control js-select2-custom-x mx-1 category">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $item)
                                            <option value="{{$item->id}}" {{$category==$item->id?'selected':''}}>{{ Str::limit($item->name, 40)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-100 ml-xl-2">
                                    <form id="search-form">
                                        <div class="input-group input-group-merge input-group-flush border rounded">
                                            <div class="input-group-prepend pl-2">
                                                <div class="input-group-text">
                                                    <img width="13" src="{{asset('assets/admin/img/icons/search.png')}}" alt="">
                                                </div>
                                            </div>
                                            <input id="datatableSearch" type="search" value="{{$keyword?$keyword:''}}" name="search" class="form-control border-0" placeholder="Cari disini" aria-label="Search here">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body pt-0" id="items">
                                <div class="pos-item-wrap justify-content-center">

                                    @foreach($products as $product)
                                        @include('branch-views.pos._single_product',['product'=>$product])
                                    @endforeach
                                </div>
                            </div>

                            <div class="p-3 d-flex justify-content-end">
                                {!!$products->withQueryString()->links()!!}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card billing-section-wrap">
                            <div class="pos-title">
                                <h4 class="mb-0">Pembayaran</h4>
                            </div>

                            <div class="p-2 p-sm-4">
                                <div class="form-group d-flex gap-2">
                                    <select name="customer_id" class="js-data-example-ajax form-control form-ellipsis customer">
                                        <option disabled selected>Pilih Pelanggan</option>
                                        @foreach(\App\Models\User::select('id', 'f_name', 'l_name')->get() as $customer)
                                            <option value="{{$customer['id']}}" {{ session()->get('customer_id') == $customer['id'] ? 'selected' : '' }}>{{$customer['f_name']. ' '. $customer['l_name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-success rounded text-nowrap" id="add_new_customer" type="button" data-toggle="modal" data-target="#add-customer" title="Add Customer">
                                        <i class="tio-add"></i>
                                        Pelanggan
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label class="input-label font-weight-semibold fz-16 text-dark">Pilih Jenis Pesanan</label>
                                    <div>
                                        <div class="form-control d-flex flex-column-3">
                                            <label class="custom-radio d-flex gap-2 align-items-center m-0">
                                                <input type="radio" class="order-type-radio" name="order_type" value="take_away" {{ !session()->has('order_type') || session()->get('order_type') == 'take_away' ? 'checked' : '' }}>
                                                <span class="media align-items-center mb-0">
                                                    Ambil Ditempat
                                                </span>
                                            </label>

                                            <label class="custom-radio d-flex gap-2 align-items-center m-0">
                                                <input type="radio" class="order-type-radio" name="order_type" value="home_delivery" {{ session()->has('order_type') && session()->get('order_type') == 'home_delivery' ? 'checked' : '' }}>
                                                <span class="media align-items-center mb-0">
                                                    <span class="media-body">Pengiriman</span>
                                                </span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group d-none" id="home_delivery_section">
                                    <div class="d-flex justify-content-between">
                                        <label for="" class="font-weight-semibold fz-16 text-dark">Informasi Pengiriman
                                            <small>Pengiriman Ke Tempat</small>
                                        </label>
                                        <span class="edit-btn cursor-pointer" id="delivery_address" data-toggle="modal"
                                              data-target="#AddressModal"><i class="tio-edit"></i>
                                        </span>
                                    </div>
                                    <div class="pos--delivery-options-info d-flex flex-wrap" id="del-add">
                                        @include('branch-views.pos._address')
                                    </div>
                                </div>

                                <div class='w-100' id="cart">
                                    @include('branch-views.pos._cart')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="quick-view" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" id="quick-view-modal">

                </div>
            </div>
        </div>

        <div class="modal fade" id="add-customer" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pelanggan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('branch.pos.customer-store')}}" method="post" id="customer-form">
                            @csrf
                            <div class="row pl-2">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">
                                            Nama Depan
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input type="text" name="f_name" class="form-control" value="" required>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">
                                            Nama Belakang
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input type="text" name="l_name" class="form-control" value="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">
                                            Email
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input type="email" name="email" class="form-control" value="" placeholder="Contoh : john@gmail.com">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">
                                            No.Hp
                                            <span class="input-label-secondary text-danger">*</span>
                                        </label>
                                        <input type="text" name="phone" class="form-control" value="" placeholder="Contoh : 081234xxxxx" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-secondary mr-1">Atur Ulang</button>
                                <button type="submit" id="" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @php($order=\App\Models\Order::find(session('last_order')))
        @if($order)
            @php(session(['last_order'=> false]))
            <div class="modal fade" id="print-invoice" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cetak Bukti Pembayaran</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body row ff-emoji">
                            <div class="col-md-12">
                                <center>
                                    <input type="button" class="btn btn-primary non-printable print-button" value="Cetak"/>
                                    <a href="{{url()->previous()}}" class="btn btn-danger non-printable">Kembali</a>
                                </center>
                                <hr class="non-printable">
                            </div>
                            <div class="row m-auto" id="printableArea">
                                @include('branch-views.pos.order.invoice')
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="modal fade" id="AddressModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light border-bottom py-3">
                        <h5 class="modal-title flex-grow-1 text-center">Informasi Pengiriman</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php
                        if(session()->has('address')) {
                            $old = session()->get('address');
                        }else {
                            $old = null;
                        }
                        ?>
                        <form id='delivery_address_store'>
                            @csrf

                            <div class="row g-2" id="delivery_address">
                                <div class="col-md-6">
                                    <label class="input-label" for="">Nama
                                        <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" class="form-control" name="contact_person_name"
                                           value="{{ $old ? $old['contact_person_name'] : '' }}" placeholder="Contoh : John Doe" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="input-label" for="">No. Hp
                                        <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="contact_person_number"
                                           value="{{ $old ? $old['contact_person_number'] : '' }}"  placeholder="Contoh : 081234xxxxx" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="input-label">Alamat</label>
                                    <textarea name="address" id="address" class="form-control" required>{{ $old ? $old['address'] : '' }}</textarea>
                                </div>

                                <?php
                                    $branchId =(int) auth('branch')->id() ?? 1;
                                    $branch = \App\Models\Branch::with(['delivery_charge_setup'])
                                        ->where(['id' => $branchId])
                                        ->first(['id', 'name', 'status']);
                                ?>

                                @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                                @if($googleMapStatus)
                                        <div class="col-md-6">
                                            <label class="input-label" for="">Latitude<span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text" class="form-control" id="latitude" name="latitude"
                                                   value="{{ $old ? $old['latitude'] : '' }}" readonly required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="input-label" for="">Longitude<span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text" class="form-control" id="longitude" name="longitude"
                                                   value="{{ $old ? $old['longitude'] : '' }}" readonly required>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex justify-content-between">
                                        <span class="text-primary">
                                            Sematkan alamat di peta untuk menghitung biaya pengiriman
                                        </span>
                                            </div>
                                            <div id="location_map_div">
                                                <input id="pac-input" class="controls rounded initial-8"
                                                       title="Cari lokasi disini" type="text"
                                                       placeholder="Cari disini" />
                                                <div id="location_map_canvas" class="overflow-hidden rounded custom-height"></div>
                                            </div>
                                        </div>
                                @endif
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="btn--container justify-content-end">
                                    <button class="btn btn-sm btn-primary w-100 delivery-address-update-button" type="button" data-dismiss="modal">
                                        Perbarui Alamat Pengiriman
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/sweet_alert.js"></script>
    <script src="{{asset('assets/admin')}}/js/toastr.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_client_key')->first()?->value }}&libraries=places&v=3.51"></script>


    @if ($errors->any())
        <script>
            "use strict";

            @foreach($errors->all() as $error)
            toastr.error('{{$error}}', Error, {
                CloseButton: true,
                ProgressBar: true
            });
            @endforeach
        </script>
    @endif

    <script>
        "use strict";

        $('.print-button').click(function() {
            printDiv('printableArea');
        });

        $('.delivery-address-update-button').click(function() {
            deliveryAdressStore();
        });

        $('.quick-view-trigger').click(function() {
            var productId = $(this).data('product-id');
            quickView(productId);
        });

        $('.category').change(function() {
            var selectedCategory = $(this).val();
            set_category_filter(selectedCategory);
        });

        $('.customer').change(function() {
            var selectedCustomerId = $(this).val();
            store_key('customer_id', selectedCustomerId);
        });

        
        $('.order-type-radio').change(function() {
            var selectedOrderType = $(this).val();
            select_order_type(selectedOrderType);
        });

        $('.select-table').change(function() {
            var selectedTableId = $(this).val();
            store_key('table_id', selectedTableId);
        });

        $('#number_of_people').keyup(function() {
            var numberOfPeople = $(this).val().replace(/[^\d]/g, '');
            $(this).val(numberOfPeople);
            store_key('people_number', numberOfPeople);
        });

        $('.sign-out-trigger').click(function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Yakin Ingin Keluar?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                cancelButtonColor: '#363636',
                confirmButtonText: 'Ya',
                denyButtonText: `Tidak`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{route('branch.auth.logout')}}';
                } else {
                    Swal.fire('Canceled', '', 'info');
                }
            });
        });

        $(document).on('ready', function () {
            @if($order)
            $('#print-invoice').modal('show');
            @endif
        });

        function printDiv(divName) {

            if($('html').attr('dir') === 'rtl') {
                $('html').attr('dir', 'ltr')
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                $('#printableAreaContent').attr('dir', 'rtl')
                window.print();
                document.body.innerHTML = originalContents;
                $('html').attr('dir', 'rtl')
                location.reload();
            }else{
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload();
            }

        }

        function set_category_filter(id) {
            var nurl = new URL('{!!url()->full()!!}');
            nurl.searchParams.set('category_id', id);
            location.href = nurl;
        }


        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var keyword= $('#datatableSearch').val();
            var nurl = new URL('{!!url()->full()!!}');
            nurl.searchParams.set('keyword', keyword);
            location.href = nurl;
        });

        function addon_quantity_input_toggle(e)
        {
            var cb = $(e.target);
            if(cb.is(":checked"))
            {
                cb.siblings('.addon-quantity-input').css({'visibility':'visible'});
            }
            else
            {
                cb.siblings('.addon-quantity-input').css({'visibility':'hidden'});
            }
        }
        function quickView(product_id) {
            $.ajax({
                url: '{{route('branch.pos.quick-view')}}',
                type: 'GET',
                data: {
                    product_id: product_id
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#quick-view').modal('show');
                    $('#quick-view-modal').empty().html(data.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });

        }

        function checkAddToCartValidity() {
            return true;
        }

        function cartQuantityInitialize() {
            $('.btn-number').click(function (e) {
                e.preventDefault();

                var fieldName = $(this).attr('data-field');
                var type = $(this).attr('data-type');
                var input = $("input[name='" + fieldName + "']");
                var currentVal = parseInt(input.val());

                if (!isNaN(currentVal)) {
                    if (type == 'minus') {

                        if (currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('min')) {
                            $(this).attr('disabled', true);
                        }

                    } else if (type == 'plus') {

                        if (currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('max')) {
                            $(this).attr('disabled', true);
                        }

                    }
                } else {
                    input.val(0);
                }
            });

            $('.input-number').focusin(function () {
                $(this).data('oldValue', $(this).val());
            });

            $('.input-number').change(function () {

                var minValue = parseInt($(this).attr('min'));
                var maxValue = parseInt($(this).attr('max'));
                var valueCurrent = parseInt($(this).val());

                var name = $(this).attr('name');
                if (valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title:'Keranjang',
                        text: 'Masukkan setidaknya 1 kuantitas produk'
                    });
                    $(this).val($(this).data('oldValue'));
                }
                if (valueCurrent <= maxValue) {
                    $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    Swal.fire({
                        icon: 'error',
                        title:'Keranjang',
                        confirmButtonText:'Ok',
                        text: 'Batas stok melampaui stok yang tersedia'
                    });
                    $(this).val($(this).data('oldValue'));
                }
            });
            $(".input-number").keydown(function (e) {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        function getVariantPrice() {
            if ($('#add-to-cart-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: '{{ route('branch.pos.variant_price') }}',
                    data: $('#add-to-cart-form').serializeArray(),
                    success: function (data) {
                        if(data.error == 'quantity_error'){
                            toastr.error(data.message);
                        }
                        else{
                            $('#add-to-cart-form #chosen_price_div').removeClass('d-none');
                            $('#add-to-cart-form #chosen_price_div #chosen_price').html(data.price);
                        }
                    }
                });
            }
        }

        function addToCart(form_id = 'add-to-cart-form') {
            if (checkAddToCartValidity()) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{ route('branch.pos.add-to-cart') }}',
                    data: $('#' + form_id).serializeArray(),
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        if (data.data == 1) {
                            Swal.fire({
                                confirmButtonColor: '#FC6A57',
                                icon: 'info',
                                title: 'Keranjang',
                                confirmButtonText:'Ok',
                                text: "Produk sudah tersedia dikeranjang"
                            });
                            return false;
                        } else if (data.data == 0) {
                            Swal.fire({
                                confirmButtonColor: '#FC6A57',
                                icon: 'error',
                                title: 'Keranjang',
                                confirmButtonText:'Ok',
                                text: 'Produk kehabisan stok'
                            });
                            return false;
                        }
                        else if (data.data == 'variation_error') {
                            Swal.fire({
                                confirmButtonColor: '#FC6A57',
                                icon: 'error',
                                title: 'Keranjang',
                                text: data.message
                            });
                            return false;
                        }
                        $('.call-when-done').click();

                        toastr.success('Produk berhasil ditambahkan ke keranjang', {
                            CloseButton: true,
                            ProgressBar: true
                        });

                        updateCart();
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    confirmButtonColor: '#FC6A57',
                    type: 'info',
                    title: 'Keranjang',
                    confirmButtonText:'Ok',
                    text: 'Silahkan pilih semua pilihan'
                });
            }
        }

        function removeFromCart(key) {
            $.post('{{ route('branch.pos.remove-from-cart') }}', {_token: '{{ csrf_token() }}', key: key}, function (data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    updateCart();
                    toastr.info('Produk telah dihapus dari keranjang', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }

            });
        }

        function emptyCart() {
            $.post('{{ route('branch.pos.empty-cart') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                updateCart();
                toastr.info('Produk telah dihapus dari keranjang', {
                    CloseButton: true,
                    ProgressBar: true
                });
                location.reload();
            });
        }

        function updateCart() {
            $.post('<?php echo e(route('branch.pos.cart-items')); ?>', {_token: '<?php echo e(csrf_token()); ?>'}, function (data) {
                $('#cart').empty().html(data);
            });
        }

        $(function(){
            $(document).on('click','input[type=number]',function(){ this.select(); });
        });


        function updateQuantity(e){
            var element = $( e.target );
            var minValue = parseInt(element.attr('min'));
            var valueCurrent = parseInt(element.val());

            var key = element.data('key');
            if (valueCurrent >= minValue) {
                $.post('{{ route('branch.pos.update-quantity') }}', {_token: '{{ csrf_token() }}', key: key, quantity:valueCurrent}, function (data) {
                    updateCart();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Keranjang',
                    confirmButtonText:'Ok',
                    text: 'Batas stok melampui stok yang tersedia'
                });
                element.val(element.data('oldValue'));
            }
            if(e.type == 'keydown')
            {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            }

        };

        $('.branch-data-selector').select2();
        $('.table-data-selector').select2();

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('branch.pos.customers')}}',
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);
                    console.log('test 3')
                    return $request;
                }
            }
        });


        $('#order_place').submit(function(eventObj) {
            if($('#customer').val())
            {
                $(this).append('<input type="hidden" name="user_id" value="'+$('#customer').val()+'" /> ');
            }
            return true;
        });

        function store_key(key, value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            $.post({
                url: '{{route('branch.pos.store-keys')}}',
                data: {
                    key:key,
                    value:value,
                },
                success: function (data) {
                    var selected_field_text = key;
                    var selected_field = selected_field_text.replace("_", " ");
                    var selected_field = selected_field.replace("id", " ");
                    var msg = selected_field.trim() == 'customer' ? 'Pelanggan' : 'Cabang';
                    var message = msg+' '+'dipilih';
                    var new_message = message.charAt(0).toUpperCase() + message.slice(1);
                    toastr.success((new_message), {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },

            });
        };


        $(document).ready(function (){
            $('#change-branch').on('change', function (){

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "{{ url('branch/pos/session-destroy') }}",
                    success: function() {
                        location.reload();
                    }
                });
            });
        });

        $(document).ready(function() {
            var orderType = {!! json_encode(session('order_type')) !!};
            
            if (orderType === 'home_delivery') {
                $('#home_delivery_section').removeClass('d-none');
                $('#dine_in_section').addClass('d-none');
            } else {
                $('#home_delivery_section').addClass('d-none');
                $('#dine_in_section').addClass('d-none');
            }
        });

        function select_order_type(order_type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            $.post({
                url: '{{route('branch.pos.order_type.store')}}',
                data: {
                    order_type:order_type,
                },
                success: function (data) {
                    updateCart();
                },
            });

            if(order_type == 'home_delivery') {
                $('#home_delivery_section').removeClass('d-none');
                $('#dine_in_section').addClass('d-none');
            }else{
                $('#home_delivery_section').addClass('d-none')
                $('#dine_in_section').addClass('d-none');
            }
        }

        $( document ).ready(function() {
            function initAutocomplete() {
                var myLatLng = {

                    lat: -8.200786388659255,
                    lng: 113.53614807128906
                };
                const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                    center: {
                        lat: -8.200786388659255,
                        lng: 113.53614807128906
                    },
                    zoom: 13,
                    mapTypeId: "roadmap",
                });

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                });

                marker.setMap(map);
                var geocoder = geocoder = new google.maps.Geocoder();
                google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {
                    var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                    var coordinates = JSON.parse(coordinates);
                    var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                    marker.setPosition(latlng);
                    map.panTo(latlng);

                    document.getElementById('latitude').value = coordinates['lat'];
                    document.getElementById('longitude').value = coordinates['lng'];

                    geocoder.geocode({
                        'latLng': latlng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                document.getElementById('address').value = results[1].formatted_address;
                            }
                        }
                    });
                });

                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });
                let markers = [];

                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    markers.forEach((marker) => {
                        marker.setMap(null);
                    });
                    markers = [];

                    const bounds = new google.maps.LatLngBounds();
                    places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            return;
                        }
                        var mrkr = new google.maps.Marker({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        });
                        google.maps.event.addListener(mrkr, "click", function(event) {
                            document.getElementById('latitude').value = this.position.lat();
                            document.getElementById('longitude').value = this.position.lng();

                        });

                        markers.push(mrkr);

                        if (place.geometry.viewport) {
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            };
            initAutocomplete();
        });

        function deliveryAdressStore(form_id = 'delivery_address_store') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('branch.pos.add-delivery-address') }}',
                data: $('#' + form_id).serializeArray(),
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        $('#del-add').empty().html(data.view);
                    }
                    updateCart();
                    $('.call-when-done').click();
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        }

        $(document).on('ready', function () {
            $('.js-select2-custom-x').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $(document).ready(function() {
            const $areaDropdown = $('#areaDropdown');
            const $deliveryChargeInput = $('#deliveryChargeInput');

            $areaDropdown.change(function() {
                const selectedOption = $(this).find('option:selected');
                const charge = selectedOption.data('charge');
                $deliveryChargeInput.val(charge);
            });
        });

    </script>

    <script>
        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
    </script>
@endpush

