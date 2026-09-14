@php
use App\SalesReturn;
use App\AdvanceCollection;
@endphp
@extends('admin.layouts.masterReport')

@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row">
    <div class="col-md-4">
        <label for="region">Region</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="region" name="region">
                <option value=''>Select Region</option>
                @foreach ($regions as $reg)
                <option value="{{ $reg->id }}" @if($region == $reg->id) selected @endif>{{ $reg->name }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-md-4">
        <label for="area">Area</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="area" name="area">
                <option value=''>Select Area</option>
                @foreach ($areas as $are)
                <option value="{{ $are->id }}" @if($area == $are->id) selected @endif>{{ $are->name }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-md-4">
        <label for="territory">Territory</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="territory" name="territory">
                <option value=''>Select Territory</option>
                @foreach ($territories as $terry)
                <option value="{{ $terry->id }}" @if($territory == $terry->id) selected @endif>{{ $terry->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2">
        <label for="dealer_type">Dealer Type</label>
        <div class="form-group">
            <select class="form-control" id="dealer_type" name="dealer_type">
                <option value="">-- Select One --</option>
                <option value="Internal">Internal</option>
                <option value="External">External</option>
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <label for="dealer">Dealer</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="dealer" name="dealer[]" multiple>
                @foreach ($dealers as $dealerInfo)
                <?php
                $select = '';
                if ($dealer) {
                    if (in_array($dealerInfo->id, $dealer)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $dealerInfo->id }}" {{ $select }}>{{ $dealerInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2">
        <label for="month">Month</label>
        <div class="form-group">
            <?php
            $months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June',
                '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' =>
                'December'];
            ?>
            <select class="form-control" id="month" name="month">
                <option value="">Select Month</option>
                <?php
                $select = '';
                if ($month == '') {
                    $month = date('m');
                }
                ?>
                @foreach ($months as $key => $value)
                <?php
                if ($key == $month) {
                    $select = 'selected';
                } else {
                    $select = '';
                }
                ?>
                <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2">
        <label for="from_date">Year</label>
        <div class="form-group">
            <select class="form-control" id="year" name="year">
                <option value="">Select Year</option>
                <?php
                $select = '';
                if ($year == '') {
                    $year = date('Y');
                }
                $currentYear = date('Y');
                ?>
                @for ($i = $currentYear; $i >= 1900; $i--)
                <?php
                if ($i == $year) {
                    $select = 'selected';
                } else {
                    $select = '';
                }
                ?>
                <option value="{{ $i }}" {{ $select }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if ($dealer)
@foreach ($dealer as $dealerInfo)
<input type="hidden" name="dealer[]" value="{{ $dealerInfo }}">
@endforeach
@endif

<input type="hidden" name="month" value="{{ $month }}">
<input type="hidden" name="year" value="{{ $year }}">
<input type="hidden" id="print_value" name="region" value="{{ $region }}">
<input type="hidden" id="print_value" name="area" value="{{ $area }}">
<input type="hidden" id="print_value" name="territory" value="{{ $territory }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
<input type="hidden" id="dealer_type" name="dealer_type" value="{{ $dealer_type }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="20px" rowspan="2" style="vertical-align: middle;">Sl</th>
            <th rowspan="2" style="vertical-align: middle;">Dealer Name</th>
            <th width="110px" rowspan="2" style="vertical-align: middle;">Previous Year</th>
            <th colspan="4" style="text-align: center;"><b>For The Year Of {{ $year == '' ? '' : $year }}</b></th>
            <th colspan="4" style="text-align: center;"><b>For The Month Of
                    {{ $month == '' ? '' : date('F', mktime(0, 0, 0, $month, 10)) }}</b></th>
            <th width="120px" rowspan="2" style="vertical-align: middle;">Current Outstanding</th>
            <th width="120px" rowspan="2" style="vertical-align: middle;">Credit %</th>
        </tr>
        <tr>
            <th width="70px">Sales</th>
            <th width="80px">Collection</th>
            <th width="80px">Return</th>
            <th width="70px">Due</th>
            <th width="7px">Sales</th>
            <th width="80px">Collection</th>
            <th width="80px">Return</th>
            <th width="70px">Due</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp
        @foreach ($data as $row)
        <?php
        if ($row['previousBalance'] == 0 && $row['year']['sales'] == 0 && $row['year']['collection'] == 0 &&
                $row['year']['return'] == 0 && $row['year']['balance'] == 0 && $row['month']['sales'] == 0 &&
                $row['month']['collection'] == 0 && $row['month']['return'] == 0 && $row['month']['balance'] == 0) {
            continue;
        }
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $row['dealerName'] }}</td>
            <td align="right">{{ round($row['previousBalance'], 2) }}</td>
            <td align="right">{{ round($row['year']['sales'], 2) }}</td>
            <td align="right">{{ round($row['year']['collection'], 2) }}</td>
            <td align="right">{{ round($row['year']['return'], 2) }}</td>
            <td align="right">
                {{ round($row['year']['balance'], 2) }}
            </td>
            <td align="right">{{ round($row['month']['sales'], 2) }}</td>
            <td align="right">{{ round($row['month']['collection'], 2) }}</td>
            <td align="right">{{ round($row['month']['return'], 2) }}</td>
            <td align="right">
                {{ round($row['month']['balance'], 2) }}
            </td>
            <td align="right">
                {{ round($row['currentOutstanding'], 2) }}
            </td>
            <td align="right">
                {{ round($row['creditP'], 2) }} %
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('custom-js')
<script>
    $('#dealer_type').change(function (e) {
        e.preventDefault();
        let dealer_type = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('dealer.get.by.type') }}",
            data: {
                dealer_type: dealer_type
            },
            success: function (response) {
                $('#dealer').empty();

                response.forEach(dealer => {
                    $('#dealer').append(
                            `<option value="${dealer.id}">${dealer.name}</option>`);
                });

                $('#dealer').trigger("chosen:updated");
            },
            error: function (response) {

            }
        });
    });


    $('#region').change(function () {
        var region = $(this).val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('productIssueHistory.dealerArea') }}",
            data: {
                region: region
            },
            success: function (response) {
                $('#dealer').empty();
                $('#area').empty();
                $('#area').append(`<option value="">Select Area</option>`);

                if (response.dealers.length > 0) {
                    response.dealers.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#dealer').append(option);
                    });
                }
                if (response.areas.length > 0) {
                    response.areas.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#area').append(option);
                    });
                }

                $('#dealer').trigger("chosen:updated");
                $('#area').trigger("chosen:updated");
            },
            error: function (response) {

            }
        });
    });



    $('#area').change(function (e) {
        var area = $(this).val();
        var region = $('#region').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('productIssueHistory.dealerArea') }}",
            data: {
                region: region,
                area: area,
            },
            success: function (response) {
                $('#dealer').empty();
                $('#territory').empty();
                $('#territory').append(`<option value="">Select Territory</option>`);
                if (response.dealers.length > 0) {
                    response.dealer.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#dealer').append(option);
                    });
                }
                if (response.territories.length > 0) {
                    response.territories.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#territory').append(option);
                    });
                }

                $('#dealer').trigger("chosen:updated");
                $('#territory').trigger("chosen:updated");
            },
            error: function (response) {

            }
        });
    });


    $('#territory').change(function (e) {
        var territory = $(this).val();
        var region = $('#region').val();
        var area = $('#area').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('productIssueHistory.dealerArea') }}",
            data: {
                region: region,
                area: area,
                territory: territory,
            },
            success: function (response) {
                $('#dealer').empty();
                if (response.dealers.length > 0) {
                    response.dealers.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#dealer').append(option);
                    });
                }
                $('#dealer').trigger("chosen:updated");
            },
            error: function (response) {

            }
        });
    });

</script>
@endsection