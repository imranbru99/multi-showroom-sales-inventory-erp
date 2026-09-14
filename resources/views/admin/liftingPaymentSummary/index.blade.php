@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">
    <div class="col-md-6">
        <label for="vendor">Vendor</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="vendor" name="vendor[]" data-placeholder="Select Vendor" multiple>
                @foreach ($vendors as $vendorInfo)
                <?php
                $select = "";
                if ($vendor) {
                    if (in_array($vendorInfo->id, $vendor)) {
                        $select = "selected";
                    } else {
                        $select = "";
                    }
                }
                ?>
                <option value="{{ $vendorInfo->id }}" {{ $select }}>{{ $vendorInfo->name }}</option>
                @endforeach
            </select>
        </div>  
    </div>

    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="from-date">Month</label>
                <div class="form-group">
                    @php
                    $months = array('1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
                    @endphp
                    <select class="form-control" id="month" name="month">
                        <?php
                        $select = "";
                        if ($month == "") {
                            $month = date('m');
                        }
                        ?>
                        @foreach ($months as $key => $value)
                        <?php
                        if ($key == $month) {
                            $select = "selected";
                        } else {
                            $select = "";
                        }
                        ?>
                        <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6 form-group">
                <label for="from_date">Year</label>
                <select class="form-control" id="year" name="year">
                    <option value="">Select Year</option>
                    <?php
                    $select = "";
                    if ($year == "") {
                        $year = date('Y');
                    }
                    $currentYear = date('Y');
                    ?>
                    @for ($i = $currentYear; $i >= 1900; $i--)
                    <?php
                    if ($i == $year) {
                        $select = "selected";
                    } else {
                        $select = "";
                    }
                    ?>
                    <option value="{{ $i }}" {{ $select }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>                                  
    </div>
</div>
@endsection

@section('print_card_header')
@if ($vendor)
@foreach ($vendor as $vendorInfo)
<input type="hidden" name="vendor[]" value="{{ $vendorInfo }}">
@endforeach
@endif

<input type="hidden" name="year" value="{{ $year }}">
<input type="hidden" name="month" value="{{ $month }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<div class="table-responsive">
    <table id="dataTable" name="liftingPaymentSummary" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px" rowspan="2">Sl</th>
                <th rowspan="2">Vendor Name</th>
                <th rowspan="2" width="120px">Previous Years</th>
                <th colspan="4" style="text-align: center;"><b>For The Year {{ $year }}</b></th>
                <th colspan="4" style="text-align: center;"><b>For The Month {{ date('F', mktime(0, 0, 0,$month, 10)) }}</b></th>
                <th rowspan="2" width="120px">Vendor Outstanding</th>
            </tr>
            <tr>
                <th width="90px">Lifting</th>
                <th width="90px">Payments</th>
                <th width="90px">Return</th>
                <th width="90px">Balance</th>
                <th width="90px">Lifting</th>
                <th width="90px">Payments</th>
                <th width="90px">Return</th>
                <th width="90px">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
            $sl = 0;
            @endphp
            @foreach ($data as $row)
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $row['vendorName'] }}</td>
                <td align="right">{{ number_format($row['previousBalance'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['year']['lifting'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['year']['payment'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['year']['return'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['year']['balance'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['month']['lifting'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['month']['payment'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['month']['return'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['month']['balance'], 2, '.', '') }}</td>
                <td align="right">{{ number_format($row['marketOutstanding'], 2, '.', '') }}</td>
            </tr>
            @endforeach        
        </tbody>
    </table>
</div>
@endsection