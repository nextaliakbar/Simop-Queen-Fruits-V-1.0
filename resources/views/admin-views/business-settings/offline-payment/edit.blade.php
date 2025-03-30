@extends('layouts.admin.app')

@section('title', 'Metode Pembayaran Offline')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title">
                    Perbarui Metode Pembayaran Offline
                </span>
            </h2>
        </div>

        @include('admin-views.business-settings.partials._business-setup-inline-menu')

        <form action="{{route('admin.business-settings.web-app.third-party.offline-payment.update', [$method['id']])}}" method="post">
            @csrf
            <div class="card mb-3">
                <div class="card-header">
                    <div class="justify-content-between align-items-center gy-2">
                        <h4 class="mb-0">
                            <i class="tio-settings mr-1"></i>
                            Informasi Pembayaran
                        </h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" id="add-payment-method-field"><i class="tio-add"></i>Tambah Bidang Baru</button>
                    </div>
                </div>

                <div class="card card-body mb-3">
                    <div class="d-flex align-items-end gap-3 mb-4">
                        <div class="flex-grow-1">
                            <label class="input-label">Nama Metode Pembayaran</label>
                            <input type="text" maxlength="255" name="method_name" id="method_name" class="form-control"
                                  value="{{ $method['method_name'] }}" placeholder="Contoh : Transfer Bank" required>
                        </div>
                    </div>

                    @if($method['method_fields'][0])
                        @php($field = $method['method_fields'][0])
                        <div class="d-flex align-items-end gap-3 mb-4 flex-wrap">
                            <div class="flex-grow-1">
                                <div>
                                    <label class="input-label">Nama Bidang</label>
                                    <input type="text" name="field_name[]" class="form-control" maxlength="255"
                                          value="{{ $field['field_name'] }}" placeholder="Contoh : Transfer Bank" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div>
                                    <label class="input-label">Data </label>
                                    <input type="text" name="field_data[]" class="form-control" maxlength="255"
                                           value="{{ $field['field_data'] }}" placeholder="Contoh : 1234567890" required>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="method-field">
                        @foreach($method['method_fields'] as $key=>$field)
                            @if($key>0)
                                <div class="d-flex align-items-end gap-3 mb-4 flex-wrap">
                                    <div class="flex-grow-1">
                                        <div>
                                            <label class="input-label">Nama Bidang </label>
                                            <input type="text" name="field_name[]" class="form-control" maxlength="255"
                                                   value="{{ $field['field_name'] }}" placeholder="Contoh : Bank BRI" required>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div>
                                            <label class="input-label">Data </label>
                                            <input type="text" name="field_data[]" class="form-control" maxlength="255"
                                                   value="{{ $field['field_data'] }}" placeholder="Contoh : 123456789" required>
                                        </div>
                                    </div>
                                    <div class="" data-toggle="tooltip" data-placement="top">
                                        <div class="btn btn-outline-danger delete" onclick="delete_input_field({{$key}})">
                                            <i class="tio-delete"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <div class="justify-content-between align-items-center gy-2">
                        <h4 class="mb-0 align-items-center">
                            <i class="tio-settings mr-1"></i>
                            Kebutuhan Informasi Dari Pelanggan
                            <i class="tio-info ml-1" data-toggle="tooltip" title="Tambahkan setidaknya satu bidanng informasi"></i>
                        </h4>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" id="add-payment-information-field"><i class="tio-add"></i>Tambah Bidang Baru</button>
                    </div>
                </div>

                <div class="card card-body mb-3">
                    <div class="d-flex align-items-end gap-3 mb-4">
                        <div class="flex-grow-1">
                            <label class="input-label">Catatan Pembayaran</label>
                            <textarea name="payment_note" class="form-control" id="payment_note"
                                      data-toggle="tooltip" title="Field is not editable" style="background-color: #e9ecef;" readonly>{{ $method['payment_note'] }}</textarea>
                        </div>
                    </div>

                    <div id="information-field">
                        @foreach($method['method_informations'] as $key=>$information)
                            <div class="d-flex align-items-end gap-3 mb-4 flex-wrap" id="information-row--{{ $method['id'] }}">
                                <div class="flex-grow-1">
                                    <div class="">
                                        <label class="input-label">Nama Bidang </label>
                                        <input type="text" name="information_name[]" class="form-control" maxlength="255"
                                               value="{{ $information['information_name'] }}" placeholder="" id="information_name_${count_info}" required>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="">
                                        <label class="input-label">Nama Bidang Pengganti </label>
                                        <input type="text" name="information_placeholder[]" class="form-control" maxlength="255"
                                               value="{{ $information['information_placeholder'] }}" placeholder="" required>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-10 mb-2">
                                        <input class="custom-control" type="checkbox" name="information_required[]"  {{$information['information_required'] ? 'checked' : ''}}>
                                        <label class="input-label mb-0">Wajib? </label>
                                    </div>
                                </div>
                                <div class="" data-toggle="tooltip" data-placement="top">
                                    <div class="btn btn-outline-danger delete" onclick="delete_information_input_field({{ $method['id']}})">
                                        <i class="tio-delete"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-4">
                <button type="reset" id="reset" class="btn btn-secondary">Atur Ulang</button>
                <button type="submit" class="btn btn-primary">Perbarui</button>
            </div>
        </form>
    </div>
@endsection

@push('script_2')

    <script>

        function delete_input_field(row_id) {
            $( `#field-row--${row_id}` ).remove();
            count--;
        }

        function delete_information_input_field(row_id) {
            $( `#information-row--${row_id}` ).remove();
            count_info--;
        }

        jQuery(document).ready(function ($) {
            count = 1;
            $('#add-payment-method-field').on('click', function (event) {
                if(count <= 15) {
                    event.preventDefault();

                    $('#method-field').append(
                        `<div class="d-flex align-items-end gap-3 mb-4 flex-wrap" id="field-row--${count}">
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">Nama Bidang </label>
                                    <input type="text" name="field_name[]" class="form-control" maxlength="255" placeholder="Contoh : Bank BRI" id="field_name_${count}" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">Data </label>
                                    <input type="text" name="field_data[]" class="form-control" maxlength="255" placeholder="Contoh : 1234567890" required>
                                </div>
                            </div>
                            <div class="" data-toggle="tooltip" data-placement="top" title="Hapus Bidang">
                                <div class="btn btn-outline-danger delete" onclick="delete_input_field(${count})">
                                    <i class="tio-delete"></i>
                                </div>
                            </div>
                        </div>`
                    );

                    count++;
                } else {
                    Swal.fire({
                        title: 'Informasi Pembayaran Melebihi Batas Maksimal',
                        confirmButtonText: 'Ok',
                    });
                }
            })


            count_info = 1;
            $('#add-payment-information-field').on('click', function (event) {
                if(count_info <= 15) {
                    event.preventDefault();

                    $('#information-field').append(
                        `<div class="d-flex align-items-end gap-3 mb-4 flex-wrap" id="information-row--${count_info}">
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">Nama Bidang </label>
                                    <input type="text" name="information_name[]" class="form-control" maxlength="255" placeholder="" id="information_name_${count_info}" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="">
                                    <label class="input-label">Nama Bidang Pengganti </label>
                                    <input type="text" name="information_placeholder[]" class="form-control" maxlength="255" placeholder="" required>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-10 mb-2">
                                    <input class="custom-control" type="checkbox" name="information_required[]">
                                    <label class="input-label mb-0">Wajib? </label>
                                </div>
                            </div>
                            <div class="" data-toggle="tooltip" data-placement="top" title="Hapus Bidang">
                                <div class="btn btn-outline-danger delete" onclick="delete_information_input_field(${count_info})">
                                    <i class="tio-delete"></i>
                                </div>
                            </div>
                        </div>`
                    );

                    count_info++;
                } else {
                    Swal.fire({
                        title: 'Kebutuhan Informasi Pelanggan Melebihi Batas Maksimal',
                        confirmButtonText: 'Ok',
                    });
                }
            })

            $('#reset').on('click', function (event) {
                $('#method-field').html("");
                $('#method_name').val("");

                $('#information-field').html("");
                $('#payment_note').val("");
                count=1;
                count_info=1;
            })
        });
    </script>

@endpush
