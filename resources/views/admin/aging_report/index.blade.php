@extends('admin.layouts.master')

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
    <div class="card-header">
        <h1 class="text-center h3">{{ $title }}</h1>
    </div>

    <div class="card-body">

        <div class="mt-3 mb-3 text-right">
            <form action="{{ route('agingReport.index') }}" method="post" target="_blank">
                {{ csrf_field() }}
                <input type="hidden" name="print" value="true">
                <button type="submit" class="btn btn-outline-primary btn-lg">Print</button>
            </form>
        </div>

        <table class="table table-bordered table-sm dataTable no-footer">
            <thead>
                <tr>
                    <th>SL#</th>
                    <th>Dealer Name</th>
                    <th>Total Due</th>
                    <th>30 Day</th>
                    <th>60 Day</th>
                    <th>90 Day</th>
                    <th>Over 90 Day</th>
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
                    <td>{{ $d['30'] }}</td>
                    <td>{{ $d['60'] }}</td>
                    <td>{{ $d['90'] }}</td>
                    <td>{{ $d['o90'] }}</td>
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
    </div>
</div>
@endsection