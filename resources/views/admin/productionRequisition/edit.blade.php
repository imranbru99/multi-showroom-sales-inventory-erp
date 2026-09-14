@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single{
            height: 35px !important;
        }
    </style>

    <div class="card-body">
         <div class="row">
            <div class="col-md-12">
                <input class="form-control" type="hidden" name="productionRequisitionId" value="{{ $productionRequisition->id }}">
            </div>
        </div>
        <div class="row">

            <div class="col-md-6">
                <label for="product">Products</label>
                <div class="form-group {{ $errors->has('product') ? ' has-danger' : '' }}">
                    <select class="form-control chosen-select product" id="product" name="product">


                        <option value="">Select Product</option>
                       @foreach ($products as $product)
                            @php
                                if ($product->id == $productionRequisition->product_id)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }                                
                            @endphp
                            <option value="{{ $product->id }}" {{ $select }}>{{ $product->name }} ( {{ $product->code }} - {{ $product->color }} - {{ $product->model_no }}  )</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label for="requisition-date">Requisition Quantitiy</label>
                <div class="form-group {{ $errors->has('productQty') ? ' has-danger' : '' }}">
                    <input type="number" class="form-control" id="productQty" name="productQty" value="{{ $productionRequisition->total_qty }}" >
                    @if ($errors->has('productQty'))
                        @foreach($errors->get('productQty') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <label for="requisition-no">Requisition No</label>
                <div class="form-group {{ $errors->has('requisitionNo') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control" name="requisitionNo" value="{{ $productionRequisition->requisition_no }}" required readonly/>
                    @if ($errors->has('requisitionNo'))
                        @foreach($errors->get('requisitionNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <label for="requisition-date">Requisition Date</label>
                <div class="form-group {{ $errors->has('requisitionDate') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control add_datepicker" name="requisitionDate" value="{{ $productionRequisition->date }}" readonly>
                    @if ($errors->has('requisitionDate'))
                        @foreach($errors->get('requisitionDate') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">

              <div class="col-md-6">
                  <div class="form-group {{ $errors->has('requisitionNameId') ? ' has-danger' : '' }}">
                    <label for="employee-id">Reqiuistion By</label>
                    <select class="form-control chosen-select employee" name="requisitionNameId">
                        <option value="">Select Name</option>
                        @foreach ($req_name as $req_nam)
                         @php
                                if ($req_nam->id == $productionRequisition->requisitions_name_id)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }                                
                            @endphp
                            <option value="{{ $req_nam->id }}"{{ $select }}>{{ $req_nam->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('requisitionNameId'))
                        @foreach($errors->get('requisitionNameId') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>

            </div>
            <div class="col-md-6">
                <label for=""></label>
                <div class="form-group">
                    <input type="hidden" class="row_count" value="0">
                    <span class="btn btn-outline-success add_item" style="width: 100%;">
                        <i class="fa fa-arrow-down"></i> Add Product
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
                                <th width="200px">Model</th>
                                <th width="80px">Qty</th>
                                <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody">

                              @php
                                $sl = 1;
                            @endphp
                            @foreach ($productionRequisitionInfo as $dealerRequisitionProduct)
                                <tr>                
                                    <td>
                                        <input class="productId_{{ $sl }}" type="hidden" name="productId[]" value="{{ $dealerRequisitionProduct->product_id }}">
                                        <input class="productName_{{ $sl }}" type="text" name="productName[]" value="{{ $dealerRequisitionProduct->product_name }}" >
                                    </td>
                                    <td>
                                        <input class="productModel_{{ $sl }}" type="text" name="productModel[]" value="{{ $dealerRequisitionProduct->model_no }}" >
                                    </td>
                                    <td>
                                        <input style="text-align: right;" class="productQty productQty_{{ $sl }}" type="number" name="productQty[]" value="{{ $dealerRequisitionProduct->qty }}" oninput="findTotalAmount('+total+')" >
                                    </td>

                                    <td align="center">
                                        <span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove({{ $sl }})" style="width: 100%;">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                   </td>
                                </tr>
                                @php
                                    $sl++;
                                @endphp
                            @endforeach



                        </tbody>

                        <tfoot>                                
                                <tr>
                                    <td colspan="2" align="right" ><p style="font-size: 14px; font-weight: bold; padding-right: 5px;">Total Quantity</p></td>
                                
                                    <td >
                                       <input style="text-align: right;" class="totalQty" type="number" name="totalQty" value="{{$productionRequisition->total_qty}}" readonly>
                                    </td>
                                </tr>

                               
                            </tfoot>
                       
                        
                    </table>
                </div>
            </div>
        </div>

        <!-- div class="row">
            <div class="col-md-6">
                <label for="total-qty">Total Quantity</label>
                <div class="form-group">
                	<input style="text-align: right;" class="form-control totalQty" type="text" name="totalQty" value="{{$productionRequisition->total_qty}} ">
                </div>
            </div>
        </div> -->
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(".add_item").click(function () {
            var productId = $("#product option:selected").val();

        	if (productId == "")
        	{
                swal("Please! Select A Product", "", "warning");
        	}
        	else
        	{

             var productQty = $("#productQty").val();

                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1;

	            $(".gridTable tbody").append(
	            	'<tr id="itemRow_' + total + '">' +                
		                '<td>'+
		                	'<input class="productId_'+total+'" type="hidden" name="productId[]" value="">'+
		                	'<input class="productName_'+total+'" type="text" name="productName[]" value="" readonly>'+
		                '</td>'+
		                '<td>'+
		                	'<input class="productModel_'+total+'" type="text" name="productModel[]" value="" readonly>'+
		                '</td>'+
		                '<td>'+
		                	'<input style="text-align: right;" class="productQty productQty_'+total+'" type="number" name="productQty[]" value="'+productQty+'" oninput="findTotalAmount('+total+')">'+
		                '</td>'+
		                '<td align="center">'+
		                	'<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove('+total+')" style="width: 100%;">'+
		                		'<i class="fa fa-trash"></i>'+
		                	'</span>'+
		                '</td>'+
	                '</tr>'
	            );
	            $('.row_count').val(total);
                 

	            $.ajax({
	                headers: {
	                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	                },
	                type: "POST",
	                url: "{{ route('productionRequisition.productInfo') }}",
	                data:{productId:productId},
	                success: function(response) {
	                    var product = response.product;

                        $('.productId_'+total).val(product.id);
                        $('.productName_'+total).val(product.name);
                        $('.productModel_'+total).val(product.model_no);
	                },
	                error: function(response) {

	                }
	            });
        	}  
            rowSum();          
        });

        function itemRemove(i) {
            var qty = parseInt($('.productQty_'+i).val());
            

            var totalQty = parseInt($('.totalQty').val()) - qty;
          

            $('.totalQty').val(totalQty);
           

            $('#itemRow_'+i).remove();
        }

        function findTotalAmount(i)
        {
            
            var qty = parseFloat($('.productQty_'+i).val());
           

            rowSum();
        }

        function rowSum()
        {
            var totalQty = 0;            
                       
            $(".productQty").each(function () {
                var stvalTotal = parseFloat($(this).val());
                // console.log(stval);
                totalQty += isNaN(stvalTotal) ? 0 : stvalTotal;
            });


            $('.totalQty').val(totalQty);
            $('#productQty').val(totalQty);
        }          
    </script>

@endsection