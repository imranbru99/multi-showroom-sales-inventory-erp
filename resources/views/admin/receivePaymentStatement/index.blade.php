@extends('admin.layouts.masterReport')

@section('search_card_body')
    <div class="row">
        <div class="col-md-12 form-group">
            <input class="form-control" type="hidden" name="print" value="print">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            <label for="from-date">From Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{ date('d-m-Y',strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>
        <div class="col-md-6 form-group">
            <label for="to-date">To Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{ date('d-m-Y',strtotime($toDate)) }}" placeholder="Select Date To">
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered table-sm">
                <thead class="thead-green">
                    <tr>
                        <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Assets</td>
                    </tr>

                    <tr>
                        <th width="20px">Sl</th>
                        <th width="100px">Head Code</th>
                        <th>Head Name</th>
                        <th width="80px">Balance</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sl = 1;
                        $totalAssets = 0;
                    @endphp
                    @foreach ($assetsLists as $assetsList)
                        @php
                            $totalAssets = $totalAssets + $assetsList->amount;
                        @endphp
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $assetsList->headCode }}</td>
                            <td>{{ $assetsList->headName }}</td>
                            <td align="right">{{ $assetsList->amount > 0 ? $assetsList->amount : '('.abs($assetsList->amount).')' }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="right" style="font-weight: bold;">{{ $totalAssets >= 0 ? $totalAssets : '('.abs($totalAssets).')' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="col-md-6">
            <table class="table table-bordered table-sm">
                <thead class="thead-green">
                    <tr>
                        <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Libilities</td>
                    </tr>

                    <tr>
                        <th width="20px">Sl</th>
                        <th width="100px">Head Code</th>
                        <th>Head Name</th>
                        <th width="80px">Balance</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sl = 1;
                        $totalLibilities = 0;
                    @endphp
                    @foreach ($libilityLists as $libilityList)
                        @php
                            $totalLibilities = $totalLibilities + $libilityList->amount;
                        @endphp
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $libilityList->headCode }}</td>
                            <td>{{ $libilityList->headName }}</td>
                            <td align="right">{{ $libilityList->amount > 0 ? $libilityList->amount : '('.abs($libilityList->amount).')' }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="right" style="font-weight: bold;">{{ $totalLibilities >= 0 ? $totalLibilities : '('.abs($totalLibilities).')' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if ($totalAssets > $totalLibilities)
                <p style="background: green; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net Profit: {{ $totalAssets - $totalLibilities }}</p>
            @endif

            @if ($totalAssets < $totalLibilities)
                <p style="background: red; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net Lose: {{ $totalLibilities - $totalAssets }}</p>
            @endif
        </div>
    </div>
@endsection