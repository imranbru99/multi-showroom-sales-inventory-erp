{{-- changed --}}
@php
use App\LiftingProduct;
use App\ProductIssueList;
use App\LiftingReturnProduct;
use App\SalesReturn;
use App\ServiceSpareAllocation;
use App\TransferProduct;
@endphp
@extends('admin.layouts.masterReport')

@section('search_card_body')
    <input type="hidden" name="print" value="print">
    <div class="row">
        <div class="col-md-3">
            <label for="product-category">Store Name</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="store_id" name="store_id">
                    <option value="">All </option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}" @if($store->id == $store_id) selected @endif>{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <label for="product-category">Category</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="productCategory" name="productCategory[]" multiple>
                    @php
                        $select = '';
                        if ($productCategory) {
                            if ($productCategory[0] == 'All') {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        }
                    @endphp

                    <option value="All" {{ $select }}>All </option>
                    @foreach ($categories as $category)
                        @php
                            $select = '';
                            if ($productCategory) {
                                if (in_array($category->id, $productCategory)) {
                                    $select = 'selected';
                                } else {
                                    $select = '';
                                }
                            }
                        @endphp
                        <option value="{{ $category->id }}" {{ $select }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-5">
            <label for="product">Product</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="product" name="product[]" multiple>

                    @php
                        $select = '';
                        if ($product) {
                            if ($product[0] == 'All') {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                        }
                    @endphp

                    <option value="All" {{ $select }}>All</option>
                    @foreach ($products as $productInfo)
                        @php
                            $select = '';
                            if ($product) {
                                if (in_array($productInfo->id, $product)) {
                                    $select = 'selected';
                                } else {
                                    $select = '';
                                }
                            }
                        @endphp
                        <option value="{{ $productInfo->id }}" {{ $select }}>{{ $productInfo->name }}
                            ({{ $productInfo->model_no }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endsection

@section('print_card_header')
    @if ($productCategory)
        @foreach ($productCategory as $productCategoryInfo)
            <input type="hidden" name="productCategory[]" value="{{ $productCategoryInfo }}">
        @endforeach
    @endif

    @if ($product)
        @foreach ($product as $productInfo)
            <input type="hidden" name="product[]" value="{{ $productInfo }}">
        @endforeach
    @endif
    <input type="hidden" class="form-control" id="store_id" name="store_id" value="{{ $store_id }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
    <table id="dataTable" name="productList" class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="20px">Sl</th>
                <th width="200px">Category</th>
                <th>Product</th>
                <th width="200px">Model</th>
                <th>Serial No</th>
                <th width="110px">Available Qty</th>
            </tr>
        </thead>

        <tbody>
            @php
                $sl = 1;
            @endphp

            @foreach ($stockOutReports as $stockOutReport)
                @php
                    if ($stockOutReport->remainingQty <= 0) {
                        continue;
                    }
                @endphp

                @php
                    
                    $liftingReturnSerials = LiftingReturnProduct::where('product_id', $stockOutReport->productId)
                        ->select(['serial_no']);
                        if($store_id){
                            $liftingReturnSerials = $liftingReturnSerials->where('store_id', $store_id);
                        }else{
                            $liftingReturnSerials = $liftingReturnSerials->whereIn('store_id', $storeIds);
                        }
                    $liftingReturnSerials = $liftingReturnSerials->get()
                        ->pluck('serial_no')
                        ->toArray();
                        
                    $actualLiftingSerials = LiftingProduct::where('product_id', $stockOutReport->productId)
                        ->whereNotIn('serial_no', $liftingReturnSerials)
                        ->select(['serial_no']);
                        if($store_id){
                            $actualLiftingSerials = $actualLiftingSerials->where('store_id', $store_id);
                        }else{
                            $actualLiftingSerials = $actualLiftingSerials->whereIn('store_id', $storeIds);
                        }
                    $actualLiftingSerials = $actualLiftingSerials->get()
                        ->pluck('serial_no')
                        ->toArray();
                    
                    $soldSerials = ProductIssueList::where('product_id', $stockOutReport->productId)
                        ->where('isReturn', '=', 0)
                        ->select(['serial_no']);
                        if($store_id){
                            $soldSerials = $soldSerials->where('store_id', $store_id);
                        }else{
                            $soldSerials = $soldSerials->whereIn('store_id', $storeIds);
                        }
                    $soldSerials = $soldSerials->get()
                        ->pluck('serial_no')
                        ->toArray();
                        
                    $usedSpares = ServiceSpareAllocation::where('product_id', $stockOutReport->productId)
                        ->select('serial_no')
                        ->get()
                        ->pluck('serial_no')
                        ->toArray();

                    $transferProducts = TransferProduct::with('transfer')
                        ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                            if($store_id){
                                $q->where('host_id', $store_id);
                            }else{
                                $q->whereIn('host_id', $storeIds);
                            }
                        })
                        // ->whereNotNull('approve_by')
                        ->where('product_id', $stockOutReport->productId)
                        ->get()
                        ->pluck('serial_no')
                        ->toArray();

                    $transferReceives = TransferProduct::with('transfer')
                        ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                            if($store_id){
                                $q->where('destination_id', $store_id);
                            }else{
                                $q->whereIn('destination_id', $storeIds);
                            }
                        })
                        ->whereNotNull('approve_by')
                        ->where('product_id', $stockOutReport->productId)
                        ->get()
                        ->pluck('serial_no')
                        ->toArray();

                    $allStock = array_merge($actualLiftingSerials, $transferReceives);
                    $allSold = array_merge($soldSerials, $usedSpares, $transferProducts);
                    $notSoldSerials = array_diff( $allStock, $allSold);

                    if( count($notSoldSerials) < 1){
                        continue;
                    }
                @endphp

                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $stockOutReport->categoryName }}</td>
                    <td>{{ $stockOutReport->productName }}</td>
                    <td>{{ $stockOutReport->modelNo }}</td>
                    <td>


                        @foreach ($notSoldSerials as $serial)
                            {{ $serial }},
                        @endforeach

                    </td>
                    <td style="text-align: right;">
                        {{ count($notSoldSerials) }}
                    </td>
                </tr>
                {{-- @endif --}}
            @endforeach
            @if(!empty($transferRecProducts) && count($transferRecProducts) > 0)
            @foreach ($transferRecProducts as $transferRecProduct)
                @php
                    
                    $tsoldSerials = ProductIssueList::where('product_id', $transferRecProduct->product_id)
                        ->where('isReturn', '=', 0)
                        ->select(['serial_no']);
                        if($store_id){
                            $tsoldSerials = $tsoldSerials->where('store_id', $store_id);
                        }else{
                            $tsoldSerials = $tsoldSerials->whereIn('store_id', $storeIds);
                        }
                    $tsoldSerials = $tsoldSerials->get()
                        ->pluck('serial_no')
                        ->toArray();
                        
                    $tusedSpares = ServiceSpareAllocation::where('product_id', $transferRecProduct->product_id)
                        ->select('serial_no')
                        ->get()
                        ->pluck('serial_no')
                        ->toArray();

                    $ttransferProducts = TransferProduct::with('transfer')
                        ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                            if($store_id){
                                $q->where('host_id', $store_id);
                            }else{
                                $q->whereIn('host_id', $storeIds);
                            }
                        })
                        // ->whereNotNull('approve_by')
                        ->where('product_id', $transferRecProduct->product_id)
                        ->get()
                        ->pluck('serial_no')
                        ->toArray();

                    $ttransferReceives = TransferProduct::with('transfer')
                        ->whereHas('transfer', function ($q) use ($store_id, $storeIds) {
                            if($store_id){
                                $q->where('destination_id', $store_id);
                            }else{
                                $q->whereIn('destination_id', $storeIds);
                            }
                        })
                        ->whereNotNull('approve_by')
                        ->where('product_id', $transferRecProduct->product_id)
                        ->get()
                        ->pluck('serial_no')
                        ->toArray();

                    $tallStock = $ttransferReceives;
                    $tallSold = array_merge($tsoldSerials, $tusedSpares, $ttransferProducts);
                    $tnotSoldSerials = array_diff( $tallStock, $tallSold);


                    if( count($tnotSoldSerials) < 1){
                        continue;
                    }

                    $product = \App\Product::find($transferRecProduct->product_id);
                    $category = \App\CategorySetup::find($product->category_id);
                @endphp

                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->model_no }}</td>
                    <td>


                        @foreach ($tnotSoldSerials as $tserial)
                            {{ $tserial }},
                        @endforeach

                    </td>
                    <td style="text-align: right;">
                        {{ count($tnotSoldSerials) }}
                    </td>
                </tr>
                {{-- @endif --}}
            @endforeach
            @endif
        </tbody>
    </table>
@endsection
