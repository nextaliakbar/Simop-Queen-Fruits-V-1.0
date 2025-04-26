@extends('layouts.admin.app')

@section('title', 'Prediksi')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/predictive-analysis.png')}}" alt="">
                <span class="page-header-title">
                    Tabel Prediksi Durasi Waktu Pengiriman Pesanan
                </span>
            </h2>
            <span class="badge badge-soft-dark rounded-50 fz-14">{{ $predicts->total() }}</span>
        </div>


        <div class="row g-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-top px-card pt-4">
                        <div class="row justify-content-between align-items-center gy-2">
                            <div class="col-lg-4">
                                <form action="{{url()->current()}}" method="GET">
                                    <div class="input-group">
                                        <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="Cari berdasarkan id pesananan" aria-label="Search" value="{{$search}}" required="" autocomplete="off">
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
                            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th>No.</th>
                                    <th>ID Pesanan</th>
                                    <th>Usia Kurir</th>
                                    <th>Rating Kurir</th>
                                    <th class="text-center">Jarak</th>
                                    <th>Jenis Kendaraan</th>
                                    <th>Hasil Prediksi</th>
                                    <th>Durasi Pengiriman</th>
                                    <th>Keterangan</th>
                                </tr>
                                </thead>

                                <tbody id="set-rows">
                                @foreach($predicts as $key=>$predict)
                                    @php
                                        $order = App\Models\Order::where(['id' =>  $predict->order_id, 'order_type' => 'delivery'])->first();
                                        $duration_time = $order->duration_time;
                                    @endphp
                                    <tr>
                                        <td>{{$predicts->firstitem()+$key}}</td>
                                        <td>
                                            <a class="text-dark" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                        </td>
                                        <td>{{$predict->delivery_person_age}}</td>
                                        <td>
                                            <div class="d-flex mb-2">
                                                <label class="badge badge-soft-info">
                                                    {{$predict->delivery_person_rating}} <i class="tio-star"></i>
                                                </label>
                                            </div>
                                        </td>
                                        <td>{{$predict->distance}} Km</td>
                                        <td>Motor</td>
                                        <td>{{$predict->prediction_duration_result}} Menit</td>
                                        <td>{{$duration_time != null ? $duration_time . ' Menit' : 'Belum tersedia'}}</td>
                                        @php
                                            if($duration_time != null) {
                                                if($duration_time == $predict->prediction_duration_result) {
                                                $interval = $predict->prediction_duration_result;
                                                } elseif($duration_time > $predict->prediction_duration_result) {
                                                    $interval = $duration_time - $predict->prediction_duration_result;
                                                } else {
                                                    $interval = $predict->prediction_duration_result - $duration_time;
                                                }

                                                $result = $interval % 2 == 1 ? number_format($interval, 1) : number_format($interval);

                                                
                                                $information = $predict->prediction_duration_result == $duration_time 
                                                ? 'Sesuai dengan waktu prediksi' : ($duration_time > $predict->prediction_duration_result 
                                                ? 'Lebih lambat ' . $result . ' menit dari prediksi' 
                                                : 'Lebih cepat ' . $result . ' menit dari prediksi');
                                            } else {
                                                $information = 'Belum tersedia';
                                            }
                                        @endphp
                                        <td>
                                            @if($duration_time == $predict->prediction_duration_result || $duration_time == null)
                                            <span class="badge-soft-info px-2 py-1 rounded">{{$information}}</span>
                                            @elseif($predict->prediction_duration_result > $duration_time)
                                            <span class="badge-soft-success px-2 py-1 rounded">{{$information}}</span>
                                            @else
                                            <span class="badge-soft-danger px-2 py-1 rounded">{{$information}}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive mt-4 px-3">
                            <div class="d-flex justify-content-lg-end">
                                {!! $predicts->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
