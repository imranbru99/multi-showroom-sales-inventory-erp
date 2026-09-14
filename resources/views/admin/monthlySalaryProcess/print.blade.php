@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Salary Month Of {{ date('F, Y', strtotime($monthlySalary->month_year)) }}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th colspan="3">Basic Information</th>
                <th colspan="6">Allowence Brackdown</th>
                <th rowspan="2">Commission Payable</th>
                <th colspan="3">Deduction Amount</th>
                <th rowspan="2">Total Payable</th>
                <th colspan="2">Account Information</th>
                <th rowspan="2" width="120px">Sign</th>
            </tr>
            <tr>
                <th>SL</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>Salary Amount</th>
                <th>Basic</th>
                <th>House Rent</th>
                <th>Medical</th>
                <th>Conveyance</th>
                <th>Children</th>
                <th>Absent Charge</th>
                <th>Late Charge</th>
                <th>AIT</th>
                <th>A/C No</th>
                <th>Branch</th>
            </tr>
        </thead>

        <tbody>
            @php
                $total_salary_amount = 0;
                $total_basic = 0;
                $total_house_rent = 0;
                $total_medical = 0;
                $total_conveyance = 0;
                $total_children = 0;
                $total_commission_payable = 0;
                $total_absent_charge = 0;
                $total_late_charge = 0;
                $total_ait = 0;
                $total_total_payable = 0;
            @endphp

            @foreach ($monthlySalary->salaryProcessList as $salaryList)

                @php
                    $total_salary_amount += $salaryList->salary_amount;
                    $total_basic += $salaryList->basic;
                    $total_house_rent += $salaryList->house_rent;
                    $total_medical += $salaryList->medical;
                    $total_conveyance += $salaryList->conveyance;
                    $total_children += $salaryList->children;
                    $total_commission_payable += $salaryList->commission_payable;
                    $total_absent_charge += $salaryList->abcent_charge;
                    $total_late_charge += $salaryList->late_charge;
                    $total_ait += $salaryList->ait;
                    $total_total_payable += $salaryList->total_payable;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $salaryList->staff->code }}</td>
                    <td>{{ $salaryList->staff->name }}</td>
                    <td>{{ $salaryList->salary_amount }}</td>
                    <td>{{ $salaryList->basic }}</td>
                    <td>{{ $salaryList->house_rent }}</td>
                    <td>{{ $salaryList->medical }}</td>
                    <td>{{ $salaryList->conveyance }}</td>
                    <td>{{ $salaryList->children }}</td>
                    <td>{{ $salaryList->commission_payable }}</td>
                    <td>{{ $salaryList->abcent_charge }}</td>
                    <td>{{ $salaryList->late_charge }}</td>
                    <td>{{ $salaryList->ait }}</td>
                    <td>{{ $salaryList->total_payable }}</td>
                    <td>{{ @$salaryList->staff->ac_no }}</td>
                    <td>{{ @$salaryList->staff->ac_branch }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td>{{ number_format($total_salary_amount, 2, '.', '') }}</td>
                <td>{{ number_format($total_basic, 2, '.', '') }}</td>
                <td>{{ number_format($total_house_rent, 2, '.', '') }}</td>
                <td>{{ number_format($total_medical, 2, '.', '') }}</td>
                <td>{{ number_format($total_conveyance, 2, '.', '') }}</td>
                <td>{{ number_format($total_children, 2, '.', '') }}</td>
                <td>{{ number_format($total_commission_payable, 2, '.', '') }}</td>
                <td>{{ number_format($total_late_charge, 2, '.', '') }}</td>
                <td>{{ number_format($total_absent_charge, 2, '.', '') }}</td>
                <td>{{ number_format($total_ait, 2, '.', '') }}</td>
                <td>{{ number_format($total_total_payable, 2, '.', '') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
    <style>
        #report-table tfoot tr td {
            background-color: #28a745 !important;
            color: white;
            font-weight: bold
        }

    </style>
@endsection
