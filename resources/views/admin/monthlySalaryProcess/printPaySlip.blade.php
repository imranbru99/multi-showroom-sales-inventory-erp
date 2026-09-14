@extends('admin.layouts.masterPrint')

@section('content')
    <table width="100%">
        <tbody>
            <tr>
                <td><b>Employee Code: </b> {{ $monthlySalary->staff->code }}</td>
                <td style="text-align: right"><b>Payment Date: </b>@if ($monthlySalary->payment_date) {{ date('d-m-Y', strtotime($monthlySalary->payment_date)) }} @else Payment Pending @endif</td>
            </tr>
            <tr>
                <td><b>Employee Name: </b> {{ $monthlySalary->staff->name }}</td>
                <td style="text-align: right"><b>Month of Salary:
                    </b>{{ date('F', strtotime($monthlySalary->month_year)) }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: right"><b>Year of Salary: </b>
                    {{ date('Y', strtotime($monthlySalary->month_year)) }}</td>
                </td>
            </tr>
        </tbody>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <tbody>
            <tr>
                <td rowspan="5" align="center">Addition</td>
                <td>Basic</td>
                <td align="right">{{ $monthlySalary->basic }}</td>
                <td align="right">0.00</td>
            </tr>
            <tr>
                <td>House Rent</td>
                <td align="right">{{ $monthlySalary->house_rent }}</td>
                <td align="right">0.00</td>
            </tr>
            <tr>
                <td>Medical</td>
                <td align="right">{{ $monthlySalary->medical }}</td>
                <td align="right">0.00</td>
            </tr>
            <tr>
                <td>Conveyance</td>
                <td align="right">{{ $monthlySalary->conveyance }}</td>
                <td align="right">0.00</td>
            </tr>
            <tr>
                <td>Children</td>
                <td align="right">{{ $monthlySalary->children }}</td>
                <td align="right">0.00</td>
            </tr>
            <tr>
                <td rowspan="3" align="center">Deduction</td>
                <td>Absent Charge</td>
                <td align="right">0.00</td>
                <td align="right">{{ $monthlySalary->abcent_charge }}</td>
            </tr>
            <tr>
                <td>Late Charge</td>
                <td align="right">0.00</td>
                <td align="right">{{ $monthlySalary->late_charge }}</td>
            </tr>
            <tr>
                <td>AIT</td>
                <td align="right">0.00</td>
                <td align="right">{{ $monthlySalary->ait }}</td>
            </tr>
            <tr style="background-color: lightgray;">
                <td colspan="2" align="right"><b>Total</b></td>
                <td align="right">
                    <b>
                        {{ number_format($monthlySalary->basic + $monthlySalary->house_rent + $monthlySalary->medical + $monthlySalary->conveyance + $monthlySalary->children, 2, '.', '') }}
                    </b>
                </td>
                <td align="right">
                    <b>
                        {{ number_format($monthlySalary->late_charge + $monthlySalary->abcent_charge + $monthlySalary->ait, 2, '.', '') }}
                    </b>
                </td>
            </tr>
        </tbody>
    </table>


    <table id="total" width="100%" style="margin-top: 10px">
        <tfoot style="margin-top:30px !important">
            <tr>
                <td colspan="2" style="font-weight: bold; font-size:12px; text-align: right">Net Payable: </td>
                <td style="font-weight: bold; font-size:12px; text-align: right">{{ $monthlySalary->total_payable }}</td>
            </tr>
        </tfoot>
    </table>

    <table style="margin-top: 100px">
        <tr>
            <td align="center">___________________</td>
            <td align="center">_______________________</td>
            <td align="center">_____________________</td>
        </tr>
        <tr>
            <td align="center">Accountant</td>
            <td align="center">Managing Director</td>
            <td align="center">Employee</td>
        </tr>
    </table>

      
    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
    <style>
        td {
            width: 50%;
        }

        #report-table td {
            font-size: 20px !important;
        }

        #total tfoot td {
            background-color: #d4d4d4 !important;
        }

    </style>
@endsection
