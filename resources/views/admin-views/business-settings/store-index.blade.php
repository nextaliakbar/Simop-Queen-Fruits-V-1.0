@extends('layouts.admin.app')

@section('title', 'Pengaturan Bisnis')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title">
                    Pengaturan Bisnis
                </span>
            </h2>
        </div>

        @include('admin-views.business-settings.partials._business-setup-inline-menu')

        <form action="{{route('admin.business-settings.store.update-setup')}}" method="post">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="tio-user"></i>
                        Informasi Toko
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php($store_name=\App\CentralLogics\Helpers::get_business_settings('store_name'))
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label text-capitalize">Nama Toko</label>
                                <input type="text" value="{{$store_name}}"
                                       name="store_name" class="form-control" placeholder="Contoh : Toko xyz">
                            </div>
                        </div>

                        @php($phone=\App\CentralLogics\Helpers::get_business_settings('phone'))
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label text-capitalize">No. Hp</label>
                                <input type="text" value="{{$phone}}"
                                       name="phone" class="form-control" placeholder="Contoh : 081234xxxxx">
                            </div>
                        </div>

                        @php($email=\App\CentralLogics\Helpers::get_business_settings('email_address'))
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label text-capitalize">Email</label>
                                <input type="email" value="{{$email}}"
                                       name="email" class="form-control" placeholder="Contoh : xyz@gmail.com">
                            </div>
                        </div>

                        @php($address=\App\CentralLogics\Helpers::get_business_settings('address'))
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label text-capitalize">Alamat</label>
                                <textarea name="address" class="form-control" placeholder="Contoh : Jl. xyz, ....">{{$address}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="tio-briefcase mr-1"></i>
                        Informasi Bisnis
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-lg-4 col-sm-6 mb-4">
                            @php($sp=\App\CentralLogics\Helpers::get_business_settings('self_pickup'))
                            <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <label class="text-dark mb-0">Ambil Ditempat
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Ketika opsi ini diaktifkan, pelanggan dapat mengambil pesanan ditoko">
                                        </i>
                                    </label>
                                </div>
                                <label class="switcher">
                                    <input class="switcher_input" type="checkbox" name="self_pickup" {{$sp == null || $sp == 0? '' : 'checked'}} id="self_pickup_btn">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mb-4">
                            @php($del=\App\CentralLogics\Helpers::get_business_settings('delivery'))
                            <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <label class="text-dark mb-0">Pengiriman
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Jika opsi ini dimatikan, pengguna tidak akan menerima pengiriman pesanan">
                                        </i>
                                    </label>
                                </div>
                                <label class="switcher">
                                    <input readonly class="switcher_input" type="checkbox" name="delivery"  {{$del == null || $del == 0? '' : 'checked'}} id="delivery_btn">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mb-4">
                            @php($google_map_status=\App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                            <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <label class="text-dark mb-0">Status Google Map
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Ketika opsi ini diaktifkan, peta google maps akan ditampilkan diseluruh aplikasi">
                                        </i>
                                    </label>
                                </div>
                                <label class="switcher">
                                    <input class="switcher_input" type="checkbox" name="google_map_status" {{$google_map_status == null || $google_map_status == 0? '' : 'checked'}} id="google_map_status">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mb-4">
                            @php($admin_order_notification=\App\CentralLogics\Helpers::get_business_settings('admin_order_notification'))
                            <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <label class="text-dark mb-0">Notifikasi Order
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Admin/cabang akan menerima notifikasi suara dari setiap pesanan yang dilakukan pelanggan">
                                        </i>
                                    </label>
                                </div>
                                <label class="switcher">
                                    <input class="switcher_input" type="checkbox" name="admin_order_notification" {{$admin_order_notification == null || $admin_order_notification == 0? '' : 'checked'}} id="admin_order_notification">
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn--container mt-4">
                <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                <button type="submit" class="btn btn-primary call-demo">Simpan</button>
            </div>
        </form>

    </div>

    <!-- Modal for checking -->
    <div class="modal fade" id="modalUncheckedDistanceNotExist" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="text-center mb-5">
                            <img src="{{ asset('assets/admin/svg/components/map-icon.svg') }}" alt="Unchecked Icon" class="mb-5">
                            <h4>Kamu Yakin?</h4>
                            <p>Apakah anda yakin ingin menonaktifkan google maps? menonaktifkan google maps akan memengaruhi pengaturan berikut:</p>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <a class="d-flex align-items-center border rounded px-3 py-2 gap-2" href="{{ route('admin.customer.list') }}" target="_blank">
                                    <img src="{{ asset('assets/admin/svg/components/people.svg') }}" width="21" alt="">
                                    <span>Lokasi Pelanggan</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a class="d-flex align-items-center border rounded px-3 py-2 gap-2" href="" target="_blank">
                                    <img src="{{ asset('assets/admin/svg/components/branch.svg') }}" width="21" alt="">
                                    <span>Wilayah Cakupan Cabang</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a class="d-flex align-items-center border rounded px-3 py-2 gap-2" href="{{ route('admin.delivery-man.list') }}" target="_blank">
                                    <img src="{{ asset('assets/admin/svg/components/delivery-car.svg') }}" width="21" alt="">
                                    <span>Lokasi Kurir</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a class="d-flex align-items-center border rounded px-3 py-2 gap-2" href="" target="_blank">
                                    <img src="{{ asset('assets/admin/svg/components/delivery-charge.svg') }}" width="21" alt="">
                                    <span>Pengaturan Biaya Pengiriman</span>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center my-4 gap-3">
                            <button class="btn btn-secondary ml-2" id="cancelButtonNotExist">Batal</button>
                            <button class="btn btn-danger" data-dismiss="modal">Ok, Matikan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Checking -->
    <div class="modal fade" id="modalCheckedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="my-4">
                            <img src="{{ asset('assets/admin/svg/components/map-icon.svg') }}" alt="Checked Icon">
                        </div>
                        <div class="my-4">
                            <h4>Hidupkan Google Maps?</h4>
                            <p>Dengan mengaktifkan opsi ini, anda juga dapat melihat peta di seluruh aplikasi. Anda sekarang juga dapat mengatur biaya pengiriman berdasarkan jarak(km) dari
                                <a class="" target="_blank" href="">Halaman Ini</a>
                            </p>
                        </div>
                        <div class="my-4">
                            <button class="btn btn-primary" data-dismiss="modal">Ya, Hidupkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script>
        @php($time_zone=\App\Models\BusinessSetting::where('key','time_zone')->first())
        @php($time_zone = $time_zone->value ?? null)
        $('[name=time_zone]').val("{{$time_zone}}");

        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        let language = <?php echo($language); ?>;
        $('[id=language]').val(language);

        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + viewer).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this, 'viewer');
        });

        $("#customFileEg2").change(function() {
            readURL(this, 'viewer_2');
        });

        $("#language").on("change", function () {
            $("#alert_box").css("display", "block");
        });
    </script>

    <script>
        @if(env('APP_MODE')=='demo')
        function maintenance_mode() {
            toastr.info('{{translate('Disabled for demo version!')}}')
        }
        @else
        function maintenance_mode() {
            Swal.fire({
                title: 'Kamu Yakin?',
                text: 'Ingin mematikan mode pemeliharaan?',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: 'Tidak',
                confirmButtonText:'Ya',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: '',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            toastr.success(data.message);
                            location.reload();
                        },
                        complete: function () {
                            $('#loading').hide();
                        },

                    });
                } else {
                    location.reload();
                }
            })
        }
        @endif

        function currency_symbol_position(route) {
            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success(data.message);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        $(document).on('ready', function () {
            @php($country=\App\CentralLogics\Helpers::get_business_settings('country')??'BD')
            $("#country option[value='{{$country}}']").attr('selected', 'selected').change();
        })
    </script>

    <script>
        $(document).ready(function() {
            function validateCheckboxes() {
                if (!$('#self_pickup_btn').prop('checked') && !$('#delivery_btn').prop('checked')) {
                    if (event.target.id === 'self_pickup_btn') {
                        $('#delivery_btn').prop('checked', true);
                    } else {
                        $('#self_pickup_btn').prop('checked', true);
                    }
                }
            }

            $('#self_pickup_btn').change(validateCheckboxes);
            $('#delivery_btn').change(validateCheckboxes);
        });

    </script>
    <script>
        $('.maintenance-mode-show').click(function (){
            $('#maintenance-mode-modal').modal('show');
        });

        $(document).ready(function() {
            var initialMaintenanceMode = $('#maintenance-mode-input').is(':checked');

            $('#maintenance-mode-modal').on('show.bs.modal', function () {
                var initialMaintenanceModeModel = $('#maintenance-mode-input').is(':checked');
                $('#maintenance-mode-checkbox').prop('checked', initialMaintenanceModeModel);
            });

            $('#maintenance-mode-modal').on('hidden.bs.modal', function () {
                $('#maintenance-mode-input').prop('checked', initialMaintenanceMode);
            });

            $('#cancelButton').click(function() {
                $('#maintenance-mode-input').prop('checked', initialMaintenanceMode);
                $('#maintenance-mode-modal').modal('hide');
            });

            $('#maintenance-mode-checkbox').change(function() {
                $('#maintenance-mode-input').prop('checked', $(this).is(':checked'));
            });
        });

        $(document).ready(function() {
            $('#advanceFeatureToggle').click(function(event) {
                event.preventDefault();
                $('#advanceFeatureSection').show();
                $('#advanceFeatureButtonDiv').hide();
            });

            $('#seeLessToggle').click(function(event) {
                event.preventDefault();
                $('#advanceFeatureSection').hide();
                $('#advanceFeatureButtonDiv').show();
            });

            $('#allSystem').change(function() {
                var isChecked = $(this).is(':checked');
                $('.system-checkbox').prop('checked', isChecked);
            });

            // If any other checkbox is unchecked, also uncheck "All System"
            $('.system-checkbox').not('#allSystem').change(function() {
                if (!$(this).is(':checked')) {
                    $('#allSystem').prop('checked', false);
                } else {
                    if ($('.system-checkbox').not('#allSystem').length === $('.system-checkbox:checked').not('#allSystem').length) {
                        $('#allSystem').prop('checked', true);
                    }
                }
            });

            $(document).ready(function() {
                var startDate = $('#startDate');
                var endDate = $('#endDate');
                var dateError = $('#dateError');

                function updateDatesBasedOnDuration(selectedOption) {
                    if (selectedOption === 'one_day' || selectedOption === 'one_week') {
                        var now = new Date();
                        var timezoneOffset = now.getTimezoneOffset() * 60000;
                        var formattedNow = new Date(now.getTime() - timezoneOffset).toISOString().slice(0, 16);

                        if (selectedOption === 'one_day') {
                            var end = new Date(now);
                            end.setDate(end.getDate() + 1);
                        } else if (selectedOption === 'one_week') {
                            var end = new Date(now);
                            end.setDate(end.getDate() + 7);
                        }

                        var formattedEnd = new Date(end.getTime() - timezoneOffset).toISOString().slice(0, 16);

                        startDate.val(formattedNow).prop('readonly', false).prop('required', true);
                        endDate.val(formattedEnd).prop('readonly', false).prop('required', true);
                        $('.start-and-end-date').removeClass('opacity');
                        dateError.hide();
                    } else if (selectedOption === 'until_change') {
                        startDate.val('').prop('readonly', true).prop('required', false);
                        endDate.val('').prop('readonly', true).prop('required', false);
                        $('.start-and-end-date').addClass('opacity');
                        dateError.hide();
                    } else if (selectedOption === 'customize') {
                        startDate.prop('readonly', false).prop('required', true);
                        endDate.prop('readonly', false).prop('required', true);
                        $('.start-and-end-date').removeClass('opacity');
                        dateError.hide();
                    }
                }

                function validateDates() {
                    var start = new Date(startDate.val());
                    var end = new Date(endDate.val());
                    if (start > end) {
                        dateError.show();
                        startDate.val('');
                        endDate.val('');
                    } else {
                        dateError.hide();
                    }
                }

                // Initial load
                var selectedOption = $('input[name="maintenance_duration"]:checked').val();
                updateDatesBasedOnDuration(selectedOption);

                // When maintenance duration changes
                $('input[name="maintenance_duration"]').change(function() {
                    var selectedOption = $(this).val();
                    updateDatesBasedOnDuration(selectedOption);
                });

                // When start date or end date changes
                $('#startDate, #endDate').change(function() {
                    $('input[name="maintenance_duration"][value="customize"]').prop('checked', true);
                    startDate.prop('readonly', false).prop('required', true);
                    endDate.prop('readonly', false).prop('required', true);
                    validateDates();
                });
            });

        });

        $('#google_map_status').change(function() {
            if ($(this).is(':checked')) {
                $('#modalCheckedModal').modal('show');
            } else {
                $.ajax({
                    url: '{{ route('admin.business-settings.store.check-distance-based-delivery') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.hasDistanceBasedDelivery) {
                            $('#modalUncheckedDistanceExist').modal('show');
                            $('#google_map_status').prop('checked', true);
                        }else{
                            $('#modalUncheckedDistanceNotExist').modal('show');
                        }
                    }
                });
            }
        });

        $('#cancelButtonNotExist').click(function() {
            $('#google_map_status').prop('checked', true);
            $('#modalUncheckedDistanceNotExist').modal('hide');
        });

    </script>
@endpush
