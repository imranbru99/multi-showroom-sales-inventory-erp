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
            <div class="col-lg-3 col-md-6 p-0 mt-4">
                <div class="ibox bg-info color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalDealers">0</h4>
                        <div class="m-b-5 card_text">DEALERS</div><i class="ti-user widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 mt-4">
                <div class="ibox bg-danger color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalSales">৳ 0</h4>
                        <div class="m-b-5 card_text">SALES</div><i class="ti-shopping-cart widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 mt-4">
                <div class="ibox bg-success color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalCollections">৳ 0</h4>
                        <div class="m-b-5 card_text">COLLECTION</div><i
                            class="ti-bar-chart widget-stat-icon text-center"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 mt-4">
                <div class="ibox bg-primary color-white widget-stat mx-3">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalOutstanding">৳ 0</h4>
                        <div class="m-b-5 card_text">OUTSTANDING</div><i class="fa fa-money widget-stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox color-white widget-stat mx-3" style="background-color: #245372">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalProduction">0</h4>
                        <div class="m-b-5 card_text">PRODUCTION</div><i class="ti-settings widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox color-white widget-stat mx-3" style="background-color: #605f5f">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalLifting">৳ 0</h4>
                        <div class="m-b-5 card_text">LIFTING</div><i class="ti-align-left widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox color-white widget-stat mx-3" style="background-color: #2e4e46">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalPayment">৳ 0</h4>
                        <div class="m-b-5 card_text">PAYMENT</div><i
                            class="ti-money widget-stat-icon text-center"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 p-0 my-4">
                <div class="ibox color-white widget-stat mx-3" style="background-color: #27254a">
                    <div class="ibox-body card_box">
                        <h4 class="m-b-5 font-strong" id="totalPaymentDue">৳ 0</h4>
                        <div class="m-b-5 card_text">PAYMENT DUE</div><i class="ti-receipt widget-stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- chart --}}
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Region Sales Chart</h3>
                    <div>Region Wise Sales Chart</div>
                </div>
                <div class="ibox-body">
                    <div id="piechart" style="height: 250px; display: block;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ibox">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Sales & Collection Chart</h3>
                    <div>12 Month Business Chart</div>
                </div>
                <div class="ibox-body">
                    <div>
                        <canvas id="bar_chart" style="height: 250px; display: block; width: 495px;" width="495" height="200"
                            class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="ibox" style="height: 597px;">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Outstanding Ratio</h3>
                    <div>Total Business Trading</div>
                </div>
                <div class="ibox-body">
                    <table class="table table-striped visitors-table" id="topTwelve" width="100%">
                        <thead style="background-color: #c9af8d">
                            <tr>
                                <th>Dealer Name</th>
                                <th class="text-center">Total Sales</th>
                                <th class="text-center">Total Collection</th>
                                <th>Due Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox" style="height: 246px;">
                <div class="px-4 pt-3">
                    <h3 class="m-0">Top 3 Seller</h3>
                    <div>On Business Collection</div>
                </div>
                <div class="ibox-body">
                    <table class="table table-striped visitors-table" id="topCollections" width="100%">
                        <thead style="background-color: #57c293">
                            <tr>
                                <th>Employee Name</th>
                                <th class="text-right">Collection</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="ibox" style="height: 326px;">
                <div class="px-4 pt-3  pb-3">
                    <h3 class="m-0">Top 5 Dealers</h3>
                    <div>On Business Transaction</div>
                </div>
                <div class="ibox-body">
                    <table class="table table-striped visitors-table" id="topFive" width="100%">
                        <thead style="background-color: #f29577">
                            <tr>
                                <th>Dealer Name</th>
                                <th class="text-right">Sale Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-12 col-md-12 p-0">
                    <div class="ibox color-white widget-stat mx-3" style="background-color: #2d1a52">
                        <div class="ibox-body card_box">
                            <h4 class="m-b-5 font-strong" id="stockQty">0</h4>
                            <div class="m-b-5 card_text">Stock Qty</div><i
                                class="fa fa-list-alt fa-2x widget-stat-icon text-center"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 p-0">
                    <div class="ibox color-white widget-stat mx-3" style="background-color: #5a2525">
                        <div class="ibox-body card_box">
                            <h4 class="m-b-5 font-strong" id="stockValue">0</h4>
                            <div class="m-b-5 card_text">Stock Value</div><i
                                class="fa fa-file-text fa-2x widget-stat-icon text-center"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 p-0">
                    <div class="ibox color-white widget-stat mx-3" style="background-color: #3a403f">
                        <div class="ibox-body card_box">
                            <h4 class="m-b-5 font-strong" id="staff">0</h4>
                            <div class="m-b-5 card_text">Current Staff</div><i
                                class="fa fa-th fa-2x widget-stat-icon text-center"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 p-0">
                    <div class="ibox color-white widget-stat mx-3" style="background-color: #30222b">
                        <div class="ibox-body card_box">
                            <h4 class="m-b-5 font-strong" id="customers">0</h4>
                            <div class="m-b-5 card_text">Retail Client</div><i
                                class="fa fa-users fa-2x widget-stat-icon text-center"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="ibox">
                <div class="ibox-body">
                    <div class="flexbox mb-4">
                        <div>
                            <h3 class="m-0">Business Flow</h3>
                            <div>Sales & Collection analytics</div>
                        </div>
                        <div class="d-inline-flex">
                            <div class="px-3" style="border-right: 1px solid rgba(0,0,0,.1);">
                                <div class="text-muted cs_text_header">MONTHLY COLLECTION</div>
                                <div>
                                    <span class="h4 m-0 cs_text">৳ {{ round($monthlycollection) }}</span>
                                </div>
                            </div>
                            <div class="px-3">
                                <div class="text-muted cs_text_header">MONTHLY SALES</div>
                                <div>
                                    <span class="h4 m-0 cs_text">৳ {{ round($montlysales) }}</span>
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

    <style>
        @media (max-width: 767px){
            .cs_text_header{
                font-size: 14px; 
            }
            .cs_text {
                font-size: 12px; 
            }
        }
    </style>
