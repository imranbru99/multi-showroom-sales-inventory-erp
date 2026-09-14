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

            @foreach ($missings as $missing)

                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$missing['sale_date'])) }}</td>
                    <td>{{ date('d-m-Y', strtotime(@$missing['last_col_date'])) }}</td>
                    <td>{{ $missing['late_days'] }}</td>
                    <td>{{ @$missing['customer_code'] }}</td>
                    <td>{{ @$missing['customer_name'] }}</td>
                    <td>{{ @$missing['phone_no'] }}</td>
                    <td>{{ @$missing['address'] }}</td>
                    <td>{{ @$missing['sales_by'] }}</td>
                    <td>{{ $missing['invoice_no'] }}</td>
                    <td>{{ $missing['sales_amount'] }}</td>
                    <td>{{ @$missing['interest_receive'] }}</td>
                    <td>{{ $missing['late_fee'] }}</td>
                    <td>{{ @$missing['missing_amount'] }}</td>
                    <td>{{ $missing['collection_amount'] }}</td>
                    <td>{{ @$missing['discount_amount'] }}</td>
                    <td>{{ $missing['due_amount'] }}</td>
                    <td>{{ @$missing['closed_by'] }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection
