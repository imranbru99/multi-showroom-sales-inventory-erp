@extends('admin.layouts.masterAddEditBlank')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h4 class="card-title">{{ $title }}</h4>
            </div>
            <div class="col-md-6 text-right">
                <span class="btn btn-outline-info btn-lg cash_account_btn">
                    <i class="fa fa-dollar"></i>
                    Installment Customer
                </span>
                <span class="btn btn-outline-info btn-lg installment_account_btn" style="display: none;">
                    <i class="fa fa-money"></i>
                    Cash Customer
                </span>

                <a class="btn btn-outline-info btn-lg go_back" href="{{ route($goBackLink) }}">
                    <i class="fa fa-arrow-circle-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>

    <div class="installment_card">
        <form class="form-horizontal" action="{{ route($formLink) }}" id="formInstallment" method="POST" enctype="multipart/form-data" name="form">
            {{ csrf_field() }}
            <div class="card-body">

                <div class="row d-flex justify-content-between">
                    <form class="">
                        <div class=" form-group col-md-4 d-flex justify-content-between">
                            <div class="___class_+?3___">
                                <label for="account_no">Account Number</label>
                                <input type="text" class="form-control" id="account_no" name="account_no" value="" placeholder="Account No">
                            </div>
                        </div>
                    </form>

                    <div class="form-group col-md-3">
                        <label for="account_no">Date</label>
                        <input type="text" class="form-control datepicker" id="account_no" name="date" value="{{ date('d-m-Y') }}">
                    </div>
                </div>
                <div class="row AccountDetails">

                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info float-right btn-lg waves-effect installmentSave" name="buttonAddEdit" value="Save" @if(auth()->user()->role == 10) disabled @endif><i class="fa fa-save"></i>
                            {{ $buttonName }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="cash_card" style="display: none">
        <form>
            <div class="col-md-12 d-flex justify-content-end">
                <div class="input-group col-md-3">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Invoice No" autocomplete="off">
                    <input type="hidden" name="project" value="{{ $project_id }}">
                    <span class="input-group-prepend">
                        <button type="submit" class="btn btn-outline-info">Search</button>
                    </span>
                </div>
            </div>
        </form>
        <form class="form-horizontal" action="{{ route('closeAccount.cashSave') }}" method="POST" enctype="multipart/form-data"">
                                {{ csrf_field() }}
                                    <div class=" card-body">
            <div class="row">
                @foreach ($cashSales as $cashSale)
                @php
                $total_sales = $cashSale->products->sum('sales_price');
                $total_discount = $cashSale->products->sum('discount');
                $total_voucher = $cashSale->products->sum('gift_voucher');
                $total_excrt = $cashSale->products->sum('exchange_crt');
                $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

                //Collection
                $collectionIds = \App\InstallmentCollection::where('customer_id', $cashSale->customer_id)
                ->select('id')
                ->get()
                ->pluck('id');
                $collections = 0;

                $collectionList = [];
                if ($collectionIds) {
                $collectionList = \App\InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->orderBy('installment_collection_date', 'asc')
                ->get();

                $collections = 0;
                foreach ($collectionList as $collection) {
                $collections += floatval($collection->installment_schedule_amount);
                }
                }
                $outstanding = $actual_sales - $collections;
                @endphp
                <div class="col-md-6" id="{{ $cashSale->id }}">
                    <div class="card border">
                        <div class="card-header">
                            <h5 class="font-weight-bold">INVOICE #{{ $cashSale->invoice_no }}</h5>
                        </div>
                        <div class="card-body pr-0">
                            <table width="100%">
                                <tr>
                                    <td width="25%"><b>Customer Name</b></td>
                                    <td width="40%">: {{ @$cashSale->customer->name }}</td>
                                    <td width="20%"><b>Sal.Amn.</b></td>
                                    <td width="20%">:
                                        @if ($total_voucher > 0)
                                        {{ $actual_sales + $total_voucher }}
                                        @else
                                        {{ $actual_sales }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%"><b>A/C</b></td>
                                    <td width="40%">: {{ @$cashSale->customer->code }}</td>
                                    <td width="20%"><b>Discount</td>
                                    <td width="20%">: {{ @$total_discount }}</td>
                                </tr>
                                <tr>
                                    <td width="20%"><b>Reference</b></td>
                                    <td width="40%">: {{ @$cashSale->seller->name }}</td>
                                    <td width="20%"><b>Gift</td>
                                    <td width="20%">: {{ $total_voucher }}</td>
                                </tr>
                                <tr>
                                    <td width="20%"><b>Sale Date</b></td>
                                    <td width="40%">: {{ date('d-m-Y', strtotime($cashSale->sale_date)) }}</td>
                                    <td width="20%"><b>EXCRT</b></td>
                                    <td width="20%">: {{ $total_excrt }}</td>
                                </tr>
                                <tr>
                                    <td width="20%"><b>Invoice No</b></td>
                                    <td width="40%">: {{ $cashSale->invoice_no }}</td>
                                    <td width="20%"><b>Collection</b></td>
                                    <td width="20%">: {{ $collections }}</td>
                                </tr>
                                <tr>
                                    <td width="20%"><b>Product</b></td>
                                    <td width="40%">:
                                        @foreach ($cashSale->products as $product)
                                        {{ @$product->product->name }},
                                        @endforeach
                                    </td>
                                    <td width="20%"><b>Outstanding</b></td>
                                    <td width="20%">:
                                        @if ($outstanding >= 0)
                                        {{ $outstanding }}
                                        @elseif($outstanding < 0) ({{ -1 * $outstanding }}) @endif </td>
                                </tr>
                                <tr>
                                    <td width="20%"><b>Model</b></td>
                                    <td colspan="3">:
                                        @foreach ($cashSale->products as $product)
                                        {{ @$product->product->model_no }},
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="col-md-12 d-flex justify-content-between">
                                <span class="text-danger"> @if ($outstanding != 0)** Outstanding is not 0 ** @endif</span>
                                <button type="button" class="btn btn-outline-info" onclick="cashClose({{ $cashSale->id }})" @if ($outstanding !=0) disabled @endif>
                                    {{ $buttonName }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-md-12">
                    <div class="d-flex justify-content-end">
                        {!! $cashSales->appends(['project' => $project_id, 'q' => $search])->links('vendor.pagination.bootstrap-4') !!}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<style>
    b {
        font-weight: bold;
        font-size: 13px
    }

    td {
        font-weight: 10px
    }

</style>
@endsection

@section('custom-js')
<script>
    function cashClose(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: "post"
            , url: "{{ route('closeAccount.cashSave') }}"
            , data: {
                id: id
            }
            , success: function(response) {
                swal({
                    title: "<small class='text-success'>Success!</small>"
                    , type: "success"
                    , text: "Account CLosed Successfully!"
                    , timer: 1000
                    , html: true
                , });
                $('#' + id).remove();
            }
            , error: function(response) {
                error = "Failed.";
                swal({
                    title: "<small class='text-danger'>Error!</small>"
                    , type: "error"
                    , text: error
                    , timer: 1000
                    , html: true
                , });
            }
        });
    }

</script>
<script>
    var myStorage = window.localStorage;

    $('.cash_account_btn').on('click', function() {
        $('.installment_card').hide();
        $('.installment_account_btn').show();
        $('.cash_account_btn').hide();
        $('.cash_card').show();

        myStorage.setItem('type', 'cash');
    });

    $('.installment_account_btn').on('click', function() {
        $('.installment_card').show();
        $('.installment_account_btn').hide();
        $('.cash_account_btn').show();
        $('.cash_card').hide();

        myStorage.setItem('type', '');
    });

    var type = myStorage.getItem('type');
    if (type == 'cash') {
        $('.installment_card').hide();
        $('.installment_account_btn').show();
        $('.cash_account_btn').hide();
        $('.cash_card').show();
    }

</script>
<script>
    $.fn.onEnterKey =
        function(closure) {
            $(this).keypress(
                function(event) {
                    var code = event.keyCode ? event.keyCode : event.which;

                    if (code == 13) {
                        closure();
                        return false;
                    }
                });
        }
    $('#account_no').onEnterKey(
        function() {
            addAccount();
        });


    function addAccount() {
        var account_no = $('#account_no').val();


        if (account_no == '') {
            swal({
                title: "Enter Acount Number"
                , type: "warning",
                // timer: 1000,
            });
            return;
        }


        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: "post"
            , url: "{{ route('closeAccount.accountInfo') }}?project={{ @$project_id }}"
            , data: {
                account_no: account_no
            }
            , success: function(response) {
                var customer = response.customer;
                var sales = response.sales;
                var collections = response.collections;
                var outstanding = response.outstanding;
                var invoice_details = response.invoice_details;
                var product = invoice_details.products[0].product;
                var installments = response.installments;
                var f_ins_date = response.first_date;
                var l_ins_date = response.last_date;


                var due_aa = 0;
                var over_col = 0;
                if (outstanding > 0) {
                    var due_aa = outstanding;
                }
                if (outstanding < 0) {
                    var over_col = -(outstanding);
                }

                $('.AccountDetails div').remove();
                let row = `
                
                    <div class="col-md-6 pr-4" style="border-right: 1px solid black;">
                        <div class="card-body p-0">
                            <h4 class="mb-3" style="border-bottom: 1px solid black;">Account Details</h4>
                            <table width="100%">
                                <thead>
                                    <th width="35%"></th>
                                    <th></th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td> <label for="accunt" class="mr-2">Account No</label></td>
                                        <td>
                                            <input type="text" class="form-control account_code" name="code" value="${customer.code}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> <label for="name" class="mr-2">Account Name</label></td>
                                        <td>
                                            <input type="text" class="form-control account_name" name="account_name" value="${customer.name}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> <label for="phone_no" class="mr-2">Phone No</label></td>
                                        <td>
                                            <input type="text" class="form-control phone_no" name="phone_no" value="${customer.phone_no}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> <label for="address" class="mr-2">Adress</label></td>
                                        <td>
                                            <textarea class="form-control address" name="address" readonly>${customer.present_address}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body p-0 mt-5">
                            <h4 class="mb-3" style="border-bottom: 1px solid black;">Product Name: <span class="font-weight-bold product"></span></h4>
                            <table>
                                <thead>
                                    <th width="35%"></th>
                                    <th></th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><b class="font-weight-bold">Model No </b> </td>
                                        <td>: <span class="model_no"></span></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Sale Type </b> </td>
                                        <td class="sale_type">: ${invoice_details.sale_type} </td>
                                    </tr>
                                    <tr>
                                        <td class="pt-4"><b class="font-weight-bold">Install. Type </b> </td>
                                        <td class="pt-4 install_type">: ${invoice_details.installment_type} </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Install. Qty </b> </td>
                                        <td class="inst_qty">: ${installments.length} </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Invoice Date </b> </td>
                                        <td class="first_ins">: ${response.invoice_date} </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Install. Start Date </b> </td>
                                        <td class="first_ins">: ${f_ins_date} </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Install. End Date </b> </td>
                                        <td class="last_ins">: ${l_ins_date} </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Install. End Date </b> </td>
                                        <td>: <input type="text" class="datepicker setDate" onchange="setDate(${customer.id})" value="{{ date('d-m-Y') }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="col-md-6 pl-4">
                        <h4 class="mb-3" style="border-bottom: 1px solid black;">Settlemrnt Information - (#Invoice-${invoice_details.invoice_no})</h4>
                        <div class="card-body p-0">
                            <table width="100%">
                                <thead>
                                    <th width="40%"></th>
                                    <th></th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><b class="font-weight-bold">Invoice Amount </b></td>
                                        <td class="text-left">: <span class="sale_price">${sales}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Collection: </b></td>
                                        <td class="total_coll">: ${collections} </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Due: </b></td>
                                        <td class="due_aa">: ${due_aa}</td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Over Collection: </b></td>
                                        <td class="over_col">: ${over_col}</td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Late Days: </b></td>
                                        <td>: <span class="late_days">${response.late_days}</span>  <input type="checkbox" class="ml-5 on_schedule" onclick="schedule(${customer.id})"> <span class="ml-2">On Schedule</span></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Late Fee: </b></td>
                                        <td>: <span class="late_fee">${response.late_fee}</span></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Interest Receive: </b></td>
                                        <td> <input type="number" class="form-control interest_receive" name="interest_receive" value="${over_col}" onkeyup="calculate()" placeholder="Amount"></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Discount Amount: </b></td>
                                        <td> <input type="number" class="form-control discount_amount" name="discount_amount" value="0" onkeyup="calculate()" placeholder="Amount"></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Cumulative Due </b></td>
                                        <td><input type="number" class="form-control cam_due" name="cam_due" value="${outstanding + response.late_fee}" placeholder="" readonly></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Employee</b></td>
                                        <td>
                                            <select class="form-control chosen-select employee" name="employee" id="employee">
                                               <option value="">Select Employee</option> 
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Missing Amount </b></td>
                                        <td><input type="number" class="form-control missing_amount" name="missing_amount" onkeyup="calculate()" value="0" placeholder="Missing Amount"></td>
                                    </tr>
                                    <tr>
                                        <td><b class="font-weight-bold">Remarks </b></td>
                                        <td><textarea class="form-control" name="remarks" placeholder="Remarks"></textarea></td>
                                    </tr>


                                <input type="hidden" class="form-control" name="customer_code" value="${customer.code}" readonly>
                                <input type="hidden" class="form-control" name="collection" value="${collections}" readonly>
                                <input type="hidden" class="form-control late_days_d" name="late_days" value="${response.late_days}" readonly>
                                <input type="hidden" class="form-control late_fee_d" name="late_fee" value="${response.late_fee}" readonly>
                                <input type="hidden" class="form-control total_due" value="${outstanding + response.late_fee}" readonly>
                                <input type="hidden" class="form-control main_due" value="${response.late_fee}" readonly>
                                <input type="hidden" class="form-control outstanding_amount" value="${outstanding}" readonly>
                                <input type="hidden" class="form-control cus_id" name="cus_id" value="${customer.id}" readonly>
                                <input type="hidden" class="form-control sale_id" name="sale_id" value="${invoice_details.id}" readonly>
                                <input type="hidden" class="form-control invoice_no" name="invoice_no" value="${invoice_details.invoice_no}" readonly>
                                <input type="hidden" class="form-control settle_value" name="settle_value" value="0" readonly>
                                <input type="hidden" class="form-control schedule_value" name="schedule_value" value="0" readonly>
                                <input type="hidden" class="form-control" name="project_id" value="${customer.project_id}" readonly>

                                </tbody>
                            </table>
                        </div>
                    </div>        
                `;



                $('.AccountDetails').append(row);


                $(".datepicker").datepicker("destroy");
                $(".datepicker").datepicker({
                    format: 'dd-mm-yyyy'
                    , changeMonth: true
                    , changeYear: true,

                });

                if (response.staffs.length > 0) {
                    response.staffs.forEach(function(item, index) {
                        var option = '<option value="' + item.id +
                            '">' + item.name + '</option>';
                        $('#employee').append(option);
                    });


                    $('.chosen-select').chosen();
                    $('.chosen-select').trigger("chosen:updated");
                }

                var separate = '';
                if (invoice_details.products.length > 1) {
                    var separate = ',';
                }

                invoice_details.products.forEach(function(item, index) {
                    $('.product').append(item.product.name + ' ' + separate);
                });

                invoice_details.products.forEach(function(item, index) {
                    $('.model_no').append(item.product.model_no + ' ' + separate);
                    console.log(item.product.name);
                });


                $('#account_no').val('');

            }
            , error: function() {
                swal({
                    title: "Invalid " + account_no + " Account Number"
                    , type: "warning",
                    // timer: 1000,
                });
                $('#account_no').val('');
            }
        });
    }

    function calculate() {
        var interest_receive = $(".interest_receive").val();
        var discount_amount = $(".discount_amount").val();
        var missing_amount = $(".missing_amount").val();
        var outstanding_amount = $(".outstanding_amount").val();
        var main_due = $(".main_due").val();
        if (isNaN(interest_receive)) {
            var interest_receive = 0;
        }

        if (isNaN(discount_amount)) {
            var discount_amount = 0;
        }

        if (isNaN(missing_amount)) {
            var missing_amount = 0;
        }

        var total = parseInt(outstanding_amount) + parseInt(main_due) + parseInt(interest_receive) - parseInt(
            discount_amount) - parseInt(missing_amount);

        $('.cam_due').val(total);

    }

