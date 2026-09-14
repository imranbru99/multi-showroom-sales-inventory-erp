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
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>
        <div class="col-md-6 form-group">
            <label for="to-date">To Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate"
                value="{{ date('d-m-Y', strtotime($toDate)) }}" placeholder="Select Date To">
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

            <div class="row">

                <div class="col-md-12">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-green">
                            <tr>
                                <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Income</td>
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
                                $totalIncome = 0;
                            @endphp
                            @foreach ($incomeLists as $incomeList)
                                @php
                                    $totalIncome = $totalIncome + abs($incomeList->amount);
                                @endphp
                                <tr>
                                    <td>{{ $sl++ }}</td>
                                    <td>{{ $incomeList->headCode }}</td>
                                    <td>{{ $incomeList->headName }}</td>
                                    <td align="right">
                                        {{ abs($incomeList->amount) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="3" align="right"><b>Total</b></td>
                                <td align="right" style="font-weight: bold;">
                                    {{ $totalIncome }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-green">
                            <tr>
                                <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Closing Stock</td>
                            </tr>

                            <tr>
                                <th width="20px">Sl</th>
                                <th>Head Name</th>
                                <th width="80px">Balance</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Closing Stock</td>
                                <td align="right">{{ $closingStockValue }}</td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2" align="right"><b>Total</b></td>
                                <td align="right" style="font-weight: bold;">{{ $closingStockValue }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>


        <div class="col-md-6">
            <table class="table table-bordered table-sm">
                <thead class="thead-green">
                    <tr>
                        <td colspan="4" style="font-size: 20px; font-weight: bold; text-align: center;">Expense</td>
                    </tr>

                    <tr>
                        <th width="20px">Sl</th>
                        <th width="100px">Head Code</th>
                        <th>Head Name</th>
                        <th width="80px">Balance</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>1</td>
                        <td></td>
                        <td>Opening Balance</td>
                        <td align="right">{{ $openingBalance }}</td>
                    </tr>
                    @php
                        $sl = 2;
                        $totalExpanse = 0;
                    @endphp
                    @foreach ($data as $d)
                        @php
                            $totalExpanse = $totalExpanse + $d['amount'];
                        @endphp
                        <tr>
                            <td>{{ $sl++ }}</td>
                            <td>{{ $d['headCode'] }}</td>
                            <td>{{ $d['headName'] }}</td>
                            <td align="right">{{ $d['amount'] > 0 ? $d['amount'] : '(' . abs($d['amount']) . ')' }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="right" style="font-weight: bold;">
                            {{ $totalExpanse >= 0 ? ($totalExpanse + $openingBalance) : '(' . abs($totalExpanse) . ')' }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            @php
                $netIncome = $totalIncome + $closingStockValue; 
            @endphp
            @if ($netIncome > $totalExpanse)
                <p style="background: green; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net
                    Profit: {{  $netIncome - ($totalExpanse + $openingBalance) }}</p>
            @endif

            @if ($netIncome < $totalExpanse)
                <p style="background: red; font-size: 20px; font-weight: bold; color: white; text-align: center;">Net Lose:
                    {{ ($totalExpanse + $openingBalance) - $netIncome }}</p>
            @endif
        </div>
    </div>
@endsection
