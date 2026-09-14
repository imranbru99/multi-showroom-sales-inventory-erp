@extends('admin.layouts.masterPrint')

@php
$criteriaText = [
'dealer' => 'Dealer',
'product' => 'Product',
'category' => 'Category',
'employee' => 'Employee',
'region' => 'Region',
'area' => 'Area',
'territory' => 'Territory',
];
@endphp

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $criteriaText[$criteria] }} Sales Contribution ON {{ date('d-m-Y', strtotime($fromDate)) }} To
            {{ date('d-m-Y', strtotime($toDate)) }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

@if ($criteria == 'dealer')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Dealer Name</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        @php
        if($d['dealer_qty'] == 0){
        continue;
        }
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['dealer_name'] }}
            </td>
            <td align="right">{{ round($d['dealer_qty'], 2) }}</td>
            <td align="right">
                {{ round($d['dealer_qty_p'], 2) }}
            </td>
            <td align="right">{{ round($d['dealer_value'], 2) }}</td>
            <td align="right">
                {{ round($d['dealer_value_p'], 2) }}
            </td>
        </tr>
        @php
        $totalQty += round($d['dealer_qty'], 2);
        $totalPercentageQty += round($d['dealer_qty_p'], 2);
        $totalAmount += round($d['dealer_value'], 2);
        $totalPercentageAmount += round($d['dealer_value_p'], 2);
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            {{-- <th align="right">{{ $totalPercentageQty }}</th> --}}
            <th></th>
            <th align="right">{{ round($totalAmount, 2) }}</th>
            <th></th>
            {{-- <th align="right">{{ $totalPercentageAmount }}</th> --}}
        </tr>
    </tfoot>

</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif

@if ($criteria == 'product')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Product - Model</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($salesContributions as $salesContribution)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ @$salesContribution[0]->product->name . ' - ' . @$salesContribution[0]->product->model_no }}
            </td>
            <td align="right">{{ round($salesContribution->sum('qty'), 2) }}</td>
            <td align="right">
                {{ round(($salesContribution->sum('qty') * 100) / $totalSalesContributions->sum('qty'), 2) }}
            </td>
            <td align="right">{{ round($salesContribution->sum('amount'), 2) }}</td>
            <td align="right">
                {{ round(($salesContribution->sum('amount') * 100) / $totalSalesContributions->sum('amount'), 2) }}
            </td>
        </tr>
        @php
        $totalQty += round($salesContribution->sum('qty'), 2);
        $totalPercentageQty += round(($salesContribution->sum('qty') * 100) / $totalSalesContributions->sum('qty'), 2);
        $totalAmount += round($salesContribution->sum('amount'), 2);
        $totalPercentageAmount += round(($salesContribution->sum('amount') * 100) / $totalSalesContributions->sum('amount'), 2);
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            {{-- <th align="right">{{ $totalPercentageQty }}</th> --}}
            <th></th>
            <th align="right">{{ round($totalAmount, 2) }}</th>
            <th></th>
            {{-- <th align="right">{{ $totalPercentageAmount }}</th> --}}
        </tr>
    </tfoot>

</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif

@if ($criteria == 'category')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Category</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        @php
        if($d['category_qty'] == 0){
        continue;
        }
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['category_name'] }}
            </td>
            <td align="right">{{ round($d['category_qty'], 2) }}</td>
            <td align="right">
                {{ round($d['category_qty_p'], 2) }}
            </td>
            <td align="right">{{ round($d['category_value'], 2) }}</td>
            <td align="right">
                {{ round($d['category_value_p'], 2) }}
            </td>
        </tr>
        @php
        $totalQty += round($d['category_qty'], 2);
        $totalPercentageQty += round($d['category_qty_p'], 2);
        $totalAmount += round($d['category_value'], 2);
        $totalPercentageAmount += round($d['category_value_p'], 2);
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            {{-- <th align="right">{{ $totalPercentageQty }}</th> --}}
            <th></th>
            <th align="right">{{ round($totalAmount, 2) }}</th>
            <th></th>
            {{-- <th align="right">{{ $totalPercentageAmount }}</th> --}}
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif

@if ($criteria == 'employee')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Employee</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        @php
        if($d['product_qty'] == 0){
        continue;
        }
        @endphp
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['employee_name'] }}
            </td>
            <td align="right">{{ round($d['product_qty'], 2) }}</td>
            <td align="right">
                {{ round($d['product_qty_p'], 2) }}
            </td>
            <td align="right">{{ round($d['product_value'], 2) }}</td>
            <td align="right">
                {{ round($d['product_value_p'], 2) }}
            </td>
        </tr>
        @php
        $totalQty += round($d['product_qty'], 2);
        $totalPercentageQty += round($d['product_qty_p'], 2);
        $totalAmount += round($d['product_value'], 2);
        $totalPercentageAmount += round($d['product_value_p'], 2);
        @endphp

        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            {{-- <th align="right">{{ $totalPercentageQty }}</th> --}}
            <th></th>
            <th align="right">{{ round($totalAmount, 2) }}</th>
            <th></th>
            {{-- <th align="right">{{ $totalPercentageAmount }}</th> --}}
        </tr>
    </tfoot>

