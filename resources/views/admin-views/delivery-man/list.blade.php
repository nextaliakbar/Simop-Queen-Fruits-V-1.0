@extends('layouts.admin.app')

@section('title', 'Daftar Kurir')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{ asset('assets/admin/img/icons/deliveryman.png') }}"
                    alt="">
                <span class="page-header-title">
                    Daftar Kurir
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-circle fz-12">{{ $deliverymen->total() }}</span>
        </div>

        <div class="card mt-3">
            <div class="card-body border-0">
                <form action="{{ url()->current() }}" method="GET" id="search-form">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <div class="d-flex gap-4 justify-content-between align-items-end flex-wrap">
                        <div class="form-group flex-grow-1 mb-0">
                            <label class="input-label">Tanggal Bergabung Kurir</label>
                            <div class="position-relative">
                                <span class="tio-calendar icon-absolute-on-right"></span>
                                <input type="text" name="date" id="js-daterangepicker-predefined" class="form-control"
                                    placeholder="Pilih Tanggal" value="{{ request()->get('date') }}" autocomplete="off">

                            </div>
                           
                        </div>
                        <div class="form-group flex-grow-1 mb-0">
                            <label class="input-label">Status Kurir</label>
                            <select class="custom-select" name="status" id="status">
                                <option value="all">Semua</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary px-6">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-5 py-4">
            <div class="flex-grow-1">
                <div class="resturant-card dashboard--card px-3 pb-0 min-h-108px border-0 shadow-none bg--1">
                    <div class="mr-4">
                        <h4 class="title text-c2">{{ $deliverymen->total() }}</h4>
                        <span class="subtitle text-title">
                            Total Kurir
                        </span>
                    </div>
                    <div class="resturant-icon">
                        <img class="" width="38"
                            src="{{ asset('assets/admin/img/deliveryman/total.png') }}" alt="">
                    </div>

                </div>
            </div>
            <div class="flex-grow-1">
                <div class="resturant-card dashboard--card px-3 pb-0 min-h-108px border-0 shadow-none bg--2">
                    <div class="mr-4">
                        <h4 class="title text-success">
                            {{ $active_count }}</h4>
                        <span class="subtitle text-title">
                            Kurir Aktif
                        </span>
                    </div>
                    <div class="resturant-icon">
                        <img class="" width="38"
                            src="{{ asset('assets/admin/img/deliveryman/active.png') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="resturant-card dashboard--card px-3 pb-0 min-h-108px border-0 shadow-none bg--3">
                    <div class="mr-4">
                        <h4 class="title text-danger">
                            {{ $in_active_count }}</h4>
                        <span class="subtitle text-title">
                            Kurir Tidak Aktif
                        </span>
                    </div>
                    <div class="resturant-icon">
                        <img class="" width="38"
                            src="{{ asset('assets/admin/img/deliveryman/inactive.png') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div
                            class="d-flex flex-column flex-sm-row flex-wrap gap-md-4 gap-3 justify-content-end align-items-sm-center">
                            <div class="flex-grow-1">
                                <h5 class="d-flex gap-1 mb-0">
                                    Daftar Kurir
                                    <span
                                        class="badge badge-soft-dark rounded-50 fz-12 ml-1">{{ $deliverymen->total() }}</span>
                                </h5>
                            </div>
                            <form action="{{ url()->current() }}" method="GET" class="flex-grow-1" id="searchForm">
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="hidden" name="date" value="{{ request()->get('date') }}" id="hiddenDate">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                        placeholder="Cari berdasarkan nama, email, atau no.hp"
                                        aria-label="Search" value="{{ $search }}" autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            Cari
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="d-flex flex-wrap justify-content-end gap-3">
                                <a href="{{ route('admin.delivery-man.add') }}" class="btn btn-primary px--12px">
                                    <i class="tio-add"></i>
                                    Tambah Kurir
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="py-4">
                        <div class="table-responsive datatable-custom">
                            <table
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama</th>
                                        <th>Info Kontak</th>
                                        <th>Tanggal Bergabung</th>
                                        <th class="text-center">Total Pesanan</th>
                                        <th class="text-center">Sedang Berlangsung</th>
                                        <th class="text-center">Dibatalkan</th>
                                        <th class="text-center">Selesai</th>
                                        <th class="text-center">Total Jumlah Pesanan</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="set-rows">
                                    @foreach ($deliverymen as $key => $dm)
                                        <tr>
                                            <td>{{ $deliverymen->firstitem() + $key }}</td>
                                            <td>
                                                <div class="media gap-3 align-items-center">
                                                    <div class="avatar">
                                                        <img width="60" class="img-fit rounded-circle"
                                                            src="{{ $dm->imageFullPath }}"
                                                            alt="deliveryman">
                                                    </div>
                                                    <div class="media-body">
                                                        <a class="text-dark"
                                                            href="{{ route('admin.delivery-man.details', [$dm['id']]) }}">{{ $dm['f_name'] . ' ' . $dm['l_name'] }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <div>
                                                        <a class="text-dark" href="mailto:{{ $dm['email'] }}">
                                                            {{ $dm['email'] }}
                                                        </a>
                                                    </div>
                                                    <a class="text-dark"
                                                        href="tel:{{ $dm['phone'] }}">{{ $dm['phone'] }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $dm->created_at->format('d M Y,') }}<br>
                                                {{ $dm->created_at->format('h:i') }}
                                            </td>
                                            <td class="text-center">
                                                {{ $dm['orders_count'] }}
                                            </td>
                                            <td class="text-center">{{ $dm->ongoing_orders_count }}</td>
                                            <td class="text-center">{{ $dm->canceled_orders_count }}</td>
                                            <td class="text-center">{{ $dm->completed_orders_count }}</td>
                                            <td class="text-center">Rp {{ number_format($dm->total_order_amount) }}
                                            </td>
                                            <td>
                                                <label class="switcher">
                                                    <input id="{{ $dm['id'] }}" type="checkbox"
                                                        class="switcher_input change-deliveryman-status"
                                                        {{ $dm['is_active'] == 1 ? 'checked' : '' }}
                                                        data-url="{{ route('admin.delivery-man.ajax-is-active', ['id' => $dm['id']]) }}">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-10">
                                                    <a class="btn btn-outline-info btn-sm edit square-btn"
                                                        href="{{ route('admin.delivery-man.details', [$dm['id']]) }}">
                                                        <i class="tio-visible"></i>
                                                    </a>
                                                    <a class="btn btn-outline-info btn-sm edit square-btn"
                                                        href="{{ route('admin.delivery-man.edit', [$dm['id']]) }}"><i
                                                            class="tio-edit"></i></a>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm delete square-btn form-alert"
                                                        data-id="delivery-man-{{ $dm['id'] }}"
                                                        data-message="Ingin menghapus kurir ini?"><i
                                                            class="tio-delete"></i></button>
                                                </div>
                                                <form action="{{ route('admin.delivery-man.delete', [$dm['id']]) }}"
                                                    method="post" id="delivery-man-{{ $dm['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="table-responsive px-3 mt-3">
                            <div class="d-flex justify-content-end">
                                {!! $deliverymen->links() !!}
                            </div>
                        </div>
                        @if (count($deliverymen) == 0)
                            <div class="text-center p-4">
                                <img class="w-120px mb-3"
                                    src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}"
                                    alt="image">
                                <p class="mb-0">Tidak ada data kurir</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('script_2')
        <script>
            "use strict";

            $(document).on('ready', function() {
                var start = "{{ request()->get('date') ? explode(' - ', request()->get('date'))[0] : null }}";
                var end = "{{ request()->get('date') ? explode(' - ', request()->get('date'))[1] : null }}";

                start = start ? moment(start, 'D MMM, YYYY') : null;
                end = end ? moment(end, 'D MMM, YYYY') : null;

                function cb(start, end) {
                    $('#js-daterangepicker-predefined').val(start.format('D MMM, YYYY') + ' - ' + end.format('D MMM, YYYY'));
                    $('.js-daterangepicker-predefined-preview').html(start.format('D MMM') + ' - ' + end.format('D MMM, YYYY'));
                }

                $('#js-daterangepicker-predefined').daterangepicker({
                    autoUpdateInput: false,
                    startDate: start || moment(),
                    endDate: end || moment(),
                    ranges: {
                        'Hari Ini': [moment(), moment()],
                        'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 Hari Terkahir': [moment().subtract(6, 'days'), moment()],
                        '30 Hari Terkahir': [moment().subtract(29, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                        'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    locale: {
                        format: 'D MMM, YYYY',
                        customRangeLabel: 'Range Kustom'
                    }
                });

                // Update the input field and preview when a range is selected
                $('#js-daterangepicker-predefined').on('apply.daterangepicker', function(ev, picker) {
                    cb(picker.startDate, picker.endDate);
                });

                // Clear the input and preview on cancel
                $('#js-daterangepicker-predefined').on('cancel.daterangepicker', function() {
                    $(this).val('');
                    $('.js-daterangepicker-predefined-preview').html('');
                });


                if (start && end) {
                    cb(start, end);
                }
            });



            $(".change-deliveryman-status").change(function() {
                var value = $(this).val();
                let url = $(this).data('url');
                statusChange(this, url);
            });

            function statusChange(t, url) {
                let checked = $(t).prop("checked");
                let status = checked === true ? 1 : 0;

                Swal.fire({
                    title: 'Kamu Yakin?',
                    text: 'Ingin merubah status',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FC6A57',
                    cancelButtonColor: 'default',
                    cancelButtonText: 'Tidak',
                    confirmButtonText: 'Ya',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: url,
                            data: {
                                status: status
                            },
                            success: function(data, status) {
                                toastr.success("Status berhasil diubah");
                                setInterval(function() {
                                    location.reload();
                                }, 1000);
                            },
                            error: function(data) {
                                toastr.error("Status gagal diubah");
                            }
                        });
                    } else if (result.dismiss) {
                        if (status == 1) {
                            $(t).prop('checked', false);
                        } else if (status == 0) {
                            $(t).prop('checked', true);
                        }
                        toastr.info("Status belum berubah");
                    }
                });
            }
        </script>
    @endpush
