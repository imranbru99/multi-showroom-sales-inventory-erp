@extends('admin.layouts.masterPrint')

@section('content')
@php
    use App\InstallmentSchedule;
    use App\InstallmentCollectionList;
@endphp
    <table  id="report-table">
        <caption>{{@$title}}</caption>
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="100px">Invoice No</th>
                <th width="180px">Client Name</th>
                <th width="90px">Phone No</th>
                <th width="115px">Collector</th>
                <th width="30px" class="text-center">Qty</th>
                <th width="180px" class="text-center">Prospective Collection</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
                $totalDueInstallment = 0;                                       
                $totalProspectiveCollectionAmount = 0;  
            @endphp
             @foreach ($prospectiveCollectionList as $prospectiveCollection )
             @php
                $totalInstallmentAmount = InstallmentSchedule::where('installment_id',$prospectiveCollection->installmentId)->where('installment_schedule_date','<=',$tillCollectionDate)->sum('installment_schedule_amount');
                $totalInstallmentCollectionAmount = InstallmentCollectionList::where('installment_id',$prospectiveCollection->installmentId)->sum('installment_schedule_amount');
                $prospectiveCollectionAmount = $totalInstallmentAmount-$totalInstallmentCollectionAmount ;

                $totalDueInstallment = $totalDueInstallment + $prospectiveCollection ->totalDueInstallment;
                $totalProspectiveCollectionAmount = $totalProspectiveCollectionAmount + $prospectiveCollectionAmount;
             @endphp
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $prospectiveCollection->invoiceNo }}</td>
                    <td>{{ $prospectiveCollection->customerName }}</td>
                    <td>{{ $prospectiveCollection->phoneNo }}</td>
                    <td style="text-align: center;">{{ $prospectiveCollection->installmentCollectorName }}</td>
                    <td style="text-align: center;">{{ $prospectiveCollection->totalDueInstallment }}</td>
                    <td style="text-align: right;">{{$prospectiveCollectionAmount}}</td>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th style="text-align: center;">{{ $totalDueInstallment }}</th>
                <th style="text-align: right;">{{ $totalProspectiveCollectionAmount}}</th>
            </tr>
        </tfoot>
    </table>
@endsection
