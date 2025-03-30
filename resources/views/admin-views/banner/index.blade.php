@extends('layouts.admin.app')

@section('title', 'Tambah Banner Baru')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/banner.png')}}" alt="">
                <span class="page-header-title">
                    Tambah Banner Baru
                </span>
            </h2>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.banner.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card banner-form">
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="input-label">Judul</label>
                                            <input type="text" name="title" class="form-control" placeholder="Banner Baru" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">Jenis Banner<span class="input-label-secondary">*</span></label>
                                            <select name="item_type" class="custom-select">
                                                <option selected disabled>Pilih Jenis Banner</option>
                                                <option value="product">Produk</option>
                                                <option value="category">Kategori</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="type-product">
                                            <label class="input-label">Produk <span class="input-label-secondary">*</span></label>
                                            <select name="product_id" class="custom-select">
                                                <option selected disabled>Pilih Produk</option>
                                                @foreach($products as $product)
                                                    <option value="{{$product['id']}}">{{$product['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group type-category" id="type-category">
                                            <label class="input-label">Kategori <span class="input-label-secondary">*</span></label>
                                            <select name="category_id" class="custom-select">
                                                <option selected disabled>Pilih Kategori</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <label class="mb-0">Gambar Banner</label>
                                                <small class="text-danger">* ( Rasio 2:1 )</small>
                                            </div>
                                            <div class="d-flex justify-content-center mt-4">
                                                <div class="upload-file">
                                                    <input type="file" name="image" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" class="upload-file__input">
                                                    <div class="upload-file__img_drag upload-file__img">
                                                        <img width="465" id="viewer" src="{{asset('assets/admin/img/icons/upload_img2.png')}}" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3 mt-4">
                                    <button type="reset" id="reset" class="btn btn-secondary">Atur Ulang</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        $("select[name='item_type']").change(function() {
            var selectedValue = $(this).val();
            show_item(selectedValue);
        });

        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
        });


        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
    </script>

    <script>
        $(".js-select2-custom").select2({
            placeholder: "Pilih Jenis Banner",
            allowClear: true
        });
    </script>
@endpush
