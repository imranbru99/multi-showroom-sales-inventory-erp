@extends('admin.layouts.master')

@section('content')
    @php
        use App\Product;
        use App\CustomerRegistrationSetup;
        use App\ShowroomSetup;
    @endphp
    <style type="text/css">
        .blockTitle span{
            font-weight: bold;
        }
    </style>
    <div style="padding-bottom: 10px;"></div>

    @php
        $message = Session::get('msg');
    @endphp

    @if (isset($message))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> {{ $message }}
        </div>
    @endif

    @php
        Session::forget('msg');
    @endphp

    @if( count($errors) > 0 )
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Oops!</strong> {{ $errors->first() }}
        </div>
    @endif

    <form class="form-horizontal" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-save"></i> {{ $buttonName }}</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <input type="hidden" value="print" name="print">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from-date">Invoice Date</label>
                            <input type="text" class="form-control add_datepicker" name="invoiceDate" placeholder="Select Date From">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from-date">Invoice No</label>
                            <input type="text" class="form-control" name="invoiceNo" placeholder="Invoice No" value="{{ $invoiceNo }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer-product-name">Customer</label>
                            <select class="form-control chosen-select customerId" name="customerId" id="customerId">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sold-out-products">Sold Out Products</label>
                            <div class="form-group" id="prodct-select-menu">
                                <select class="form-control chosen-select retailSalesId" name="retailSalesId" id="retailSalesId">
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer-product-serial">Product Serial</label>
                            <div class="form-group" id="prodct-serial-select-menu">
                                <select class="form-control chosen-select productSerial" name="productSerial" id="productSerial">
                                    <option value="">Select Product Serial</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for=""></label>
                        <div class="form-group">
                            <input type="hidden" class="row_count" value="0">
                            <span class="btn btn-outline-success add_item" style="width: 100%;">
                                <i class="fa fa-plus-circle"></i> Add More
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
                                        <th>Product Name</th>
                                        <th>Product Serial</th>
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
    </form>
 
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).on('change', '#customerId', function(){                
            var customerId = $('#customerId').val();

            $('.productSerial').empty().append('<option value="">Select Product Serial</option>').trigger('chosen:updated');

            if (customerId == "")
            {
                $('.retailSalesId').empty().append('<option value="">Select Product</option>').trigger('chosen:updated');
            }
            else
            {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type:'post',
                    url:'{{ route('invoiceSetup.getAllProduct') }}',
                    data:{customerId:customerId},
                    success:function(data){
                        $('#prodct-select-menu').html(data);
                        $(".chosen-select").chosen();

                        var retailSalesId = $('#retailSalesId').val();

                        if (retailSalesId)
                        {
                            getAllProductSerial(retailSalesId);
                        }
                    }
                });
            }
        });

        $(document).on('change', '#retailSalesId', function(){                
            var retailSalesId = $('#retailSalesId').val();

            if (retailSalesId == "")
            {
                $('.productSerial').empty().append('<option value="">Select Product Serial</option>').trigger('chosen:updated');
            }
            else
            {
                getAllProductSerial(retailSalesId);
            }
        });

        function getAllProductSerial(retailSalesId)
        {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                url:'{{ route('invoiceSetup.getAllProductSerial') }}',
                data:{retailSalesId:retailSalesId},
                success:function(data){
                    $('#prodct-serial-select-menu').html(data);
                    $(".chosen-select").chosen();
                }
            });
        }

        $(".add_item").click(function () {
            var retailSalesId = $(".retailSalesId option:selected").val();
            var productName = $(".retailSalesId option:selected").text();
            var productSerial = $(".productSerial option:selected").val();
            var customerId = $('#customerId').val();
            var customerName = $(".customerId option:selected").text();

            if (retailSalesId == "" || productSerial == "")
            {
                swal("Please! Select A Product And Product Serial", "", "warning");
            }
            else
            {
                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1;

                if (total > 400)
                {
                    swal("You Can't Selecte Product More Than 400", "", "warning");                    
                }
                else
                {
                    $(".gridTable tbody").append(
                        '<tr id="itemRow_' + total + '">' +
                            '<td>'+
                                '<input class="retailSalesIdArray_'+total+'" type="hidden" name="retailSalesIdArray[]" value="'+retailSalesId+'">'+
                                '<input class="productName_'+total+'" type="text" name="productName[]" value="'+productName+'" readonly>'+
                            '</td>'+
                            '<td>'+
                                '<input class="customerId_'+total+'" type="hidden" name="customerId[]" value="'+customerId+'">'+
                                '<input class="customerName_'+total+'" type="hidden" name="customerName[]" value="'+customerName+'">'+
                                '<input class="productSerialArray_'+total+'" type="text" name="productSerialArray[]" value="'+productSerial+'" readonly>'+
                            '</td>'+
                            '<td align="center">'+
                                '<span class="btn btn-outline-danger btn-sm item_remove" onclick="itemRemove('+total+')" style="width: 100%;">'+
                                    '<i class="fa fa-trash"></i>'+
                                '</span>'+
                            '</td>'+
                        '</tr>'
                    );
                    $('.row_count').val(total);

                    $('.retailSalesId option[value='+retailSalesId+']').remove();
                    $('.retailSalesId').trigger('chosen:updated');
                    $('.productSerial').empty().append('<option value="">Select Product Serial</option>').trigger('chosen:updated');
                    $('.row_count').val(total);
                        
                    var retailSalesId = $('#retailSalesId').val();

                    if (retailSalesId)
                    {
                        getAllProductSerial(retailSalesId);
                    }

                    if ($('.retailSalesId').has('option').length == 0)
                    {
                        var customerId = $('#customerId').val();

                        $('#customerId option[value='+customerId+']').remove();
                        $('#customerId').trigger('chosen:updated');
                    }

                    // $('#product_serial_no').val('').focus();
                }
            }
        });

        function itemRemove(i) {
            var retailSalesId = $(".retailSalesIdArray_"+i).val();
            var productName = $(".productName_"+i).val();
            var customerId = $(".customerId_"+i).val();
            var customerName = $(".customerName_"+i).val();
            var selectedCustomerId = $('#customerId').val();

            $('.retailSalesId').prepend(
                '<option value="'+retailSalesId+'" selected>'+productName+'</option>'
            );
            
            $('.retailSalesId').trigger('chosen:updated');
                        
            var selectedRetailSalesId = $('#retailSalesId').val();

            if (selectedRetailSalesId)
            {
                getAllProductSerial(selectedRetailSalesId);
            }

            if ($('.retailSalesId').has('option').length > 0)
            {
                if (selectedCustomerId != customerId)
                {
                    $('#customerId').append(
                        '<option value="'+customerId+'" selected>'+customerName+'</option>'
                    );
                    
                    $('#customerId').trigger('chosen:updated');

                }
            }

            $("#itemRow_" + i).remove();
            // $('#product_serial_no').val('').focus();
        }
    </script>
@endsection
