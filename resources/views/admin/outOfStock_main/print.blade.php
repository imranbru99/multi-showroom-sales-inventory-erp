{{-- changed --}}
@extends('admin.layouts.masterPrint')

@php
use App\LiftingProduct;
use App\ProductIssueList;
use App\LiftingReturnProduct;
use App\SalesReturn;
use App\ServiceSpareAllocation;
use App\TransferProduct;
@endphp


@section('content')
    <table id="report-header">
        <tr>
            <td>{{ $title }}
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
        <thead>
            <tr>
                <th width="5%">Sl</th>
                <th width="10%">Category</th>
                <th width="8%">Product</th>
                <th width="12%">Model</th>
                <th width="55%">Serial No</th>
                <th width="10%">Available Qty</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sl = 1;
                $totalRemainingQty = 0;
            @endphp

            @foreach ($stockOutReports as $stockOutReport)
                @php
                    if($stockOutReport->remainingQty <= 0){
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
                    <td style="text-align: right;">{{ count($notSoldSerials) }}</td>
                </tr>
                {{-- @endif --}}
                @php
                $totalRemainingQty += count($notSoldSerials);
                @endphp
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
                    <td style="text-align: right;">{{ count($tnotSoldSerials) }}</td>
                </tr>
                {{-- @endif --}}
                @php
                $totalRemainingQty += count($tnotSoldSerials);
                @endphp
            @endforeach
            @endif
            
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><b>Total Available Qty</b></td>
                <td  align="right"><b>{{$totalRemainingQty}}</b></td>
            </tr>
        </tfoot>
    </table>
  
    <div class="row">
        <div class="col-md-12 text-right">
            <?php date_default_timezone_set('Asia/Dhaka'); ?>
            <p>Print Date & Time : <?php echo date('d-m-Y h:i:sa'); ?></p>
        </div>
    </div>
@endsection
