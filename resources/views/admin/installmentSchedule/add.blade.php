@extends('admin.layouts.master')

@section('content')
    @php
        use App\Product;
        use App\Installment;
        use App\CustomerRegistrationSetup;
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
                         <button type="submit" class="btn btn-outline-info btn-lg waves-effect saveButton"><i class="fa fa-save"></i> {{ $buttonName }}</button>

                        <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <input type="hidden" value="print" name="print">
                <div class="row">
                    <div class="col-md-6">
                        <label for="customerProductId">Customer & Product Name</label>
                        <div class="form-group {{ $errors->has('customerProductId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select product" name="customerProductId" data-placeholder="Select One" required="">
                                <option value="">Select One</option>
                                @foreach ($customerProducts as $customerProduct)
                                    @php
                                        $product = Product::where('id',$customerProduct->product_id)->first();
                                        $customer = CustomerRegistrationSetup::where('id',$customerProduct->customer_id)->first();
                                        $optionName = $customer->name.' / '.$product->name.'('.$product->code.')';
                                        $installment = Installment::where('customer_product_id',$customerProduct->id)->first();
                                    @endphp
                                    @if (!@$installment)
                                        <option value="{{ $customerProduct->retail_sales_id }}">{{ $optionName }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('customerProductId'))
                                @foreach($errors->get('customerProductId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Invoice No</label>
                        <div class="form-group {{ $errors->has('invoiceNo') ? ' has-danger' : '' }}">
                            <input type="hidden" name="customerName" readonly="" class="form-control customerName">
                            <input type="text" name="invoiceNo" readonly="" class="form-control invoiceNo">
                            @if ($errors->has('invoiceNo'))
                                @foreach($errors->get('invoiceNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Collector</label>
                        <div class="form-group {{ $errors->has('installmentCollectorId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select" name="installmentCollectorId" required="">
                                <option value="">Select Collector</option>
                                @foreach ($collectorList as $collector)
                                    <option value="{{$collector->id}}">{{$collector->name}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('installmentCollectorId'))
                                @foreach($errors->get('installmentCollectorId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Product Amount</label>
                                <div class="form-group {{ $errors->has('productAmount') ? ' has-danger' : '' }}">
                                    <input type="text" name="productAmount" readonly="" class="form-control productAmount">
                                    @if ($errors->has('productAmount'))
                                        @foreach($errors->get('productAmount') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>Booking Amount</label>
                                <div class="form-group {{ $errors->has('bookingAmount') ? ' has-danger' : '' }}">
                                    <input type="hidden" readonly="" class="form-control bookingDate">
                                    <input type="text" name="bookingAmount" readonly="" class="form-control bookingAmount">
                                    @if ($errors->has('bookingAmount'))
                                        @foreach($errors->get('bookingAmount') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Installment Qty</label>
                                <div class="form-group {{ $errors->has('installmentQty') ? ' has-danger' : '' }}">
                                    <input type="text" name="installmentQty" readonly="" class="form-control installmentQty">
                                    @if ($errors->has('installmentQty'))
                                        @foreach($errors->get('installmentQty') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label>Installment Amount</label>
                                <div class="form-group {{ $errors->has('installmentAmount') ? ' has-danger' : '' }}">
                                    <input type="text" name="installmentAmount" readonly="" class="form-control installmentAmount">
                                    @if ($errors->has('installmentAmount'))
                                        @foreach($errors->get('installmentAmount') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label>Schedule Date</label>
                                <div class="form-group">
                                    <input type="text" readonly="" class="form-control installmentDatepicker installmentScheduleDate">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <label for=""></label>
                        <input type="hidden" class="row_count" value="0">
                        <a href="javascript:void(0)" class="btn btn-info btn-md createSchedule"><i class="fa fa-plus"></i> Create Schedule</a>
                    </div>
                </div>
            </div> 
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <h4 class="card-title text-center">Schedule List</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                        <label for=""></label>
                        <div class="form-group">
                            <table class="table table-striped gridTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="30px">SL</th>
                                        <th class="text-center">Invoice No</th>
                                        <th class="text-center">Schedule Date</th>
                                        <th class="text-center">Installment Amount</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="tbody">
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect saveButton"><i class="fa fa-save"></i> {{ $buttonName }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
 
@endsection

@section('custom-js')
    <script type="text/javascript">
    /*code for product info*/
        $('.saveButton').hide();
        $(document).on('change', '.product', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var customerProductId = $('.product option:selected').val();
            if(customerProductId != ''){
                $.ajax({
                    type:'post',
                    url:'{{ route('installmentSchedule.getCustomerProductInfo') }}',
                    data:{customerProductId:customerProductId},
                    success:function(data){
                        var customerProduct = data.customerProduct;
                        var customer = data.customer;
                        var invoice = data.invoice;
                        $('.invoiceNo').val(invoice.invoice_no);
                        $('.customerName').val(customer.name);
                        $('.productAmount').val(invoice.customer_product_price);
                        $('.bookingAmount').val(customerProduct.deposite);
                        $('.bookingDate').val(customerProduct.purchase_date);
                        $('.installmentAmount').val(customerProduct.monthly_installment_amount);
                        $('.installmentQty').val(customerProduct.total_installment);
                    }
                });
            }
        });

    /*end code for product info*/
    </script>

   <script type="text/javascript">
        $(".createSchedule").click(function () {
            var product = $('.product').val();
            var scheduleDate = $('.installmentScheduleDate').val();

            if(product != '' && scheduleDate != '')
            {
                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1; 

                var invoiceNo = $('.invoiceNo').val();
                var installmentQty = $('.installmentQty').val();

                if(total == 1)
                {
                     var installmentAmount = $('.bookingAmount').val();
                     var scheduleDate = DateFormate($('.bookingDate').val());
                }
                else
                {
                   var installmentAmount = $('.installmentAmount').val(); 
                }

                $(".gridTable tbody").append('<tr id="itemRow_' + total + '">' +
                    '<td><input type="text" class="text-center" value="'+total+'" readonly></td>'+
                    '<td><input style="text-align: center;" type="text" value="'+invoiceNo+'" readonly></td>'+
                    '<td><input style="text-align: center;" type="text" name="installmentScheduleDate[]" class="installmentScheduleDate_'+total+'" value="'+scheduleDate+'" required readonly></td>'+
                    '<td><input style="text-align: center;" type="text" name="installmentScheduleAmount[]" value="'+installmentAmount+'" readonly></td>'+
                    '<td align="center"><span class="btn btn-danger item_remove" onclick="itemRemove(' + total + ')"><i class="fa fa-trash"></i> Delete</span></td>'+
                    '</tr>');
                $('.row_count').val(total);
                var installmentScheduleDate = $('.installmentScheduleDate_'+total).val();
                $('.installmentScheduleDate').val(NextScheduleDate(installmentScheduleDate));
                TotalInstallmetnQty(total);
            }
            else
            {
                if(product == '')
                {
                    swal("Please! Select Product", "", "warning");
                }
                else
                {
                    if(scheduleDate == '')
                    {
                        swal("Please! Select Schedule Date", "", "warning");  
                    }
                }
            }
        });
    

        function itemRemove(total)
        {
            var total_row_value = total-1;
            $('.row_count').val(total_row_value);
            $("#itemRow_" + total).remove();
            TotalInstallmetnQty(total)
        }

        function TotalInstallmetnQty(total)
        {
            var installmentQty = parseInt($('.installmentQty').val())+parseInt(1);
            var finalTotal = parseInt(total)+parseInt(1);
            if(total == installmentQty)
            {
                $('.createSchedule').hide();
                $('.saveButton').show();
            }
            else
            {
               $('.createSchedule').show();
               $('.saveButton').hide(); 
            }
        }

        $( function() {
            $(".installmentDatepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd-M-yy',
            }).datepicker('setDate', 'today');
        });

        function NextScheduleDate(scheduleDate) {
            var d = new Date(scheduleDate);
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
              "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
            ];
            if(d.getMonth()+1 == 12)
            {
                var nextYear = d.getFullYear()+1;
            }
            else
            {
                var nextYear = d.getFullYear();
            }
            var datestring = ("0" + (d.getDate())).slice(-2)  + "-" + (monthNames[(d.getMonth()+1)%12]) + "-" + nextYear;
            return datestring;
        }


         function DateFormate(scheduleDate){
            var d = new Date(scheduleDate);
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
              "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
            ];

            var datestring = ("0" + (d.getDate())).slice(-2)  + "-" + (monthNames[d.getMonth()%12]) + "-" + d.getFullYear();
            return datestring;
        }

    </script>
@endsection
