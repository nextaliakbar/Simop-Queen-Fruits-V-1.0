@extends('layouts.admin.app')

@section('title', 'Review Produk')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/icons/bulk_import.png')}}" alt="">
                <span class="page-header-title">
                    {{ Str::limit($product['name'], 30) }}
                </span>
            </h2>

            <a href="{{url()->previous()}}" class="btn btn-primary">
                <i class="tio-back-ui"></i> Kembali
            </a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-md-center g-3">
                    <div class="col-md-5 d-flex justify-content-center">
                        <div class="d-flex align-items-center">
                            <img class="avatar avatar-xxl avatar-4by3 mr-4"
                                 src="{{$product['imageFullPath']}}"
                                 alt="Image Description">
                            <div class="d-block">
                                <h4 class="display-2 text-dark mb-0">
                                    <span class="c1">{{count($product->rating)>0?number_format($product->rating[0]->average, 1, '.', ' '):0}}</span><span class="text-muted">/5</span>
                                </h4>
                                <p> Dari {{$product->reviews->count()}} Review
                                    <span class="badge badge-soft-dark badge-pill ml-1"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <ul class="list-unstyled list-unstyled-py-2 mb-0">

                        @php($total=$product->reviews->count())
                            <li class="d-flex align-items-center font-size-sm">
                                @php($five=\App\CentralLogics\Helpers::rating_count($product['id'],5))
                                <span
                                    class="progress-name">Bagus Sekali</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($five/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($five/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$five}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($four=\App\CentralLogics\Helpers::rating_count($product['id'],4))
                                <span class="progress-name">Bagus</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($four/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($four/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$four}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($three=\App\CentralLogics\Helpers::rating_count($product['id'],3))
                                <span class="progress-name">Cukup</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($three/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($three/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$three}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($two=\App\CentralLogics\Helpers::rating_count($product['id'],2))
                                <span class="progress-name">Kurang</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($two/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($two/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$two}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($one=\App\CentralLogics\Helpers::rating_count($product['id'],1))
                                <span class="progress-name">Buruk</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($one/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($one/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$one}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3 mb-lg-5">
            <div class="">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered table-nowrap card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Deskripsi Singkat</th>
                                <th>Harga</th>
                                <th>Variasi</th>
                                <th>Tagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="max-w300 text-wrap">
                                        <div class="d-block text-break text-dark __descripiton-txt __not-first-hidden" id="__descripiton-txt-des{{$product->id}}">
                                            <div>
                                                {!! $product['description'] !!}
                                            </div>
                                            <div class="show-more text-info text-center">
                                                <span id="show-more-des{{$product->id}}"
                                                      data-id="-des{{$product->id}}"
                                                      class="see-more">Lihat Lebih Banyak</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        <div><strong>Harga :</strong>Rp {{number_format($product['price'])}}</div>
                                        <div><strong>Pajak :</strong>Rp {{number_format(\App\CentralLogics\Helpers::tax_calculate($product,$product['price'])) }}</div>
                                        <div><strong>Diskon :</strong>Rp {{number_format(\App\CentralLogics\Helpers::discount_calculate($product,$product['price'])) }}</div>
                                        <div><strong>Waktu Mulai Tersedia :</strong> {{date(config('time_format'), strtotime($product['available_time_starts']))}}</div>
                                        <div><strong>Waktu Selesai Tersedia :</strong> {{date(config('time_format'), strtotime($product['available_time_ends']))}}</div>
                                    </div>
                                </td>
                                <td class="px-4">
                                    @foreach(json_decode($product->variations,true) as $variation)
                                        @if(isset($variation["price"]))
                                            <span class="d-block mb-1 text-capitalize">
                                                <strong>Silahkan perbarui variasi produk</strong>
                                            </span>
                                        @break
                                        @else
                                            <span class="d-block text-capitalize">
                                                <strong>{{$variation['name']}} -</strong>
                                                @if ($variation['type'] == 'multi')
                                                    Banyak Pilihan
                                                @elseif($variation['type'] =='single')
                                                    Satu Pilihan
                                                @endif
                                                @if ($variation['required'] == 'on')
                                                    - Wajib
                                                @endif
                                            </span>

                                            @if ($variation['min'] != 0 && $variation['max'] != 0)
                                                (Min. Pilihan: {{ $variation['min'] }} - Maks. Pilihan: {{ $variation['max'] }})
                                            @endif

                                            @if (isset($variation['values']))
                                                @foreach ($variation['values'] as $value)
                                                    <span class="d-block text-capitalize">
                                                        {{ $value['label']}} :<strong>Rp {{number_format($value['optionPrice'])}}</strong>
                                                    </span>
                                                @endforeach
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($product->tags as $tag)
                                        <span class="badge-soft-success mb-1 mr-1 d-inline-block px-2 py-1 rounded">{{$tag->tag}} </span> <br>
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-top p-3">
                <h5 class="mb-0">Review Produk</h5>
            </div>
            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                       data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0, 3, 6],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                    <thead class="thead-light">
                        <tr>
                            <th>Reviewer</th>
                            <th>Review</th>
                            <th>Gambar</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                <a class="d-flex align-items-center"
                                   href="{{route('admin.customer.view',[$review['user_id']])}}">
                                    <div class="avatar avatar-circle">
                                        @if($review->customer)
                                            <img class="avatar-img" width="75" height="75"
                                                 src="{{$review->customer->imageFullPath}}"
                                                 alt="">
                                        @else
                                            <img class="avatar-img" width="75" height="75"
                                                 src="{{ asset('assets/admin/img/160x160/img1.jpg') }}"
                                                 alt="">
                                        @endif

                                    </div>
                                    <div class="ml-3">
                                    <span class="d-block h5 text-hover-primary mb-0">
                                        @if(isset($review->customer))
                                        {{$review->customer['f_name']." ".$review->customer['l_name']}}
                                        <i class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                            title="Verified Customer"></i></span>
                                        <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                        @else
                                            <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                                Pelanggan tidak tersedia
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </td>
                            <td>
                                <div class="text-wrap width-18rem">
                                    <div class="d-flex mb-2">
                                        <label class="badge badge-soft-info">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </label>
                                    </div>

                                    <div class="max-w300 text-wrap">
                                        <div class="d-block text-break text-dark __descripiton-txt __not-first-hidden" id="__descripiton-txt{{$review->id}}">
                                            <div>
                                                {!! $review['comment'] !!}
                                            </div>
                                            <div class="show-more text-info text-center">
                                                <span class="see-more" id="show-more-{{$review->id}}" data-id="{{$review->id}}">Lihat Lebih Banyak</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach(json_decode($review['attachment'], true) as $image)
                                    <div class="avatar avatar-circle">
                                        <img class="cursor-pointer rounded img-fit custom-img-fit image-preview"
                                             src="{{asset('storage/review/'.$image)}}"
                                             alt="Image Description">
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                {{date('d M Y H:i:s',strtotime($review['created_at']))}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-12">
                        {!! $reviews->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('.see-more').on('click', function (){
            let id = $(this).data('id');
            showMore(id)
        });
        function showMore(id) {
            $('#__descripiton-txt' + id).toggleClass('__not-first-hidden')
            if($('#show-more' + id).hasClass('active')) {
                $('#show-more' + id).text('Lihat Lebih Banyak')
                $('#show-more' + id).removeClass('active')
            }else {
                $('#show-more' + id).text('Lihat Lebih Sedikit')
                $('#show-more' + id).addClass('active')
            }
        }
    </script>
@endpush
