@extends('layouts.admin.app')

@section('title', 'Perbarui Produk')

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
                    Perbarui Produk
                </span>
            </h2>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="javascript:" method="post" id="product_form" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="lang_form" id="en-form">
                                        <div class="form-group">
                                            <label class="input-label" for="en_name">Nama Produk</label>
                                            <input type="text" name="name" id="en_name" value="{{$product['name']}}" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="input-label"
                                                for="en_description">Deskripsi Singkat</label>
                                            <textarea name="description" class="form-control textarea-h-100" id="en_hiddenArea">{{$product['description']}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Gambar Produk</label>
                                        <small class="text-danger">* ( Rasio 1:1 )</small>

                                        <div class="d-flex justify-content-center mt-4">
                                            <div class="upload-file">
                                                <input type="file" name="image" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" class="upload-file__input">
                                                <div class="upload-file__img_drag upload-file__img">
                                                    <img width="176" src="{{$product['imageFullPath']}}" alt="produk">
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
                                                        <label class="input-label" for="exampleFormControlSelect1">Kategori
                                                            <span class="text-danger">*</span></label>
                                                        <select name="category_id" id="category-id" class="form-control js-select2-custom"
                                                                onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                                            @foreach($categories as $category)
                                                                <option
                                                                    value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label" for="exampleFormControlSelect1">Sub Kategori<span
                                                                class="input-label-secondary"></span></label>
                                                        <select name="sub_category_id" id="sub-categories"
                                                                data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                                                class="form-control js-select2-custom"
                                                                onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label" for="exampleFormControlInput1">Jenis Produk
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="product_type" class="form-control js-select2-custom">
                                                            <option value="1" {{$product['local_product']==1?'selected':''}}>Produk Lokal</option>
                                                            <option value="0" {{$product['local_product']==0?'selected':''}}>Produk Impor</option>
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
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number" value="{{$product['price']}}" min="0" name="price"
                                                            class="form-control" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Jenis Diskon
                                                            <span class="text-danger">*</span></label>
                                                        <select id="discount_type" name="discount_type" class="form-control js-select2-custom">
                                                            <option value="nothing" {{is_null($product['discount_type'])? 'selected' : ''}}>
                                                                Tidak Ada
                                                            </option>
                                                            <option value="percent" {{$product['discount_type']!= null ? ($product['discount_type'] == 'percent' ? 'selected' : ''):''}}>
                                                                Diskon Persentase
                                                            </option>
                                                            <option value="amount" {{$product['discount_type']!= null ? ($product['discount_type'] == 'amount' ? 'selected' : ''):''}}>
                                                                Diskon Langsung
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Diskon
                                                            <span class="text-danger">*</span></label>
                                                        <input id="discount_input" type="number" min="0" value="{{$product['discount']}}"
                                                            name="discount" class="form-control"
                                                            placeholder="Kosongkan jika tidak perlu">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Jenis Pajak
                                                            <span class="text-danger">*</span></label>
                                                        <select id="tax_type" name="tax_type" class="form-control js-select2-custom">
                                                            <option value="nothing" {{is_null($product['tax_type'])? 'selected':''}}>
                                                                Tidak Ada
                                                            </option>
                                                            <option value="percent" {{$product['tax_type']!= null ? ($product['tax_type'] == 'precent' ? 'selected' : ''):''}}>
                                                                Pajak Persentase
                                                            </option>
                                                            <option value="amount" {{$product['tax_type']!= null ? ($product['tax_type'] == 'amount' ? 'selected' : ''):''}}>
                                                                Pajak Langsung
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label" for="exampleFormControlInput1">Tarif Pajak
                                                            <span class="text-danger">*</span></label>
                                                        <input id="tax_input" type="number" value="{{$product['tax']}}" min="0" name="tax"
                                                            class="form-control" step="0.01"
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
                                                            <option value="unlimited" {{ $product->main_branch_product?->stock_type == 'unlimited' ? 'selected' : '' }}>Selalu ada (Unlimited)</option>
                                                            <option value="fixed" {{ $product->main_branch_product?->stock_type == 'fixed' ? 'selected' : '' }}>Tetap</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 d-none" id="product_stock_div">
                                                    <div class="form-group">
                                                        <label class="input-label">Stok Produk
                                                        </label>
                                                        <input id="product_stock" type="number" name="product_stock" class="form-control"
                                                              value="{{ $product->main_branch_product?->stock }}" placeholder="Contoh : 100">
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
                                                        <input class="switcher_input" type="checkbox" name="status" {{$product->status == 1? 'checked' : ''}} >
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
                                                        <input type="time" value="{{$product['available_time_starts']}}"
                                                            name="available_time_starts" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="input-label">Sampai</label>
                                                        <input type="time" value="{{$product['available_time_ends']}}"
                                                            name="available_time_ends" class="form-control" required>
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
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <div class="">
                                                        <label class="input-label">Pencarian Tagar</label>
                                                        <input type="text" class="form-control" name="tags" placeholder="Contoh : #bestseller" value="@foreach($product->tags as $c) {{$c->tag.','}} @endforeach" data-role="tagsinput">
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
                                                        <input class="switcher_input" type="checkbox" name="is_recommended" {{$product->is_recommended == 1? 'checked' : ''}} >
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
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-12" >
                                        <div id="add_new_option">
                                            @if (isset($product->variations))
                                                @foreach (json_decode($product->variations,true) as $key_choice_options=>$item)
                                                    @if (isset($item["price"]))
                                                        @break
                                                    @else
                                                        @include('admin-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-success" id="add_new_option_button">Tambah Variasi Produk</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')
    <script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>

    <script>
        //Select 2
        $("#choose_addons").select2({
            placeholder: "Select Addons",
            allowClear: true
        });
       /* $("#choice_attributes").select2({
            placeholder: "Select Attributes",
            allowClear: true
        });*/
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

    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: '215px',
                groupClassName: 'col-3',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('File yang diizinkan hanya png atau jpg', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('Ukuran file terlalu besar', {
                        CloseButton: true,
                        ProgressBar: true
                    });
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

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                let sub_sub_category = '{{count($product_category)>=3?$product_category[2]->id:''}}';
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });
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
        function show_min_max(data){
            $('#min_max1_'+data).removeAttr("readonly");
            $('#min_max2_'+data).removeAttr("readonly");
            $('#min_max1_'+data).attr("required","true");
            $('#min_max2_'+data).attr("required","true");
        }
        function hide_min_max (data){
            $('#min_max1_'+data).val(null).trigger('change');
            $('#min_max2_'+data).val(null).trigger('change');
            $('#min_max1_'+data).attr("readonly","true");
            $('#min_max2_'+data).attr("readonly","true");
            $('#min_max1_'+data).attr("required","false");
            $('#min_max2_'+data).attr("required","false");
        }

        var count= {{isset($product->variations)?count(json_decode($product->variations,true)):0}};

        $(document).ready(function(){
            console.log(count);

            $("#add_new_option_button").click(function(e){
                count++;
                var add_option_view = `
        <div class="card view_new_option mb-2" >
            <div class="card-header">
                <label for="" id=new_option_name_`+count+`>Variasi Baru</label>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-lg-3 col-md-6">
                        <label for="">Nama Variasi</label>
                        <input required name=options[`+count+`][name] class="form-control" type="text" onkeyup="new_option_name(this.value,`+count+`)">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="input-label text-capitalize d-flex alig-items-center"><span class="line--limit-1">Jenis Pilihan</span>
                            </label>
                            <div class="resturant-type-group border">
                                <label class="form-check form--check mr-2 mr-md-4">
                                    <input class="form-check-input" type="radio" value="multi"
                                    name="options[`+count+`][type]" id="type`+count+`" checked onchange="show_min_max(`+count+`)"
                                    >
                                    <span class="form-check-label">
                                        Beberapa
                    </span>
                </label>

                <label class="form-check form--check mr-2 mr-md-4">
                    <input class="form-check-input" type="radio" value="single"
                    name="options[`+count+`][type]" id="type`+count+`" onchange="hide_min_max(`+count+`)"
                                    >
                                    <span class="form-check-label">
                                        Tunggal
                    </span>
                </label>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="row g-2">
            <div class="col-sm-6 col-md-4">
                <label for="">Min. Pembelian</label>
                                <input id="min_max1_`+count+`" required name="options[`+count+`][min]" class="form-control" type="number" min="1">
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <label for="">Maks. Pembelian</label>
                                <input id="min_max2_`+count+`" required name="options[`+count+`][max]" class="form-control" type="number" min="1">
                            </div>

                            <div class="col-md-4">
                                <label class="d-md-block d-none">&nbsp;</label>
                                    <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <input id="options[`+count+`][required]" name="options[`+count+`][required]" type="checkbox">
                                        <label for="options[`+count+`][required]" class="m-0">Wajib</label>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-danger btn-sm delete_input_button" onclick="removeOption(this)"
                                            title="Hapus">
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
                    count + `)" >Tambah Opsi Baru</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
                $("#add_new_option").append(add_option_view);

            });

        });

        function new_option_name(value,data)
        {
            $("#new_option_name_"+data).empty();
            $("#new_option_name_"+data).text(value)
            console.log(value);
        }
        function removeOption(e)
        {
            element = $(e);
            element.parents('.view_new_option').remove();
        }
        function deleteRow(e)
        {
            element = $(e);
            element.parents('.add_new_view_row_class').remove();
        }


        function add_new_row_button(data)
        {
            count = data;
            countRow = 1 + $('#option_price_view_'+data).children('.add_new_view_row_class').length;
            var add_new_row_view = `
                <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-md-0">
                    <div class="col-md-4 col-sm-5">
                            <label for="">Nama Opsi</label>
                            <input class="form-control" required type="text" name="options[`+count+`][values][`+countRow+`][label]" id="">
                        </div>
                        <div class="col-md-4 col-sm-5">
                            <label for="">Tambahan Harga</label>
                            <input class="form-control"  required type="number" min="0" step="0.01" name="options[`+count+`][values][`+countRow+`][optionPrice]" id="">
                        </div>
                        <div class="col-sm-2 max-sm-absolute">
                            <label class="d-none d-md-block">&nbsp;</label>
                            <div class="mt-1">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"
                                    title="Hapus">
                                    <i class="tio-add-to-trash"></i>
                                </button>
                            </div>
                    </div>
                </div>`;
            $('#option_price_view_'+data).append(add_new_row_view);

        }

    </script>

    <script>

        $('#product_form').on('submit', function () {
            var formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',[$product['id']])}}',
                // data: $('#product_form').serialize(),
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
                        toastr.success('Produk berhasil diperbarui', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.product.list')}}';
                        }, 2000);
                    }
                },
                error: function (xhr) {
                    toastr.error('Terjadi kesalahan ', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    console.log(xhr.responseText);
                }
            });
        });

        @if($product->main_branch_product?->stock_type == 'fixed')
            $("#product_stock_div").removeClass('d-none')
        @endif

        $("#discount_type").change(function(){
            if(this.value === 'nothing') {
                $("#discount_input").val("")
                $("#discount_input").removeAttr('required')
            } else {
                $("#discount_input").attr('required', true)
            }
        });

        $("#tax_type").change(function(){
            if(this.value === 'nothing') {
                $("#tax_input").val("")
                $("#tax_input").removeAttr('required')
            } else {
                $("#tax_input").attr('required', true)
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
