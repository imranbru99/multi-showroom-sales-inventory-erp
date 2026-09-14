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
        use App\ProductionIssue;
        use App\ProductionIsuuList;


        $maxLifting = ProductionIssue::max('serial_no');

        if (@$maxLifting)
        {
            $serialNo = $maxLifting + 1;
        }
        else
        {
            $serialNo = 1000000 + 1;
        }

        $maxLiftingProduct = ProductionIsuuList::max('serial_no');

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
                <input class="form-control" type="hidden" id="productSerialNo" name="productSerialNo" value="{{ $productSerialNo }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                     <div id="productQtyStore"></div>

                <div class="col-md-2">
                    <div class="form-check-inline 
                    @if ($requisitionRequired)
                    d-none
                    @endif
                    ">
                    <label class="form-check-label">
                        <input type="checkbox" id="requisition_checkbox" onclick="toggleRequitionDropdown()"
                        class="form-check-input" checked> Requisition
                    </label>
                </div>
            </div>

            <div class="col-md-10">
                <div id="requisition">
                    <div class="form-group {{ $errors->has('dealerRequisitionId') ? ' has-danger' : '' }}">
                        <select class="form-control chosen-select requisition" id="requisitionDD" name="dealerRequisitionId"
                        @if ($requisitionRequired) required="" @endif>
                        <option value="">Select Requisition</option>

                        @foreach ($requisitons as $requisiton)
                        <option value="{{ $requisiton->id }}">{{ $requisiton->requisition_no }} -
                            {{ $requisiton->staff->name }}</option>
                            @endforeach

                        </select>
                        @if ($errors->has('dealerRequisitionId'))
                        @foreach($errors->get('dealerRequisitionId') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>


        </div>
                <div class="row">
                     <div class="col-md-6">
                        <label for="supplier">Requition By</label>
                        <div class="form-group {{ $errors->has('vendorId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select" name="vendorId" id="vendorId" required="">
                                <option value=" ">Select Staff</option>
                                @foreach ($staffs as $vendor)
                                    <option value="{{$vendor->id}}">{{ $vendor->name }}</option>
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
                        <label for="sl-no">Requition No</label>
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
                                <label for="submission-date">Requition  Date</label>
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
                                    <option value="{{$product->id}}">{{ $product->name }} ( {{ $product->code }} - {{ $product->color }} - {{ $product->model_no }}  )</option>
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
                            <input style="text-align: right;" class="form-control totalQty" type="number" name="totalQty" value="0" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for=""></label>
                        <div class="form-group">
	                        <input type="hidden" class="row_count" value="0">
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
                            <table class="table table-bordered table-striped gridTable">
                                
                                <thead>
                                    <tr>
                                        <th width="400px">Product Name & Code</th>
                                        <th width="160px">Model</th>
                                     {{--    <th width="100px">Color</th> --}}
                                        <th width="160px">Serial No</th>
                                        <th width="80px">Price</th>
                                        <th width="10px"><i class="fa fa-trash" style="color: white;"></i></th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
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
              var productQtyStore = parseInt($('.pqty').val());
            // $('#qty').val(1);
            if(AddProductQty > productQtyStore )
            {
                 swal("You Can't  Product More Than approval Quantity ", "", "warning");
            }
            else
            {


        	if (productId == "")
        	{
                swal("Please! Select A Product", "", "warning");
        	}
        	else
        	{



            for( var c = 0; c< AddProductQty; c++){

                var row_count = $('.row_count').val();
                            
                      

                var total = parseInt(row_count);
               

                if (total > 400 )
                {
                    swal("You Can't  Product More Than 400 Quantity", "", "warning");                    
                }
                else
                {
                    



                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ route('lifting.productInfo') }}",
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
                                        '<input class="productColor_'+total+'" type="text" name="productColor[]" value="">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input class="productSerialNo_'+total+'" type="text" name="productSerialNo[]" value="" required>'+
                                    '</td>'+

                                    '<td>'+
                                        '<input class="productQty_'+total+'" type="hidden" name="productQty[]" value="1">'+
                                        '<input style="text-align: right;" class="productPrice productPrice_'+total+'" type="number" name="productPrice[]" value="" oninput="findMrpHairePrice('+total+')" required>'+
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

        function toggleRequitionDropdown(){

    let showReqDropDown = $('#requisition_checkbox').is(":checked");

    if(showReqDropDown){

        $('#requisition').show();

    }else{

        $('#requisition').hide();

    }

}

toggleRequitionDropdown(); 

 $('#requisitionDD').change(function (e) { 
            e.preventDefault();

            let requisitionNo = $('#requisitionDD').val();

            if(requisitionNo == ''){
                return true;
            }

            $.ajax({
            
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('proSales.getRequisitionProduct') }}",
                data: {
                    requisitionNo: requisitionNo
                },
                success: function(response) {

                    // products dropdown start

                    $('#product').html('');

                    $('#product').append(`<option value="">Select Product</option>`);

                    let products = response.products;

                    for (const product in products) {
                        if (Object.hasOwnProperty.call(products, product)) {
                            const p = products[product];

                            let option = `<option value="${p.id}">${p.name} - ${p.model_no}</option>`;

                            $('#product').append(option);

                            $('#product').trigger("chosen:updated");
                            
                        }
                    }
                    // products dropdown end


                    // Dealer dropdown start

                    $('#vendorId').html('');

                    let dealer = response.dealer;

                    let option = `<option value="${dealer.id}" selected>${dealer.name}</option>`;

                    $('#vendorId').append(option);

                    $('#vendorId').trigger("chosen:updated");

                    // Dealer dropdown end

                    // requisition count store start

                    $('#productQtyStore').html('');

                    let reqp = response.requisition.requisitions;

                    for (const prod in reqp) {
                        if (Object.hasOwnProperty.call(reqp, prod)) {
                            const p = reqp[prod];

                            let input = `<input type="hidden" class="pqty" id="${p.product_id}" value="${p.approved_qty}">`;



                            $('#productQtyStore').append(input);

                        }
                    }

                    // requisition count store end

                },

                error: function(response) {
                
                }

                });

        });        
    </script>

@endsection