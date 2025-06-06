@extends('layouts.admin.app')
@section('title', 'Tambah Produk')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/product.png')}}" alt="">
                <span class="page-header-title">
                    Tambah Produk
                </span>
            </h2>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <form action="javascript:" method="post" id="product_form" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-lg-6">
                            <div class="card card-body h-100">
                                <div class="" id="en-form">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">Nama Produk</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label"
                                                for="exampleFormControlInput1">Deskripsi Singkat</label>
                                        <textarea name="description" class="form-control textarea-h-100" id="hiddenArea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card card-body h-100">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Gambar Produk</label>
                                    <small class="text-danger">* ( Rasio 1:1 )</small>
                                    <div class="d-flex justify-content-center mt-4">
                                        <div class="upload-file">
                                            <input type="file" name="image" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" class="upload-file__input">
                                            <div class="upload-file__img_drag upload-file__img">
                                                <img width="176" src="{{asset('assets/admin/img/icons/upload_img.png')}}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                                <i class="tio-category"></i>
                                                Kategori
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label" for="exampleFormControlSelect1">
                                                            Kategori
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="category_id" class="form-control js-select2-custom"
                                                                onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                                            <option selected disabled>---Pilih---</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label" for="exampleFormControlSelect1">Sub Kategori<span
                                                                class="input-label-secondary"></span></label>
                                                        <select name="sub_category_id" id="sub-categories"
                                                                class="form-control js-select2-custom"
                                                                onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">
                                                            <option selected disabled>---Pilih---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label" for="exampleFormControlInput1">
                                                            Jenis Produk
                                                            <span class="text-danger">*</span>
                                                        </label>

                                                        <select name="product_type" class="form-control js-select2-custom">
                                                            <option selected disabled>---Pilih---</option>
                                                            <option value="1">Produk Lokal</option>
                                                            <option value="0">Produk Impor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                                <i class="tio-dollar"></i>
                                                Informasi Harga
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="input-label">Harga
                                                            <span class="text-danger">*</span></label>
                                                        <input type="number" min="0" step="any" name="price" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Jenis Diskon
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="discount_type" class="form-control js-select2-custom" id="discount_type">
                                                            <option selected disabled>---Pilih---</option>
                                                            <option value="nothing">Tidak Ada</option>
                                                            <option value="percent">Diskon persentase</option>
                                                            <option value="amount">Diskon langsung</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label id="discount_label" class="input-label">Diskon
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input id="discount_input" type="number" min="0" name="discount" class="form-control"
                                                               placeholder="Kosongkan jika tidak perlu">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Jenis Pajak
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="tax_type" class="form-control js-select2-custom" id="tax_type">
                                                            <option selected disabled>---Pilih---</option>
                                                            <option value="nothing">Tidak Ada</option>
                                                            <option value="percent">Pajak persentase</option>
                                                            <option value="amount">Pajak langsung</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label id="tax_label" class="input-label" for="exampleFormControlInput1">Tarif Pajak
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input id="tax_input" type="number" min="0" step="any" name="tax" class="form-control"
                                                               placeholder="Kosongkan jika tidak perlu">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                                <i class="tio-dollar"></i>
                                                Informasi Stok
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Jenis Stok</label>
                                                        <select name="stock_type" class="form-control js-select2-custom" id="stock_type">
                                                            <option value="unlimited">Selalu ada (Unlimited)</option>
                                                            <option value="fixed">Tetap</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 d-none" id="product_stock_div">
                                                    <div class="form-group">
                                                        <label class="input-label">Stok Produk
                                                        </label>
                                                        <input id="product_stock" type="number" name="product_stock" class="form-control"
                                                               placeholder="Contoh : 100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="text-dark">Tampilkan produk ini di aplikasi ecommerce</div>
                                                <div class="d-flex gap-3 align-items-center">
                                                    <h5>Tampilkan</h5>
                                                    <label class="switcher">
                                                        <input class="switcher_input" type="checkbox" checked="checked" name="status">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                                <i class="tio-watches"></i>
                                                Waktu Tersedia Produk
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Mulai</label>
                                                        <input type="time" name="available_time_starts" class="form-control" value="08:00:00" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Sampai</label>
                                                        <input type="time" name="available_time_ends" class="form-control" value="20:00:00" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                                <i class="tio-label"></i>
                                                Tagar
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="">
                                                        <label class="input-label">Pencarian Tagar</label>
                                                        <input type="text" class="form-control" name="tags" placeholder="Contoh : #bestseller" data-role="tagsinput">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="text-dark">Rekomendasikan produk ini di aplikasi ecommerce</div>
                                                <div class="d-flex gap-3 align-items-center">
                                                    <h5>Rekomendasikan</h5>
                                                    <label class="switcher">
                                                        <input class="switcher_input" type="checkbox" checked="checked" name="is_recommended">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                <i class="tio-canvas-text"></i>
                                Variasi Produk
                            </h4>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="add_new_option">
                                    </div>
                                    <br>
                                    <div class="">
                                        <a class="btn btn-outline-success"
                                           id="add_new_option_button">Tambah Variasi Produk</a>
                                    </div>
                                    <br><br>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>


    <script>
        var count = 0;
        $(document).ready(function() {

            $("#add_new_option_button").click(function(e) {
                count++;
                var add_option_view = `
                    <div class="card view_new_option mb-2" >
                        <div class="card-header">
                            <label for="" id=new_option_name_` + count + `>Variasi Baru</label>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-lg-3 col-md-6">
                                    <label for="">Nama Variasi</label>
                                    <input required name=options[` + count + `][name] class="form-control" type="text"
                                        onkeyup="new_option_name(this.value,` + count + `)">
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">Jenis Pilihan</span></label>
                                        <div class="resturant-type-group border">
                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input" type="radio" value="multi "name="options[` + count + `][type]" id="type` + count +
                                                    `" checked onchange="show_min_max(` + count + `)">
                                                <span class="form-check-label">Beberapa</span>
                                            </label>

                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input" type="radio" value="single" name="options[` + count + `][type]" id="type` + count +
                                                    `" onchange="hide_min_max(` + count + `)" >
                                                <span class="form-check-label">Tunggal</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="row g-2">
                                        <div class="col-sm-6 col-md-4">
                                            <label for="">Min. Pembelian</label>
                                            <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <label for="">Maks. Pembelian</label>
                                            <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="d-md-block d-none">&nbsp;</label>
                                        <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <input id="options[` + count + `][required]" name="options[` + count + `][required]" type="checkbox">
                                            <label for="options[` + count + `][required]" class="m-0">Wajib</label>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-danger btn-sm delete_input_button" onclick="removeOption(this)"title="Hapus">
                                                <i class="tio-add-to-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="option_price_` + count + `" >
                        <div class="border rounded p-3 pb-0 mt-3">
                            <div  id="option_price_view_` + count + `">
                                <div class="row g-3 add_new_view_row_class mb-3">
                                    <div class="col-md-4 col-sm-6">
                                        <label for="">Nama Opsi</label>
                                        <input class="form-control" required type="text" name="options[` + count +`][values][0][label]" id="">
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <label for="">Tambahan Harga</label>
                                        <input class="form-control" required type="number" min="0" step="0.01" name="options[` + count + `][values][0][optionPrice]" id="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count + `">
                                <button type="button" class="btn btn-outline-primary" onclick="add_new_row_button(` +
                                    count + `)" >Tambah Opsi Baru
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            $("#add_new_option").append(add_option_view);
            });
        });

        function show_min_max(data) {
            $('#min_max1_' + data).removeAttr("readonly");
            $('#min_max2_' + data).removeAttr("readonly");
            $('#min_max1_' + data).attr("required", "true");
            $('#min_max2_' + data).attr("required", "true");
        }

        function hide_min_max(data) {
            $('#min_max1_' + data).val(null).trigger('change');
            $('#min_max2_' + data).val(null).trigger('change');
            $('#min_max1_' + data).attr("readonly", "true");
            $('#min_max2_' + data).attr("readonly", "true");
            $('#min_max1_' + data).attr("required", "false");
            $('#min_max2_' + data).attr("required", "false");
        }

        function new_option_name(value, data) {
            $("#new_option_name_" + data).empty();
            $("#new_option_name_" + data).text(value)
            console.log(value);
        }

        function removeOption(e) {
            element = $(e);
            element.parents('.view_new_option').remove();
        }

        function deleteRow(e) {
            element = $(e);
            element.parents('.add_new_view_row_class').remove();
        }


        function add_new_row_button(data) {
            count = data;
            countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
            var add_new_row_view = `
                <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                    <div class="col-md-4 col-sm-5">
                        <label for="">Nama Opsi</label>
                        <input class="form-control" required type="text" name="options[` + count + `][values][` + countRow + `][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">Tambahan Harga</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` + count + `][values][` + countRow + `][optionPrice]" id="">
                    </div>
                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"title="Hapus">
                                    <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            $('#option_price_view_' + data).append(add_new_row_view);
        }
    </script>


    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
            $('#image-viewer-section').show(1000)
        });
    </script>

    </script>

    <script>
        //Select 2
        $("#choose_addons").select2({
            placeholder: "Select Addons",
            allowClear: true
        });

    </script>

    <script>
        $('#product_form').on('submit', function (event) {
            var formData = new FormData(this);
            $.post({
                url: '{{route('admin.product.store')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('Produk berhasil ditambahkan', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.product.list')}}';
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan ', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    console.log(xhr.responseText);
                }
            });
        });
    </script>

    <script>
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }
    </script>

    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script src="{{asset('assets/admin')}}/js/tags-input.min.js"></script>


    <script>
        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for(var i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {
                $('input[name="total_stock"]').attr("readonly", true);
                $('input[name="total_stock"]').val(total_qty);
                console.log(total_qty)
            }
            else{
                $('input[name="total_stock"]').attr("readonly", false);
            }
        }
    </script>

    <script>
        $("#discount_type").change(function(){
            if(this.value === 'amount') {
                $("#discount_label").text("Diskon langsung");
                $("#discount_input").attr("placeholder", "Contoh : 5000")
            }
            else if(this.value === 'percent') {
                $("#discount_label").text("Diskon Persentase")
                $("#discount_input").attr("placeholder", "Contoh : 5%")
            } else {
                $("#discount_label").text("Diskon");
                $("#discount_input").attr("placeholder", "Kosongkan jika tidak perlu")   
            }
        });

        $("#tax_type").change(function(){
            if(this.value === 'amount') {
                $("#tax_label").text("Pajak langsung");
                $("#tax_input").attr("placeholder", "Contoh : 2000")
            }
            else if(this.value === 'percent') {
                $("#tax_label").text("Pajak persentase")
                $("#tax_input").attr("placeholder", "Contoh : 12%")
            } else {
                $("#tax_label").text("Tarif pajak");
                $("#tax_input").attr("placeholder", "Kosongkan jika tidak perlu")   
            }
        });


        $("#stock_type").change(function(){
            if(this.value === 'fixed') {
               $("#product_stock_div").removeClass('d-none')
            }
            else {
                $("#product_stock_div").addClass('d-none')
            }
        });

    </script>
@endpush




