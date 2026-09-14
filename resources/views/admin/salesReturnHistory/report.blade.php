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

    <div class="col-md-4">
        <label for="productType">Product Type</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="productType" name="productType" required>
                <option value="">Select Product Type</option>
                @foreach ($productTypes as $key => $value)
                <option value="{{ $key }}" @if($key == $type) selected @endif>{{ $value }}</option>
                @endforeach
            </select>
        </div>
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
    

    <div class="col-md-8">
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
                <option value="{{ $dealerInfo->id }}">{{ $dealerInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <div class="col-md-4">
        <label for="category">Category</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="category" name="category[]" multiple>
                @foreach ($categories as $categoryInfo)
                <?php
                $select = '';
                if ($category) {
                    if (in_array($categoryInfo->id, $category)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $categoryInfo->id }}" {{ $select }}>{{ $categoryInfo->name }}</option>
                @endforeach
            </select>
        </div>
    </div>



    <div class="col-md-12">
        <label for="product">Product</label>
        <div class="form-group">
            <select class="form-control chosen-select" id="product" name="product[]" multiple>
                @foreach ($products as $productInfo)
                <?php
                $select = '';
                if ($product) {
                    if (in_array($productInfo->id, $product)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                }
                ?>
                <option value="{{ $productInfo->id }}" {{ $select }}>{{ $productInfo->name }}
                    ({{ $productInfo->model_no }})</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endsection

@section('print_card_header')
@if ($dealer)
@foreach ($dealer as $dealerIn)
<input type="hidden" name="dealer[]" value="{{ $dealerIn }}">
@endforeach
@endif

@if ($category)
@foreach ($category as $categoryInfo)
<input type="hidden" name="category[]" value="{{ $categoryInfo }}">
@endforeach
@endif

@if ($product)
@foreach ($product as $productInfo)
<input type="hidden" name="product[]" value="{{ $productInfo }}">
@endforeach
@endif

<input type="hidden" id="print_value" name="productType" value="{{ $type }}">

<input type="hidden" id="print_value" name="region" value="{{ $region }}">
<input type="hidden" id="print_value" name="area" value="{{ $area }}">
<input type="hidden" id="print_value" name="territory" value="{{ $territory }}">
<input type="hidden" name="fromDate" value="{{ $fromDate }}">
<input type="hidden" name="toDate" value="{{ $toDate }}">
<input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th width="15px">SL#</th>
            <th width="80px">Date</th>
            <th width="130px">Dealer Name</th>
            <th width="30px">Category Name</th>
            <th width="80px">Product Name</th>
            @if($type == 'warranty_product')
            <th width="90px">Model</th>
            <th width="90px">Serial No</th>
            @endif
            <th width="80px">Qty</th>
            <th width="80px">Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
        $sl = 1;
        @endphp

        @foreach ($productReturnHistories as $productReturnHistory)

        <tr>
            <td>{{ $sl++ }}</td>
            <td>{{ date('d-m-Y', strtotime($productReturnHistory->return_date)) }}</td>
            <td>{{ @$productReturnHistory->dealer->name }}</td>
            <td>{{ @$productReturnHistory->product->category->name }}</td>
            <td>{{ @$productReturnHistory->product->name }}</td>
            @if($type == 'warranty_product')
            <td>{{ @$productReturnHistory->product->model_no }}</td>
            <td>{{ @$productReturnHistory->serial_no }}</td>
            @endif
            <td align="right">{{ $productReturnHistory->qty }}</td>
            <td align="right">{{ number_format($productReturnHistory->amount, 2, '.', '') }}</td>
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

<script>
    $('#productType').change(function () {
        var type = $(this).val();
        $('#product option').remove();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('product.type') }}",
            data: {
                type: type,
            },
            success: function (response) {
                response.forEach(function (item, index) {
                    var option = `<option value="${item.id}">${item.name} - (${item.model_no})</option>`;
                    $('#product').append(option);
                    $('.chosen-select').chosen();
                    $('.chosen-select').trigger("chosen:updated");
                });
            }
        });
        $('.chosen-select').chosen();
        $('.chosen-select').trigger("chosen:updated");
    });
</script>
@endsection
