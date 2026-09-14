@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
            <td>Vendor Name: {{ $vendorName->name }}</td>
        </tr>
        <tr>
            <td>Vendor Statement On {{ date('d-m-Y', strtotime($fromDate)) }} To {{ date('d-m-Y', strtotime($toDate)) }}
            </td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead class="thead-light">
            <tr>
                <th colspan="5" style="text-align: right; font-weight: bold;">Previous Balance</th>
                <th style="text-align: right;">{{ $data['previousBalance'] }}</th>
            </tr>
            <tr>
                <th width="100px">Date</th>
                <th width="100px">Remarks </th>
                <th width="100px">Purchase</th>
                <th width="100px">Payment</th>
                <th width="100px">Returns</th>
                <th width="100px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 0;
                $totalLifting = 0;
                $totalPayment = 0;
                $totalReturn = 0;
            @endphp
            @foreach ($data['statements'] as $statement)
                <tr>
                    <td>{{ $statement['date'] }}</td>
                    <td>
                        @foreach ($statement['remarks'] as $value)
                            {{ $value['purchase'] }}
                            @if ($value['purchase'] && $value['payment'])
                                ,<br>
                            @endif
                            {{ $value['payment'] }}
                        @endforeach
                    </td>
                    <td>{{ $statement['lifting'] }}</td>
                    <td>{{ $statement['payment'] }}</td>
                    <td>{{ $statement['return'] }}</td>
                    <td>{{ $statement['balance'] }}</td>
                </tr>
                @php
                    $totalLifting += $statement['lifting'];
                    $totalPayment += $statement['payment'];
                    $totalReturn += $statement['return'];
                @endphp
            @endforeach
        </tbody>
        {{-- <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th>{{ $totalLifting }}</th>
            <th>{{ $totalPayment }}</th>
            <th>{{ $totalReturn }}</th>
            <th></th>
        </tr>
    </tfoot> --}}
    </table>


    <div id="pad-bottom"></div>

    <table id="report-table">
        <tfoot>
            <tr>
                <th style="text-align: right;"><b>Total Purchase : </b></th>
                <td style="text-align: right;">{{ round($totalLifting, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Payment : </b></th>
                <td style="text-align: right;">{{ round($totalPayment, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Return : </b></th>
                <td style="text-align: right;">{{ round($totalReturn, 2) }}</td>
            </tr>

            <tr>
                <th style="text-align: right;"><b>Total Balance : </b></th>
                <td style="text-align: right;">{{ round(($totalLifting - ($totalPayment - $totalReturn)), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div id="pad-bottom"></div>

    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection
