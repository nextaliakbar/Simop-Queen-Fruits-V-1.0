@extends('layouts.admin.app')

@section('title', 'Pengaturan Profil')

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">Pengaturan</h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-primary" href="{{route('admin.dashboard')}}">
                        <i class="tio-home mr-1"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                            aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                            data-toggle="collapse" data-target="#navbarVerticalNavMenu">
                        <span class="d-flex justify-content-between align-items-center">
                          <span class="h5 mb-0">Navbar Menu</span>

                          <span class="navbar-toggle-default">
                            <i class="tio-menu-hamburger"></i>
                          </span>

                          <span class="navbar-toggle-toggled">
                            <i class="tio-clear"></i>
                          </span>
                        </span>
                    </button>

                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                        <ul id="navbarSettings"
                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="javascript:" id="generalSection">
                                    <i class="tio-user-outlined nav-icon"></i> Informasi Profil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:" id="passwordSection">
                                    <i class="tio-lock-outlined nav-icon"></i> Password
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <form action="{{env('APP_MODE')!='demo'?route('admin.settings'):'javascript:'}}" method="post" enctype="multipart/form-data" id="admin-settings-form">
                @csrf
                    <div class="card mb-3 mb-lg-5" id="generalDiv">
                        <div class="profile-cover">
                            <div class="profile-cover-img-wrapper"></div>
                        </div>

                        <label
                            class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar"
                            for="avatarUploader">
                            <img id="viewer" class="avatar-img"
                                onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'"
                                 src="{{auth('admin')->user()->imageFullPath}}"
                                 alt="Image">

                            <input type="file" name="image" class="js-file-attach avatar-uploader-input"
                                   id="customFileEg1"
                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="avatar-uploader-trigger" for="customFileEg1">
                                <i class="tio-edit avatar-uploader-icon shadow-soft"></i>
                            </label>
                        </label>
                    </div>

                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <fil class="card-title h4"><i class="tio-info"></i>Informasi Profil</fil>
                        </div>

                        <div class="card-body">

                            <div class="row form-group">
                                <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">Nama Lengkap<i
                                        class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Nama Tampilan"></i></label>

                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="f_name" id="firstNameLabel"
                                               placeholder="Nama Depan" aria-label="Your first name"
                                               value="{{auth('admin')->user()->f_name}}">
                                        <input type="text" class="form-control" name="l_name" id="lastNameLabel"
                                               placeholder="Nama Belakang" aria-label="Your last name"
                                               value="{{auth('admin')->user()->l_name}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <label for="phoneLabel" class="col-sm-3 col-form-label input-label">No. Hp</label>

                                <div class="col-sm-9">
                                    <input type="text" class="js-masked-input form-control" name="phone" id="phoneLabel"
                                           placeholder="081234xxxxx" aria-label="081234xxxxx"
                                           value="{{auth('admin')->user()->phone}}"
                                           data-hs-mask-options='{
                                           "template": "081234xxxxx"
                                         }'>
                                </div>
                            </div>

                            <div class="row form-group">
                                <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">Email</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                           value="{{auth('admin')->user()->email}}"
                                           placeholder="Masukkan alamat email" aria-label="Masukkan alamat email">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" id="saveChangesButton" class="btn btn-primary">Simpan Berubah</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="passwordDiv" class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h4 class="card-title"><i class="tio-lock"></i>Ubah Password</h4>
                    </div>

                    <div class="card-body">
                        <form id="changePasswordForm" action="{{route('admin.settings-password')}}" method="post"
                              enctype="multipart/form-data">
                        @csrf

                            <div class="row form-group">
                                <label for="newPassword" class="col-sm-3 col-form-label input-label">Password Baru</label>
                                <div class="col-sm-9 input-group input-group-merge">
                                    <input type="password" name="password" class="js-toggle-password form-control form-control input-field" id="password"
                                           placeholder="Masukkan password baru" required
                                           data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                        }'>
                                    <div id="changePassTarget" class="input-group-append">
                                        <a class="input-group-text" href="javascript:">
                                            <i id="changePassIcon" class="tio-visible-outlined mr-3"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row form-group">
                                <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label">Konfirmasi Password</label>
                                <div class="col-sm-9 input-group input-group-merge">
                                    <input type="password" name="confirm_password" class="js-toggle-password form-control form-control input-field"
                                           id="confirm_password" placeholder="Konfirmasi password baru" required
                                           data-hs-toggle-password-options='{
                                                "target": "#changeConPassTarget",
                                                "defaultClass": "tio-hidden-outlined",
                                                "showClass": "tio-visible-outlined",
                                                "classChangeTarget": "#changeConPassIcon"
                                                }'>
                                    <div id="changeConPassTarget" class="input-group-append">
                                        <a class="input-group-text" href="javascript:">
                                            <i id="changeConPassIcon" class="tio-visible-outlined mr-3"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" id="savePasswordButton" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="stickyBlockEndPoint"></div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/settings.js') }}"></script>
    <script>
        $('#saveChangesButton').click(function() {
            if ('{{ env('APP_MODE') }}' === 'demo') {
                call_demo();
            } else {
                form_alert('admin-settings-form', 'Ingin memperbarui informasi profil?');
            }
        });

        $('#savePasswordButton').click(function() {
            if ('{{ env('APP_MODE') }}' === 'demo') {
                call_demo();
            } else {
                form_alert('changePasswordForm', 'Ingin memperbarui password?');
            }
        });
    </script>
@endpush
