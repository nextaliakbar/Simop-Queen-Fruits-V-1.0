<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cabang | Masuk</title>

    {{-- @php($icon = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first()?->value??'') --}}
    <link rel="shortcut icon" href="{{asset('assets/admin/img/logo_store_2.png')}}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/admin/img//logo_store_2.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">

    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
</head>

<body>
    <main id="content" role="main" class="main">
        <div class="auth-wrapper">
            <div class="auth-wrapper-left"></div>

            <div class="auth-wrapper-right">
                <div class="auth-wrapper-form">
                    <form class="" id="form-id" action="{{route('branch.auth.login')}}" method="post">
                        @csrf
                        <div class="auth-header">
                            <div class="mb-5">
                                <h2 class="title">
                                    <span class="c1">SIM<span class="c3">OP</span></span>
                                    <img src="{{asset('assets/admin/img/logo_store_2.png')}}" 
                                    alt="" style="width: 75px; height: 75px">
                                </h2>
                                <p class="mb-3 text-capitalize text-dark">Sistem Informasi Manajemen & Operasional</p>
                                <p class="mb-0 text-capitalize text-dark">Ingin masuk ke cabang utama atau admin?
                                    <a href="{{route('admin.auth.login')}}">Klik disini</a>
                                </p>
                            </div>
                        </div>

                        <div class="js-form-message form-group">
                            <label class="input-label text-capitalize" for="signinSrEmail">Email</label>

                            <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                                tabindex="1" placeholder="contoh@email.com" aria-label="email@address.com"
                                required data-msg="Silahkan masukkan email yang valid">
                        </div>

                        <div class="js-form-message form-group">
                            <label class="input-label" for="signupSrPassword" tabindex="0">
                                <span class="d-flex justify-content-between align-items-center">
                                Password
                                </span>
                            </label>

                            <div class="input-group input-group-merge">
                                <input type="password" class="js-toggle-password form-control form-control-lg"
                                    name="password" id="signupSrPassword" placeholder="password"
                                    aria-label="password" required
                                    data-msg="Password tidak valid, silahkan ulangi lagi"
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

                        @php($recaptcha = \App\CentralLogics\Helpers::get_business_settings('recaptcha'))

                        @if(isset($recaptcha) && $recaptcha['status'] == 1)
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                            <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0" >

                            <div class="row p-2 d-none" id="reload-captcha">
                                <div class="col-5 pr-0">
                                    <input type="text" class="form-control form-control-lg default-captcha-value" name="default_captcha_value" value=""
                                           placeholder="Captcha" autocomplete="off">
                                </div>
                                <div class="col-7 input-icons bg-white rounded">
                                    <a class="re-captcha">
                                        <img src="{{ URL('/admin/auth/code/captcha/1') }}" class="input-field default-recaptcha" id="default_recaptcha_id">
                                        <i class="tio-refresh icon"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row p-2">
                                <div class="col-5 pr-0">
                                    <input type="text" class="form-control form-control-lg default-captcha-value" name="default_captcha_value" value=""
                                        placeholder="Captcha" autocomplete="off">
                                </div>
                                <div class="col-7 input-icons bg-white rounded">
                                    <a class="re-captcha">
                                        <img src="{{ URL('/admin/auth/code/captcha/1') }}" class="input-field default-recaptcha" id="default_recaptcha_id">
                                        <i class="tio-refresh icon"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-lg btn-block btn-primary" id="signInBtn">Masuk</button>                  
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/toastr.js"></script>

    {!! Toastr::message() !!}

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

        $(document).on('ready', function () {
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init()
            });

            $('.js-validate').each(function () {
                $.HSCore.components.HSValidation.init($(this));
            });

            $(".re-captcha").click(function() {
                console.log("Click");
                re_captcha();
            });

            $(".copy-cred").click(function() {
                copy_cred();
            });
        });
    </script>

    @if(isset($recaptcha) && $recaptcha['status'] == 1)
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
            <script>
                "use strict";
                $('#signInBtn').click(function (e) {

                    if( $('#set_default_captcha_value').val() == 1){
                        $('#form-id').submit();
                        return true;
                    }

                    e.preventDefault();

                    if (typeof grecaptcha === 'undefined') {
                        toastr.error('Kunci recaptcha yang diberikan tidak valid. Silakan periksa konfigurasi recaptcha.');

                        $('#reload-captcha').removeClass('d-none');
                        $('#set_default_captcha_value').val('1');

                        return;
                    }

                    grecaptcha.ready(function () {
                        grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                            document.getElementById('g-recaptcha-response').value = token;
                            document.querySelector('form').submit();
                        });
                    });

                    window.onerror = function(message) {
                        var errorMessage = 'Terjadi kesalahan. Silakan periksa konfigurasi recaptcha';
                        if (message.includes('Invalid site key')) {
                            errorMessage = 'Kunci recaptcha yang diberikan tidak valid. Silakan periksa konfigurasi recaptcha.';
                        } else if (message.includes('not loaded in api.js')) {
                            errorMessage = 'reCAPTCHA API tidak dapat dimuat. Silakan periksa konfigurasi API recaptcha.';
                        }

                        $('#reload-captcha').removeClass('d-none');
                        $('#set_default_captcha_value').val('1');

                        toastr.error(errorMessage)
                        return true;
                    };
                });
            </script>
    @endif
        <script>
            "use strict";

            function re_captcha() {
                let $url = "{{ URL('/admin/auth/code/captcha') }}";
                $url = $url + "/" + Math.random();
                document.getElementById('default_recaptcha_id').src = $url;
                console.log('url: '+ $url);
            }
        </script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
