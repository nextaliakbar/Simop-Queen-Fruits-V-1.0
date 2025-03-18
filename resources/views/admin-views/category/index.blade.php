@extends('layouts.admin.app')

@section('title', 'Tambah Kategori Produk')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/category.png')}}" alt="">
                <span class="page-header-title">
                    Tambah Kategori Produk
                </span>
            </h2>
        </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card card-body">
                    <form action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-12">
                                <div class="form-group lang_form" id="en-form">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">Nama Kategori</label>
                                    <input type="text" name="name" class="form-control" maxlength="255"
                                        placeholder="Nama Kategori" required>
                                </div>
                                <input name="position" value="0" class="d--none">
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="from_part_2 mt-2">
                                    <div class="form-group">
                                        <div class="text-center">
                                            <img width="105" class="rounded-10 border" id="viewer"
                                                src="{{ asset('assets/admin/img/400x400/img2.jpg') }}" alt="image" />
                                        </div>
                                    </div>
                                </div>
                                <div class="from_part_2">
                                    <label>Gambar Kategori</label>
                                    <small class="text-danger">* ( Rasio 1:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required
                                            oninvalid="document.getElementById('en-link').click()">
                                        <label class="custom-file-label" for="customFileEg1">Pilih Gambar</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="from_part_2 mb-4 px-4">
                                    <div class="form-group">
                                        <div class="text-center max-h-200px overflow-hidden">
                                            <img width="500" class="rounded-10 border" id="viewer2"
                                                src="{{ asset('assets/admin/img/900x400/img1.jpg') }}" alt="image" />
                                        </div>
                                    </div>
                                </div>
                                <div class="from_part_2">
                                    <label>Gambar Banner</label>
                                    <small class="text-danger">* ( Rasio 8:1 )</small>
                                    <div class="custom-file">
                                        <input type="file" name="banner_image" id="customFileEg2" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required
                                            oninvalid="document.getElementById('en-link').click()">
                                        <label class="custom-file-label" for="customFileEg2">Pilih Gambar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-3">
                            <button type="reset" id="reset" class="btn btn-secondary">Atur Ulang</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
            </div>
        </div>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-top px-card pt-4">
                    <div class="row justify-content-between align-items-center gy-2">
                        <div class="col-sm-4 col-md-6 col-lg-8">
                            <h5 class="d-flex gap-1 mb-0">
                                Daftar Kategori Produk
                                <span class="badge badge-soft-dark rounded-50 fz-12">{{ $categories->total() }}</span>
                            </h5>
                        </div>
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="Cari berdasarkan nama kategori" aria-label="Cari"
                                        value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="py-4">
                    <div class="table-responsive datatable-custom">
                        <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Gambar Kategori</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Nomor Urut Prioritas</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($categories as $key=>$category)
                                <tr>
                                    <td>{{$categories->firstitem()+$key}}</td>
                                    <td>
                                        <div>
                                            <img width="50" class="avatar-img rounded" src="{{$category->imageFullPath}}"  alt="">
                                        </div>
                                    </td>
                                    <td><div class="text-capitalize">{{$category['name']}}</div></td>
                                    <td>
                                        <div class="">
                                            <label class="switcher">
                                                <input class="switcher_input status-change" type="checkbox" {{$category['status']==1? 'checked' : ''}} id="{{$category['id']}}"
                                                       data-url="{{route('admin.category.status',[$category['id'],1])}}"
                                                >
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="">
                                            <select name="priority" class="custom-select redirect-url-value"
                                                    data-url="{{ route('admin.category.priority', ['id' => $category['id'], 'priority' => '']) }}">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ $category->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info btn-sm edit square-btn"
                                            href="{{route('admin.category.edit',[$category['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm delete square-btn form-alert"
                                                data-id="category-{{$category['id']}}" data-message="Ingin menghapus kategori ini?">
                                                <i class="tio-delete"></i>
                                            </button>
                                        </div>
                                        <form action="{{route('admin.category.delete',[$category['id']])}}"
                                            method="post" id="category-{{$category['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4 px-3">
                        <div class="d-flex justify-content-lg-end">
                            {!! $categories->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            

@endsection

@push('script_2')
{{--    <script src="{{asset('assets/admin/js/read-url.js')}}"></script>--}}
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


       function change_priority(id, priority, message) {
           Swal.fire({
               title: 'Kamu yakin?',
               text: message,
               type: 'warning',
               showCancelButton: true,
               cancelButtonColor: 'default',
               confirmButtonColor: '#FC6A57',
               cancelButtonText: 'Tidak',
               confirmButtonText: 'Ya',
               reverseButtons: true
           }).then((result) => {
               if (result.value) {
                   const csrfToken = $('meta[name="csrf-token"]').attr('content');

                   const formData = new FormData();
                   formData.append('_token', csrfToken);
                   formData.append('id', id);
                   formData.append('priority', priority);

                   $.ajax({
                       url: "{{ route('admin.category.priority') }}",
                       method: "POST",
                       data: formData,
                       processData: false,
                       contentType: false,
                       success: function(response) {
                           toastr.success("Nomor urut prioritas berhasil diperbarui");
                           setTimeout(function() {
                               location.reload();
                           }, 2000);
                       },
                       error: function(xhr) {
                           toastr.error("Nomor urut prioritas gagal diperbarui");
                       }
                   });
               }
           })
       }
    </script>

@endpush
