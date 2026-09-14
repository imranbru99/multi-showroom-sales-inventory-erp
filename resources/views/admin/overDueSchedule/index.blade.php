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

        <div class="col-md-4">
            <label for="duration">Duration</label>
            <input type="number" class="form-control" name="duration" value="{{ $days }}"
                placeholder="Day Duration" </div>

        </div>
    @endsection

    @section('print_card_header')
        <input type="hidden" class="form-control" name="project" value="{{ $project_id }}">
        <input type="hidden" class="form-control" name="duration" value="{{ $days }}">
        <input type="hidden" class="form-control" name="staff" value="{{ $staffId }}">
        <input type="hidden" id="print_value" name="print" value="Print">
    @endsection

    @section('print_card_body')
        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Invoice Date</th>
                    <th>A/C No.</th>
                    <th>Account Name</th>
                    <th>Mobile#</th>
                    <th>Address</th>
                    <th>Sales By</th>
                    <th>Invoice#</th>
                    <th>Invoice Amount</th>
                    <th>Schedule Amount</th>
                    <th>Collection Amount</th>
                    <th>Due Amount</th>
                    <th>Last Schedule Duration</th>
                    {{-- <th>Duration</th>
                <th>Day Over</th> --}}
                </tr>
            </thead>

            <tbody>
                @php
                    $i = 1;
                    $startDate = date('Y-m-d');
                    $duration = date('Y-m-d', strtotime(now()->subDays($days)));
                @endphp

                @foreach ($retailSales as $retailSale)

                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ date('d-m-Y', strtotime($retailSale['sale_date'])) }}</td>
                        <td>{{ $retailSale['customer_code'] }}</td>
                        <td>{{ $retailSale['customer_name'] }}</td>
                        <td>{{ $retailSale['phone_no'] }}</td>
                        <td>{{ $retailSale['address'] }}</td>
                        <td>{{ $retailSale['seller'] }}</td>
                        <td>{{ $retailSale['invoice_no'] }}</td>
                        <td>{{ $retailSale['sale_amount'] }}</td>
                        <td>{{ $retailSale['schedule_amount'] }}</td>
                        <td>{{ $retailSale['collection_amount'] }}</td>
                        <td>{{ $retailSale['over_due'] }}</td>
                        <td>{{ $retailSale['last_date_duration'] }}</td>
                        {{-- <td>{{ $duration }}</td>
                    <td>{{ $over_due_days }}</td> --}}
                    </tr>
                @endforeach

            </tbody>
        </table>
    @endsection
