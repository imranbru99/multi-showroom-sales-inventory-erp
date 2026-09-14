@extends('admin.layouts.masterView')

@section('custom_css')
<style type="text/css">
    .info_head {
        font-weight: bold;
    }
    .table-borderless tr td {
        border: none !important;
        /*padding: 0px !important;*/
    }
</style>
@endsection

@section('card_body')
<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <table id="" class="table table-borderless">
                <tbody>
                    <tr>
                        <td><b>Employee Code: </b>{{ $monthlySalary->staff->code }}</td>
                        <td style="text-align: right"><b>Payment Date: </b>@if($monthlySalary->payment_date) {{ date('d-m-Y', strtotime($monthlySalary->payment_date)) }} @else Payment Pending @endif</td>
                    </tr>
                    <tr>
                        <td><b>Employee Name: </b>{{ $monthlySalary->staff->name }}</td>
                        <td style="text-align: right"><b>Month of Salary: </b>{{ date('F', strtotime($monthlySalary->month_year)) }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="text-align: right"><b>Year of Salary: </b> {{ date('Y', strtotime($monthlySalary->month_year)) }}</td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="" class="table table-bordered table-sm productTable">
                    <thead class="">
                        <tr class="text-center bg-success text-white">
                            <th colspan="2">Addition</th>
                            <th colspan="2">Deduction</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Basic</td>
                            <td>{{ $monthlySalary->basic }}</td>
                            <td>Late Charge</td>
                            <td>{{ $monthlySalary->late_charge }}</td>
                        </tr>
                        <tr>
                            <td>House Rent</td>
                            <td>{{ $monthlySalary->house_rent }}</td>
                            <td>Absent Charge</td>
                            <td>{{ $monthlySalary->abcent_charge }}</td>
                        </tr>
                        <tr>
                            <td>Medical</td>
                            <td>{{ $monthlySalary->medical }}</td>
                            <td>AIT</td>
                            <td>{{ $monthlySalary->ait }}</td>
                        </tr>
                        <tr>
                            <td>Conveyance</td>
                            <td>{{ $monthlySalary->conveyance }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Children</td>
                            <td>{{ $monthlySalary->children }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-header">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-end">
                <a class="btn btn-outline-info btn-lg mr-2" href="{{ route('monthlySalaryProcess.printPaySlip', $monthlySalary->id) }}" target="_blank">
                    <i class="fa fa-print"></i> Print
                </a>
                @if($monthlySalary->payment_status != 1)
                <a>
                    <form action="{{ route('monthlySalaryProcess.payment') }}" method="post"> 
                        {{ csrf_field() }}
                        <input type="hidden" name="paymentId" value="{{ $monthlySalary->id }}">
                        <button type="submit" class="btn btn-outline-info btn-lg">
                            <i class="fa fa-credit-card"></i> Payment
                        </button>
                    </form>
                </a>
                @else
                <a class="btn btn-outline-info btn-lg mr-2" href="{{ route('monthlySalaryProcess.printPaySlip', $monthlySalary->id) }}" target="_blank">
                    <i class="fa fa-credit-card-alt"></i> Paid
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection