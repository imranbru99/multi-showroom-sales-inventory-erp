@extends('admin.layouts.masterAddEditBlank')

@section('custom_css')
    <style type="text/css">
        thead {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
        }

    </style>
@endsection

@section('content')
    @include('admin.partials.loader')
    <form class="form-horizontal" action="{{ route($formLink) }}" id="formAddEdit" method="POST"
        enctype="multipart/form-data" name="form">
        {{ csrf_field() }}
        <div class="card">
            <div class="card-body row">
                <div class="col-md-6">
                    <h4 class="card-title">{{ $title }}</h4>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn btn-outline-info btn-lg go_back" href="{{ route($goBackLink) }}">
                        <i class="fa fa-arrow-circle-left"></i> Go Back
                    </a>
                    <button type="submit" class="btn btn-outline-info btn-lg ml-2 submitBtn">
                        <i class="fa fa-save"></i> Submit
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                        <label for="date">Collection Date</label>
                        <input type="text" name="date" class="form-control add_datepicker" readonly="">
                        @if ($errors->has('date'))
                            @foreach ($errors->get('date') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="project_id">Projecct</label>
                        <select class="form-control" name="project" id="project_id">
                            <option value="">Select An Collector</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <form action="">
                        <div class="form-group">
                            <label for="date">Collector</label>
                            <select name="collector" class="form-control chosen-select" id="collector">
                                <option value="">Select An Collector</option>
                                @foreach ($staffs as $staff)
                                    <option value="{{ $staff->id }}" @if ($collectorID == $staff->id) selected @endif>{{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('date') ? ' has-danger' : '' }}">
                        <label for="date">Account No</label>
                        <div class="">
                        <input type=" text" class="form-control" id="account_no"
                            autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <span class="btn btn-outline-info cash_show" onclick="cashShow()">Show Customer</span>
                        </div>
                        <div class="form-group col-md-7 d-flex cash_dropdown" style="display: none !important;">
                            <select class="form-control chosen-select" id="cash_customer">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}
                                        ({{ $customer->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2 cash_dropdown" style="display: none !important;">
                            <span class="btn btn-outline-info" onclick="addCash()">Add List</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" id="last_account_name" class="form-control" value="" readonly>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" id="last_account_no" class="form-control" value="" readonly>
                </div>
            </div>

            <div>
                <div class="d-flex justify-content-end mt-4">
                    <h5 class="text-nowrap">Total Receive Amount: <input type="text" class="form-control totalReceive"
                            style="width: 200px; text-align: right" value="0" readonly></h5>
                </div>
            </div>

            <table class="table table-bordered" id="CollecctionTable">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Customer Name</th>
                        <th>Customer Account</th>
                        <th>Outstanding</th>
                        <th>Recieve Amt</th>
                        <th>Balance</th>
                        <th>MR No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($data) && count($data) > 0)
                        @foreach ($data as $key => $d)
                            <tr id="{{ $d['sl'] }}">
                                <td>{{ $d['sl'] }}</td>
                                <td>{{ $d['customerName'] }}</td>
                                <td>
                                    <input type="hidden" id="{{ $d['customerAccountCode'] }}"
                                        value="{{ $d['customerAccountCode'] }}">
                                    {{ $d['customerAccountCode'] }}
                                </td>
                                <td>
                                    {{ $d['outstanding'] }}
                                    <input type="hidden" class="outstandingAmt" value="{{ $d['outstanding'] }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control receiveAmount" onkeyup="UpdateNumbers(event)"
                                        name="receiveAmount[{{ $d['customerID'] }}]" min="0" value="">
                                </td>
                                <td>
                                    <input type="number" class="form-control balance" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control mr_no" name="mrpNo[{{ $d['customerID'] }}]"
                                        data-mrpNo="{{ $key }}">
                                </td>
                                <td></td>
                                <input type="hidden" name="collector" class="reference_id" value="{{ $collectorID }}">
                                <input type="hidden" name="date" class="collection_date" value="{{ date('d-m-Y') }}">
                            </tr>
                        @endforeach
                    @else
                    @endif
                </tbody>
            </table>

            <button type="submit" class="btn btn-outline-info btn-lg float-right mb-3 submitBtn">
                <i class="fa fa-save"></i> Submit
            </button>



        </div>
        <div class="clearfix"></div>
    </form>
    <style>
        .last_account_right {
            width: 250px;
            float: right;
        }

    </style>

@endsection

@section('custom-js')
    <script>
        function cashShow() {
            var data = $('.cash_show').html();
            if (data == 'Show Customer') {
                $('.cash_show').html('Hide Customer');
                $('.cash_dropdown').show();
            } else {
                $('.cash_show').html('Show Customer');
                $('.cash_dropdown').attr("style", "display: none !important");;
            }

        }
    </script>
    <script>
        $('.spinner-div').hide();

        function UpdateNumbers(e) {

            let thisEl = $(e.target);

            let thisParent = thisEl.parent().parent();

            let outStandingAmount = +thisParent.find('.outstandingAmt').val();

            let receiveAmount = +thisEl.val();

            let balanceAmount = outStandingAmount - receiveAmount;

            thisParent.find('.balance').val(balanceAmount);
            toTal();

        }


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
                    title: "Enter Acount Number",
                    type: "warning",
                    // timer: 1000,
                });
                return;
            }

            var cus_id = $('#' + account_no).val();
            // if (account_no == cus_id) {
            //     swal({
            //         title: "Already Exist!",
            //         type: "warning",
            //         // timer: 1000,
            //     });
            //     $('#account_no').val('');
            //     return;
            // }

            var rowCount = $('#CollecctionTable tbody tr').length;


            var no_row = rowCount + 1;


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('retailCollectionByCollector.addAccount') }}",
                data: {
                    account_no: account_no,
                    project: "{{ $project_id }}"
                },
                success: function(response) {

                    let collections = response;

                    response.forEach(function(c) {
                        //                    


                        let row = `
                                <tr id="${c.customerID}">
                                    <td>${no_row}</td>
                                    <td>${c.customerName}</td>
                                    <td>
                                        <input type="hidden" id="${c.customerAccountCode}" value="${c.customerAccountCode}">
                                            ${c.customerAccountCode}
                                    </td>
                                    <td>
                                        ${c.outstanding}
                                        <input type="hidden" class="outstandingAmt" value="${c.outstanding}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control receiveAmount" onkeyup="UpdateNumbers(event)" name="receiveAmount[]" value="">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control balance" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control mr_no increment_${no_row}" name="mrpNo[]" onkeyup="increment(${no_row});">
                                    </td>
                                    <td>
                                        <span class="btn btn-outline-danger" onclick="removeAcc(${c.customerID})">Remove</span>
                                    </td>
                                        <input type="hidden" name="customer_id[]" class="customer_id" value="${c.customerID}">
                                        <input type="hidden" name="collector" class="reference_id" value="">
                                        <input type="hidden" name="project_id" class="project_id" value="">
                                        <input type="hidden" name="date" class="collection_date" value="">
                                </tr>

                            `;

                        $('.table tbody').append(row);


                        $('#last_account_no').val(c.customerAccountCode);
                        $('#last_account_name').val(c.customerName);

                    });

                    $('.spinner-div').hide();
                    $('.no_data').remove();
                    var collector = $('#collector').chosen().val();
                    $('.reference_id').val(collector);
                    var project = $('#project_id').val();
                    $('.project_id').val(project);
                    var date = $('.add_datepicker').val();
                    $('.collection_date').val(date);
                    $('#account_no').val('');

                },
                error: function() {
                    swal({
                        title: "No Data Found",
                        type: "warning",
                        // timer: 1000,
                    });
                    $('#account_no').val('');
                }
            });
        }

        // $(".submitBtn").prop("disabled", true);

        $('#collector').change(function(e) {
            var collector = $('#collector').chosen().val();
            $('.reference_id').val(collector);
        });

        $('#project_id').change(function(e) {
            var project_id = $('#project_id').val();
            $('.project_id').val(project_id);
        });


        $('.add_datepicker').change(function(e) {
            var date = $('.add_datepicker').val();
            $('.collection_date').val(date);
        });

        var key_count = 1;

        $('form').bind("keypress", function(e) {
            if (e.keyCode == 13) {
                key_count = key_count + 1;
                e.preventDefault();
                var index = $('.mr_no').index(this) + key_count;
                $('.mr_no').eq(index).focus();
                console.log(index);
                return;
            } else {
                $(".submitBtn").click(function(e) {

                    e.preventDefault();

                    var collector = $('#collector').val();
                    var project = $('#project_id').val();

                    console.log(collector);
                    console.log(project);

                    var rowCount = $('#CollecctionTable tbody tr').length;

                    if (project == '') {
                        swal({
                            title: "Select Project",
                            type: "warning",
                            // timer: 1000,
                        });
                    }


                    if (collector == '') {
                        swal({
                            title: "Select Collector",
                            type: "warning",
                            // timer: 1000,
                        });
                    }

                    if (rowCount == 0) {
                        swal({
                            title: "No Data Found",
                            type: "warning",
                            // timer: 1000,
                        });
                    }

                    if (collector != '' && rowCount != 0 && project != '') {


                        $('#formAddEdit').submit();

                        //            $('#formAddEdit').submit();
                    }
                });
            }
        });

        function toTal() {
            // short_long_installment
            var total = 0;
            $('.receiveAmount').each(function() {
                var recAmt = $(this).val();
                if (recAmt == '') {
                    recAmt = 0;
                }
                total = total + parseFloat(recAmt);
            });
            $('.totalReceive').val(total);
        }

        function removeAcc(id) {
            $('#' + id).remove();
        }

        function increment(id) {
            var increment = $('.increment_' + id).val();
            var rowCount = $('#CollecctionTable tbody tr').length;
            var inc = 1;

            $('.mr_no').each(function(i) {
                var newID = parseInt(increment) + i;
                $(this).val(newID);
            });
        }
    </script>



    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$project_id }}');

        $('#project_id').change(function() {
            var selected = $('#project_id').val();
            $('.go_back').attr('href', href + '?project=' + selected);
        });
    </script>

    <script>

        function addCash() {
            var id = $('.cash_dropdown').find("option:selected").val();


            if (id == '') {
                swal({
                    title: "Select Customer",
                    type: "warning",
                });
                return;
            }


            var rowCount = $('#CollecctionTable tbody tr').length;


            var no_row = rowCount + 1;


            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('retailCollectionByCollector.addCashAccount') }}",
                data: {
                    id: id
                },
                success: function(response) {

                    let collections = response;

                    response.forEach(function(c) {
                        //                    


                        let row = `
                                <tr id="${c.customerID}">
                                    <td>${no_row}</td>
                                    <td>${c.customerName}</td>
                                    <td>
                                        <input type="hidden" id="${c.customerAccountCode}" value="${c.customerAccountCode}">
                                            ${c.customerAccountCode}
                                    </td>
                                    <td>
                                        ${c.outstanding}
                                        <input type="hidden" class="outstandingAmt" value="${c.outstanding}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control receiveAmount" onkeyup="UpdateNumbers(event)" name="receiveAmount[]" value="">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control balance" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control mr_no increment_${no_row}" name="mrpNo[]" onkeyup="increment(${no_row});">
                                    </td>
                                    <td>
                                        <span class="btn btn-outline-danger" onclick="removeAcc(${c.customerID})">Remove</span>
                                    </td>
                                        <input type="hidden" name="customer_id[]" class="customer_id" value="${c.customerID}">
                                        <input type="hidden" name="collector" class="reference_id" value="">
                                        <input type="hidden" name="project_id" class="project_id" value="">
                                        <input type="hidden" name="date" class="collection_date" value="">
                                </tr>

                            `;

                        $('.table tbody').append(row);


                        $('#last_account_no').val(c.customerAccountCode);
                        $('#last_account_name').val(c.customerName);

                    });

                    $('.spinner-div').hide();
                    $('.no_data').remove();
                    var collector = $('#collector').chosen().val();
                    $('.reference_id').val(collector);
                    var project = $('#project_id').val();
                    $('.project_id').val(project);
                    var date = $('.add_datepicker').val();
                    $('.collection_date').val(date);
                    $('.cash_dropdown').val('');

                },
                error: function() {
                    swal({
                        title: "No Data Found",
                        type: "warning",
                        // timer: 1000,
                    });
                }
            });
        }
    </script>
@endsection