</script>

<script>
    function schedule(customerId) {
        var checked = $('.on_schedule').is(":checked");
        if (checked == true) {
            var value = 1;
            $('.schedule_value').val(1);
        }
        if (checked != true) {
            var value = 0;
            $('.schedule_value').val(0);
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: "post"
            , url: "{{ route('closeAccount.lateDays') }}"
            , data: {
                customer_id: customerId
                , schedule: value
            , }
            , success: function(response) {
                $('.late_days').html(response.late_days);
            }
            , error: function(response) {}
        });
    }

    function setDate(customerId) {

        let date = $('.setDate').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: "post"
            , url: "{{ route('closeAccount.piclateDays') }}"
            , data: {
                customer: customerId
                , date: date
            , }
            , success: function(response) {
                $('.late_days').html(response.duration);
                $('.late_fee').html(response.late_fee);
                $('.late_days_d').val(response.duration);
                $('.late_fee_d').val(response.late_fee);

                let outstanding = parseInt($('.outstanding_amount').val());

                $('.cam_due').val(outstanding + response.late_fee);
                $('.total_due').val(outstanding + response.late_fee);
                $('.main_due').val(response.late_fee);
            }
            , error: function(response) {}
        });

    }

    // function settle() {
    //     var checked = $('.settle').is(":checked");

    //     var type = $('.sale_type').html();
    //     var sale_type = type.replace(': ', '');

    //     if (checked == true) {
    //         $('.late_fee').html(0);
    //         $('.settle_value').val(1);
    //         $('.main_due').val(0);
    //         $(".interest_receive").val(0);
    //         $(".discount_amount").val(0);
    //         $(".missing_amount").val(0);
    //         calculate();
    //     }
    //     if (checked != true) {
    //         var days = parseInt($('.late_days').html());
    //         var price = parseInt($('.sale_price').html());
    //         $('.settle_value').val(0);

    //         var total = price * 0.12;
    //         var fee = total / 365;
    //         if (sale_type == 'Long Installment ') {
    //             var late_fee = Math.round(fee * days);
    //         } else {
    //             var late_fee = Math.round(fee * (days + 183));
    //         }
    //         if (late_fee < 0) {
    //             var late_fee = 0;
    //         }

    //         $('.late_fee').html(late_fee);


    //         var outstanding_amount = parseInt($('.outstanding_amount').val());
    //         var total_due = outstanding_amount + late_fee;
    //         $('.main_due').val(late_fee);
    //         $(".interest_receive").val(0);
    //         $(".discount_amount").val(0);
    //         $(".missing_amount").val(0);
    //         if (outstanding_amount < 0) {
    //             $(".interest_receive").val(-(outstanding_amount));
    //             $('.main_due').val(total_due);
    //         }
    //         calculate();
    //     }
    // }



    $(".installmentSave").click(function(e) {
        e.preventDefault();

        var acc = $('.account_code').val();

        console.log(acc);

        if (acc == undefined) {
            return;
        } else {
            $('#formInstallment').submit();
        }

    });

</script>

<script>
    var href = $('.go_back').attr('href');
    $('.go_back').attr('href', href + '?project={{ @$project_id }}');

</script>
@endsection
