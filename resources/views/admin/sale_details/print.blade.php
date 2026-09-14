@extends('admin.layouts.masterPrint')

@section('content')
    {{-- <style>
        th {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
        }

    </style> --}}

    <table id="report-header">
        <tr>
            <td>Sales Details List On {{ date('d-m-Y', strtotime($fromDate)) }} To
                {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>



    <div class="card-body">
        @foreach ($eWiseProductIssuelist as $data)

            @php
                $totalBalance = 0;
                $totalQty = 0;
                $totalAmount = 0;
                $totalCollection = 0;
                $totalBalance2 = 0;
                $productsSum = 0;
                
            @endphp

            @foreach ($data['productIssuelist'] as $issue)

                @php
                    $tempModelName = [];
                    // $byModel = $issue->products->groupBy('model_no');
                    // $byModel = $issue->products->where('collection', '>', 0)->groupBy('model_no');
                    
                    if ($param == 'bySale') {
                        $byModel = $issue->products->groupBy('model_no');
                    } else {
                        $byModel = $issue->products->where('collection', '>', 0)->groupBy('model_no');
                    }
                @endphp

                @foreach ($byModel as $model => $products)
                    @php
                        $totalBalance += round($products->sum('amount') - $products->sum('collection'), 2);
                        $productsSum += $products->sum('amount');
                    @endphp
                @endforeach
            @endforeach

            {{-- <table style="margin-top: 30px;width: 100%;">
                <tr>
                    <td style="width: 48%;  float: left;">
                        <p class="text-left">Sale By - {{ $data['eInfo']->name }}</p>
                    </td>
                    <td style="width: 48%; text-align: right;">
                        <p>Due Amount - {{ $totalBalance }}</p>
                    </td>
                </tr>
            </table> --}}

            @if ($productsSum > 0)

                <table id="report-table" style="margin-top: 30px;">
                    <thead>
                        <tr>
                            <th>SL#</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Product Name</th>
                            <th>Model No</th>
                            <th>Qty</th>
                            <th>Actual Sale</th>
                            <th>Collection</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($data['productIssuelist'] as $issue)
                            @php
                                $tempModelName = [];
                                // $byModel = $issue->products->groupBy('model_no');
                                // $byModel = $issue->products->where('collection', '>', 0)->groupBy('model_no');
                                
                                if ($param == 'bySale') {
                                    $byModel = $issue->products->groupBy('model_no');
                                } else {
                                    $byModel = $issue->products->where('collection', '>', 0)->groupBy('model_no');
                                }
                            @endphp

                            @foreach ($byModel as $model => $products)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ date('d-m-Y', strtotime($issue->date)) }}</td>
                                    <td>{{ $issue->issue_no }}</td>
                                    <td>
                                        {{ $products[0]->product->name }}
                                    </td>
                                    <td>
                                        {{ $model }}
                                    </td>
                                    <td align="right">{{ $products->sum('qty') }}</td>
                                    <td align="right">{{ round($products->sum('amount'), 2) }}</td>
                                    <td align="right">
                                        {{ round($products->sum('collection'), 2) }}
                                    </td>
                                    <td align="right">
                                        {{ round($products->sum('amount') - $products->sum('collection'), 2) }}
                                    </td>
                                </tr>
                                @php
                                    $totalQty += $products->sum('qty');
                                    $totalAmount += round($products->sum('amount'), 2);
                                    $totalCollection += round($products->sum('collection'), 2);
                                @endphp
                            @endforeach
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" align="right">Sale By {{ $data['eInfo']->name }}</th>
                            <th>{{ $totalQty }}</th>
                            <th>{{ $totalAmount }}</th>
                            <th>{{ $totalCollection }}</th>
                            <th>{{ $totalAmount - $totalCollection }}</th>
                        </tr>
                    </tfoot>
                </table>

            @endif

        @endforeach
        <div class="row">
            <div class="col-md-12 text-right">
                <?php date_default_timezone_set('Asia/Dhaka'); ?>
                <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
            </div>
        </div>
    </div>
@endsection
