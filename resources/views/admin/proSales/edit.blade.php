@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>
    @php
        $serialNo = "";     
        $productSerialNo = "";     
        use App\Lifting;
        use App\LiftingProduct;

        $purchaseBy = Auth::user()->name;

        $maxLifting = Lifting::max('serial_no');

        if (@$maxLifting)
        {
            $serialNo = 1000000 + 1;
        }
        else
        {
            $serialNo = 1000000 + 1;
        }

        $maxLiftingProduct = LiftingProduct::max('serial_no');

        if (@$maxLiftingProduct)
        {
            $productSerialNo = $maxLiftingProduct;
        }
        else
        {
            $productSerialNo = 1000000;
        }
    @endphp

    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <input class="form-control" type="hidden" name="liftingId" value="{{ $lifting->id }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <input class="form-control" type="hidden" id="productSerialNo" name="productSerialNo" value="{{ $productSerialNo }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">


                    <div class="col-md-6">
                       <label for="supplier">Requition by</label>
                        <div class="form-group {{ $errors->has('vendorId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select" name="vendorId" required="">
                                <option value=" ">Select Staff</option>
                                @foreach ($vendors as $vendor)
                                    @php
                                        if ($vendor->id == $lifting->requisitions_name_id)
                                        {
                                            $select = 'selected';
                                        }
                                        else
                                        {
                                            $select = '';
                                        }                                       
                                    @endphp
                                    <option value="{{$vendor->id}}" {{ $select }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('vendorId'))
                                @foreach($errors->get('vendorId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="sl-no">Reqution No</label>
                        <div class="form-group {{ $errors->has('serialNo') ? ' has-danger' : '' }}">
                            <input type="text" class="form-control" name="serialNo" value="{{ $serialNo }}" required readonly/>
                            @if ($errors->has('serialNo'))
                                @foreach($errors->get('serialNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                                <label for="submission-date">Requition Date</label>
                                <div class="form-group {{ $errors->has('submissionDate') ? ' has-danger' : '' }}">
                                    <input type="text" class="form-control add_datepicker" name="submissionDate" value="{{ old('submissionDate') }}" readonly>
                                    @if ($errors->has('submissionDate'))
                                        @foreach($errors->get('submissionDate') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                   

                    

                 

            
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <label for="supplier">Products</label>
                        <div class="form-group {{ $errors->has('product_d') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select" id="product" name="product_d">
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    @php
                                        if ($product->id == $lifting->product_id)
                                        {
                                            $select = "selected";
                                        }
                                        else
                                        {
                                            $select = "";
                                        }                                        
                                    @endphp


                                    <option value="{{$product->id}}"{{ $select }} >{{ $product->name }} ( {{ $product->code }} - {{ $product->color }} - {{ $product->model_no }}  )</option>
                                @endforeach
                            </select>

                            @if ($errors->has('product_d'))
                                @foreach($errors->get('product_d') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3" style="display: none;">
                        <label for="product-serial-no">Products Serial No.</label>
                        <div class="form-group {{ $errors->has('product_serial_no') ? ' has-danger' : '' }}">
                            <input  type="text" class="form-control" id="product_serial_no" name="product_serial_no" value="">
                            @if ($errors->has('product_serial_no'))
                                @foreach($errors->get('product_serial_no') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                     <div class="col-md-3">
                        <label for="qty">Products QTY</label>
                        <div class="form-group {{ $errors->has('qty') ? ' has-danger' : '' }}">
                            <input  type="number" class="form-control" id="qty" name="qty" value="1" min="1">
                            @if ($errors->has('qty'))
                                @foreach($errors->get('qty') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>

                <div class="row">

                     <div class="col-md-6">
                        <label for="supplier">Total Quantity</label>
                        <div class="form-group">
                            <input style="text-align: right;" class="form-control totalQty" type="number" name="totalQty" value="{{ $lifting->total_qty }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for=""></label>
                        <div class="form-group">
                            <input type="hidden" class="row_count" value="{{ count($liftingProducts) }}">
                            <span class="btn btn-outline-success add_item" style="width: 100%;">
                                <i class="fa fa-arrow-down"></i> Listing
                            </span>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <label for=""></label>
                        <div class="form-group">
                            <table class="table table-bordered table-striped gridTable" >
                                <thead>
                                    <tr>
                                        <th>Product Name & Code</th>
                                        <th width="160px">Model</th>
                                        <!-- <th width="100px">Color</th> -->
                                        <th width="160px">Serial No</th>
                                        <th width="80px">Price</th>
                                        <!-- <th width="90px">MRP Price</th> -->
                                        <!-- <th width="110px">Higher Price</th> -->
                                        <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach ($liftingProducts as $liftingProduct)
                                        @php
                                            $i++;
                                        @endphp
                                        <tr id="itemRow_{{ $i }}">                
                                            <td>
                                                <input class="productId_{{ $i }}" type="hidden" name="productId[]" value="{{ $liftingProduct->product_id }}">
                                                <input class="productName_{{ $i }}" type="text" name="productName[]" value="{{ $liftingProduct->productName }}" required readonly>
                                            </td>
                                            <td>
                                                <input class="productModel_{{ $i }}" type="text" name="productModel[]" value="{{ $liftingProduct->model_no }}" readonly>
                                            </td>
                                         
                                                <input class="productColor_{{ $i }}" type="hidden" name="productColor[]" value="{{ $liftingProduct->color }}" readonly>
                                         
                                            <td>
                                                <input class="productSerialNo_{{ $i }}" type="text" name="productSerialNo[]" value="{{ $liftingProduct->serial_no }}" required readonly>
                                            </td>

                                            <td>
                                                <input class="productQty productQty_{{ $i }}" type="hidden" name="productQty[]" value="{{ $liftingProduct->qty }}" required>
                                                <input style="text-align: right;" class="productPrice productPrice_{{ $i }}" type="number" name="productPrice[]" value="{{ $liftingProduct->price }}" oninput="findMrpHairePrice({{ $i }})" required readonly>
                                            </td>
                                            
                                            <td align="center">
                                                <span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove({{ $i }})" style="width: 100%;">
                                                    <i class="fa fa-trash"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">

        $(".add_item").click(function () {
            var productId = $("#product option:selected").val();
            var AddProductQty = parseInt($('#qty').val());
            // $('#qty').val(1);


            if (productId == "")
            {
                swal("Please! Select A Product", "", "warning");
            }
            else
            {



            for( var c = 0; c< AddProductQty; c++){

                                var row_count = $('.row_count').val();

                var total = parseInt(row_count);

                if (total > 400)
                {
                    swal("You Can't Lifting Product More Than 400", "", "warning");                    
                }
                else
                {
                    



                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ route('proSales.productInfo') }}",
                        data:{productId:productId},
                        success: function(response) {

                            if ($("#product_serial_no").val() == "")
                            {
                                var productSerialNo = parseInt($('#productSerialNo').val()) + 1;
                                var serialNo = productSerialNo;
                                $('#productSerialNo').val(productSerialNo);
                            }
                            else
                            {
                                var serialNo = $("#product_serial_no").val();                        
                            }

                             $(".gridTable tbody").append(
                                '<tr id="itemRow_' + total + '">' +                
                                    '<td>'+
                                        '<input class="productId_'+total+'" type="hidden" name="productId[]" value="">'+
                                        '<input class="productName_'+total+'" type="text" name="productName[]" value="" readonly>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input class="productModel_'+total+'" type="text" name="productModel[]" value="" readonly>'+
                                    '</td>'+
                                    '<td style="display: none">'+
                                        '<input class="productColor_'+total+'" type="hi" name="productColor[]" value="">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input class="productSerialNo_'+total+'" type="text" name="productSerialNo[]" value="" required>'+
                                    '</td>'+

                                    '<td>'+
                                        '<input class="productQty_'+total+'" type="hidden" name="productQty[]" value="1">'+
                                        '<input style="text-align: right;" class="productPrice productPrice_'+total+'" type="number" name="productPrice[]" value="" oninput="findMrpHairePrice('+total+')" required>'+
                                    '</td>'+
                                    '</td>'+
                                    '<td align="center">'+
                                        '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove('+total+')" style="width: 100%;">'+
                                            '<i class="fa fa-trash"></i>'+
                                        '</span>'+
                                    '</td>'+
                                '</tr>'
                            );

                            var product = response.product;

                            // console.log("in ajax " + serialNo);

                            $('.productId_'+total).val(product.id);
                            $('.productName_'+total).val(product.name);
                            $('.productModel_'+total).val(product.model_no);
                            $('.productColor_'+total).val(product.color);
                            $('.productSerialNo_'+total).val(serialNo);
                            $('.price_'+total).val(product.price);
                            $('.productPrice_'+total).val(product.price);

                            var totalQty = parseFloat($('.totalQty').val()) + parseFloat($('.productQty_'+total).val());
                        
                            $('.totalQty').val(totalQty);
                       

                            total++;
                            $('.row_count').val(total);

                        },
                        error: function(response) {

                        }
                    });

                    $('#product_serial_no').val('').focus();
                }
            }

            }
        });

        function findMrpHairePrice(i)
        {
            
                var price = parseFloat($(".productPrice_"+i).val());
            

      

            rowSum();
        }

        function rowSum()
        {
            var totalPrice = 0;            
                      
            $(".productPrice").each(function () {
                var price = parseFloat($(this).val());
                totalPrice += isNaN(price) ? 0 : price;
            });

       

            $('.totalPrice').val(totalPrice);
         
        }

        function itemRemove(i) {
            var totalQty = parseFloat($('.totalQty').val());
            var totalPrice = parseFloat($('.totalPrice').val());
         

            var quantity = parseFloat($('.productQty_'+i).val());
            var productPrice = parseFloat($('.productPrice_'+i).val());
          

            totalQty = totalQty - quantity;
         

            $('.totalQty').val(totalQty);
        

            $("#itemRow_" + i).remove();
            $('#product_serial_no').val('').focus();
        }          
    </script>

@endsection