@endsection

@section('custom-js')
    <script src="{{ asset('public/admin-elite/dist/js/Chart.min.js') }}" type="text/javascript"></script>


    <script>
        $(function() {
            
//            Top 12 Dealers
            $.ajax({
                    type: 'get',
                    url: '{{ route('dashboard.topTwelveDealers') }}',
                    data: {},
                    success: function(data) {

                    if (data.top_dealers.length > 0) {
                        data.top_dealers.forEach(function(item, index) {
                            var value = parseInt(item.averagePercent);
                            var tr = `
                                <tr>
                                    <td>${item.dealer_name}</td>
                                    <td class="text-center">${item.purchase}</td>
                                    <td class="text-center">${item.collection}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" role="progressbar"
                                                style="width:${value}%; height:5px;"
                                                aria-valuenow="${value}"
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="progress-parcent">${item.averagePercent}%</span>
                                    </td>
                                </tr>
                                      `;
                        
                        
                            $('#topTwelve tbody').append(tr);
                        });

                    }
                    
                    $('#totalDealers').html(data.dealers);

                    }
                });
                
                
                ////Sales Collection
                $.ajax({
                    type: 'get',
                    url: '{{ route('dashboard.salesCollection') }}',
                    data: {},
                    success: function(data) {
                        $('#totalSales').html('৳ ' + data.sales);
                        $('#totalCollections').html('৳ ' + data.collections);
                        $('#totalOutstanding').html('৳ ' + data.outstanding);
                    }
                });
                
                
                
//              Payment Lifting Production
                $.ajax({
                    type: 'get',
                    url: '{{ route('dashboard.productionLift') }}',
                    data: {},
                    success: function(data) {
                        $('#totalProduction').html(data.productions);
                        $('#totalLifting').html('৳ ' + data.liftings);
                        $('#totalPayment').html('৳ ' + data.payments);
                        $('#totalPaymentDue').html('৳ ' + data.paymentDue);
                    }
                });
                
                
                // Stock
                $.ajax({
                    type: 'get',
                    url: '{{ route('dashboard.stock') }}',
                    data: {},
                    success: function(data) {
                        $('#stockQty').html(data.stockQty);
                        $('#stockValue').html('৳ ' + data.stockValue);
                        $('#staff').html(data.staff);
                        $('#customers').html(data.customers);
                    }
                });
                
                
                
//                Top 5 Dealers
                
                $.ajax({
                    type: 'get',
                    url: '{{ route('dashboard.topFiveDealers') }}',
                    data: {},
                    success: function(data) {

                    if (data.length > 0) {
                        data.forEach(function(item, index) {
                            var tr = `
                                <tr>
                                    <td>${item.short_name}</td>
                                    <td class="text-right">${item.purchase}</td>
                                </tr>
                                      `;
                        
                        
                            $('#topFive tbody').append(tr);
                        });

                    }


                    }
                });
        
        
//        topCollections
                $.ajax({
                    type: 'get',
                    url: '{{ route('dashboard.topCollections') }}',
                    data: {},
                    success: function(data) {

                    if (data.length > 0) {
                        data.forEach(function(item, index) {
                            var tr = `
                                <tr>
                                    <td>${item.name}</td>
                                    <td class="text-right">${item.total_amount}</td>
                                </tr>
                                      `;
                        
                        
                            $('#topCollections tbody').append(tr);
                        });

                    }


                    }
                });
        
        
        
        
        //Monthly Flow
        $.ajax({
                type: 'get',
                url: '{{ route('dashboard.monthlyFlow') }}',
                data: {},
                success: function(response) {
                    var barData = {
                        labels: response.month,
                        datasets: [{
                                label: "Sales",
                                backgroundColor: '#2ecc71',
                                data: response.sales_amount
                            },
                            {
                                label: "Collection",
                                backgroundColor: '#fb9678',
                                borderColor: "#fff",
                                data: response.collection_amount
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


                }
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
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Region', 'Amount'],
          @if(!empty($Regionsales) && count($Regionsales) > 0)
          @foreach($Regionsales as $Regionsale)
                ['{{ $Regionsale['name'] }}', {{ $Regionsale['amount'] }}],
            @endforeach
            @endif
        ]);

        var options = {
//          title: 'My Daily Activities',
          is3D: true,
          chartArea: {width: 400, height: 300},
          legend: { position: "none" },
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>

@endsection
