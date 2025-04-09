@extends('layouts.admin.app')

@section('title', 'Daftar Penjualan')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-1">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/all_orders.png')}}" alt="">
                <span class="page-header-title">
                    Penjualan Kasir
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{ $orders->total() }}</span>
        </div>

        <div class="card">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url()->current() }}" id="form-data" method="GET">
                        <input type="hidden" name="filter">
                        <div class="row gy-3 gx-2 align-items-end">
                            <div class="col-12 pb-0">
                                <h4 class="mb-0">Pilih Rentang Tanggal</h4>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <select name="branch_id" class="form-control">
                                        <option value="all"
                                            {{ $branch_id == 'all'? 'selected' : '' }}
                                        >Semua Cabang</option>
                                    @forelse($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $branch_id == $branch->id? 'selected' : '' }}
                                        >{{ $branch->name }}</option>
                                    @empty
                                        <option>Cabang Tidak Ditemukan</option>
                                    @endforelse

                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">Dari</label>
                                    <input type="date" name="from" id="from_date" class="form-control" value="{{$from}}" >
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group mb-0">
                                    <label class="text-dark">Sampai</label>
                                    <input type="date" name="to" id="to_date" class="form-control" value="{{$to}}" >
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <button type="submit" class="btn btn-primary btn-block">Tampilkan Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-top px-card pt-4">
                <div class="row justify-content-between align-items-center gy-2">
                    <div class="col-sm-8 col-md-6 col-lg-4">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="Cari berdasarkan id, pelanggan atau status pembayaran" aria-label="Search"
                                        value="{{$search}}" required autocomplete="off">
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

            <div class="py-4">
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>
                                    No. 
                                </th>
                                <th>ID Pesanan</th>
                                <th>Tanggal Pesanan</th>
                                <th>Info Pelanggan</th>
                                <th>Cabang</th>
                                <th>Total Jumlah</th>
                                <th>Status Pesanan</th>
                                <th>Jenis Pesanan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td>{{$key+$orders->firstItem()}}</td>
                                <td>
                                    <a class="text-dark" href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td>
                                    <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                    <div>{{date("h:i A",strtotime($order['created_at']))}}</div>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <h6 class="text-capitalize mb-1">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</h6>
                                        <a class="text-dark fz-12" href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                    @else
                                        <h6 class="text-capitalize text-muted">Pelanggan tidak tersedia</h6>
                                    @endif
                                </td>
                                <td>{{ $order->branch?->name }}</td>
                                <td>
                                    <div>Rp {{number_format($order['order_amount']) }}</div>
                                    @if($order->payment_status=='paid')
                                        <span class="text-success">Terbayar</span>
                                    @else
                                        <span class="text-danger">Belum Terbayar</span>
                                    @endif
                                </td>
                                <td class="text-capitalize">
                                    @if($order['order_status']=='pending')
                                        <span class="badge-soft-info px-2 rounded">Tertunda</span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge-soft-info px-2 rounded">Dikonfirmasi</span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge-soft-warning px-2 rounded">Diproses</span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge-soft-warning px-2 rounded">Dalam Pengiriman</span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge-soft-success px-2 rounded">Terkirim</span>
                                    @else
                                        <span class="badge-soft-danger px-2 rounded">{{str_replace('_',' ',$order['order_status'])}}</span>
                                    @endif
                                </td>
                                <td class="text-capitalize">
                                    <span class="badge-soft-success px-2 py-1 rounded">{{($order['order_type']) == 'pos' ? 'Kasir' : ''}}</span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-sm btn-outline-primary square-btn" href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-success square-btn print-invoice-button" data-order-id="{{$order->id}}" type="button">
                                            <i class="tio-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-responsive mt-4 px-3">
                <div class="d-flex justify-content-lg-end">
                    {!! $orders->links() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print-invoice" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Bukti Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row custom-modal-body">
                    <div class="col-md-12">
                        <center>
                            <input type="button" class="btn btn-primary non-printable print-button" value="Cetak" />
                            <a href="{{url()->previous()}}" class="btn btn-danger non-printable">Kembali</a>
                        </center>
                        <hr class="non-printable">
                    </div>
                    <div class="row custom-print-area-auto" id="printableArea">

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('assets/admin/js/loyalty-point.js')}}"></script>
    <script>
        "use strict";

        $('.print-button').click(function() {
            printDiv('printableArea');
        });

        $('.print-invoice-button').click(function() {
            var orderId = $(this).data('order-id');
            print_invoice(orderId);
        });

        function print_invoice(order_id) {
            $.get({
                url: '{{url('/')}}/admin/pos/invoice/'+order_id,
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    console.log("success...")
                    $('#print-invoice').modal('show');
                    $('#printableArea').empty().html(data.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

    function printDiv(divName) {

        if($('html').attr('dir') === 'rtl') {
            $('html').attr('dir', 'ltr')
            var printContents = document.getElementById(divName).innerHTML;
            document.body.innerHTML = printContents;
            $('#printableAreaContent').attr('dir', 'rtl')
            window.print();
            $('html').attr('dir', 'rtl')
            location.reload();
        }else{
            var printContents = document.getElementById(divName).innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            location.reload();
        }

    }
    </script>

@endpush
