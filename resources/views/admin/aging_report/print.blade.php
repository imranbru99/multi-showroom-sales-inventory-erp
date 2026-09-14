@extends('admin.layouts.masterPrint')

@section('content')
    <style>
        th {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
        }

    </style>


    <div class="card" style="margin-bottom: 0px;">

        <table id="report-header">
            <tr>
                <td>{{ $title }}</td>
            </tr>
        </table>

        <div class="card-body">

            <table id="report-table">
                <thead>
                    <tr>
                        <th>SL#</th>
                        <th>Dealer Name</th>
                        <th>Total Due</th>
                        <th style="width:75px">30 Day</th>
                        <th style="width:75px">60 Day</th>
                        <th style="width:75px">90 Day</th>
                        <th style="width:75px">Over 90 Day</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $totalDue = 1;
                        $s30 = 0;
                        $s60 = 0;
                        $s90 = 0;
                        $so90 = 0;
                    @endphp
                    @foreach ($data as $d)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $d['DealerName'] }}</td>
                            <td>{{ $d['dealerTotalDue'] }}</td>
                            <td align="right">{{ $d['30'] }}</td>
                            <td align="right">{{ $d['60'] }}</td>
                            <td align="right">{{ $d['90'] }}</td>
                            <td align="right">{{ $d['o90'] }}</td>
                        </tr>
                        @php
                            $totalDue += $d['dealerTotalDue'];
                            $s30 += $d['30'];
                            $s60 += $d['60'];
                            $s90 += $d['90'];
                            $so90 += $d['o90'];
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th>{{ number_format((float)$totalDue, 2, '.', '') }}</th>
                        <th>{{ number_format((float)$s30, 2, '.', '') }}</th>
                        <th>{{ number_format((float)$s60, 2, '.', '') }}</th>
                        <th>{{ number_format((float)$s90, 2, '.', '') }}</th>
                        <th>{{ number_format((float)$so90, 2, '.', '') }}</th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-12 text-right">
                    <p>Print Date & Time : {{ date('d-m-Y h:i:sa', strtotime(now())) }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
