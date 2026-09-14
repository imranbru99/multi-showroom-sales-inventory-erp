<?php

use App\Installment;
use Illuminate\Support\Carbon;
use App\InstallmentCollectionList;
?>

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Upcomming Collection List</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th>A/C No.</th>
                <th width="120px">Account Name</th>
                <th width="100px">Mobile No</th>
                <th width="60px">Prev Due</th>
                <th width="110px">Last Collection Date</th>
                <th width="80px">Duration (Day)</th>
                <th width="100px">Duration (Month)</th>
                <th width="80px">Schedule Collection</th>
                <th width="80px">Total Collection</th>
            </tr>
        </thead>

        <tbody>

            @php
                $totalCollection = 0;
                $data = [];
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
                    
                    $totalCollection += $prevDue + @$upComingCollection->schedule->sum('installment_schedule_amount');
                    
                    $data[] = [
                        'customerCode' => @$upComingCollection->customer->code,
                        'customerName' => @$upComingCollection->customer->name,
                        'customerPhone' => @$upComingCollection->customer->phone_no,
                        'PrevDue' => @$prevDue,
                        'lastCollectionDate' => @$lastCollectionDate->format('d-m-Y'),
                        'DateDiff' => @$dateDiff,
                        'DateDiffMonth' => @$dateDiffInMonth . ' Months ago',
                        'upComingCollection' => @$upComingCollection->schedule->sum('installment_schedule_amount'),
                        'totalCollection' => $prevDue + @$upComingCollection->schedule->sum('installment_schedule_amount'),
                    ];
                    
                @endphp

            @endforeach

            {{-- {{ dd($data) }} --}}

            @php
                function cmp($a, $b)
                {
                    if ($a == $b) {
                        return 0;
                    }
                    return $a['DateDiff'] > $b['DateDiff'] ? -1 : 1;
                }

                uasort($data, 'cmp');
                
                $i = 1;

            @endphp

            @foreach ($data as $d)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        {{ $d['customerCode'] }}
                    </td>
                    <td>
                        {{ $d['customerName'] }}
                    </td>
                    <td>
                        {{ $d['customerPhone'] }}
                    </td>
                    <td>
                        {{ $d['PrevDue'] }}
                    </td>
                    <td>
                        {{ $d['lastCollectionDate'] }}
                    </td>
                    <td>
                        {{ $d['DateDiff'] }}
                    </td>
                    <td>
                        {{ $d['DateDiffMonth'] }}
                    </td>
                    <td>
                        {{ $d['upComingCollection'] }}
                    </td>
                    <td>
                        {{ $d['totalCollection'] }}
                    </td>
                </tr>
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <th colspan="9">Total</th>
                <th>{{ $totalCollection }}</th>
            </tr>

        </tfoot>
    </table>
@endsection
