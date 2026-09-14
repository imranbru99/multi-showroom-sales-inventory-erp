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
                    <div class="col-md-8">
                        <label for="customerProductId">Customer & Product Name</label>
                        <div class="form-group {{ $errors->has('customerProductId') ? ' has-danger' : '' }}">
                            <select class="form-control chosen-select product" name="customerProductId" data-placeholder="Select One" required="">
                                <option value="">Select One</option>
                                @foreach ($customerProducts as $customerProduct)
                                    @php
                                        $product = Product::where('id',$customerProduct->product_id)->first();
                                        $customer = CustomerRegistrationSetup::where('id',$customerProduct->customer_id)->first();
                                        $optionName = $customer->name.' / '.$product->name.'('.$product->code.')';
                                       /* $installment = Installment::where('customer_product_id',$customerProduct->id)->first();
                                        if(!@$installment){*/
                                    @endphp
                                  {{-- <option value="{{$customerProduct->id}}">{{$optionName}}</option> --}}
                                  <option value="{{$customerProduct->customer_product_id}}">{{$optionName}}</option>
                                  @php
                                      //}
                                  @endphp
                                @endforeach
                            </select>
                            @if ($errors->has('customerProductId'))
                                @foreach($errors->get('customerProductId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
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

                            <div class="col-md-4">
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

                            <div class="col-md-4">
                                <label>Collection Date</label>
                                <div class="form-group {{ $errors->has('installmentCollectionDate') ? ' has-danger' : '' }}">
                                    <input type="text" name="installmentCollectionDate" readonly="" class="form-control installmentCollectionDate" value="{{date('d-M-Y')}}">
                                    @if ($errors->has('installmentCollectionDate'))
                                        @foreach($errors->get('installmentCollectionDate') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title text-center">Schedule List</h4>
                    </div>

                    <div class="col-md-6">
                        <h4 class="card-title text-center">Collection List</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for=""></label>
                        <div class="form-group">
                            <table class="table table-striped gridTable installmentScheduleTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="120px">Invoice No</th>
                                        <th class="text-center" width="120px">Schedule Date</th>
                                        <th class="text-center" width="160px">Installment Amount</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="tbody">
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for=""></label>
                        <div class="form-group">
                            <table class="table table-striped gridTable collectionTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="120px">Invoice No</th>
                                        <th class="text-center" width="120px;">Schedule Date</th>
                                        <th class="text-center" width="160px">Installment Amount</th>
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
    /*code for installment schedule list*/
        $(document).on('change', '.product', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var customerProductId = $('.product option:selected').val();
            $('.instalmentScheduleRow').remove();
            $('.collectionListRow').remove();
            if(customerProductId != ''){
                $.ajax({
                    type:'post',
                    url:'{{ route('installmentCollection.getInstallmentInfo') }}',
                    data:{customerProductId:customerProductId},
                    success:function(data){
                        var installment = data.installment;
                        var installmentScheduleList = data.installmentScheduleList;
                        var installmentCollectionList = data.installmentCollectionList;
                        $('.invoiceNo').val(installment.invoice_no);
                        $('.customerName').val(installment.customer_name);
                        $('.productAmount').val(installment.installment_price);
                        $('.bookingAmount').val(installment.booking_amount);
                        $('.installmentAmount').val(installment.installment_amount);
                        $('.installmentQty').val(installment.installment_qty);


                        for (var row of installmentScheduleList)
                        {
                            if (row.id != $('.collectedSchedule_'+row.id).val())
                            {
                            var installment_schedule_date = ScheduleDate(row.installment_schedule_date);

                            $(".installmentScheduleTable tbody").append(
                                '<tr class="instalmentScheduleRow" id="instalmentScheduleRow_' + row.id + '">' +
                                '<td><input class="form-control" style="text-align: center;" type="text" value="'+row.invoice_no+'" readonly></td>'+
                                '<td><input class="form-control installmentScheduleDate_'+row.id+'" style="text-align: center;" type="text" value="'+installment_schedule_date+'" required readonly></td>'+
                                '<td><input class="form-control installmentScheduleAmount_'+row.id+'" style="text-align: center;" type="text" value="'+row.installment_schedule_amount+'" readonly></td>'+
                                '<td align="center"><span class="btn btn-success item_remove" style="text-align: center;width:100%;padding:7px;" onclick="InstallmentScheduleCollect(' + row.id + ')">Collect</span></td>'+
                                '</tr>'
                            );
                                
                            }
                        }

                        for (var row of installmentCollectionList)
                        {
                            var installment_schedule_date = ScheduleDate(row.installment_schedule_date);

                            $(".collectionTable tbody").append(
                                '<tr class="collectionListRow" id="collectionListRow">'+
                                '<td><input class="form-control" style="text-align: center;" type="text" value="'+row.invoice_no+'" readonly></td>'+
                                '<td><input class="form-control installmentScheduleDate_'+row.id+'" style="text-align: center;" type="text" value="'+installment_schedule_date+'" required readonly></td>'+
                                '<td><input class="form-control installmentScheduleAmount_'+row.id+'" style="text-align: center;width:100%;padding:7px;" type="text" value="'+row.installment_schedule_amount+'" readonly></td>'+
                                '<td><span class="btn btn-info">Collected</span></td>'+
                                '</tr>'
                            );
                        }
                    }
                });
            }
        });

    /*end code for installment schedule list*/

    /*code for installment schedule collect which are not collect*/
    function InstallmentScheduleCollect(installmentScheduleId) {
        var invoiceNo = $('.invoiceNo').val();   
        var installmentScheduleDate = $('.installmentScheduleDate_'+installmentScheduleId).val();   
        var installmentScheduleAmount = $('.installmentScheduleAmount_'+installmentScheduleId).val();
        $(".collectionTable tbody").append('<tr id="instalmentCollectionRow_' + installmentScheduleId + '">' +
            '<td><input class="form-control collectedSchedule_'+installmentScheduleId+'" type="hidden" name="installmentScheduleId[]" value="'+installmentScheduleId+'"> <input class="form-control" style="text-align: center;" type="text" value="'+invoiceNo+'" readonly></td>'+
            '<td><input class="form-control installmentScheduleCollectionDate_'+installmentScheduleId+'" style="text-align: center;" type="text" name="installmentScheduleDate[]" value="'+installmentScheduleDate+'" required readonly></td>'+
            '<td><input class="form-control installmentScheduleCollectionAmount_'+installmentScheduleId+'" style="text-align: center;" type="text" name="installmentScheduleAmount[]" value="'+installmentScheduleAmount+'" readonly></td>'+
            '<td align="center"><span class="btn btn-danger item_remove" style="text-align: center;width:100%;padding:7px;" onclick="InstallmentCollectionRemove(' + installmentScheduleId + ')">Remove</span></td>'+
            '</tr>');

         $('#instalmentScheduleRow_'+installmentScheduleId).remove();
    }

    /*code for remove installment collection which are collected*/
    function InstallmentCollectionRemove(installmentScheduleId) {
        var invoiceNo = $('.invoiceNo').val();   
        var installmentScheduleDate = $('.installmentScheduleCollectionDate_'+installmentScheduleId).val();   
        var installmentScheduleAmount = $('.installmentScheduleCollectionAmount_'+installmentScheduleId).val();

         $(".installmentScheduleTable tbody").append('<tr id="instalmentScheduleRow_' + installmentScheduleId + '">' +
            '<td><input class="form-control" style="text-align: center;" type="text" value="'+invoiceNo+'" readonly></td>'+
            '<td><input class="form-control installmentScheduleDate_'+installmentScheduleId+'" style="text-align: center;" type="text" value="'+installmentScheduleDate+'" required readonly></td>'+
            '<td><input class="form-control installmentScheduleAmount_'+installmentScheduleId+'" style="text-align: center;" type="text" value="'+installmentScheduleAmount+'" readonly></td>'+
            '<td align="center"><span class="btn btn-success item_remove" style="text-align: center;width:100%;padding:7px;" onclick="InstallmentScheduleCollect(' + installmentScheduleId + ')">Collect</span></td>'+

            '</tr>');
          $('#instalmentCollectionRow_'+installmentScheduleId).remove();
    }


    function ScheduleDate(scheduleDate) {
            var d = new Date(scheduleDate);
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
              "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
            ];
            var datestring = ("0" + (d.getDate())).slice(-2)  + "-" + (monthNames[d.getMonth()]) + "-" + d.getFullYear();
            return datestring;
        }
    </script>

@endsection
