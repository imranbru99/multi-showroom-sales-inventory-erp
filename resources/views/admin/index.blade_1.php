@extends('admin.layouts.master')

@section('title')
    <title>{{ $title }}</title>
@endsection

@section('custom_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('public/admin-elite/dist/css/dashboard.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox bg-info color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong">{{ $dealers }}</h4>
                        <div class="m-b-5 card_text">DEALERS</div><i class="ti-user widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox bg-danger color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong">৳ {{ round($total_sales_info) }}</h4>
                        <div class="m-b-5 card_text">SALES</div><i class="ti-shopping-cart widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox bg-success color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong">৳ {{ round($total_collection_info) }}</h4>
                        <div class="m-b-5 card_text">COLLECTION</div><i
                            class="ti-bar-chart widget-stat-icon text-center"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox bg-primary color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong">৳ {{ round($total_outstanding_info) }}</h4>
                        <div class="m-b-5 card_text">OUTSTANDING</div><i class="fa fa-money widget-stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- chart --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Sales & Collection Chart</h3>
                    <div>12 Month Business Chart</div>
                </div>
                <div class="ibox-body">
                    <div>
                        <canvas id="bar_chart" style="height: 350px; display: block; width: 495px;" width="495" height="200"
                            class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Outstanding Ratio</h3>
                    <div>Total Business Trading</div>
                </div>
                <div class="ibox-body">
                    <table class="table table-striped visitors-table" width="100%">
                        <thead>
                            <tr>
                                <th>Dealer Name</th>
                                <th class="text-center">Total Sales</th>
                                <th class="text-center">Total Collection</th>
                                <th>Due Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($top_dealers as $top_dealer)
                                <tr>
                                    <td>{{ $top_dealer['dealer_name'] }}</td>
                                    <td class="text-center">{{ $top_dealer['purchase'] }}</td>
                                    <td class="text-center">{{ $top_dealer['collection'] }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" role="progressbar"
                                                style="width:{{ round($top_dealer['averagePercent']) }}%; height:5px;"
                                                aria-valuenow="{{ round($top_dealer['averagePercent']) }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="progress-parcent">{{ $top_dealer['averagePercent'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Top 3 Seller</h3>
                    <div>On Business Collection</div>
                </div>
                <div class="ibox-body">
                    <table class="table table-striped visitors-table" width="100%">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th class="text-right">Collection</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($top_collections as $top_collection)
                                <tr>
                                    <td>{{ $top_collection['name'] }}</td>
                                    <td class="text-right">{{ $top_collection['total_amount'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ibox">
                <div class="px-4 pt-3  pb-3">
                    <h3 class="m-0">Top 5 Dealers</h3>
                    <div>On Business Transaction</div>
                </div>
                <div class="ibox-body">
                    <table class="table table-striped visitors-table" width="100%">
                        <thead>
                            <tr>
                                <th>Dealer Name</th>
                                <th class="text-right">Sale Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($top_five_sales as $top_five_sale)
                                <tr>
                                    <td>{{ $top_five_sale['short_name'] }}</td>
                                    <td class="text-right">{{ $top_five_sale['purchase'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-body">
                    <div class="flexbox mb-4">
                        <div>
                            <h3 class="m-0">Business Flow</h3>
                            <div>Sales & Collection analytics</div>
                        </div>
                        <div class="d-inline-flex">
                            <div class="px-3" style="border-right: 1px solid rgba(0,0,0,.1);">
                                <div class="text-muted">MONTHLY COLLECTION</div>
                                <div>
                                    <span class="h3 m-0">৳ {{ round($monthlycollection) }}</span>
                                    {{-- <span class="text-success ml-2"><i class="fa fa-level-up"></i> +25%</span> --}}
                                </div>
                            </div>
                            <div class="px-3">
                                <div class="text-muted">MONTHLY SALES</div>
                                <div>
                                    <span class="h3 m-0">৳ {{ $montlysales }}</span>
                                    {{-- <span class="text-warning ml-2"><i class="fa fa-level-down"></i> -12%</span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="col-md-12">
                            <div id="monthly_flow" style="height: 375px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





@endsection

@section('custom-js')
    <script src="{{ asset('public/admin-elite/dist/js/Chart.min.js') }}" type="text/javascript"></script>


    <script>
        $(function() {
            // var a = {
            //         labels: [
            @foreach ($total_sales_collections as $total_sale)
                // "{{ $total_sale['date'] }}",
                // @endforeach
            //         ],
            //         datasets: [{
            //             label: "Sales",
            //             borderColor: 'rgba(52,152,219,1)',
            //             backgroundColor: 'rgba(52,152,219,1)',
            //             pointBackgroundColor: 'rgba(52,152,219,1)',
            //             data: [
            @foreach ($total_sales_collections as $total_sale)
                // {{ $total_sale['total_sales'] }},
                // @endforeach
            //             ]
            //         }, {
            //             label: "Collection",
            //             backgroundColor: "#91acc7",
            //             borderColor: "#91acc7",
            //             data: [
            @foreach ($total_sales_collections as $total_collection)
                // {{ $total_collection['total_collection'] }},
                // @endforeach
            //             ]
            //         }]
            //     },
            //     t = {
            //         responsive: !0,
            //         maintainAspectRatio: !1
            //     },
            //     e = document.getElementById("line").getContext("2d");
            // new Chart(e, {
            //     type: "line",
            //     data: a,
            //     options: t
            // });




            // Morris.Bar({
            //     element: 'morris_bar_chart',
            //     data: [

            @foreach ($monthly_sales_collections as $sales_collection)
                // {
                // y: '{{ $sales_collection['month'] }}',
                // a: {{ $sales_collection['sales_amount'] }},
                // b: {{ $sales_collection['collection_amount'] }}
                // },
                // @endforeach
            //     ],
            //     xkey: 'y',
            //     ykeys: ['a', 'b'],
            //     labels: ['Sales', 'Collection'],
            //     hideHover: 'auto',
            //     resize: true,
            //     barColors: ['#2ecc71', '#c7cccf'],
            // });

            // Bar Chart example

            var barData = {
                labels: [
                    @foreach ($monthly_sales_collections as $sales_collection)
                        "{{ $sales_collection['month'] }}",
                    @endforeach
                ],
                datasets: [{
                        label: "Sales",
                        backgroundColor: '#2ecc71',
                        data: [
                            @foreach ($monthly_sales_collections as $sales_collection)
                                {{ $sales_collection['sales_amount'] }},
                            @endforeach
                        ]
                    },
                    {
                        label: "Collection",
                        backgroundColor: '#fb9678',
                        borderColor: "#fff",
                        data: [
                            @foreach ($monthly_sales_collections as $sales_collection)
                                {{ $sales_collection['collection_amount'] }},
                            @endforeach
                        ]
                    }
                ]
            };
            var barOptions = {
                responsive: true,
                maintainAspectRatio: false
            };

            var ctx = document.getElementById("bar_chart").getContext("2d");
            new Chart(ctx, {
                type: 'bar',
                data: barData,
                options: barOptions
            });

        });
    </script>



    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });

        google.charts.setOnLoadCallback(salesCollection);

        function salesCollection() {
            var data = google.visualization.arrayToDataTable([
                [
                    'Date',
                    'Sales',
                    'Collection'
                ],

                @foreach ($total_sales_collections as $sale_collection)
                    [
                    '{{ $sale_collection['date'] }}',
                    {{ $sale_collection['total_sales'] }},
                    {{ $sale_collection['total_collection'] }}
                    ],
                @endforeach
            ]);

            var dataList = {
                title: 'Business Flow',
                hAxis: {
                    title: 'Date',
                    titleTextStyle: {
                        color: '#333'
                    }
                },
                vAxis: {
                    minValue: 0
                }
            };

            var salesCollection = new google.visualization.LineChart(document.getElementById('monthly_flow'));
            salesCollection.draw(data, dataList);
        }
    </script>

@endsection
