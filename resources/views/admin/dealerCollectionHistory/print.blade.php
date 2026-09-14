@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>
            Dealer Collection History ON {{ date('d-m-Y', strtotime($fromDate)) }} To
            {{ date('d-m-Y', strtotime($toDate)) }}
        </td>
    </tr>
    @if(!empty($employeName))
    <tr>
        <td>

            Collection By
            @foreach($employeName as $employee)
            {{$employee->name}}, 
            @endforeach
        </td>
    </tr>
    @endif

</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th width="80px">Date</th>
            <th>Dealer Name</th>
            <th style="white-space: nowrap">Employee Name</th>
            <th>Area</th>
            <th width="100px">Payment No.</th>
            <th width="90px">Pay Mode</th>
            <th width="80px">Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        use Illuminate\Support\Collection;

        $sl = 1;
        $currentDealerId = 0;
        $totalAmount = 0;
        $data = new Collection($data);
        @endphp

        @foreach ($data as $d)

        @php
        // if ($collectionHistory->dealer == null) {
        // continue;
        // }

        // if ($collectionHistory->adjustment == 1) {
        // continue;
        // }

        // $payment = $collectionHistory->payment_amount;

        // if ($collectionHistory->advance_id !== null) {
        // $payment = $payment + $collectionHistory->advance->advance_amount;
        // }

        $totalAmount += $d['amount'];
        @endphp

        {{-- @if ($d['dealerId'] != $currentDealerId)
                    @php
                        $currentDealerId = $d['dealerId'];
                        
                        $dataCollection = new Collection($data);

                        $rowSpan = $dataCollection->where('dealerId', $currentDealerId)->count();
                        
                    @endphp --}}
        <tr>
            <td>{{ $sl++ }}</td>
            <td align='center'>{{ $d['date'] }}</td>
            <td>{{ $d['dealerName'] }}</td>
            <td>{{ $d['sale_by'] }}</td>
            <td></td>
            <td align='center'>{{ $d['paymentNo'] }}</td>
            <td align='center'>
                <?php
                if (!empty($d['bankId'])) {
                    $bankname = \App\CoaSetup::where('id', $d['bankId'])->first();
                    $type = $bankname->head_name;
                } else {
                    $type = $d['paymentType'];
                }
                ?>

                {{ $type }}
            </td>
            <td align="right">{{ $d['amount'] }}</td>
        </tr>
        {{-- @else --}}
        {{-- <tr>
    <td>{{ $sl++ }}</td>
    <td>{{ $d['date'    ] }}</td>
    <td>{{ $d['paymentNo'] }}</td>
    <td>{{ $d['paymentType'] }}</td>
    <td align="right">{{ $d['amount'] }}</td>
</tr>
@endif --}}
@endforeach

</tbody>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <tfoot>
        <tr>
            <th style="text-align: right;"><b>Total Amount : </b></th>
            <td style="text-align: right;">{{ $totalAmount }}</td>
        </tr>
    </tfoot>
</table>

<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>
@endsection