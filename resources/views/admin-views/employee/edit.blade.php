@extends('layouts.admin.app')

@section('title', 'Perbarui Karyawan')

@push('css_or_js')
    <link href="{{asset('assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                Perbarui Karyawan
            </span>
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form action="{{route('admin.employee.update',[$employee['id']])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2"><span class="tio-user"></span> Informasi Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama</label>
                                    <input type="text" name="name" value="{{$employee['f_name'] . ' ' . $employee['l_name']}}" class="form-control" id="name">
                                </div>
                                <div class="form-group">
                                    <label for="phone">No. Hp</label>
                                    <input type="tel" value="{{$employee['phone']}}" required name="phone" class="form-control" id="phone"
                                        placeholder="Contoh : 081234xxxxxx">
                                </div>
                                <div class="form-group">
                                    <label for="name">Peran</label>
                                    <select class="custom-select" name="role_id">
                                            <option value="0" selected disabled>---Pilih Peran---</option>
                                            @foreach($roles as $role)
                                                <option
                                                    value="{{$role->id}}" {{$role['id']==$employee['admin_role_id']?'selected':''}}>{{$role->name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="identity_type">Jenis Identitas</label>
                                    <select class="custom-select" name="identity_type" id="identity_type">
                                        <option value="ktp" selected>KTP</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="identity_number">Nomor Identitas</label>
                                    <input type="text" name="identity_number" class="form-control" id="identity_number" required value="{{$employee->identity_number}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="text-center mb-3">
                                        <img width="180" class="rounded-10 border" id="viewer"
                                             src="{{$employee->imageFullPath}}" alt="Employee thumbnail"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Foto Karyawan</label>
                                        <span class="text-danger">( Rasio 1:1 )</span>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileUpload">Pilih File</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="input-label">Gambar Identitas</label>
                                    <div class="product--coba">
                                        <div class="row g-2" id="coba">
                                            @foreach($employee->identityImageFullPath as $identification_image)
                                                <div class="two__item w-50">
                                                    <div class="max-h-140px existing-item">
                                                        <img src="{{$identification_image}}" alt="Identity Image">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2"><span class="tio-user"></span> Informasi Akun</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" value="{{$employee['email']}}" name="email" class="form-control" id="email"
                                        placeholder="Contoh : employee@gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <small> ( Masukkan password baru jika ingin merubah password lama )</small>
                                    <div class="input-group input-group-merge">
                                        <input type="password" name="password" class="js-toggle-password form-control form-control input-field" id="password"
                                               data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                                        <div id="changePassTarget" class="input-group-append">
                                            <a class="input-group-text" href="javascript:">
                                                <i id="changePassIcon" class="tio-visible-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="confirm_password">Konfirmasi Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" name="confirm_password" class="js-toggle-password form-control form-control input-field"
                                               id="confirm_password" placeholder="Konfirmasi Password"
                                               data-hs-toggle-password-options='{
                                                "target": "#changeConPassTarget",
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": "#changeConPassIcon"
                                                }'>
                                        <div id="changeConPassTarget" class="input-group-append">
                                            <a class="input-group-text" href="javascript:">
                                                <i id="changeConPassIcon" class="tio-visible-outlined"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
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
    @include('admin-views.employee.partials.image-process._image-crop-modal',['modal_id'=>'employee-image-modal'])
</div>
@endsection

@push('script_2')
    <script src="{{asset('assets/back-end')}}/js/select2.min.js"></script>
    <script src="{{asset('assets/admin/js/image-upload.js')}}"></script>
    <script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script>
        "use strict";

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });

        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '230px',
                groupClassName: 'col-6 col-lg-4 ',
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
                    toastr.error('Please only input png or jpg type file', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('File size too big', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>

    @include('admin-views.employee.partials.image-process._script',[
   'id'=>'employee-image-modal',
   'height'=>200,
   'width'=>200,
   'multi_image'=>false,
   'route'=>null
   ])
@endpush
