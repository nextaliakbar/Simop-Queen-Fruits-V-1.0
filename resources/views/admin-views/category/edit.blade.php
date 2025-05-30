@extends('layouts.admin.app')

@section('title', $category->parent_id == 0 ? 'Edit Kategori Porduk' : 'Edit Sub Kategori Produk')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/category.png')}}" alt="">
                <span class="page-header-title">
                    {{ $category->parent_id == 0 ? 'Edit Kategori' : 'Edit Sub Kategori' }}
                </span>
            </h2>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <form action="{{route('admin.category.update',[$category['id']])}}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-12">
                                <div class="form-group lang_form" id="en-form">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">Nama{{$category->parent_id == 0 ? ' Kategori' : ' Sub Kategori'}}</label>
                                    <input type="text" name="name" value="{{$category['name']}}"
                                        class="form-control" oninvalid="document.getElementById('en-link').click()"
                                        placeholder="Nama {{$category->parent_id == 0 ? 'Kategori' : 'Sub Kategori'}}" required>
                                </div>
                                <input class="position-area" name="position" value="0">
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="from_part_2 mt-2">
                                    <div class="form-group">
                                        <div class="text-center">
                                            <img width="105" class="rounded-10 border" id="viewer"
                                                src="{{$category->imageFullPath}}" alt="image" />
                                        </div>
                                    </div>
                                </div>
                                <div class="from_part_2">
                                    <label>Gambar {{$category->parent_id == 0 ? ' Kategori' : ' Sub Kategori'}}</label>
                                    <small class="text-danger">* ( Rasio 1:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                            oninvalid="document.getElementById('en-link').click()">
                                        <label class="custom-file-label" for="customFileEg1">Pilih Gambar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="from_part_2">
                                    <div class="form-group">
                                        <div class="text-center max-h-200px overflow-hidden">
                                            <img width="500" class="rounded-10 border" id="viewer2"
                                                src="{{$category->bannerImageFullPath}}" alt="image" />
                                        </div>
                                    </div>
                                </div>
                                <div class="from_part_2">
                                    <label>Banner Gambar {{$category->parent_id == 0 ? ' Kategori' : ' Sub Kategori'}}</label>
                                    <small class="text-danger">* ( Rasio 8:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="banner_image" id="customFileEg2" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                            oninvalid="document.getElementById('en-link').click()">
                                        <label class="custom-file-label" for="customFileEg2">Pilih Gambar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                            <button type="submit" class="btn btn-primary">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        function readURL(input, viewerId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#' + viewerId).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
        });

        $("#customFileEg2").change(function () {
            readURL(this, 'viewer2');
        });
    </script>
@endpush
