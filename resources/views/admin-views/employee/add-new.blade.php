@extends('layouts.admin.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                Tambah Karyawan Baru
            </span>
        </h2>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form action="{{route('admin.employee.add-new')}}" method="post" enctype="multipart/form-data">
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
                                    <input type="text" name="name" class="form-control" id="name"
                                        value="{{old('name')}}" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">No. Hp</label>
                                    <input type="tel" name="phone" value="{{old('phone')}}" class="form-control" id="phone"
                                        placeholder="Contoh : 081234xxxxxx" required>
                                </div>

                                <div class="form-group">
                                    <label for="role_id">Peran</label>
                                    <select class="custom-select" name="role_id">
                                        <option value="0" selected disabled>---Pilih Peran---</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->id}}" {{old('role_id')==$role->id?'selected':''}}>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="identity_type">Jenis Identitas</label>
                                    <select class="custom-select" name="identity_type" id="identity_type" required>
                                        <option selected disabled>---Pilih Jenis Identitas---</option>
                                        <option value="ktp">KTP</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="identity_number">Nomor Identitas</label>
                                    <input type="text" name="identity_number" class="form-control" id="identity_number" required value="{{old('identity_number')}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="text-center mb-3">
                                        <img width="180" class="rounded-10 border" id="viewer"
                                            src="{{asset('assets/admin/img/400x400/img2.jpg')}}" alt="image"/>
                                    </div>
                                    <label for="name">Foto Karyawan</label>
                                    <span class="text-danger">( Rasio 1:1 )</span>
                                    <div class="form-group">
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label" for="customFileUpload">Pilih File</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="input-label">Gambar Identitas</label>
                                    <div>
                                        <div class="row" id="coba"></div>
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
                                    <input type="email" name="email" value="{{old('email')}}" class="form-control" id="email"
                                        placeholder="Contoh : employee@gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" name="password" class="js-toggle-password form-control form-control input-field" id="password" required
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
                                               id="confirm_password" placeholder="Konfirmasi Password" required
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
                    <button type="reset" id="reset" class="btn btn-secondary">Atur Ulang</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src="{{asset('assets/admin/js/vendor.min.js')}}"></script>
    <script src="{{asset('assets/admin')}}/js/select2.min.js"></script>
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
                groupClassName: 'col-6 col-lg-4',
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
                    toastr.error('File yang diizinkan hanya png dan jpg', {
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
@endpush
