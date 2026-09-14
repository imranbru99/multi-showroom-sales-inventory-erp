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
                    Of {{ date('M Y', strtotime($fromDate)) }}
                </th>
            </tr>
        </thead>
    </table>
    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Group</th>
                <th>Month-Year</th>
                <th>Employee Name</th>
                <th>Salary</th>
                <th>Collect. By Customer</th>
                <th>Salary Ach</th>
                <th>Target</th>
                <th>Cash Ach.</th>
                <th>Hire Ach.</th>
                <th>MSP-C Ach.</th>
                <th>MSP-H Ach.</th>
                <th>Total Ach.</th>
                <th>Achive %</th>
                <th>Cash Comm</th>
                <th>Total Comm.</th>
                <th>Salary Ach</th>
                {{-- <th>Salary Ach</th> --}}
                <th>Agree.</th>
                <th>Agree. Comm</th>
                <th>Net Salary</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $sl = 1;
            $grandTarget = 0;
            $grandAvg = 0;
            $grandSalary = 0;
            
            $totalCC = 0;
            $totalHC = 0;
            $totalMspC = 0;
            $totalMspH = 0;
            $totalC = 0;
            $totalCom = 0;
            
            $grandCC = 0;
            $grandCAS = 0;
            $grandTAS = 0;
            $grandCM = 0;
            $grandCMAS = 0;
            
            $totalAgree = 0;
            $totalAgreeCom = 0;
            $totalnetSalary = 0;
            ?>
            @foreach ($data as $d)
                <?php
                
                $grandCM += $d['leader_info']['cash_com'];
                //customer collection
                if ($d['cus_col_per'] == 100) {
                    $customerAS = $d['leader_info']['salary'];
                } else {
                    if ($d['cus_col_per'] > 0) {
                        $customerAS = $d['leader_info']['salary'] * ($d['cus_col_per'] / 100);
                    } else {
                        $customerAS = 0;
                    }
                }
                
                //target achieve
                if ($d['leader_info']['average'] >= 100) {
                    $over = $d['leader_info']['totalCollection'] - $d['leader_info']['target'];
                    $targetAS = $customerAS + $d['leader_info']['totalCommission'];
                } else {
                    if ($d['leader_info']['average'] > 0) {
                        $targetAS = $customerAS * ($d['leader_info']['average'] / 100);
                    } else {
                        $targetAS = 0;
                    }
                }

                //cash commission
               // $achieve = $targetAS + $d['leader_info']['cash_com'];

                $netSalary = $targetAS;
                if($d['leader_info']['agrCom'] > 0){
                    $netSalary = $d['leader_info']['agrCom'] + $netSalary;
                }
                ?>
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $d['group'] }}</td>
                    <td>{{ $d['month'] }}</td>
                    <td>{{ $d['leader_info']['member'] }}</td>
                    <td align="right">{{ $d['leader_info']['salary'] }}</td>
                    <td align="right">{{ $d['cus_col_per'] }}%</td>
                    <td align="right">{{ number_format($customerAS, 2, '.', '') }}</td>
                    <td align="right">{{ $d['leader_info']['target'] }}</td>
                    <td align="right">{{ $d['leader_info']['cashCollection'] }}</td>
                    <td align="right">{{ $d['leader_info']['higherCollection'] }}</td>
                    <td align="right">{{ $d['leader_info']['mspCCollection'] }}</td>
                    <td align="right">{{ $d['leader_info']['mspHCollection'] }}</td>
                    <td align="right">{{ $d['leader_info']['totalCollection'] }}</td>
                    <td align="right">{{ $d['leader_info']['average'] }} %</td>
                    <td align="right">{{ $d['leader_info']['cash_com'] }}</td>
                    <td align="right">{{ $d['leader_info']['totalCommission'] }}</td>
                    <td align="right">{{ number_format($targetAS, 2, '.', '') }}</td>
                    {{-- <td align="right">{{ number_format($achieve, 2, '.', '') }}</td> --}}
                    <td align="right">{{ number_format($d['leader_info']['agreement'], 2, '.', '') }}</td>
                    <td align="right">{{ number_format($d['leader_info']['agrCom'], 2, '.', '') }}</td>
                    <td align="right">{{ number_format($netSalary, 2, '.', '') }}</td>
                </tr>
                <?php
                $grandTarget += $d['leader_info']['target'];
                $grandAvg += $d['leader_info']['average'];
                $grandSalary += $d['leader_info']['salary'];
                
                $totalCC += $d['leader_info']['cashCollection'];
                $totalHC += $d['leader_info']['higherCollection'];
                $totalMspC += $d['leader_info']['mspCCollection'];
                $totalMspH += $d['leader_info']['mspHCollection'];
                $totalC += $d['leader_info']['totalCollection'];
                $totalCom += $d['leader_info']['totalCommission'];
                
                $grandCC += $d['cus_col_per'];
                $grandCAS += $customerAS;
                $grandTAS += $targetAS;
                //$grandCMAS += $achieve;
                
                $totalAgree += $d['leader_info']['agreement'];
                $totalAgreeCom += $d['leader_info']['agrCom'];
                $totalnetSalary += $netSalary;
                ?>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($grandSalary, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">
                    {{ $grandCC > 0 ? number_format($grandCC / count($data), 2, '.', '') : 0.0 }} %</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($grandCAS, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($grandTarget, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalCC, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalHC, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalMspC, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalMspH, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalC, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">
                    {{ $grandAvg > 0 ? number_format($grandAvg / count($data), 2, '.', '') : 0.0 }} %</td>
                    <td style="text-align: right;font-weight: bold">{{ number_format($grandCM, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalCom, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($grandTAS, 2, '.', '') }}</td>
                {{-- <td style="text-align: right;font-weight: bold">{{ number_format($grandCMAS, 2, '.', '') }}</td> --}}
                <td style="text-align: right;font-weight: bold">{{ number_format($totalAgree, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalAgreeCom, 2, '.', '') }}</td>
                <td style="text-align: right;font-weight: bold">{{ number_format($totalnetSalary, 2, '.', '') }}</td>
            </tr>
        </tfoot>
    </table>



    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>

    <h4 style="text-align: center; background-color: green; padding: 5px; color: #fff">
        MSP Collection
    </h4>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="200px">Month-Year</th>
                <th>MSP Cash Collection</th>
                <th>MSP Hire Collection</th>
                <th>Total Collection</th>
                <th>Total Target</th>
                <th>MSP Cash Commission Without Discount Collection</th>
                <th>MSP Cash Commission Without Discount Commission</th>
                <th>MSP Cash Commission With Discount Collection</th>
                <th>MSP Cash Commission With Discount Commission</th>
                <th>MSP Hire Commission</th>
                <th>Total Commission</th>
            </tr>
        </thead>

        <tbody>
            @if (!empty($mspCollection))
                <tr>
                    <td>1</td>
                    <td>{{ $mspCollection['month'] }}</td>
                    <td align="right">{{ $mspCollection['cash'] }}</td>
                    <td align="right">{{ $mspCollection['haire'] }}</td>
                    <td align="right">{{ $mspCollection['total'] }}</td>
                    <td align="right">{{ $mspCollection['target'] }}</td>
                    <td align="right">{{ $mspCollection['CashC'] }}</td>
                    <td align="right">{{ $mspCollection['CashCC'] }}</td>
                    <td align="right">{{ $mspCollection['CashDC'] }}</td>
                    <td align="right">{{ $mspCollection['CashDCC'] }}</td>
                    <td align="right">{{ $mspCollection['hireC'] }}</td>
                    <td align="right">{{ $mspCollection['commission'] }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            @if (!empty($mspCollection))
                <tr>
                    <td colspan="2">Total</td>
                    <td align="right">{{ $mspCollection['cash'] }}</td>
                    <td align="right">{{ $mspCollection['haire'] }}</td>
                    <td align="right">{{ $mspCollection['total'] }}</td>
                    <td align="right">{{ $mspCollection['target'] }}</td>
                    <td align="right">{{ $mspCollection['CashC'] }}</td>
                    <td align="right">{{ $mspCollection['CashCC'] }}</td>
                    <td align="right">{{ $mspCollection['CashDC'] }}</td>
                    <td align="right">{{ $mspCollection['CashDCC'] }}</td>
                    <td align="right">{{ $mspCollection['hireC'] }}</td>
                    <td align="right">{{ $mspCollection['commission'] }}</td>
                </tr>
            @endif
        </tfoot>
    </table>
    <div id="pad-bottom"></div>
    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Group</th>
                <th width="200px">Month-Year</th>
                <th>Employee Name</th>
                <th>Commission Amount</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $l = 1;
            $totalMsp = 0;
            ?>
            @foreach ($msp as $m)
                <?php
                $totalMsp += $m['amount'];
                ?>
                <tr class="text-right">
                    <td align="center">{{ $l++ }}</td>
                    <td align="center">{{ $m['group'] }}</td>
                    <td align="center">{{ $m['month'] }}</td>
                    <td>{{ $m['name'] }}</td>
                    <td align="right">{{ number_format($m['amount'], 2, '.', '') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td align="right">{{ number_format($totalMsp, 2, '.', '') }}</td>
            </tr>
        </tfoot>
    </table>


    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection
