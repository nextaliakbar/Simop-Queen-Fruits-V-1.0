@extends('layouts.branch.app')

@section('title', 'Dashboard')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" src="{{asset('assets/admin')}}/vendor/apex/apexcharts.css"></link>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="card card-body mb-3">
            <div class="row justify-content-between align-items-center g-2 mb-3">
                <div class="col-auto">
                    <h4 class="d-flex align-items-center gap-10 mb-0">
                        <img width="20" class="avatar-img rounded-0" src="{{asset('assets/admin/img/icons/business_analytics.png')}}" alt="Business Analytics">
                        Analisis & Statistik Usaha
                    </h4>
                </div>
                <div class="col-auto">
                    <select class="custom-select  min-w200" name="statistics_type" onchange="order_stats_update(this.value)">
                        <option value="overall" {{session()->has('statistics_type') && session('statistics_type') == 'overall'?'selected':''}}>
                            Statistik Keseluruhan
                        </option>
                        <option value="today" {{session()->has('statistics_type') && session('statistics_type') == 'today'?'selected':''}}>
                            Statistik Hari Ini
                        </option>
                        <option value="this_month" {{session()->has('statistics_type') && session('statistics_type') == 'this_month'?'selected':''}}>
                            Statistik Bulan Ini
                        </option>
                    </select>
                </div>
            </div>
            <div class="row g-2" id="order_stats">
                @include('branch-views.partials._dashboard-order-stats',['data'=>$data])
            </div>
        </div>

        <div class="grid-chart mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            <img width="20" class="avatar-img rounded-0" src="{{asset('assets/admin/img/icons/earning_statistics.png')}}" alt="">
                            Statistik Pesanan
                        </h4>

                        <ul class="option-select-btn">
                            <li>
                                <label>
                                    <input type="radio" name="statistics" hidden checked>
                                    <span data-order-type="yearOrder"
                                          onclick="orderStatisticsUpdate(this)">Tahun Ini</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="statistics" hidden="">
                                    <span data-order-type="MonthOrder"
                                          onclick="orderStatisticsUpdate(this)">Bulan Ini</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="statistics" hidden="">
                                    <span data-order-type="WeekOrder"
                                          onclick="orderStatisticsUpdate(this)">Minggu Ini</span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div id="updatingOrderData" class="custom-chart mt-2">
                        <div id="order-statistics-line-chart"></div>
                    </div>
                </div>
            </div>

            <div class="card h-100 order-last order-lg-0">
                <div class="card-header">
                    <h4 class="d-flex text-capitalize mb-0">
                        Statistik Status Pesanan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mt-2">
                        <div>
                            <div class="position-relative pie-chart">
                                <div id="dognut-pie"></div>
                                <div class="total--orders">
                                    <h3>{{$donut['pending'] + $donut['ongoing'] + $donut['delivered']+ $donut['canceled']+ $donut['returned']+ $donut['failed']}} </h3>
                                    <span>Pesanan</span>
                                </div>
                            </div>
                            <div class="apex-legends">
                                <div class="before-bg-pending">
                                    <span>Tertunda ({{$donut['pending']}})</span>
                                </div>
                                <div class="before-bg-ongoing">
                                    <span>Berlangsung ({{$donut['ongoing']}})</span>
                                </div>
                                <div class="before-bg-delivered">
                                    <span>Terkirim ({{$donut['delivered']}})</span>
                                </div>
                                <div class="before-bg-17202A">
                                    <span>Dibatalkan ({{$donut['canceled']}})</span>
                                </div>
                                <div class="before-bg-21618C">
                                    <span>Dikembalikan ({{$donut['returned']}})</span>
                                </div>
                                <div class="before-bg-27AE60">
                                    <span>Gagal ({{$donut['failed']}})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card h100">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                            <img width="20" class="avatar-img rounded-0" src="{{asset('assets/admin/img/icons/earning_statistics.png')}}" alt="">
                            Statistik Pendapatan
                        </h4>
                        <ul class="option-select-btn">
                            <li>
                                <label>
                                    <input type="radio" name="statistics2" hidden="" checked="">
                                    <span data-earn-type="yearEarn"
                                          onclick="earningStatisticsUpdate(this)">Tahun Ini</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="statistics2" hidden="">
                                    <span data-earn-type="MonthEarn"
                                          onclick="earningStatisticsUpdate(this)">Bulan Ini</span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="statistics2" hidden="">
                                    <span data-earn-type="WeekEarn"
                                          onclick="earningStatisticsUpdate(this)">Minggu Ini</span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div id="updatingData" class="custom-chart mt-2">
                        <div id="line-adwords"></div>
                    </div>
                </div>
            </div>

            <div class="card h100 recent-orders">
                <div class="card-header d-flex justify-content-between gap-10">
                    <h5 class="mb-0">Pesanan Terbaru</h5>
                    <a href="{{ route('branch.orders.list', ['status' => 'all']) }}" class="btn-link">Tampilkan Semua</a>
                </div>
                <div class="card-body">
                    <ul class="common-list">
                        @foreach($data['recent_orders'] as $recent)
                            <li class="pt-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                <div class="order-info ">
                                    <h5><a href="{{route('branch.orders.details', ['id' => $recent->id])}}" class="text-dark" >Pesanan# {{$recent->id}}</a></h5>
                                    <p>{{\Illuminate\Support\Carbon::parse($recent->created_at)->format('d-m-y, h:m A')}}</p>
                                </div>
                                @if($recent['order_status'] == 'pending')
                                        <span
                                            class="status text-primary">Tertunda</span>
                                    @elseif($recent['order_status'] == 'delivered')
                                        <span
                                            class="status text-success">Terkirim</span>
                                    @elseif($recent['order_status'] == 'confirmed')
                                        <span
                                            class="status text-warning">Dikonfirmasi</span>
                                    
                                    @elseif ($recent['order_status'] == 'processing')
                                        <span
                                            class="status text-warning">Diproses</span>
                                    @elseif ($recent['order_status'] == 'out_for_delivery')
                                        <span
                                            class="status text-warning">Dalam Pengiriman</span>
                                    
                                    @elseif($recent['order_status'] == 'canceled' || $recent['order_status'] == 'failed')
                                        @if($recent['order_status'] == 'failed')
                                            <span
                                                class="status text-warning">Gagal Terkirim</span>
                                        @else
                                            <span
                                                class="status text-warning">Dibatalkan</span>
                                        @endif
                                    @elseif($recent['order_status'] == 'completed')
                                        <span
                                            class="status text-success">Selesai</span>
                                    @elseif($recent['order_status'] == 'returnred')
                                        <span
                                            class="status text-success">Dikembalikan</span>
                                    @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('script')
    <script src="{{asset('assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/apex/apexcharts.min.js"></script>
