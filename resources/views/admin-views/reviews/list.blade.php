@extends('layouts.admin.app')

@section('title', 'Daftar Review')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/review.png')}}" alt="">
                <span class="page-header-title">
                    Review Produk
                </span>
            </h2>
        </div>

        <div class="row g-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="row justify-content-between align-items-center gy-2">
                            <div class="col-sm-4 col-md-6 col-lg-8">
                                <h4>Daftar Review <span id="total_count" class="badge badge-soft-dark rounded-50 fz-14">{{ $reviews->total() }}</span></h4>
                            </div>
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="" method="GET" id="search-form">
                                    @csrf
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Cari berdasarkan nama produk" aria-label="Search" value="{{ request()->search }}" required="" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </div>
                                </form>
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
                                    <th>Nama Produk</th>
                                    <th>Info Pelanggan</th>
                                    <th>Review</th>
                                    <th>Rating</th>
                                </tr>
                                </thead>
                                <tbody id="set-rows">
                                @foreach($reviews as $key=>$review)
                                    <tr>
                                        <td>{{$reviews->firstitem()+$key}}</td>
                                        <td>
                                            <div>
                                                @if($review->product)
                                                    <a class="text-dark media align-items-center gap-2" href="{{route('admin.product.view',[$review['product_id']])}}">
                                                        <div class="avatar">
                                                            <img class="rounded-circle img-fit" src="{{$review->product['imageFullPath']}}" alt="image">
                                                        </div>
                                                        <span class="media-body max-w220 text-wrap">{{$review->product['name']}}</span>
                                                    </a>
                                                @else
                                                    <span class="badge-pill badge-soft-dark text-muted small">
                                                        Produk tidak tersedia
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->customer)
                                                <div class="d-flex flex-column gap-1">
                                                    <a class="text-dark" href="{{route('admin.customer.view',[$review->user_id])}}">
                                                        {{$review->customer->f_name." ".$review->customer->l_name}}
                                                    </a>
                                                    <a class="text-dark fz-12" href="tel:'{{$review->customer->phone}}'">{{$review->customer->phone}}</a>
                                                </div>
                                            @else
                                                <span class="badge-pill badge-soft-dark text-muted small">
                                                    Pelanggan tidak tersedia
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="max-w300 line-limit-3">{{$review->comment}}</div>
                                        </td>
                                        <td>
                                            <label class="badge badge-soft-info">
                                                {{$review->rating}} <i class="tio-star"></i>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive mt-4 px-3">
                            <div class="d-flex justify-content-lg-end">
                                {!! $reviews->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