</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif

@if ($criteria == 'dealer_type')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Dealer Type</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['type'] }}
            </td>
            <td align="right">{{ round($d['product_qty'], 2) }}</td>
            <td align="right">
                {{ round($d['product_qty_p'], 2) }}
            </td>
            <td align="right">{{ round($d['product_value'], 2) }}</td>
            <td align="right">
                {{ round($d['product_value_p'], 2) }}
            </td>
        </tr>
        @php
        $totalQty += round($d['product_qty'], 2);
        $totalPercentageQty += round($d['product_qty_p'], 2);
        $totalAmount += round($d['product_value'], 2);
        $totalPercentageAmount += round($d['product_value_p'], 2);
        @endphp
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            {{-- <th align="right">{{ $totalPercentageQty }}</th> --}}
            <th></th>
            <th align="right">{{ round($totalAmount, 2) }}</th>
            <th></th>
            {{-- <th align="right">{{ $totalPercentageAmount }}</th> --}}
        </tr>
    </tfoot>

</table>

<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif

@if ($criteria == 'region')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Region Name</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        <?php
        if ($d['region_qty'] == 0) {
            continue;
        }

        $totalQty += round($d['region_qty'], 2);
        $totalPercentageQty += number_format($d['region_qty_p'], 2, '.', '');
        $totalAmount += number_format($d['region_value'], 2, '.', '');
        $totalPercentageAmount += number_format($d['region_value_p'], 2, '.', '');
        
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['region_name'] }}</td>
            <td align="right">{{ number_format($d['region_qty'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['region_qty_p'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['region_value'], 2, '.', '') }}</td>
            <td align="right">
                {{ number_format($d['region_value_p'], 2, '.', '') }}
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            <th></th>
            <th align="right">{{ number_format($totalAmount, 2, '.', '') }}</th>
            <th></th>
        </tr>
    </tfoot>

</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif

@if ($criteria == 'area')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Area Name</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        <?php
        if ($d['area_qty'] == 0) {
            continue;
        }

        $totalQty += round($d['area_qty'], 2);
        $totalPercentageQty += number_format($d['area_qty_p'], 2, '.', '');
        $totalAmount += number_format($d['area_value'], 2, '.', '');
        $totalPercentageAmount += number_format($d['area_value_p'], 2, '.', '');
        
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['area_name'] }}</td>
            <td align="right">{{ number_format($d['area_qty'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['area_qty_p'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['area_value'], 2, '.', '') }}</td>
            <td align="right">
                {{ number_format($d['area_value_p'], 2, '.', '') }}
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            <th></th>
            <th align="right">{{ number_format($totalAmount, 2, '.', '') }}</th>
            <th></th>
        </tr>
    </tfoot>

</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif


@if ($criteria == 'territory')

<table id="report-table" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px">SL#</th>
            <th>Territory Name</th>
            <th width="90px">By Qty</th>
            <th width="85px">Qty(%)</th>
            <th width="105px">By Value</th>
            <th width="85px">Value(%)</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        $totalQty = 0;
        $totalPercentageQty = 0;
        $totalAmount = 0;
        $totalPercentageAmount = 0;
        @endphp

        @foreach ($data as $d)
        <?php
        if ($d['territory_qty'] == 0) {
            continue;
        }

        $totalQty += round($d['territory_qty'], 2);
        $totalPercentageQty += number_format($d['territory_qty_p'], 2, '.', '');
        $totalAmount += number_format($d['territory_value'], 2, '.', '');
        $totalPercentageAmount += number_format($d['territory_value_p'], 2, '.', '');
        
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $d['territory_name'] }}</td>
            <td align="right">{{ number_format($d['territory_qty'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['territory_qty_p'], 2, '.', '') }}</td>
            <td align="right">{{ number_format($d['territory_value'], 2, '.', '') }}</td>
            <td align="right">
                {{ number_format($d['territory_value_p'], 2, '.', '') }}
            </td>
        </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th align="right" colspan="2">Total</th>
            <th align="right">{{ $totalQty }}</th>
            <th></th>
            <th align="right">{{ number_format($totalAmount, 2, '.', '') }}</th>
            <th></th>
        </tr>
    </tfoot>

</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php date_default_timezone_set('Asia/Dhaka'); ?>
        <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
    </div>
</div>

@endif
@endsection
