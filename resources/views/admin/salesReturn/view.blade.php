@extends('admin.layouts.masterIndexSalesReturn')

@php

    $product = DB::table('tbl_products')
    ->where('id',$sale->product_id)
    ->first(); 
    if(!empty($product)){
        $productName = $product->name;
        $productModel = $product->model_no;
        $cat = DB::table('tbl_categories')
                ->where('id',$product->category_id)
                ->first();
        $catName = $cat->name;
    }else{
        $productName = "";
        $productModel = "";
        $catName ="";
     }

    $productIssue = DB::table('tbl_product_issue')
    ->select('tbl_product_issue.*', 'tbl_dealer_requisitions.requisition_no as requisitionNo')
    ->leftJoin('tbl_dealer_requisitions', 'tbl_dealer_requisitions.id', '=', 'tbl_product_issue.requisition_id')
    ->where('tbl_product_issue.showroom_id',$showroomId)
    ->where('tbl_product_issue.id',$sale->issue_id)
    ->first();

    if(!empty($productIssue)){
          $dealer = DB::table('tbl_dealers')
            ->where('showroom_id',$showroomId)
            ->where('id',$productIssue->dealer_id)
            ->first(); 

            $dealerName = $dealer->name;
            $dealerCode = $dealer->code;
            $dealerAddress = $dealer->address;

            if(empty($dealerName)){
             $dealerName = "";}
            if(empty($dealerCode)){
             $dealerCode = "";}
            if(empty($dealerAddress)){
             $dealerAddress = "";}

    }else{
            $dealerName = "";
            $dealerCode = "";
            $dealerAddress = "";
    }
                
@endphp

@section('custom_css')
    <style type="text/css">
        .table th{
            background: #00c292;
            text-align: center;
        }
    </style>
@endsection

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label for="issue-type">Issue Type</label>
                <div class="form-group">
                    <input type="text" class="form-control" name="issueType" value="{{ $productIssue->issue_type }}" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <label for="issue-no">Issue No</label>
                <div class="form-group {{ $errors->has('productIssueNo') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control" name="productIssueNo" value="{{ $productIssue->issue_no }}" required readonly>
                    @if ($errors->has('productIssueNo'))
                        @foreach($errors->get('productIssueNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <label for="issue-date">Issue Date</label>
                <div class="form-group">
                    <input type="text" class="form-control" name="issueDate" value="{{ date('d-m-Y', strtotime($productIssue->date)) }}" readonly>
                   
                </div>
            </div>
        </div>

        

        <div class="row">
            <div class="col-md-4">
                <label for="dealer">Requisitions No</label>
                <div class="form-group">
                    <input type="text" class="form-control" name="issueType" value="{{ $productIssue->requisitionNo }}" readonly="">
                </div>
            </div>

            <div class="col-md-4">
                <label for="dealer-code">Dealer Code</label>
                <div class="form-group">
                    <input type="text" class="form-control" id="dealerCode" name="dealerCode" value="{{$dealerCode }}" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <label for="dealer-name">Dealer Name</label>
                <div class="form-group">
                    <input type="text" class="form-control" id="dealerName" name="dealerName" value="{{ $dealerName }}" readonly>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label for="dealer-address">Dealer Address</label>
                <div class="form-group">
                    <textarea class="form-control" id="dealerAddress" name="dealerAddress" rows="5">{{ $dealerAddress }}</textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label></label>
                <div class="form-group">
                    <table class="table table-bordered table-striped table-sm gridTable issueProductList" >
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="150px">Serial</th>
                                <th width="40px">Qty</th>
                                <th width="80px">Price</th>
                                <th width="110px">Commision (%)</th>
                                <th width="80px">Promo</th>
                                <th width="80px">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                       
                            <tr>                
                                <td>
                                    
                                    <input  type="text" name="productName[]" value="{{ $productName }}" readonly>
                                </td>
                                <td>
                                    <input  type="text" name="productModel[]" value="{{ $productModel }}" readonly>
                                </td>

                                 <td>
                                    <input style="text-align: right;"  type="number" name="productQty[]" value="{{ $sale->serial_no }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"  type="number" name="productQty[]" value="{{ $sale->qty }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"  type="number" name="productPrice[]" value="{{ $sale->price }}" required readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"  type="number" name="commission[]" value="{{ $sale->commission_rate }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;"  type="number" name="offer[]" value="{{ $sale->offer}}" readonly>
                                </td>
                                
                                <td>
                                    <input style="text-align: right;"  type="number" name="amount[]" value="{{ $sale->amount }}" readonly>
                                </td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        function itemRemove(i)
        {            
            var issueQty = parseInt($('.issueQty_'+i).val());
            issueQty = issueQty - 1;
            $('.issueQty_'+i).val(issueQty);

            var productPrice = parseFloat($('.productPrice_'+i).val());

            var totalQty = parseInt($('.totalQty').val());
            totalQty = totalQty - 1;
            $('.totalQty').val(totalQty);

            var totalAmount = parseInt($('.totalAmount').val());
            totalAmount = totalAmount - productPrice;
            $('.totalAmount').val(totalAmount);

            $('.issueProductRow_'+i).remove();
        }

        function findTotalAmount(i)
        {
            var rate = parseFloat($('.productPrice_'+i).val());
            var qty = parseFloat($('.productQty_'+i).val());
            var amount = rate * qty;

            $('.amount_'+i).val(Math.round(amount));

            rowSum();
        }

        function rowSum()
        {
            var totalQty = 0;            
            var totalAmount = 0;            
            $(".productQty").each(function () {
                var stvalTotal = parseFloat($(this).val());
                // console.log(stval);
                totalQty += isNaN(stvalTotal) ? 0 : stvalTotal;
            });

            $(".amount").each(function () {
                var stvalAmount = parseFloat($(this).val());
                // console.log(stval);
                totalAmount += isNaN(stvalAmount) ? 0 : stvalAmount;
            });

            $('.totalQty').val(totalQty);
            $('.totalAmount').val(Math.round(totalAmount));
        }          
    </script>

@endsection