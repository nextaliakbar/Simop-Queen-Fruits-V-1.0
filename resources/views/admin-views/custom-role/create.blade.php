@extends('layouts.admin.app')

@section('title', 'Tambah Peran')

@push('css_or_js')
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/employee.png')}}" alt="">
                <span class="page-header-title">
                    Pengaturan Peran Karyawan
                </span>
            </h2>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Peran</h5>
            </div>
            <div class="card-body">
                <form id="submit-create-role" method="post" action="{{route('admin.custom-role.store')}}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Peran</label>
                        <input type="text" name="name" class="form-control" id="name"
                                aria-describedby="emailHelp"
                                placeholder="Contoh : Admin" required>
                    </div>

                    <div class="mb-5 d-flex flex-wrap align-items-center gap-3">
                        <h5 class="mb-0">Izin Peran : </h5>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="select-all-btn">
                            <label class="form-check-label" for="select-all-btn">Pilih Semua</label>
                        </div>
                    </div>
                    <div class="row">
                        @foreach(MANAGEMENT_SECTION as $section)
                            <div class="col-xl-4 col-lg-4 col-sm-6">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="modules[]" value="{{$section}}" class="form-check-input select-all-associate"
                                            id="{{$section}}">
                                    <label class="form-check-label ml-2" for="{{$section}}">{{$section}}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">Atur Ulang</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-top px-card pt-4">
                <div class="d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-md-between align-items-md-center">
                    <h5 class="d-flex gap-2 mb-0">
                        Tabel Peran Karyawan
                        <span class="badge badge-soft-dark rounded-50 fz-12">{{$roles->count()}}</span>
                    </h5>

                    <div class="d-flex flex-wrap justify-content-md-end gap-3">
                        <form action="" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Cari berdasarkan nama" aria-label="Cari" value="{{ $search }}" required="" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="py-4">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" id="dataTable">
                        <thead class="thead-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Peran</th>
                            <th>Izin Peran</th>
                            <th>Tanggal Dibuat</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $k=>$role)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$role['name']}}</td>
                                <td class="text-capitalize">
                                    <div class="max-w300 text-wrap">
                                        @if($role['module_access']!=null)
                                            @php($comma = '')
                                            @foreach((array)json_decode($role['module_access']) as $module)
                                                {{$comma}}{{str_replace('_',' ',$module)}}
                                                @php($comma = ', ')
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td>{{date('d-M-Y',strtotime($role['created_at']))}}</td>
                                <td>
                                    <label class="switcher">
                                        <input type="checkbox" name="status" class="switcher_input status-change" {{$role['status'] == true? 'checked' : ''}}
                                        data-url="{{route('admin.custom-role.change-status', ['id' => $role['id']])}}" id="{{$role['id']}}"
                                        >
                                        <span class="switcher_control"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{route('admin.custom-role.update',[$role['id']])}}"
                                        class="btn btn-outline-info btn-sm square-btn"
                                        title="Edit">
                                        <i class="tio-edit"></i>
                                        </a>
                                        <a data-id="role-{{$role->id}}" data-message="Ingin menghapus karyawan ini?"
                                           class="btn btn-outline-danger btn-sm delete square-btn form-alert"
                                           title="Hapus">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </div>
                                </td>
                                <form action="{{route('admin.custom-role.delete')}}" method="post" id="role-{{$role->id}}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{$role->id}}">
                                </form>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('assets/admin/js/role.js')}}"></script>

    <script>
        "use strict";

        $('#submit-create-role').on('submit',function(e){

            var fields = $("input[name='modules[]']").serializeArray();
            if (fields.length === 0)
            {
                toastr.warning('Pilih minimal 1 pilihan', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                return false;
            }else{
                $('#submit-create-role').submit();
            }
        });
    </script>

@endpush