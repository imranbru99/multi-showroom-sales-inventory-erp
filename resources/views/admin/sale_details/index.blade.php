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


<form class="form-horizontal" id="search" action="{{ route('sales.details.report.page') }}" target="_blank" method="POST"
      enctype="multipart/form-data">
    {{ csrf_field() }}

    <input type="hidden" name="print" value="print">

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">{{ $title }}</h4>
                </div>
                <div class="col-md-4" style="margin-top: 13px;">
                    <div class="row">
                        <select class="form-control" name="by_param">
                            <option value="bySale"@if ($param=='bySale') checked @endif>By Sale</option>
                            <option value="byCollection"@if ($param=='byCollection') checked @endif>Collection</option>
                        </select> 
                    </div> 
                </div>
            </div>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 form-group">
                    <label for="from-date">From Date</label>
                    <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}"
                           name="fromDate" placeholder="Select Date From"
                           value="{{ date('d-m-Y', strtotime($fromDate)) }}" readonly>
                </div>
                <div class="col-md-4 form-group">
                    <label for="to-date">To Date</label>
                    <input type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}"
                           name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y', strtotime($toDate)) }}"
                           readonly>
                </div>
                <div class="col-md-4">
                    <label for="product">Employee</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="staffs" name="staffs[]"
                                data-placeholder="Select staffs" multiple>
                            @foreach ($staffs as $staff)
                            <option value="{{ $staff->id }}" @if (in_array($staff->id, $employee)) selected @endif>
                                {{ $staff->name }}
                            </option>
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

                <div class="col-md-4">
                    <label for="category">Category</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="category" name="category[]"
                                data-placeholder="Select Categories" multiple>
                            @foreach ($categories as $categoryInfo)
                            @php
                            $select = '';
                            if ($category) {
                            if (in_array($categoryInfo->id, $category)) {
                            $select = 'selected';
                            } else {
                            $select = '';
                            }
                            }
                            @endphp
                            <option value="{{ $categoryInfo->id }}" {{ $select }}>
                                {{ $categoryInfo->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="product">Product</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="product" name="product[]"
                                data-placeholder="Select Products" multiple>
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
                            <option value="{{ $productInfo->id }}" {{ $select }}>
                                {{ $productInfo->name }} - ({{ $productInfo->model_no }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="submit"
                            value="Search"><i class="fa fa-search"></i> Search</button>
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="submit"
                            value="Print"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
</form>


<div class="card" style="margin-bottom: 0px;">
    <div class="card-header">
        <h1 class="text-center h3">Sales Details List</h1>
    </div>

    <div class="card-body">
        @foreach ($eWiseProductIssuelist as $data)

        @php
        $totalBalance = 0;
        $productsSum = 0;
        @endphp

        @foreach ($data['productIssuelist'] as $issue)

        @php
        $tempModelName = [];
        $byModel = $issue->products->groupBy('model_no');
        @endphp

        @foreach ($byModel as $model => $products)
        @php
        $totalBalance += round($products->sum('amount') - $products->sum('collection'), 2);
        $productsSum += $products->sum('amount');
        @endphp
        @endforeach
        @endforeach

        @if ($productsSum > 0)

        <div class="row">
            <div class="col-md-6">
                <p class="text-left">Sale By - {{ $data['eInfo']->name }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-right">Due On Invoice - {{ $totalBalance }}</p>
            </div>
        </div>

        <table class="table table-bordered table-sm dataTable no-footer">
            <thead>
                <tr>
                    <th>SL#</th>
                    <th>Date</th>
                    <th>Invoice No</th>
                    <th>Product Name</th>
                    <th>Model No</th>
                    <th>Qty</th>
                    <th>Actual Sale</th>
                    <th>Collection</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                $i = 1;
                @endphp
                @foreach ($data['productIssuelist'] as $issue)
                @php
                $tempModelName = [];
                $byModel = $issue->products->groupBy('model_no');
                @endphp

                @foreach ($byModel as $model => $products)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        @if ($param == 'bySale')
                        {{ date('d-m-Y', strtotime($issue->date)) }}
                        @else
                        {{ date('d-m-Y', strtotime($products[0]->updated_at)) }}
                        @endif
                    </td>
                    <td>{{ $issue->issue_no }}</td>
                    <td>
                        {{ $products[0]->product->name }}
                    </td>
                    <td>
                        {{ $model }}
                    </td>
                    <td>{{ $products->sum('qty') }}</td>
                    <td>{{ round($products->sum('amount'), 2) }}</td>
                    <td>
                        {{ round($products->sum('collection'), 2) }}
                    </td>
                    <td>
                        {{ round($products->sum('amount') - $products->sum('collection'), 2) }}
                    </td>
                </tr>
                @endforeach

                @endforeach
            </tbody>
        </table>

        @endif

        @endforeach
    </div>
</div>
@endsection
@section('custom-js')

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
