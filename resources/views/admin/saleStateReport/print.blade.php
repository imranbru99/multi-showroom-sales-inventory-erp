@extends('admin.layouts.masterPrint')

@php
$criteriaText = [
    'dealer' => 'Dealer',
    'd_internal' => 'Dealer (Internal)',
    'd_external' => 'Dealer (External)',
    'employee' => 'Employee',
    'dealer_type' => 'Dealer Type',
    'employee_dealer' => 'Employee Dealer',
];
@endphp

@section('content')
    <table id="report-header">
        <tr>
            <td>{{ $criteriaText[$criteria] }} Sales State ON
                @if (date('d-m-Y', strtotime($fromDate)) == '01-01-1970')
                    01-01-2020
                @else
                    {{ date('d-m-Y', strtotime($fromDate)) }}
                @endif
                To
                {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    @php
    $dealerCriterias = ['dealer', 'd_internal', 'd_external', 'employee_dealer'];
    @endphp

    @if (in_array($criteria, $dealerCriterias))

        @if (!empty($staff))
            <table style="width:100%;">
                <tr>
                    <td style="width:33%;">Empployee Name: {{ @$staff->name }}</td>
                    <td style="width:33%;text-align: right;">Empployee Phone: {{ @$staff->contact }}</td>
                </tr>
            </table>
        @endif

        <table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th width="100px">Dealer Name</th>
                    <th width="50px">Previous Balance</th>
                    <th width="50px">Sales Amount</th>
                    <th width="50px">Sales Return Amount</th>
                    <th width="50px">Actual Sales Amount</th>
                    <th width="50px">Collection Amount</th>
                    <th width="50px">Current Balance</th>
                    <th width="50px">Sales Qty</th>
                    <th width="50px">Sales Return Qty</th>
                    <th width="50px">Actual Sales Qty</th>
                    <th width="85px">Periodical Collec Ratio %</th>
                    <th width="85px">Average Collec Ratio %</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $sale = 0;
                    $collection = 0;
                    $return = 0;
                    $balance = 0;
                    $qty = 0;
                    $returnQty = 0;
                    $actualAmt = 0;
                    $actualQty = 0;
                    $preBalanceTotal = 0;
                    $currBalance = 0;
                    
                    $totalperiodPercent = 0;
                    $totalaveragePercent = 0;
                @endphp
                @foreach ($data as $d)
                    @php
                        $actualSale = $d['purchase'] - $d['return'];
                        if ($d['collection'] != 0 && $d['actualSale'] != 0) {
              
                            $periodPercent = ($d['collection'] / ($d['prev_balance'] + $d['actualSale'])) * 100;
                            $periodPercent = number_format($periodPercent, 2, '.', '');
                        
                        } else {
                            $periodPercent = 0;
                        }

                        if($d['total_collection'] != 0 && $d['total_sales'] != 0){
                            $averagePercent = ($d['total_collection'] / ($d['total_sales'] - $d['total_return'])) * 100;
                            $averagePercent =  number_format($averagePercent, 2, '.', '');
                        }else{
                            $averagePercent = 0;
                        }
                        
                        $totalperiodPercent += $periodPercent;
                        $totalaveragePercent += $averagePercent;

			if($d['prev_balance'] == 0 && $d['balance'] == 0 && $d['collection'] == 0){
        		    continue;
      			  }
                    @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $d['dealerName'] }}</td>
                        <td align="right">{{ $d['prev_balance'] }}</td>
                        <td align="right">{{ $d['purchase'] }}</td>
                        <td align="right">{{ $d['return'] }}</td>
                        <td align="right">{{ $d['actualSale'] }}</td>
                        <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
                        <td align="right"> {{ round($d['balance'], 2) }}</td>
                        <td align="right">{{ $d['qty'] }}</td>
                        <td align="right">{{ $d['returnQty'] }}</td>
                        <td align="right">{{ $d['actualQty'] }}</td>
                        {{-- <td align="right">{{ $d['creditP'] }}%</td> --}}
                        <td align="right"> {{ $periodPercent }}% </td>
                        <td align="right"> {{ $averagePercent }}% </td>
                    </tr>
                    @php
                        $sale += $d['purchase'];
                        $collection += $d['collection'];
                        $return += $d['return'];
                        $balance += $d['balance'];
                        $qty += $d['qty'];
                        $returnQty += $d['returnQty'];
                        $actualAmt += $d['actualSale'];
                        $actualQty += $d['actualQty'];
                        $preBalanceTotal += $d['prev_balance'];
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                @php
                    
                    $totalDue = $preBalanceTotal + $actualAmt; //ex: 10000
                    
                    if ($totalDue == 0) {
                        $totalDue = 1;
                    }
                    
                    $collection_a = $collection; // ex: 2500
                    $dueMinusCollection = $totalDue - $collection_a; //ex: 7500
                    // $creditP = ($dueMinusCollection / $totalDue) * 100; //ex: 75%;
                    $totalPPercent = $totalperiodPercent / count($data);
                    $totalAPercent = $totalaveragePercent / count($data);
                    
                @endphp
                <tr>
                    <th colspan="2">Total</th>
                    <th>{{ $preBalanceTotal }}</th>
                    <th>{{ $sale }}</th>
                    <th>{{ $return }}</th>
                    <th>{{ $actualAmt }}</th>
                    <th>{{ $collection }}</th>
                    <th>{{ round($balance, 2) }}</th>
                    <th>{{ $qty }}</th>
                    <th>{{ $returnQty }}</th>
                    <th>{{ $actualQty }}</th>
                    <th>{{ round($totalPPercent, 2) }} %</th>
                    <th>{{ round($totalAPercent, 2) }} %</th>
                </tr>
            </tfoot>
        </table>

    @endif

    @if ($criteria == 'employee')

        <table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th width="100px">Employee Name</th>
                    <th width="50px">Previous Balance</th>
                    <th width="50px">Sales Amount</th>
                    <th width="50px">Sales Return Amount</th>
                    <th width="50px">Actual Sales Amount</th>
                    <th width="50px">Collection Amount</th>
                    <th width="50px">Current Balance</th>
                    <th width="50px">Sales Qty</th>
                    <th width="50px">Sales Return Qty</th>
                    <th width="50px">Actual Sales Qty</th>
                    <th width="85px">Periodical Collec Ratio %</th>
                    <th width="85px">Average Collec Ratio %</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $sale = 0;
                    $preBalanceTotal = 0;
                    $collection = 0;
                    $return = 0;
                    $balance = 0;
                    $qty = 0;
                    $currBalance = 0;
                    $returnQty = 0;
                    $actualAmt = 0;
                    $actualQty = 0;
                    
                    $totalperiodPercent = 0;
                    $totalaveragePercent = 0;
                @endphp

                @foreach ($data as $d)
                    @php
			$actualSale = $d['purchase'] - $d['return'];
                        if ($d['collection'] != 0 && $d['actualSale'] != 0) {
              
                            $periodPercent = ($d['collection'] / ($d['prev_balance'] + $d['actualSale'])) * 100;
                            $periodPercent = number_format($periodPercent, 2, '.', '');
                        
                        } else {
                            $periodPercent = 0;
                        }

                        if($d['total_collection'] != 0 && $d['total_purchase'] != 0){
                            $averagePercent = ($d['total_collection'] / ($d['total_purchase'] - $d['total_return'])) * 100;
                            $averagePercent =  number_format($averagePercent, 2, '.', '');
                        }else{
                            $averagePercent = 0;
                        }
                        
                        $totalperiodPercent += $periodPercent;
                        $totalaveragePercent += $averagePercent;

			$balance =$d['prev_balance'] + $actualSale - $d['collection'];

      			  if($d['prev_balance'] == 0 && $balance == 0 && $d['collection'] ==0){
         		   continue;
       			 }
                        
                    @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $d['employeeName'] }}</td>
                        <td align="right">{{ $d['prev_balance'] }}</td>
                        <td align="right">{{ $d['purchase'] }}</td>
                        <td align="right">{{ $d['return'] }}</td>
                        <td align="right">{{ $d['actualSale'] }}</td>
                        <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
                        <td align="right"> {{ round($d['balance'], 2) }}</td>
                        <td align="right">{{ $d['qty'] }}</td>
                        <td align="right">{{ $d['returnQty'] }}</td>
                        <td align="right">{{ $d['actualQty'] }}</td>
                        {{-- <td align="right">{{ $d['creditP'] }}%</td> --}}
                        <td align="right"> {{ $periodPercent }}% </td>
                        <td align="right"> {{ $averagePercent }}% </td>
                    </tr>
                    @php
                        $preBalanceTotal += $d['prev_balance'];
                        $sale += $d['purchase'];
                        $collection += $d['collection'];
                        $return += $d['return'];
                        $balance += $d['balance'];
                        $qty += $d['qty'];
                        $returnQty += $d['returnQty'];
                        $actualAmt += $d['actualSale'];
                        $actualQty += $d['actualQty'];
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                @php
                    
                    $totalDue = $preBalanceTotal + $actualAmt; //ex: 10000
                    
                    if ($totalDue == 0) {
                        $totalDue = 1;
                    }
                    
                    $collection_a = $collection; // ex: 2500
                    $dueMinusCollection = $totalDue - $collection_a; //ex: 7500
                    // $creditP = ($dueMinusCollection / $totalDue) * 100; //ex: 75%;
                    
                    $totalPPercent = $totalperiodPercent / count($data);
                    $totalAPercent = $totalaveragePercent / count($data);
                    
                @endphp
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th>{{ $preBalanceTotal }}</th>
                    <th>{{ $sale }}</th>
                    <th>{{ $return }}</th>
                    <th>{{ $actualAmt }}</th>
                    <th>{{ $collection }}</th>
                    <th>{{ round($balance, 2) }}</th>
                    <th>{{ $qty }}</th>
                    <th>{{ $returnQty }}</th>
                    <th>{{ $actualQty }}</th>
                    <th>{{ round($totalPPercent, 2) }} %</th>
                    <th>{{ round($totalAPercent, 2) }} %</th>
                </tr>
            </tfoot>
        </table>

    @endif

    @if ($criteria == 'dealer_type')

        <table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Dealer Type</th>
                    <th width="90px">Sales Amount</th>
                    <th width="105px">Sales Return Amount</th>
                    <th width="105px">Actual Sales Amount</th>
                    <th width="85px">Collection Amount</th>
                    <th width="85px">Outstanding</th>
                    {{-- <th width="85px">Outstanding</th> --}}
                    <th width="85px">Sales Qty</th>
                    <th width="85px">Sales Return Qty</th>
                    <th width="85px">Actual Sales Qty</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                    $sale = 0;
                    $collection = 0;
                    $return = 0;
                    $balance = 0;
                    $qty = 0;
                    $returnQty = 0;
                    $actualAmt = 0;
                    $actualQty = 0;
                    $outstanding = 0;
                @endphp

                @foreach ($data as $d)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $d['employeeName'] }}</td>
                        <td align="right">{{ $d['purchase'] }}</td>
                        <td align="right">{{ $d['return'] }}</td>
                        <td align="right">{{ $d['purchase'] - $d['return'] }}</td>
                        <td align="right">{{ number_format($d['collection'], 2, '.', '') }}</td>
                        <td align="right">{{ number_format($d['outstanding'], 2, '.', '') }}</td>
                        {{-- <td align="right">{{ number_format($d['balance'], 2, '.', '') }}</td> --}}
                        <td align="right">{{ $d['qty'] }}</td>
                        <td align="right">{{ $d['returnQty'] }}</td>
                        <td align="right">{{ $d['qty'] - $d['returnQty'] }}</td>
                    </tr>
                    @php
                        $sale += $d['purchase'];
                        $collection += $d['collection'];
                        $return += $d['return'];
                        $balance += $d['balance'];
                        $qty += $d['qty'];
                        $returnQty += $d['returnQty'];
                        $actualAmt += $d['purchase'] - $d['return'];
                        $actualQty += $d['qty'] - $d['returnQty'];
                        $outstanding += $d['outstanding'];
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th>{{ $sale }}</th>
                    <th>{{ $return }}</th>
                    <th>{{ $actualAmt }}</th>
                    <th>{{ $collection }}</th>
                    <th>{{ $outstanding }}</th>
                    {{-- <th>{{ $balance }}</th> --}}
                    <th>{{ $qty }}</th>
                    <th>{{ $returnQty }}</th>
                    <th>{{ $actualQty }}</th>
                </tr>
            </tfoot>
        </table>

    @endif

    <div class="row">
        <div class="col-md-12 text-right">
            <p>Print Date & Time : {{ date('d-m-Y h:i:sa', strtotime(now())) }}</p>
        </div>
    </div>

@endsection
