@extends('admin.layouts.masterPrint')

@section('content')
@php
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\Product;
@endphp
<table id="report-header">
    <thead>
        <tr>
            <th>
                {{ @$title }}
                Of {{ date('F Y', strtotime($month)) }}
            </th>
        </tr>
    </thead>
</table>
<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>EmployeeName</th>
            {{-- <th>DealerCollection Target</th> --}}

            @if ($showroomId == 1)
            <th>Dealer Target</th>
            <th>DealerCollection Commission</th>
            @endif


            {{-- <th>Retail CashCollection Target</th> --}}
            <th>Retail Target</th>
            <th>Hire Target</th>
            <th>Retail Discount Collection Commission</th>
            <th>Retail Cash Without Discount Collection Commission</th>
            <th>Retail Cash With Discount Collection Commission</th>
            {{-- <th>Retail HireCollection Target</th> --}}
            <th>Retail HireCollection Commission</th>
            {{-- <th>Recovery CashCollection Target</th> --}}



            <th>Recovery CashCollection Commission</th>
            {{-- <th>Recovery HireCollection Target</th> --}}
            <th>Recovery HireCollection Commission</th>
            {{-- <th>MSPCHTarget</th> --}}
            <th>MSP Cash Discount Commission</th>
            <th>MSP Cash Without Discount Commission</th>
            <th>MSP Cash With Discount Commission</th>
            <th>MSP Hire Commission</th>





            <th>Total Commission</th>
        </tr>
    </thead>

    <tbody>
        <?php
            
                $totalDealerTarget = 0;
                $totalDealer = 0;
                $totalreC = 0;
                $totalreH = 0;
                $totalmspCc = 0;
                $totalmspC = 0;
                $totalmspDC = 0;
                $totalmspH = 0;

            $totalMemComm = 0;
            
            $totalretailCc = 0;
            $totalretailC = 0;
            $totalretailDC = 0;
            $totalretailH = 0;
            
            ?>
        @if ($data)
        <?php
                

                        $totalCollection = $data['collections']['dealer'] + $data['collections']['retailCc'] + $data['collections']['retailC'] + $data['collections']['retailDC'] + $data['collections']['retailH'] + $data['collections']['mspCc'] + $data['collections']['mspC'] + $data['collections']['mspDC'] + $data['collections']['mspH'] + $data['collections']['reC'] + $data['collections']['reH'];
                        $totalCommssion = $data['commissions']['dealer'] + $data['commissions']['retailCc'] + $data['commissions']['retailC'] + $data['commissions']['retailDC'] + $data['commissions']['retailH'] + $data['commissions']['mspCc'] + $data['commissions']['mspC'] + $data['commissions']['mspDC'] + $data['commissions']['mspH'] + $data['commissions']['reC'] + $data['commissions']['reH'];

                    // $totalCollection = $data['collections']['retailCc'] + $data['collections']['retailC'] + $data['collections']['retailDC'] + $data['collections']['retailH'];
                    // $totalCommssion = $data['commissions']['retailCc'] + $data['commissions']['retailC'] + $data['commissions']['retailDC'] + $data['commissions']['retailH'];

                ?>
        <tr>
            <td style="font-weight: bold" colspan="2">Total Collection</td>


            @if ($showroomId == 1)
            <td align="right" style="font-weight: bold">{{ $data['collections']['dealerTarget'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['dealer'] }}</td>
            @endif



            <td align="right" style="font-weight: bold">{{ $data['collections']['retailTarget'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['hireTarget'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['retailCc'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['retailC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['retailDC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['retailH'] }}</td>


            <td align="right" style="font-weight: bold">{{ $data['collections']['reC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['reH'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['mspCc'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['mspC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['mspDC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['collections']['mspH'] }}</td>




            <td align="right" style="font-weight: bold">{{ number_format($totalCollection, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold" colspan="2">Total Commission</td>



            @if ($showroomId == 1)
            <td align="right" style="font-weight: bold"></td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['dealer'] }}</td>
            @endif



            <td align="right" style="font-weight: bold"></td>
            <td align="right" style="font-weight: bold"></td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['retailCc'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['retailC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['retailDC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['retailH'] }}</td>



            <td align="right" style="font-weight: bold">{{ $data['commissions']['reC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['reH'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['mspCc'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['mspC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['mspDC'] }}</td>
            <td align="right" style="font-weight: bold">{{ $data['commissions']['mspH'] }}</td>




            <td align="right" style="font-weight: bold">{{ number_format($totalCommssion, 2, '.', '') }}</td>
        </tr>
        @foreach ($data['data'] as $d)
        <?php

                        $employeeTarget = \App\EmployeeCommissionAllocationList::where('staff_id', $d['dealer_id'])
                        ->where('showroom_id', $showroomId)
                        ->with(['allocation'])
                        ->whereHas('allocation', function($q) use($month){
                            $q->where('month', $month);
                        })
                        ->get();

                        $mspDC = ($data['commissions']['mspDC'] / 100 ) * $employeeTarget->sum('msp');
                    
                        $totalDealer += $d['dealer'];
                        $totalreC += $d['reC'];
                        $totalreH += $d['reH'];
                        $totalmspCc += $d['mspCc'];
                        $totalmspC += $d['mspC'];
                        // $totalmspDC += $d['mspDC'];
                        $totalmspDC += $mspDC;
                        $totalmspH += $d['mspH'];
                        $totalretailCc += $d['retailCc'];
                        $totalretailC += $d['retailC'];
                        $totalretailDC += $d['retailDC'];
                        $totalretailH += $d['retailH'];
                        $memCommission = $d['dealer'] + $d['retailCc'] + $d['retailC'] + $d['retailDC'] + $d['retailH'] + $d['reC'] + $d['reH'] + $d['mspC'] + $mspDC + $d['mspH'] + $d['mspCc'];
                    
                        $totalMemComm += $memCommission;

                    ?>
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d['name'] }}</td>


            @if ($showroomId == 1)
            <td align="right"></td>
            <td align="right">{{ $d['dealer'] }} <br> ({{ $employeeTarget->sum('dealer_collection') }}%)</td>
            @endif


            <td align="right"></td>
            <td align="right"></td>
            <td align="right">{{ $d['retailCc'] }} <br> ({{ $employeeTarget->sum('retail_cash_collection') }}%)
            <td align="right">{{ $d['retailC'] }} <br> ({{ $employeeTarget->sum('retail_cash_collection') }}%)
            </td>
            <td align="right">{{ $d['retailDC'] }} <br> ({{ $employeeTarget->sum('retail_cash_collection') }}%)
            </td>
            <td align="right">{{ $d['retailH'] }} <br> ({{ $employeeTarget->sum('retail_hire_collection') }}%)
            </td>




            <td align="right">{{ $d['reC'] }} <br>
                ({{ $employeeTarget->sum('recovery_cash_collection') }}%)
            </td>
            <td align="right">{{ $d['reH'] }} <br>
                ({{ $employeeTarget->sum('recovery_hire_collection') }}%)
            </td>
            <td align="right">{{ $d['mspCc'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>
            <td align="right">{{ $d['mspC'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>
            {{-- <td align="right">{{ $d['mspDC'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td> --}}
            <td align="right">{{ ($data['commissions']['mspDC'] / 100 ) * $employeeTarget->sum('msp') }} <br> ({{
                $employeeTarget->sum('msp') }}%)</td>
            <td align="right">{{ $d['mspH'] }} <br> ({{ $employeeTarget->sum('msp') }}%)</td>



            <td align="right">{{ $memCommission }}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
    <tfoot>
        <tr>
            @if ($showroomId == 1)
            <th colspan="3">Total</th>
            @else
            <th colspan="2">Total</th>
            @endif

            @if ($showroomId == 1)
            <th align="right">{{ number_format($totalDealer, 2, '.', '') }}</th>
            @endif



            <th align="right"></th>
            <th align="right"></th>
            <th align="right">{{ number_format($totalretailCc, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalretailC, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalretailDC, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalretailH, 2, '.', '') }}</th>



            <th align="right">{{ number_format($totalreC, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalreH, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalmspCc, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalmspC, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalmspDC, 2, '.', '') }}</th>
            <th align="right">{{ number_format($totalmspH, 2, '.', '') }}</th>




            <th align="right">{{ number_format($totalMemComm, 2, '.', '') }}</th>
        </tr>
    </tfoot>

</table>



<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time :
            <?php echo date('d-m-Y h:i:sa'); ?>
        </p>
    </div>
</div>
@endsection