@endpush


@push('script_2')

    <script>
        var OSDCoptions = {
            chart: {
                height: 328,
                type: 'line',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false,
                },
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['rgba(255, 111, 112, 0.5)', '#107980'],
            series: [{
                name: "Pesanan",
                    data: [{{$order_statistics_chart[1]}}, {{$order_statistics_chart[2]}}, {{$order_statistics_chart[3]}}, {{$order_statistics_chart[4]}},
                {{$order_statistics_chart[5]}}, {{$order_statistics_chart[6]}}, {{$order_statistics_chart[7]}}, {{$order_statistics_chart[8]}},
                {{$order_statistics_chart[9]}}, {{$order_statistics_chart[10]}}, {{$order_statistics_chart[11]}}, {{$order_statistics_chart[12]}}]
                },
            ],
            markers: {
                size: 2,
                strokeWidth: 0,
                hover: {
                    size: 5
                }
            },
            grid: {
                show: true,
                padding: {
                    bottom: 0
                },
                borderColor: "rgba(180, 208, 224, 0.5)",
                strokeDashArray: 7,
                xaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            xaxis: {
                tooltip: {
                    enabled: false
                }
            },
            legend: {
                show: false,
                position: 'top',
                horizontalAlign: 'right',
                offsetY: 10
            }
        }

        var chartLine = new ApexCharts(document.querySelector('#order-statistics-line-chart'), OSDCoptions);
        chartLine.render();
    </script>

    <script>
        var earningOptions = {
            chart: {
                height: 328,
                type: 'line',
                zoom: {
                enabled: false
                },
                toolbar: {
                    show: false,
                },
            },
            stroke: {
                curve: 'straight',
                width: 3
            },
            colors: ['rgba(255, 111, 112, 0.5)', '#107980'],
            series: [{
                name: "Pendapatan",
                data: [{{$earning[1]}}, {{$earning[2]}}, {{$earning[3]}}, {{$earning[4]}}, {{$earning[5]}}, {{$earning[6]}},
                    {{$earning[7]}}, {{$earning[8]}}, {{$earning[9]}}, {{$earning[10]}}, {{$earning[11]}}, {{$earning[12]}}]
                },
            ],
            markers: {
                size: 2,
                strokeWidth: 0,
                hover: {
                    size: 5
                }
            },
            grid: {
                show: true,
                padding: {
                    bottom: 0
                },
                borderColor: "rgba(180, 208, 224, 0.5)",
                strokeDashArray: 7,
                xaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            xaxis: {
                tooltip: {
                    enabled: false
                }
            },
            legend: {
                show: false,
                position: 'top',
                horizontalAlign: 'right',
                offsetY: 10
            }
        }

        var chartLine = new ApexCharts(document.querySelector('#line-adwords'), earningOptions);
        chartLine.render();
    </script>
    <script>
        Chart.plugins.unregister(ChartDataLabels);

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

    </script>

    <script>
        function order_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{route('branch.order-stats')}}",
                type: "post",
                data: {
                    statistics_type: type,
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    $('#order_stats').html(data.view)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }
    </script>

    <script>
        function orderStatisticsUpdate(t) {
            let value = $(t).attr('data-order-type');
            console.log(value);

            $.ajax({
                url: '{{route('branch.order-statistics')}}',
                type: 'GET',
                data: {
                    type: value
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (response_data) {
                    console.log(response_data);
                    document.getElementById("order-statistics-line-chart").remove();
                    let graph = document.createElement('div');
                    graph.setAttribute("id", "order-statistics-line-chart");
                    document.getElementById("updatingOrderData").appendChild(graph);

                    var options = {
                        series: [{
                            name: "Pesanan",
                            data: response_data.orders,
                        }],
                        chart: {
                            height: 316,
                            type: 'line',
                            zoom: {
                                enabled: false
                            },
                            toolbar: {
                                show: false,
                            },
                            markers: {
                                size: 5,
                            }
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        colors: ['rgba(255, 111, 112, 0.5)', '#107980'],
                        stroke: {
                            curve: 'smooth',
                            width: 3,
                        },
                        xaxis: {
                            categories: response_data.orders_label,
                        },
                        grid: {
                            show: true,
                            padding: {
                                bottom: 0
                            },
                            borderColor: "rgba(180, 208, 224, 0.5)",
                            strokeDashArray: 7,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        yaxis: {
                            tickAmount: 4,
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#order-statistics-line-chart"), options);
                    chart.render();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }

        function earningStatisticsUpdate(t) {
            let value = $(t).attr('data-earn-type');
            $.ajax({
                url: '{{route('branch.earning-statistics')}}',
                type: 'GET',
                data: {
                    type: value
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (response_data) {
                    document.getElementById("line-adwords").remove();
                    let graph = document.createElement('div');
                    graph.setAttribute("id", "line-adwords");
                    document.getElementById("updatingData").appendChild(graph);

                    var optionsLine = {
                        chart: {
                            height: 328,
                            type: 'line',
                            zoom: {
                                enabled: false
                            },
                            toolbar: {
                                show: false,
                            },
                        },
                        stroke: {
                            curve: 'straight',
                            width: 2
                        },
                        colors: ['rgba(255, 111, 112, 0.5)', '#107980'],
                        series: [{
                            name: "Pendapatan",
                            data: response_data.earning,
                        }],
                        markers: {
                            size: 6,
                            strokeWidth: 0,
                            hover: {
                                size: 9
                            }
                        },
                        grid: {
                            show: true,
                            padding: {
                                bottom: 0
                            },
                            borderColor: "rgba(180, 208, 224, 0.5)",
                            strokeDashArray: 7,
                            xaxis: {
                                lines: {
                                    show: true
                                }
                            }
                        },
                        labels: response_data.earning_label,
                        xaxis: {
                            tooltip: {
                                enabled: false
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'right',
                            offsetY: -20
                        }
                    }
                    var chartLine = new ApexCharts(document.querySelector('#line-adwords'), optionsLine);
                    chartLine.render();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }
    </script>
    <script>
        var options = {
            series: [{{$donut['ongoing']}}, {{$donut['delivered']}}, {{$donut['pending']}}, {{$donut['canceled']}}, {{$donut['returned']}}, {{$donut['failed']}}],
            chart: {
                width: 256,
                type: 'donut',
            },
            labels: ['Berlangsung', 'Terkirim', 'Tertunda', 'Dibatalkan', 'Dikembalikan', 'Gagal'],
            dataLabels: {
                enabled: false,
                style: {
                    colors: ['#803838', '#27AE60', '#FF6F70', '#17202A', '#21618C', '#FF0000']
                }
            },
            responsive: [{
                breakpoint: 1650,
                options: {
                    chart: {
                        width: 250
                    },
                }
            }],
            colors: ['#803838', '#27AE60', '#FF6F70', '#17202A', '#21618C', '#FF0000'],
            fill: {
                colors: ['#803838', '#27AE60', '#FF6F70', '#17202A', '#21618C', '#FF0000']
            },
            legend: {
                show: false
            },
        };

        var chart = new ApexCharts(document.querySelector("#dognut-pie"), options);
        chart.render();

    </script>
@endpush
