@extends('admin.layouts.master')

@section('content')
<form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="print" value="print">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="supplier">Supplier</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="vendor" name="vendor[]" data-placeholder="Select Supplier" multiple>
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

                <div class="col-md-3 form-group">
                    <label for="from-date">From Date</label>
                    <input  type="text" class="form-control datepicker" id="{{ $print == "print" ? "" : "from_date" }}" name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y',strtotime($fromDate)) }}" readonly>
                </div>
                <div class="col-md-3 form-group">
                    <label for="to-date">To Date</label>
                    <input  type="text" class="form-control datepicker" id="{{ $print == "print" ? "" : "to_date" }}" name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y',strtotime($toDate)) }}" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label for="category">Category</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="category" name="category[]" data-placeholder="Select Category" multiple>
                            @foreach ($categories as $categoryInfo)
                            <?php
                            $select = "";
                            if ($category) {
                                if (in_array($categoryInfo->id, $category)) {
                                    $select = "selected";
                                } else {
                                    $select = "";
                                }
                            }
                            ?>
                            <option value="{{ $categoryInfo->id }}" {{ $select }}>{{ $categoryInfo->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                </div>
                <div class="col-md-3">
                    <label for="productType">Product Type</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="productType" name="productType">
                            <option value="">Select Product Type</option>
                            @foreach ($productTypes as $key => $value)
                            <option value="{{ $key }}" @if($key == $type) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="store-or-showroom">Store</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" name="storeOrShowroom">
                            <option value=" ">Select Company Name</option>
                            @foreach ($stores as $str)
                            <option value="{{ $str->id }}" @if($str->id == $type) selected @endif>{{ $str->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                </div>
            </div>

            <div class="row">

                <div class="col-md-12">
                    <label for="product">Product</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="product" name="product[]" data-placeholder="Select Product" multiple>
                            @foreach ($products as $productInfo)
                            <?php
                            $select = "";
                            if ($product) {
                                if (in_array($productInfo->id, $product)) {
                                    $select = "selected";
                                } else {
                                    $select = "";
                                }
                            }
                            ?>
                            <option value="{{ $productInfo->id }}" {{ $select }}>{{$productInfo->name}} ({{$productInfo->model_no}})</option>
                            @endforeach
                        </select>
                    </div>                                  
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnSummary" value="Summary"><i class="fa fa-search"></i> Lifting Return Summary</button>
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnRecord" value="Record"><i class="fa fa-search"></i> Lifting Return History</button>
                    </div>
                </div>              
            </div>
        </div>
    </div>
</form>



@if ($btnSummary != "" || $btnRecord != "")
<div class="card" style="margin-bottom: 0px;">              
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">Searched Report</h4></div>
            <div class="col-md-6 text-right">
                <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    @if ($vendor)
                    @foreach ($vendor as $vendorInfo)
                    <input type="hidden" name="vendor[]" value="{{ $vendorInfo }}">
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

                    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                    <input type="hidden" name="toDate" value="{{ $toDate }}">

                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="store" value="{{ $store }}">
                    <input type="hidden" id="print_value" name="print" value="{{ $print }}">

                    @if ($btnSummary == "Summary")
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnPrintSummary" value="Print Summary"><i class="fa fa-print"></i> Print Lifting Return Summary</button>
                    @endif

                    @if ($btnRecord == "Record")
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnPrintRecord" value="Print Record"><i class="fa fa-print"></i> Print Lifting Return History</button>
                    @endif

                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            @if ($btnSummary == "Summary")
            <table id="dataTable" name="liftingSummary" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">Sl</th>
                        <th>Vendor Name</th>
                        <th>Product Information</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $sl = 1;
                    @endphp
                    @foreach ($liftingReturnSummaries as $liftingReturnSummary)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $liftingReturnSummary->vendorName }}</td>
                        <td>
                            <table class="w-100">
                                <thead class="bg-success">
                                    <tr>
                                        <th>Category Name</th>
                                        <th>Product Name</th>
                                        <th>Store</th>
                                        <th width="100px">Total Lifting</th>
                                        <th width="110px">Toal Amount</th>
                                    </tr>
                                </thead>
                                <?php
                                $totalQty = 0;
                                $totalAmount = 0;
                                $liftingSum = DB::table('view_lifting_return_record')
                                        ->select('productName',
                                                'productModelNo',
                                                'vendorName',
                                                'categoryName',
                                                'tbl_stores.name as storeName',
                                                DB::raw('SUM(productQty) as totalLifting'),
                                                DB::raw('SUM(amount) as totalLiftingPrice')
                                        )
                                        ->leftjoin('tbl_stores', 'tbl_stores.id', '=', 'view_lifting_return_record.storeOrShowroomId')
                                        ->orWhere(function ($query) use ($fromDate, $toDate, $vendor, $category, $product, $type, $store, $liftingReturnSummary) {
                                            if (!empty($fromDate)) {
                                                $query->whereBetween('liftingReturnDate', array($fromDate, $toDate));
                                            }

                                            if ($category) {
                                                $query->whereIn('categoryId', $category);
                                            }

                                            if ($product) {
                                                $query->whereIn('productId', $product);
                                            }

                                            if ($store) {
                                                $query->where('storeOrShowroomType', 'store')
                                                ->where('storeOrShowroomId', $liftingReturnSummary->storeOrShowroomId);
                                            }
                                        })
                                        ->where('vendorId', $liftingReturnSummary->vendorId)
                                        ->groupBy('productId')
                                        ->groupBy('vendorId')
                                        ->orderBy('productName', 'asc')
                                        ->get();
                                ?>
                                @foreach ($liftingSum as $liftingS)
                                <?php
                                $totalQty += $liftingS->totalLifting;
                                $totalAmount += $liftingS->totalLiftingPrice;
                                ?>
                                <tr>
                                    <td>{{ $liftingS->categoryName }}</td>
                                    <td>{{ $liftingS->productName }} - ({{ $liftingS->productModelNo }})</td>
                                    <td>{{ $liftingS->storeName }}</td>
                                    <td align="right">{{ $liftingS->totalLifting }}</td>
                                    <td align="right">{{ number_format($liftingS->totalLiftingPrice, 2, '.', '') }}</td>
                                </tr>
                                @endforeach
                                <tfoot>
                                    <tr class="bg-success font-weight-bold">
                                        <td colspan="3">Total</td>
                                        <td align="right">{{$totalQty}}</td>
                                        <td align="right">{{ number_format($totalAmount, 2, '.', '') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            
            @if ($btnRecord == "Record")
            <table id="dataTable" name="liftingRecord" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">Sl</th>
                        <th>Date</th>
                        <th>Lifting Return No</th>
                        <th>Vendor</th>
                        <th>Company Name</th>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Serial</th>
                        <th>Model</th>
                        <th>Qty</th>
                        <th>Price</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $sl = 0;
                    @endphp
                    @foreach ($liftingReturnRecords as $liftingReturnRecord)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ date('d-m-Y',strtotime($liftingReturnRecord->liftingReturnDate))}}</td>
                        <td>{{ $liftingReturnRecord->liftingReturnNo }}</td>
                        <td>{{ $liftingReturnRecord->vendorName }}</td>
                        <td>{{ $liftingReturnRecord->storeOrShowroomName }}</td>
                        <td>{{ $liftingReturnRecord->categoryName }}</td>
                        <td>{{ $liftingReturnRecord->productName }}</td>
                        <td>{{ $liftingReturnRecord->productSerialNo }}</td>
                        <td>{{ $liftingReturnRecord->productModelNo }}</td>
                        <td>{{ $liftingReturnRecord->productQty }}</td>
                        <td>{{ $liftingReturnRecord->price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endif

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