<?php
use App\RetailSale;
?>
@extends('admin.layouts.masterPrint')

@section('custome-css')
    <style>
        #report-table td, #report-table th {
            border: 0px solid #ddd;
           /* padding: 5px;*/
        }

        #up-header{
            background-color: white;
        }
    </style>

@endsection

@php
    use App\InstallmentCollectionList;
@endphp

@section('content')
    <table id="report-header">
        <tr>
            <td>{{$title}}</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table width="100%">
        <tr>
            <td width="50%">
                <table id="report-table" width="100%">
                    <thead>
                        <tr>
                            <th colspan="3">Client Information</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td width="40px">Name</td>
                            <td width="5px">:</td>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <td width="40px">Mobile No</td>
                            <td width="5px">:</td>
                            <td>{{ $customer->phone_no }}</td>
                        </tr>

                        <tr>
                            <td width="40px">Address</td>
                            <td width="5px">:</td>
                            <td>{{ $customer->present_address }}</td>
                        </tr>

                        <tr>
                            <td width="40px">Profession</td>
                            <td width="5px">:</td>
                            <td>{{ $customer->profession_name }}</td>
                        </tr>

                    </tbody>
                </table>
            </td>

            <td width="50%">
                <table id="report-table">
                    <thead>
                        <tr>
                            <th colspan="3">Installment Information</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td width="110px">Invoice No</td>
                            <td width="5px">:</td>
                            <td>{{ $installment->invoice_no }}</td>
                        </tr>
                        <tr>
                            <td width="110px">Product Name</td>
                            <td width="5px">:</td>
                            <td>
                                @foreach ($retailSale->products as $p)
                                {{ $p->product->name }}, 
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td width="110px">Total Installment</td>
                            <td width="5px">:</td>
                            <td>{{ $installment->installment_qty }}</td>
                        </tr>

                        <tr>
                            <td width="110px">Installment Amount</td>
                            <td width="5px">:</td>
                            <td>{{ $installment->installment_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr></tr>
    </table>

     <div id="pad-bottom"></div>   

    <table id="report-table">
        <caption style="text-decoration: none;">Schedule & Collection List</caption>
        <thead>
            <tr>
                <th width="40px">SL</th>
                <th width="120px">Schedule Date</th>
                <th>Collection Date</th>
                <th>Amount</th>
                <th width="250px">Collector</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 0;
            @endphp
            @foreach ($installmentScheduleList as $schedule)
                 @php
                    $sl++;
                    $scheduleDate = Date('d-M-Y',strtotime($schedule->installment_schedule_date));
                    $installmentCollection = InstallmentCollectionList::where('installment_schedule_id',$schedule->id)->first();
                    if(@$installmentCollection->installment_collection_date){
                        $collectionDate = Date('d-M-Y',strtotime(@$installmentCollection->installment_collection_date));
                        $retailSale = RetailSale::where('invoice_no', $installmentCollection->invoice_no)->with(['seller'])->first();
                        $collector = $retailSale->seller->name;
                    }else{
                        $collectionDate = '---------';
                        $collector = '---------';
                    }
                    
                 @endphp
            <tr>
                <td style="text-align: center;">{{$sl}}</td>
                <td style="text-align: center;">{{@$scheduleDate}}</td>
                <td style="text-align: center;">{{@$collectionDate}}</td>
                <td style="text-align: center;">{{@$schedule->installment_schedule_amount}}</td>
                <td style="text-align: center;">{{@$collector}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
@endsection