@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>
            Customer Outstanding

            @if ($staff)
                Of {{ $staff->name }}
            @endif
        </td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>A/C No.</th>
            <th>Account Name</th>
            <th width="80px">Mobile No</th>
            <th width="60px">Sales Amount</th>
            <th width="60px">Discount</th>
            <th width="60px">Gift Voucher</th>
            <th width="60px">Exchange Crt</th>
            <th width="65px">Collection</th>
            <th width="90px">Outstanding</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalSale = 0;
        $totalDiscount = 0;
        $totalGiftVoucher = 0;
        $totalExchangeCrt = 0;
        $totalCollection = 0;
        $totalOutstanding = 0;
        @endphp
        @foreach ($customerOutstandings as $customerOutstanding)
        @php
        $totalSale += $customerOutstanding['totalsaleAmount'];
        $totalCollection += $customerOutstanding['totalCollectionAmount'];
        $totalOutstanding += $customerOutstanding['outstanding'];
        $totalDiscount += $customerOutstanding['totalsaleDiscount'];
        $totalGiftVoucher += $customerOutstanding['totalsaleGiftVoucher'];
        $totalExchangeCrt += $customerOutstanding['totalsaleExchangeCrt'];
        @endphp
        <tr>
            <td>{{ $customerOutstanding['sl'] }}</td>
            <td>{{ $customerOutstanding['customerAccountCode'] }}</td>
            <td>{{ $customerOutstanding['customerName'] }}</td>
            <td>{{ $customerOutstanding['customerMobile'] }}</td>
            <td>{{ $customerOutstanding['totalsaleAmount'] }}</td>
            <td>{{ $customerOutstanding['totalsaleDiscount'] }}</td>
            <td>{{ $customerOutstanding['totalsaleGiftVoucher'] }}</td>
            <td>{{ $customerOutstanding['totalsaleExchangeCrt'] }}</td>
            <td>
                {{$customerOutstanding['totalCollectionAmount']}}
            </td>
            <td>
                {{ $customerOutstanding['outstanding'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total</th>
            <th>{{ $totalSale }}</th>
            <th>{{ $totalDiscount }}</th>
            <th>{{ $totalGiftVoucher }}</th>
            <th>{{ $totalExchangeCrt }}</th>
            <th>{{ $totalCollection }}</th>
            <th>{{ $totalOutstanding }}</th>
        </tr>

    </tfoot>
</table>
@endsection