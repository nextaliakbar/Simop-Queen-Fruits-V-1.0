@extends('layouts.branch.app')

@section('title', 'Perbarui Produk')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/product.png')}}" alt="">
                <span class="page-header-title">
                    Perbarui Harga Produk
                </span>
            </h2>
        </div>

        <form action="javascript:" method="post" id="set_price_form" enctype="multipart/form-data">
            @csrf

            @php($productByBranch = json_decode($product->product_by_branch, true))
            <?php
            if(isset($productByBranch[0])){
                $price = $productByBranch[0]['price'];
                $discountType = $productByBranch[0]['discount_type'];
                $discount = $productByBranch[0]['discount'];
            }else{
                $price = $product['price'];
                $discountType = $product['discount_type'];
                $discount = $product['discount'];
            }
            ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                <i class="tio-premium"></i>
                                Informasi Produk
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="card p-4" id="">
                                <div class="form-group">
                                    <label class="input-label">Nama Produk</label>
                                    <input type="text" name="" value="{{$product['name']}}" class="form-control" placeholder="Nama produk" readonly required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
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
                                        <label class="input-label">Tipe Stok</label>
                                        <select name="stock_type" class="form-control js-select2-custom" id="stock_type">
                                            @if($product->sub_branch_product)
                                                <option value="unlimited" {{ $product->sub_branch_product?->stock_type == 'unlimited' ? 'selected' : '' }}>Selalu ada</option>
                                                <option value="fixed" {{ $product->sub_branch_product?->stock_type == 'fixed' ? 'selected' : '' }}>Tetap</option>
                                            @else
                                                <option value="unlimited" {{ $main_branch_product?->stock_type == 'unlimited' ? 'selected' : '' }}>Selalu ada</option>
                                                <option value="fixed" {{ $main_branch_product?->stock_type == 'fixed' ? 'selected' : '' }}>Tetap</option>
                                            @endif

                                        </select>
                                    </div>
                                </div>
                                <?php
                                $stock = $product->sub_branch_product?->stock ?? $main_branch_product?->stock ?? null;

                                ?>
                                <div class="col-sm-6 d-none" id="product_stock_div">
                                    <div class="form-group">
                                        <label class="input-label">Stok Produk</label>
                                        <input id="product_stock" type="number" name="product_stock" class="form-control"
                                               value="{{ $stock}}" placeholder="Contoh : 10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card h-100 mt-3">
                        <div class="card-header">
                            <h4 class="mb-0 d-flex gap-2 align-items-center">
                                <i class="tio-dollar"></i>
                                Informasi Harga
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="input-label">Harga</label>
                                        <input type="number" value="{{ $price }}" min="0.1" name="price" class="form-control" step="0.01"
                                               placeholder="Contoh : 5000}" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="input-label">Jenis Diskon</label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="nothing" {{$discountType == null ? 'selected' :''}}>Tidak Ada</option>
                                            <option value="percent" {{$discountType != null ? ($discountType == 'percent'? 'selected' : '') :''}}>Diskon Persentase</option>
                                            <option value="amount" {{$discountType != null ? ($discountType == 'amount'? 'selected' : '') :''}}>Diskon Langsung</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="input-label">Diskon</label>
                                        <input type="number" min="0" value="{{$discount}}"
                                               name="discount" class="form-control" placeholder="Kosongkan jika tidak perlu">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-12">
                    <div class="card mt-5">
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
                                        @if (isset($product->product_by_branch) && count($product->product_by_branch))
                                            @foreach($product->product_by_branch as $branch_product)
                                                @forelse ($branch_product->variations as $key_choice_options=>$item)
                                                    @include('branch-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                                @empty
                                                    <h5 class="text-center">Tidak ada variasi pada produk ini</h5>
                                                @endforelse
                                            @endforeach
                                        @else
                                            @if (isset($product->variations))
                                                @forelse (json_decode($product->variations,true) as $key_choice_options=>$item)
                                                    @if (isset($item["price"]))
                                                        <h5 class="text-center">Produk ini memiliki variasi lama, harap perbarui variasi terlebih dahulu</h5>
                                                        @break
                                                    @else
                                                        @include('branch-views.product.partials._new_variations',['item'=>$item,'key'=>$key_choice_options+1])
                                                    @endif
                                                @empty
                                                    <h5 class="text-center">Tidak ada variasi pada produk ini</h5>
                                                @endforelse
                                            @endif
                                        @endif
                                    </div>
                                </div>

                            </div>
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

@endsection

@push('script_2')
    <script>
        "use strict";

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

        $('#set_price_form').on('submit', function () {
            var formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('branch.product.set-price-update',[$product['id']])}}',
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
                            location.href = '{{route('branch.product.list')}}';
                        }, 2000);
                    }
                }
            });
        });

        @if($product->sub_branch_product)
        @php($stock = $product->sub_branch_product?->stock ?? null)
        @if( $stock == 'fixed')
        $("#product_stock_div").removeClass('d-none')
        @endif
        @else
        @php($stock = $main_branch_product?->stock ?? null)

        @if($stock == 'fixed')
        $("#product_stock_div").removeClass('d-none')
        @endif
        @endif


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
