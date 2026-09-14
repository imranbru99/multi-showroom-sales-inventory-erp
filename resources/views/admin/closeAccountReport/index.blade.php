<?php
use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
use Illuminate\Support\Facades\DB;
?>
@extends('admin.layouts.masterReport')
@section('search_card_body')
    <input type="hidden" value="true" name="searched">
    <div class="row d-flex justify-content-center">
        <div class="col-md-6 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="from_date"
                name="fromDate" placeholder="Select Date From" value="" required>
        </div>
        <div class="col-md-6 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="to_date" name="toDate"
                placeholder="Select Date To" value="" required>
            <input type="hidden" name="stockSearch" value="stockStatus">
        </div>
        <div class="col-md-4">
            <label for="customer">Type</label>
            <div class="form-group">
                <select name="type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="Cash" @if($type == 'Cash') selected @endif>Cash</option>
                    <option value="Short Installment" @if($type == 'Short Installment') selected @endif>Short Installment</option>
                    <option value="Long Installment" @if($type == 'Long Installment') selected @endif>Long Installment</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <label for="customer">Project</label>
            <div class="form-group">
                <select name="project" class="form-control">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @if ($project->id == $project_id) selected @endif>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-4">
            <label for="staff">Employee</label>
            <select name="staff" class="form-control chosen-select">
                <option value="">Select Employee</option>
                @foreach ($staffs as $staff)
                    <option value="{{ $staff->id }}" @if ($staff->id == $staffId) selected @endif>{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>

    </div>
@endsection

@section('print_card_header')
    <input type="hidden" class="form-control" name="project" value="{{ $project_id }}">
    <input type="hidden" class="form-control" name="staff" value="{{ $staffId }}">
    <input type="hidden" class="form-control" name="type" value="{{ $type }}">
    <input type="hidden" class="form-control datepicker" id="from_date"
                name="fromDate" placeholder="Select Date From" value="{{ $fromDate }}" required>
    <input type="hidden" class="form-control datepicker" id="to_date" name="toDate"
                placeholder="Select Date To" value="{{ $toDate }}" required>      
    <input type="hidden" id="print_value" name="print" value="Print">
@endsection

@section('print_card_body')
    <table id="dataTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>Invoice Date</th>
                <th>Last Collec. Date</th>
                <th>Late Days</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Sales By</th>
                <th>Invoice</th>
                <th>Inv. Amnt.</th>
                <th>Inter. Rec.</th>
                <th>Late Fee</th>
                <th>Mis. Amnt.</th>
                <th>Collec.</th>
                <th>Close Dis.</th>
                <th>Bal.</th>
                <th>Closed By</th>
            </tr>
        </thead>

        <tbody>
            @php
                $i = 1;
            @endphp

            @foreach ($closes as $close)

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$close['sale_date'])) }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$close['last_col_date'])) }}</td>
                    <td>{{ $close['late_days'] }}</td>
                    <td>{{ @$close['customer_code'] }}</td>
                    <td>{{ @$close['customer_name'] }}</td>
                    <td>{{ @$close['phone_no'] }}</td>
                    <td>{{ @$close['address'] }}</td>
                    <td>{{ @$close['sales_by'] }}</td>
                    <td>{{ $close['invoice_no'] }}</td>
                    <td>{{ $close['sales_amount'] }}</td>
                    <td>{{ @$close['interest_receive'] }}</td>
                    <td>{{ $close['late_fee'] }}</td>
                    <td>{{ @$close['missing_amount'] }}</td>
                    <td>{{ $close['collection_amount'] }}</td>
                    <td>{{ @$close['discount_amount'] }}</td>
                    <td>{{ $close['due_amount'] }}</td>
                    <td>{{ @$close['closed_by'] }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection
