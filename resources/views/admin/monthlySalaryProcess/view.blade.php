@extends('admin.layouts.masterView')

@section('custom_css')
<style type="text/css">
    .info_head {
        font-weight: bold;
    }
</style>
@endsection

@section('card_body')
<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="">Salary Month Of {{ date('F, Y', strtotime($monthlySalary->month_year)) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-sm productTable">
                    <thead class="text-center">
                        <tr>
                            <th colspan="4">Basic Information</th>
                            <th colspan="5">Allowence Brackdown</th>
                            <th rowspan="2">Commission Payable</th>
                            <th colspan="3">Deduction Amount</th>
                            <th rowspan="2">Total Payable</th>
                            <th rowspan="2">Action</th>
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
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($monthlySalary->salaryProcessList as $salaryList)
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
                            <td>
                                <a href="{{ route('monthlySalaryProcess.viewByEmployee', $salaryList->id) }}"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection