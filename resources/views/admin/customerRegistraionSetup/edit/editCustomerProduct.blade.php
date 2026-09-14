@extends('admin.layouts.master')

@section('content')
    <style type="text/css">
        .blockTitle{
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }
    </style>

    <div style="padding-bottom: 10px;"></div>

    <form class="form-horizontal" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="customerProductId" value="{{$customerProduct->id}}">
        <input type="hidden" name="customerId" value="{{$customerProduct->customer_id}}">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-outline-info btn-lg" onclick="GoBack()">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-save"></i> {{ $buttonName }}</button>
                    </div>
                </div>
            </div>

            @php
                $purchaseDate =  date('d-m-Y',strtotime($customerProduct->purchase_date));
            @endphp
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Product Description</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('productId') ? ' has-danger' : '' }}">
                            <label for="productId">Product Name</label>
                            <select class="form-control chosen-select product" name="productId" data-placeholder="Select Product">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                @php
                                    if($product->id == $customerProduct->product_id){
                                        $selected = "selected";
                                    }else{
                                      $selected = "";  
                                    }
                                @endphp
                                  <option value="{{$product->id}}" {{$selected}}>{{$product->name}} ({{$product->code}})</option>
                                @endforeach
                            </select>
                            @if ($errors->has('productId'))
                                @foreach($errors->get('productId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('showroomName') ? ' has-danger' : '' }}">
                            <label for="showroom">Showroom</label>
                            <input type="text" class="form-control" name="showroomName" value="{{ $showroomName }}" required readonly="">
                            @if ($errors->has('showroomName'))
                                @foreach($errors->get('showroomName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('productModel') ? ' has-danger' : '' }}">
                            <label for="nick-name">Product Model</label>
                            <input type="text" class="form-control productModel" name="productModel" value="{{ $customerProduct->product_model }}" required readonly="">
                            @if ($errors->has('productModel'))
                                @foreach($errors->get('productModel') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group {{ $errors->has('warranty') ? ' has-danger' : '' }}">
                            <label for="warranty">Warranty</label>
                            <input type="number" name="warranty" class="form-control warranty" value="{{$customerProduct->warranty}}" readonly>
                            @if ($errors->has('warranty'))
                                @foreach($errors->get('warranty') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('purchaseDate') ? ' has-danger' : '' }}">
                            <label for="purchase-date">Purchase Date</label>
                            <input type="text" name="purchaseDate" class="form-control datepicker" value="{{ $purchaseDate }}" readonly="">
                            @if ($errors->has('purchaseDate'))
                                @foreach($errors->get('purchaseDate') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
        
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('remarks') ? ' has-danger' : '' }}">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" class="form-control" value="{{ $customerProduct->remarks }}">
                            @if ($errors->has('remarks'))
                                @foreach($errors->get('remarks') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('referenceId') ? ' has-danger' : '' }}">
                            <label for="showroom-id">Reference</label>
                            <select class="form-control chosen-select" name="referenceId">
                                <option value="">Select Reference Staff</option>
                                @foreach ($staffs as $staff)
                                    @php
                                    if($staff->id == $customerProduct->reference_id)
                                    {
                                        $select = "selected";
                                    }
                                    else
                                    {
                                        $select = "";  
                                    }
                                    @endphp
                                    <option value="{{$staff->id}}" {{ $select }}>{{$staff->name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('referenceId'))
                                @foreach($errors->get('referenceId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('cashPrice') ? ' has-danger' : '' }}">
                            <label for="cash-price">Cash Price</label>
                            <input type="number" name="cashPrice" class="form-control cashPrice" value="{{ $customerProduct->cash_price }}">
                            @if ($errors->has('cashPrice'))
                                @foreach($errors->get('cashPrice') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label for="purchase-type">Purchase Type</label>
                         <div class="form-group {{ $errors->has('purchaseType') ? ' has-danger' : '' }}">
                             <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="Cash" name="purchaseType" class="product purchaseType" {{ $customerProduct->purchase_type == "Cash" ? "checked" : ""}} required> Cash
                                </label>
                            </div>

                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="Short Installment" name="purchaseType" class="product purchaseType" {{ $customerProduct->purchase_type == "Short Installment" ? "checked" : ""}}> Short Installment
                                </label>
                            </div>

                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="Long Installment" name="purchaseType" class="product purchaseType" {{ $customerProduct->purchase_type == "Long Installment" ? "checked" : ""}}> Long Installment
                                </label>
                            </div>
                            @if ($errors->has('purchaseType'))
                                @foreach($errors->get('purchaseType') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3 installmentType">
                        <div class="form-group {{ $errors->has('installmentType') ? ' has-danger' : '' }}">
                            <label for="installment-type">Installment Type</label>
                            @php
                                $installmentTypes = array('Daily'=>'Daily','Weekly'=>'Weekly','Bi-Monthlyt'=>'Bi-Monthly','Monthly'=>'Monthly')
                            @endphp
                            <select class="form-control" name="installmentType">
                                <option value="">Select Installment Type</option>
                                @foreach ($installmentTypes as $key => $value)
                                    @php
                                        if ($key == $customerProduct->installment_type)
                                        {
                                            $select= "selected";
                                        }
                                        else
                                        {
                                            $select= "";
                                        }
                                    @endphp
                                    <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('installmentType'))
                                @foreach($errors->get('installmentType') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row shortInstallmentRow">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('shortInstallmentDeposite') ? ' has-danger' : '' }}">
                                    <label for="deposite">Deposite</label>
                                    <input type="number" class="form-control shortInstallmentDeposite" name="shortInstallmentDeposite" value="{{ $customerProduct->deposite }}">
                                    @if ($errors->has('shortInstallmentDeposite'))
                                        @foreach($errors->get('shortInstallmentDeposite') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('shortInstallmentPrice') ? ' has-danger' : '' }}">
                                    <label for="mrp-price">MRP Price</label>
                                    <input type="number" class="form-control shortInstallmentPrice" name="shortInstallmentPrice" value="{{ $customerProduct->mrp_price }}">
                                    @if ($errors->has('shortInstallmentPrice'))
                                        @foreach($errors->get('shortInstallmentPrice') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                           <div class="col-md-6">
                                <div class="form-group {{ $errors->has('shortTotalInstallment') ? ' has-danger' : '' }}">
                                    <label for="total-installment">Total Installment</label>
                                    <input type="text" class="form-control shortTotalInstallment" name="shortTotalInstallment" value="{{ $customerProduct->total_installment }}" oninput="calculateShortInstallmentAmount()">
                                    @if ($errors->has('shortTotalInstallment'))
                                        @foreach($errors->get('shortTotalInstallment') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('shortInstallmentAmount') ? ' has-danger' : '' }}">
                                    <label for="monthly-installment-amount">Installment Amount</label>
                                    <input type="number" class="form-control shortInstallmentAmount" name="shortInstallmentAmount" value="{{ $customerProduct->monthly_installment_amount }}">
                                    @if ($errors->has('shortInstallmentAmount'))
                                        @foreach($errors->get('shortInstallmentAmount') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row longInstallmentRow">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('longInstallmentDeposite') ? ' has-danger' : '' }}">
                                    <label for="deposite">Deposite</label>
                                    <input type="number" class="form-control longInstallmentDeposite" name="longInstallmentDeposite" value="{{ $customerProduct->deposite }}">
                                    @if ($errors->has('longInstallmentDeposite'))
                                        @foreach($errors->get('longInstallmentDeposite') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('longInstallmentPrice') ? ' has-danger' : '' }}">
                                    <label for="higher-price">Higher Price</label>
                                    <input type="number" class="form-control longInstallmentPrice" name="longInstallmentPrice" value="{{ $customerProduct->installment_price }}">
                                    @if ($errors->has('longInstallmentPrice'))
                                        @foreach($errors->get('longInstallmentPrice') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                           <div class="col-md-6">
                                <div class="form-group {{ $errors->has('longTotalInstallment') ? ' has-danger' : '' }}">
                                    <label for="total-installment">Total Installment</label>
                                    <input type="text" class="form-control longTotalInstallment" name="longTotalInstallment" value="{{ $customerProduct->total_installment }}" oninput="calculateLongInstallmentAmount()">
                                    @if ($errors->has('longTotalInstallment'))
                                        @foreach($errors->get('longTotalInstallment') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('longInstallmentAmount') ? ' has-danger' : '' }}">
                                    <label for="monthly-installment-amount">Installment Amount</label>
                                    <input type="number" class="form-control longInstallmentAmount" name="longInstallmentAmount" value="{{ $customerProduct->monthly_installment_amount }}">
                                    @if ($errors->has('longInstallmentAmount'))
                                        @foreach($errors->get('longInstallmentAmount') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('productUsageAddress') ? ' has-danger' : '' }}">
                            <label for="product-usage-address">Product Usage Address</label>
                            <textarea name="productUsageAddress" class="form-control" rows="5">{{ $customerProduct->product_usage_address }}</textarea>
                            @if ($errors->has('productUsageAddress'))
                                @foreach($errors->get('productUsageAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-save"></i> {{ $buttonName }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('custom-js')
    <script type="text/javascript">

        // start code installment

        $('.installmentType').hide();
        $('.longInstallmentRow').hide();
        $('.shortInstallmentRow').hide();

         $('.purchaseType').click(function(event) {
            var purchaseType =  $("input[name='purchaseType']:checked").val();

            if(purchaseType == "Cash")
            {
                $('.installmentType').hide();
                $("select[name='installmentType']").prop('required',false);
            }

            if(purchaseType == "Short Installment")
            {
                $('.shortInstallmentRow').show();
                $('.installmentType').show();
                $("input[name='shortInstallmentDeposite']").prop('required',true);
                $("input[name='shortInstallmentPrice']").prop('required',true);
                $("input[name='shortTotalInstallment']").prop('required',true);
                $("input[name='shortInstallmentAmount']").prop('required',true);
                $("select[name='installmentType']").prop('required',true);
            }
            else
            {
                $('.shortInstallmentRow').hide();
                $("input[name='shortInstallmentDeposite']").prop('required',false);
                $("input[name='shortInstallmentPrice']").prop('required',false);
                $("input[name='shortTotalInstallment']").prop('required',false);
                $("input[name='shortInstallmentAmount']").prop('required',false);
            }

            if(purchaseType == "Long Installment")
            {
                $('.longInstallmentRow').show();
                $('.installmentType').show();
                $("input[name='longInstallmentDeposite']").prop('required',true);
                $("input[name='longInstallmentPrice']").prop('required',true);
                $("input[name='longTotalInstallment']").prop('required',true);
                $("input[name='longInstallmentAmount']").prop('required',true);
                $("select[name='installmentType']").prop('required',true);
            }
            else
            {
                $('.longInstallmentRow').hide();
                $("input[name='longInstallmentDeposite']").prop('required',false);
                $("input[name='longInstallmentPrice']").prop('required',false);
                $("input[name='longTotalInstallment']").prop('required',false);
                $("input[name='longInstallmentAmount']").prop('required',false);
            }
        })

        /*end code for installment*/

        /*code for product info*/

            $(document).on('change', '.product', function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                var productId = $('.product option:selected').val();
                if(productId != '')
                {
                    $.ajax({
                        type:'post',
                        url:'{{ route('customerRegistration.getProductInfo') }}',
                        data:{productId:productId},
                        success:function(data){
                            var product = data.product;
                            var purchaseType =  $("input[name='purchaseType']:checked").val();
                            $('.productModel').val(product.model_no);
                            $('.cashPrice').val(product.price);
                            $('.warranty').val(product.warranty);

                            if (purchaseType == 'Short Installment')
                            {
                                $('.shortInstallmentPrice').val(product.mrp_price);
                            }
                            else
                            {
                                $('.shortInstallmentPrice').val('');                             
                            }

                            if (purchaseType == 'Long Installment')
                            {
                                $('.longInstallmentPrice').val(product.haire_price);
                            }
                            else
                            {
                                $('.longInstallmentPrice').val('');                             
                            }
                            calculateShortInstallmentAmount();
                            calculateLongInstallmentAmount();
                        }
                    });
                }
                else
                {
                   $('.productModel').val('');
                   $('.cashPrice').val('');  
                   $('.warranty').val(''); 
                   $('.longInstallmentPrice').val(''); 
                   $('.shortInstallmentPrice').val(''); 
                }
            });

            function calculateShortInstallmentAmount()
            {
                var shortInstallmentDeposite = parseFloat($('.shortInstallmentDeposite').val());
                var shortInstallmentPrice = parseFloat($('.shortInstallmentPrice').val());
                var shortTotalInstallment = parseFloat($('.shortTotalInstallment').val());

                if (shortTotalInstallment == 0 || $('.shortTotalInstallment').val() == "")
                {
                    var shortInstallmentAmount = (shortInstallmentPrice - shortInstallmentDeposite);
                }
                else
                {
                    var shortInstallmentAmount = (shortInstallmentPrice - shortInstallmentDeposite)/shortTotalInstallment;                
                }

                $('.shortInstallmentAmount').val(Math.round(shortInstallmentAmount));
            }

            function calculateLongInstallmentAmount()
            {
                var longInstallmentDeposite = parseFloat($('.longInstallmentDeposite').val());
                var longInstallmentPrice = parseFloat($('.longInstallmentPrice').val());
                var longTotalInstallment = parseFloat($('.longTotalInstallment').val());

                if (longTotalInstallment == 0 || $('.longTotalInstallment').val() == "")
                {
                    var longInstallmentAmount = (longInstallmentPrice - longInstallmentDeposite);
                }
                else
                {
                    var longInstallmentAmount = (longInstallmentPrice - longInstallmentDeposite)/longTotalInstallment;                
                }

                $('.longInstallmentAmount').val(Math.round(longInstallmentAmount));
            }

        /*end code for product info*/

        function monthlyInstallment()
        {
            var deposite = parseFloat($('.deposite').val());
            var installmentPrice = parseFloat($('.installmentPrice').val());
            var totalInstallment = parseFloat($('.totalInstallment').val());

            if (totalInstallment == 0 || $('.totalInstallment').val() == "")
            {
                var monthlyInstallmentPrice = (installmentPrice - deposite);
            }
            else
            {
                var monthlyInstallmentPrice = (installmentPrice - deposite)/totalInstallment;                
            }

            $('.monthlyInstallmentAmount').val(Math.round(monthlyInstallmentPrice));
        }
    </script>

@endsection