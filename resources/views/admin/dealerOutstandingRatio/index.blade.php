@extends('admin.layouts.masterReport')
@section('search_card_body')
<input type="hidden" name="print" value="print">
<div class="row d-flex justify-content-center">
    <div class="col-md-4 form-group">
        <label for="">Emoloyee</label>
        <select name="staff" class="form-control chosen-select">
            <option value="">Select Employee</option>
            @foreach ($staffs as $stf)
            <option value="{{ $stf->id }}" @if ($stf->id == $staff) selected @endif>{{ $stf->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label for="">Distribution Chain</label>
        <select name="distribution" class="form-control chosen-select" id="distribution">
            <option value="">Select Distribution</option>
            @foreach($distributions as $key => $value)
            <option value="{{ $key }}" @if($distribution == $key) selected @endif>{{ $value }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label for="">Region/Area/Territory</label>
        <select name="loaction" class="form-control chosen-select" id="location">
            <option value="">Select Option</option>
            @foreach($locations as $loc)
            <option value="{{ $loc->id }}" @if($location == $loc->id) selected @endif>{{ $loc->name }}</option>
            @endforeach
        </select>
    </div>
</div>
@endsection

@section('print_card_header')
<input type="hidden" name="staff" value="{{ $staff }}">
<input type="hidden" name="distribution" value="{{ $distribution }}">
<input type="hidden" name="location" value="{{ $location }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')


<table id="dataTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>SL</th>
            <th>Dealer Name</th>
            <th>Contact No</th>
            <th class="text-center">Sales</th>
            <th class="text-center">Collection</th>
            <th class="text-center">Outstanding</th>
            <th class="text-center">Last Collection</th>
            <th class="text-center">Collection Duration</th>
            <th class="text-center">Last Sales</th>
            <th class="text-center">Sales Duration</th>
            <th class="text-center">Sales By</th>
            <th>Due Ratio</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp

        @foreach ($outstanding as $dealer)
        <?php
        if ($dealer['outstanding'] <= 0) {
            continue;
        }
        ?>
        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ $dealer['dealer_name'] }}</td>
            <td>{{ $dealer['contact_no'] }}</td>
            <td class="text-center">{{ number_format($dealer['purchase'], 2, '.', '') }}</td>
            <td class="text-center">{{ number_format($dealer['collection'], 2, '.', '') }}</td>
            <td class="text-center">{{ number_format($dealer['outstanding'], 2, '.', '') }}</td>
            <td class="text-center">{{ $dealer['last_collection'] }}</td>
            <td class="text-center">{{ $dealer['collection_duration'] }} Day(s)</td>
            <td class="text-center text-nowrap">{{ $dealer['last_sales'] }}</td>
            <td class="text-center">{{ $dealer['sales_duration'] }} Day(s)</td>
            <td>{{ $dealer['sale_by'] }}</td>
            <td>
                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar"
                         style="width:{{ round($dealer['averagePercent']) }}%; height:5px;"
                         aria-valuenow="{{ round($dealer['averagePercent']) }}" aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
                <span class="progress-parcent">{{ $dealer['averagePercent'] }}%</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@section('custom-js')
<script>
    $('#distribution').change(function () {
        var distribution = $(this).val();
        $('#location option').remove();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'post',
            url: "{{ route('areaRegionTerritory') }}",
            data: {distribution: distribution},
            success: function (data) {
                if (distribution == 'region') {
                    $('#location').append('<option value="">Select Region</option>');
                }
                if (distribution == 'area') {
                    $('#location').append('<option value="">Select Area</option>');
                }
                if (distribution == 'territory') {
                    $('#location').append('<option value="">Select Territory</option>');
                }

                data.forEach(function (item, index) {
                    var option = `<option value="${item.id}">${item.name}</option>`;
                    $('#location').append(option);
                });



                $('.chosen-select').chosen();
                $('.chosen-select').trigger("chosen:updated");
            }
        });
    });
</script>
@endsection