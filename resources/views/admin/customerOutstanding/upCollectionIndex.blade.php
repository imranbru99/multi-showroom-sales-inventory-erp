<?php
use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
use Illuminate\Support\Facades\DB;
?>
@extends('admin.layouts.masterReport')
@section('search_card_body')
    <input type="hidden" value="true" name="searched">
    <div class="row">

        <div class="col-md-4">
            <label for="customer">Start Date</label>
            <div class="form-group">
                <input type="text" class="form-control datepicker" name="start_date" value="{{ $start_date }}">
            </div>
        </div>

        <div class="col-md-4">
            <label for="customer">End Date</label>
            <div class="form-group">
                <input type="text" class="form-control datepicker" name="end_date" value="{{ $end_date }}">
            </div>
        </div>

        <div class="col-md-4">
            <label for="customer">Collector</label>
            <div class="form-group">
                <select name="staff" class="form-control chosen-select">
                    <option value="">Select a collector</option>
                    @foreach ($staffs as $staff)
                        <option value="{{ $staff->id }}"
                            @if ($staffId == $staff->id)
                                selected
                            @endif
                            >{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
@endsection

@section('print_card_header')
    <input type="hidden" class="form-control" name="start_date" value="{{ $start_date }}">
    <input type="hidden" class="form-control" name="end_date" value="{{ $end_date }}">
    <input type="hidden" class="form-control" name="staffId" value="{{ $staffId }}">
    <input type="hidden" id="print_value" name="print" value="Print">
@endsection

@section('print_card_body')
    <table id="dataTableUpcomingCollection" name="paymentRecordTable" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>A/C No.</th>
                <th>Account Name</th>
                <th width="100px">Mobile No</th>
                <th width="110px">Prev Due</th>
                <th width="110px">Last Collection Date</th>
                <th width="110px">Date Duration</th>
                <th width="110px">Month Duration</th>
                <th width="110px">Schedule Collection</th>
                <th width="110px">Total Collection</th>
            </tr>
        </thead>

        <tbody>

            @php
                $i = 1;
                $addedCustomers = [];
            @endphp
            @foreach ($upComingCollections['installments'] as $upComingCollection)
                @php
                    
                    if (@$upComingCollection->schedule->sum('installment_schedule_amount') == 0) {
                        continue;
                    }

                    if(in_array(@$upComingCollection->customer->id, $addedCustomers)){
                        continue;
                    }

                    array_push($addedCustomers, @$upComingCollection->customer->id);
                    
                    // Last Collection Date
                    $lastCollectionDate = InstallmentCollectionList::with(['collection'])
                        ->whereHas('collection', function ($q) use ($upComingCollection) {
                            $q->where('customer_id', @$upComingCollection->customer->id);
                        })
                        ->orderBy('installment_collection_date', 'desc')
                        ->first();
                    
                    $lastCollectionDate = Carbon::parse(@$lastCollectionDate->installment_collection_date);
                    
                    // Date Diff
                    $todayDate = Carbon::now();
                    
                    $dateDiff = $todayDate->diffInDays($lastCollectionDate);
                    $dateDiffInMonth = $todayDate->diffInMonths($lastCollectionDate);

                    
                    // prev Installment Amount
                    $prevInstallments = Installment::with(['schedule', 'customer'])
                        ->whereHas('schedule', function ($q) use ($start_date) {
                            $q->where('installment_schedule_date', '<=', date('Y-m-d', strtotime($start_date)));
                        })
                        ->whereHas('customer', function ($q) use ($upComingCollection) {
                            $q->where('id', @$upComingCollection->customer->id);
                        })
                        ->get();
                    
                    $prevInstallmentsAmount = 0;
                    
                    foreach ($prevInstallments as $prevInstallment) {
                        $prevInstallmentsAmount += $prevInstallment->schedule->sum('installment_schedule_amount');
                    }
                    
                    // prev collection Amount
                    
                    $prevCollection = InstallmentCollectionList::with(['installment'])
                        ->where('installment_schedule_date', '<=', date('Y-m-d', strtotime($start_date)))
                        ->whereHas('installment', function ($q) use ($upComingCollection) {
                            $q->where('customer_id', @$upComingCollection->customer->id);
                        })
                        ->sum('installment_schedule_amount');
                    
                    $prevDue = $prevInstallmentsAmount - $prevCollection;
                    
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        @if ($upComingCollection->has('customer'))
                            {{ @$upComingCollection->customer->code }}
                        @endif
                    </td>
                    <td>
                        @if ($upComingCollection->has('customer'))
                            {{ @$upComingCollection->customer->name }}
                        @endif
                    </td>
                    <td>
                        @if ($upComingCollection->has('customer'))
                            {{ @$upComingCollection->customer->phone_no }}
                        @endif
                    </td>
                    <td>
                        {{ $prevDue }}
                    </td>
                    <td>
                        {{ $lastCollectionDate->format('d-m-Y') }}
                    </td>
                    <td>
                        {{ $dateDiff }} (Days)
                    </td>
                    <td>
                        {{ $dateDiffInMonth }}
                    </td>
                    <td>
                        @if ($upComingCollection->has('schedule'))
                            {{ @$upComingCollection->schedule[0]->installment_schedule_amount }}
                        @endif
                    </td>
                    <td>
                        {{ $prevDue + @$upComingCollection->schedule->sum('installment_schedule_amount') }}
                    </td>
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
