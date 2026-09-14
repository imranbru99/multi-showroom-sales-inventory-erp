@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-4 form-group">
            <label for="from-date">Criteria</label>
            <select name="criteria" class="form-control chosen-select">
                <option value="dealer">Dealer</option>
                <option value="product">Product</option>
                <option value="category">Category</option>
                <option value="employee">Employee</option>
                <option value="dealer_type">Dealer Type</option>
                <option value="region">Region</option>
                <option value="area">Area</option>
                <option value="territory">Territory</option>
            </select>
        </div>

        <div class="col-md-4 form-group">
            <label for="from-date">From Date</label>
            <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                name="fromDate" value="{{ date('d-m-Y', strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>

        <div class="col-md-4 form-group">
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
    <input type="hidden" name="criteria" value="{{ $criteria }}">
@endsection

@section('print_card_body')

    @if ($criteria == 'dealer')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Dealer Name</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
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
                @endforeach
            </tbody>
        </table>

    @endif

    @if ($criteria == 'product')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Product - Model</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
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
                        {{-- <td align="right">
                            {{ ($totalSalesContributions->sum('amount') / 100) * $salesContribution->sum('amount') }}
                        </td> --}}
                        <td align="right">
                            {{ round(($salesContribution->sum('amount') * 100) / $totalSalesContributions->sum('amount'), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

    @if ($criteria == 'category')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Category</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
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
                @endforeach
            </tbody>
        </table>

    @endif

    @if ($criteria == 'employee')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Employee</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
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
                @endforeach
            </tbody>
        </table>

    @endif

    @if ($criteria == 'dealer_type')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Dealer Type</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
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
                @endforeach
            </tbody>
        </table>

    @endif
    
    @if ($criteria == 'region')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Region Name</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                @endphp

                @foreach ($data as $d)
		@php
                 if($d['region_qty'] == 0){
                     continue;
                 }
                 @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $d['region_name'] }}</td>
                        <td align="right">{{ number_format($d['region_qty'], 2, '.', '') }}</td>
                        <td align="right">
                            {{ number_format($d['region_qty_p'], 2, '.', '') }}
                        </td>
                        <td align="right">{{ number_format($d['region_value'], 2, '.', '') }}</td>
                        <td align="right">
                            {{ number_format($d['region_value_p'], 2, '.', '') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
    
    @if ($criteria == 'area')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Area Name</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                @endphp

                @foreach ($data as $d)
		@php
                 if($d['area_qty'] == 0){
                     continue;
                 }
                 @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $d['area_name'] }}</td>
                        <td align="right">{{ round($d['area_qty'], 2) }}</td>
                        <td align="right">
                            {{ round($d['area_qty_p'], 2) }}
                        </td>
                        <td align="right">{{ round($d['area_value'], 2) }}</td>
                        <td align="right">
                            {{ round($d['area_value_p'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
    
    @if ($criteria == 'territory')

        <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="20px">Sl</th>
                    <th>Territory Name</th>
                    <th width="90px">By Qty</th>
                    <th width="85px">Qty (%)</th>
                    <th width="105px">By Value</th>
                    <th width="85px">Value (%)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $sl = 1;
                @endphp

                @foreach ($data as $d)
		@php
                 if($d['territory_qty'] == 0){
                     continue;
                 }
                 @endphp
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $d['territory_name'] }}</td>
                        <td align="right">{{ round($d['territory_qty'], 2) }}</td>
                        <td align="right">
                            {{ round($d['territory_qty_p'], 2) }}
                        </td>
                        <td align="right">{{ round($d['territory_value'], 2) }}</td>
                        <td align="right">
                            {{ round($d['territory_value_p'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif

    <script type="text/javascript">
        document.forms['searchForm'].elements['criteria'].value = "{{ $criteria }}";
    </script>

@endsection
