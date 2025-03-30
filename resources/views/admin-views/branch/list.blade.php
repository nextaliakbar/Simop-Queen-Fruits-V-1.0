@extends('layouts.admin.app')

@section('title', 'Daftar Cabang')

@push('css_or_js')
    <style>
        .alert--container .alert:not(.active) {
            display: none;
        }

        .alert--message-2 {
            border-left: 3px solid var(--primary);
            border-radius: 6px;
            position: fixed;
            right: 20px;
            top: 80px;
            z-index: 9999;
            background: #FFFFFF;
            width: 80vw;
            display: flex;
            max-width: 380px;
            align-items: center;
            gap: 12px;
            padding: 16px;
            font-size: 12px;
            transition: all ease 0.5s;
            box-shadow: 0 0 2rem rgba(0, 0, 0, 0.15);
        }

        .alert--message-2 h6 {
            font-size: 1rem;
        }

        .alert--message-2:not(.active) {
            transform: translateX(calc(100% + 40px));
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @if(session('branch-store'))
            <div class="d-flex align-items-center gap-2 alert--message-2 fade show active" id="branch-alert">
                <img width="28" class="align-self-start image"
                     src="{{ asset('assets/admin/svg/components/CircleWavyCheck.svg') }}" alt="">
                <div class="">
                    <h6 class="title mb-2 text-truncate">Berhasil membuat cabang baru!</h6>
                </div>
                <button type="button" class="close position-relative p-0" aria-label="Close" id="close-alert">
                    <i class="tio-clear"></i>
                </button>
            </div>
        @endif

        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/branch.png')}}" alt="">
                <span class="page-header-title">
                    Daftar Cabang
                </span>
            </h2>
        </div>

        <div class="card">
            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-4 col-md-6 col-lg-8">
                        <h5 class="d-flex align-items-center gap-2 mb-0">
                            Daftar Cabang
                            <span class="badge badge-soft-dark rounded-50 fz-12">{{ $branches->total() }}</span>
                        </h5>
                    </div>
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="#" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Cari berdasarkan id atau nama" aria-label="Search" value="{{$search??''}}" required="" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pb-0">
                <div class="table-responsive datatable-custom">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th>No. </th>
                            <th>Nama Cabang</th>
                            <th>Jenis Cabang</th>
                            <th>Info Kontak</th>
                            <th>Kampanye Promosi</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($branches as $key=>$branch)
                            <tr>
                                <td>{{$branches->firstItem()+$key}}</td>
                                <td>
                                    <div class="media align-items-center gap-3 px-3">
                                        <img width="50" class="rounded"
                                             src="{{$branch->imageFullPath}}">
                                        <div class="media-body d-flex align-items-center flex-wrap">
                                            <span> {{$branch['name']}}</span>
                                            @if($branch['id']==1)
                                                <span class="badge badge-soft-danger">utama</span>
                                            @else
                                                <span class="badge badge-soft-info">sub</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{$branch->id == 1 ? 'Cabang Utama' : 'Sub Cabang'}}</td>
                                <td>
                                    <div>
                                        <strong><a href="mailto:{{$branch['email']}}" class="mb-0 text-dark bold fz-12">{{$branch['email']}}</a></strong><br>
                                        <a href="tel:{{$branch['phone']}}" class="text-dark fz-12">{{$branch['phone']}}</a>
                                    </div>
                                </td>
                                <td>
                                    <label class="switcher">
                                        <input class="switcher_input redirect-url" data-url="{{route('admin.promotion.status',[$branch['id'],$branch->branch_promotion_status?0:1])}}" type="checkbox" {{$branch->branch_promotion_status?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switcher">
                                        <input class="switcher_input redirect-url" type="checkbox" data-url="{{route('admin.branch.status',[$branch['id'],$branch->status?0:1])}}" {{$branch->status?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </td>
                                <td>
                                    @if(env('APP_MODE')!='demo' || $branch['id']!=1)
                                        <div class="d-flex justify-content-center gap-3">
                                            <a class="btn btn-outline-secondary btn-sm square-btn"
                                               href="{{ route('admin.business-settings.store.delivery-fee-setup') }}">
                                                <i class="tio-settings"></i>
                                            </a>
                                            <a class="btn btn-outline-info btn-sm edit square-btn"
                                                href="{{route('admin.branch.edit',[$branch['id']])}}"><i class="tio-edit"></i></a>
                                            @if($branch['id']!=1)
                                                <button type="button" class="btn btn-outline-danger btn-sm delete square-btn form-alert"
                                                        data-id="branch-{{$branch['id']}}" data-message="Ingin menhapus cabang ini?"><i class="tio-delete"></i></button>
                                            @endif
                                        </div>
                                        <form action="{{route('admin.branch.delete',[$branch['id']])}}"
                                                method="post" id="branch-{{$branch['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    @else
                                        <label class="badge badge-soft-danger">Tidak Diizinkan</label>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4 px-3">
                    <div class="d-flex justify-content-lg-end">
                        {!! $branches->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).ready(function () {
            let alert = $('.alert--message-2');

            setTimeout(function () {
                alert.removeClass('show active').addClass('fade');
            }, 5000);

            alert.find('.close').on('click', function () {
                alert.removeClass('show active').addClass('fade');
            });
        });
    </script>
@endpush
