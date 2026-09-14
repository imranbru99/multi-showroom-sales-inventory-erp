@extends('admin.layouts.master')

@section('content')
    @php
        use App\Product;
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
        <input type="hidden" name="installmentId" value="{{$installment->id}}">
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
                    <div class="col-md-7">
                        <label for="customerProductId">Customer & Product Name</label>
                        <div class="form-group {{ $errors->has('customerProductId') ? ' has-danger' : '' }}">
                            @php
                                $customerAndPorduct = $installment->customer_name.' / '. @$product->name.'('.@$product->code.')';
                            @endphp
                           <input type="text" value="{{$customerAndPorduct}}" class="form-control" readonly="">
                            @if ($errors->has('customerProductId'))
                                @foreach($errors->get('customerProductId') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label>Invoice No</label>
                        <div class="form-group {{ $errors->has('invoiceNo') ? ' has-danger' : '' }}">
                            <input type="text" name="invoiceNo" value="{{$installment->invoice_no}}" class="form-control invoiceNo" readonly="">
                            @if ($errors->has('invoiceNo'))
                                @foreach($errors->get('invoiceNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label>Collector</label>
                        <div class="form-group {{ $errors->has('installmentCollector') ? ' has-danger' : '' }}">
                            <input type="text" name="installmentCollector" value="{{$installment->installment_collector_name    }}" class="form-control installmentCollector" readonly="">
                            @if ($errors->has('installmentCollector'))
                                @foreach($errors->get('installmentCollector') as $error)
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
                                    <input type="text" name="productAmount"  value="{{$installment->installment_price}}" readonly="" class="form-control productAmount">
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
                                    <input type="text" name="bookingAmount" value="{{$installment->booking_amount}}" readonly="" class="form-control bookingAmount">
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
                                    <input type="text" name="installmentQty" value="{{$installment->installment_qty}}" readonly="" class="form-control installmentQty">
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
                                    <input type="text" name="installmentAmount" value="{{$installment->installment_amount}}" readonly="" class="form-control installmentAmount">
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
                        <input type="hidden" class="row_count" value="{{count($installmentScheduleList)}}">
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
                                    @php
                                        $i = 0;
                                    @endphp
                                   @foreach($installmentScheduleList as $schedule)
                                   @php
                                        $i++;
                                        $scheduleDate = date('d-M-Y',strtotime($schedule->installment_schedule_date));
                                   @endphp
                                    <tr id="itemRow_{{$i}}">
                                        <td><input  class="text-center" type="text" value="{{$i}}" readonly></td>
                                        <td><input style="text-align: center;" type="text" value="{{$schedule->invoice_no}}" readonly></td>
                                        <td><input style="text-align: center;" type="text" value="{{$scheduleDate}}" name="installmentScheduleDate[]" required readonly></td>
                                        <td><input style="text-align: center;" type="text" name="installmentScheduleAmount[]"  value="{{$schedule->installment_schedule_amount}}" readonly></td>
                                        <td align="center"><span class="btn btn-danger item_remove" onclick="itemRemove({{$i}})"><i class="fa fa-trash"></i> Delete</span></td>
                                    </tr>
                                   @endforeach
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
         var installmentQty = parseInt($('.installmentQty').val())+parseInt(1);
            var total = $('.row_count').val();
            if(total == installmentQty){
                $('.createSchedule').hide();
                $('.saveButton').show();
            }else{
               $('.createSchedule').show();
            }
            $('.saveButton').hide(); 
        $(".createSchedule").click(function () {
            var product = $('.product').val();
            var scheduleDate = $('.installmentScheduleDate').val();
            if(product != '' && scheduleDate != ''){
                var row_count = $('.row_count').val();
                var total = parseInt(row_count) + 1; 

                var invoiceNo = $('.invoiceNo').val();
                var installmentQty = $('.installmentQty').val();
                var installmentAmount = $('.installmentAmount').val();
                $(".gridTable tbody").append('<tr id="itemRow_' + total + '">' +
                    '<td><input type="text" class="text-center" value="'+total+'" readonly></td>'+
                    '<td><input style="text-align: center;" type="text" value="'+invoiceNo+'" readonly></td>'+
                    '<td><input style="text-align: center;" class="installmentScheduleDate_'+total+'"  type="text" name="installmentScheduleDate[]" value="'+scheduleDate+'" required readonly></td>'+
                    '<td><input style="text-align: center;" type="text" name="installmentScheduleAmount[]" value="'+installmentAmount+'" readonly></td>'+
                    '<td align="center"><span class="btn btn-danger item_remove" onclick="itemRemove(' + total + ')"><i class="fa fa-trash"></i> Delete</span></td>'+
                    '</tr>');
                $('.row_count').val(total);
                var installmentScheduleDate = $('.installmentScheduleDate_'+total).val();
                $('.installmentScheduleDate').val(NextScheduleDate(installmentScheduleDate));
                TotalInstallmetnQty();
            }else{
                if(product == ''){
                    swal("Please! Select Product", "", "warning");
                }else{
                    if(scheduleDate == ''){
                        swal("Please! Select Schedule Date", "", "warning");  
                    }
                }
            }
        });
    

        function itemRemove(total) {
            var total_row_value = total-1;
            $('.row_count').val(total_row_value);
            $("#itemRow_" + total).remove();
            TotalInstallmetnQty()
        }

        function TotalInstallmetnQty(){
            var installmentQty = parseInt($('.installmentQty').val())+parseInt(1);
            var total = $('.row_count').val();
            if(total == installmentQty){
                $('.createSchedule').hide();
                $('.saveButton').show();
            }else{
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
            if(d.getMonth()+1 == 12){
                var nextYear = d.getFullYear()+1;
            }else{
                var nextYear = d.getFullYear();
            }
            var datestring = ("0" + (d.getDate())).slice(-2)  + "-" + (monthNames[(d.getMonth()+1)%12]) + "-" + nextYear;
            return datestring;
        }

    </script>
@endsection
