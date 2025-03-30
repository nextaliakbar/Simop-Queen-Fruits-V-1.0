@extends('layouts.admin.app')

@section('title', 'Metode Pembayaran Offline')
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}" />

@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/business_setup2.png')}}" alt="">
                <span class="page-header-title">
                    Daftar Metode Pembayaran Offline
                </span>
            </h2>
        </div>

        @include('admin-views.business-settings.partials._business-setup-inline-menu')
        
        <div class="row g-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="justify-content-between align-items-center gy-2">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Cari berdasarkan nama metode" aria-label="Search" value="{{ $search }}" required="" autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            Cari
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div>
                            <a href="{{ route('admin.business-settings.web-app.third-party.offline-payment.add') }}" type="button" class="btn btn-primary"><i class="tio-add"></i>Tambah</a>
                        </div>
                    </div>

                    <div class="py-4">
                        <div class="table-responsive datatable-custom">
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th>No. </th>
                                    <th>Nama Metode Pembayaran</th>
                                    <th>Info Pembayaran</th>
                                    <th>Kebutuhan Info Dari Pelanggan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($methods as $key=>$method)
                                    <tr>
                                        <td>{{$methods->firstitem()+$key}}</td>
                                        <td>
                                            <div class="max-w300 text-wrap">
                                                {{$method['method_name']}}
                                            </div>
                                        </td>
                                        <td>
                                            @foreach($method['method_fields'] as $k=>$fields)
                                                <span class="border border-white max-w300 text-wrap text-left">
                                                    {{$fields['field_name']}} : {{$fields['field_data']}}
                                                </span><br/>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($method['method_informations'] as $k=>$informations)
                                                <span class="border border-white max-w300 text-wrap text-left">
                                                     {{$informations['information_name']}} |
                                                </span>
                                            @endforeach
                                            <div class="max-w300 text-wrap">
                                                Catatan Pembayaran
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <label class="switcher">
                                                    <input class="switcher_input" type="checkbox" {{$method['status']==1? 'checked' : ''}} id="{{$method['id']}}"
                                                           onchange="status_change(this)" data-url="{{route('admin.business-settings.web-app.third-party.offline-payment.status',[$method['id'],1])}}">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm edit square-btn"
                                                   href="{{ route('admin.business-settings.web-app.third-party.offline-payment.edit', [$method['id']]) }}"><i class="tio-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-danger btn-sm delete square-btn delete-item" data-id="{{ $method->id }}">
                                                    <i class="tio-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4 px-3">
                            <div class="d-flex justify-content-lg-end">
                                {!! $methods->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

    <script>
        $('.delete-item').on('click', function (){
            let id = $(this).data('id');
            deleteItem(id)
        });
        function deleteItem(id) {
            Swal.fire({
                title: 'Kamu Yakin?',
                text: "Metode pembayaran yang dihapus tidak dapat dikembalikan!",
                showCancelButton: true,
                confirmButtonColor: '#FC6A57',
                cancelButtonColor: '#EA295E',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya!'
            }).then((result) => {
                if (result.value) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.business-settings.web-app.third-party.offline-payment.delete')}}",
                        method: 'POST',
                        data: {
                                id: id,
                                "_token": "{{ csrf_token() }}",
                            },
                        success: function () {
                            toastr.success('Metode pembayaran berhasil dihapus');
                            location.reload();
                        }
                    });
                }
            })
        }
    </script>

@endpush
