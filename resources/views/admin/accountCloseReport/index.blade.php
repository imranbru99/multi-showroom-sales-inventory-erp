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
            <label for="project">Projects <span>*</span></label>
            <div class="form-group">
                <select name="project" class="form-control" id="project">
                    <option value="">Select Projject</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @if ($project->id == $project_id) selected @endif>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
@endsection

@section('print_card_header')
    <input type="hidden" class="form-control" name="project" value="{{ $project_id }}">
    <input type="hidden" id="print_value" name="print" value="Print">
@endsection

@section('print_card_body')
    <table id="dataTableUpcomingCollection" name="paymentRecordTable" class="table table-bordered table-sm">
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
                <th>Collection</th>
                <th>Due Amount</th>
            </tr>
        </thead>

        <tbody>
            <?php $sl = 1; ?>
            @foreach ($closeCustomers as $closeCustomer)

                <?php
                $collection_amount = 0;
                foreach ($closeCustomer->collection as $collection) {
                    $collect = 0;
                    foreach ($collection->collections as $indcollection) {
                        $collect += floatval($indcollection->installment_schedule_amount);
                    }
                    $collection_amount += $collect;
                }
                
                $due_amount = $closeCustomer->products->sum('sales_price') - $collection_amount;
                
                if ($due_amount <= 0) {
                    continue;
                }
                ?>
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($closeCustomer->date)) }}</td>
                    <td>{{ @$closeCustomer->customer->code }}</td>
                    <td>{{ @$closeCustomer->customer->name }}</td>
                    <td>{{ @$closeCustomer->customer->mobile_no }}</td>
                    <td>{{ @$closeCustomer->customer->present_address }}</td>
                    <td>{{ @$closeCustomer->seller->name }}</td>
                    <td>{{ $closeCustomer->invoice_no }}</td>
                    <td>{{ $closeCustomer->products->sum('sales_price') }}</td>
                    <td>{{ $collection_amount }}</td>
                    <td>{{ $due_amount }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {
            var table = $('#dataTableUpcomingCollection').DataTable({
                "order": [
                    [6, "desc"]
                ],
            });
        });
    </script>
@endsection
