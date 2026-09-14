@extends('admin.layouts.masterAddEdit')
<?php

use App\Product;
?>
@section('custom_css')
<style type="text/css">
    .table th {
        background: #00c292;
        text-align: center;
    }

</style>
@endsection

@section('card_body')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }

</style>

<div class="card-body">
    <div class="row">
        <input type="hidden"  name="issue_no" value="{{ $singleSalesReturn->issue_no }}"> 

        <div class="col-md-4">
            <label for="dealer-address">Return Date</label>
            <div class="form-group">
                <input type="text" class="form-control datepicker" name="return_date" value="{{ date('d-m-Y', strtotime($singleSalesReturn->return_date)) }}">
            </div>
        </div>

        <div class="col-md-4">
            <label for="issue-no">Return No</label>
            <div class="form-group {{ $errors->has('productIssueNo') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="productIssueNo" value="{{ $singleSalesReturn->issue_no }}" required
                       readonly>
                @if ($errors->has('productIssueNo'))
                @foreach ($errors->get('productIssueNo') as $error)
                <div class="form-control-feedback">{{ $error }}</div>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <label for="dealer-code">Dealer Code</label>
            <div class="form-group">
                <input type="text" class="form-control" id="dealerCode" name="dealerCode"
                       value="{{ $dealer->code }}" readonly>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="dealer-name">Dealer Name</label>
            <div class="form-group">
                <input type="text" class="form-control" value="{{ $dealer->name }}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <label for="dealer-address">Dealer Address</label>
            <div class="form-group">
                <input class="form-control" id="dealerAddress" name="dealerAddress" rows="5"
                       value="{{ $dealer->address }}">
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12">
            <label for="dealer-address">Return Reason</label>
            <div class="form-group">
                <textarea class="form-control" id="reason" name="reason" rows="2">{{$singleSalesReturn->reason}}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label></label>
            <div class="form-group">
                <div class="table-responsive">
                    @if($singleSalesReturn->product_type == 'warranty_product')
                    <table class="table table-bordered table-striped table-sm issueProductList">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="150px">Serial</th>
                                <th width="40px">Qty</th>
                                <th width="80px">Price</th>
                                <th width="110px">Commision (%)</th>
                                <th width="80px">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">

                            @foreach ($allSalesReturn as $sale)
                            <?php
                            $product = Product::where('id', $sale->product_id)->first();
                            ?>
                            <tr>
                                <td>
                                    <input type="text" size="15" value="{{ $product->name }}" readonly>
                                </td>
                                <td>
                                    <input type="text" name="productModel" value="{{ $product->model_no }}"
                                           readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="text" name="productQty"
                                           value="{{ $sale->serial_no }}" size="15" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="productQty"
                                           value="{{ $sale->qty }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="productPrice"
                                           value="{{ $sale->price }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="commission"
                                           value="{{ $sale->commission_rate }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="amount"
                                           value="{{ $sale->amount }}" readonly>
                                </td>

                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <table class="table table-bordered table-striped table-sm issueProductList">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="40px">Qty</th>
                                <th width="80px">Price</th>
                                <th width="110px">Commision (%)</th>
                                <th width="80px">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">

                            @foreach ($allSalesReturn as $sale)
                            <?php
                            $product = Product::where('id', $sale->product_id)->first();
                            ?>
                            <tr>
                                <td>
                                    <input type="text" class="w-100" value="{{ $product->name }}" readonly>
                                    <input type="hidden" name="productModel" value="{{ $product->model_no }}"
                                           readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="productQty"
                                           value="{{ $sale->qty }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="productPrice"
                                           value="{{ $sale->price }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="commission"
                                           value="{{ $sale->commission_rate }}" readonly>
                                </td>

                                <td>
                                    <input style="text-align: right;" type="number" name="amount"
                                           value="{{ $sale->amount }}" readonly>
                                </td>

                            </tr>

                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
