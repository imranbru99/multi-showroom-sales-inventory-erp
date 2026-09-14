@extends('admin.layouts.master')

@section('content')
    <style>
        /* .table thead {
                                                                                            position: absolute;
                                                                                            width: 100%;
                                                                                            top: -27px;
                                                                                            left: 9px;
                                                                                        } */

        /* .table tr {
                                                                                            width: 163px;
                                                                                        } */

        .table th {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
            /* width: 140px; */
        }

        /* .table td {
                                                                                            width: 34.5%;
                                                                                        } */

        /* .tableFixHead {
                                                                                            overflow-y: auto;
                                                                                            overflow-x: hidden;
                                                                                            height: 380px;
                                                                                            border: 1px solid #00c292;
                                                                                        } */

        /* .info-table tr td {
                                                                                            padding: 0 10px;
                                                                                        } */

    </style>


    <div class="card" style="margin-bottom: 0px;">
        <div class="card-header">
            <h1 class="text-center h3">{{ $title }}</h1>
        </div>

        <form action="{{ route('reschedule.installment') }}" method="get">
            <input type="hidden" name="searched" value="true">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="customers">Customers</label>
                            <select name="customer" id="customers" class="form-control chosen-select">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @if ($customer->id == $customerID) seelcted @endif>
                                        {{ $customer->name }} - {{ $customer->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="submit" name="submit" value="Search"
                            class="mt-4 btn btn-outline-info btn-lg text-info">
                    </div>
                </div>
            </div>
        </form>

    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            @if ($data)
                <table class="info-table">
                    <tr>
                        <td><span class="font-weight-bold">Name</span> : {{ $data['customer']->name }}</td>
                        <td>||</td>
                        <td><span class="font-weight-bold">A/C No</span> : {{ $data['customer']->code }}</td>
                    </tr>
                </table>
            @endif
        </div>


        <div class="col-md-6">

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="mt-1">
                        <span class="font-weight-bold">ReSchedule Amount</span>
                        <br>
                        <span id="totalRescheduleAmount"></span>
                    </div>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-outline-info btn-lg btn-block" id="rescheduleBtn"
                        onclick="toogleRescheduleInfo()">Reschedule</button>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-outline-info btn-lg btn-block" id="processBtn">Process</button>
                </div>

            </div>

            <div class="row">

                <div class="col-md-4" id="type_div">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="1">Daily</option>
                            <option value="7">Weekly</option>
                            <option value="15">Bi-Monthly</option>
                            <option value="30">Monthly</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4" id="amount_div">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount">
                    </div>
                </div>

                <div class="col-md-4" id="start_date_div">
                    <div class="form-group">
                        <label for="start_date">Schedule Start Date</label>
                        <input type="text" class="form-control datepicker" id="start_date" name="start_date"
                            autocomplete="off">
                    </div>
                </div>



            </div>
        </div>

    </div>

    <form action="{{ route('reschedule.installment.auto.save') }}" method="post">

        {{ csrf_field() }}

        @if ($data)
            <input type="hidden" name="customerId" id="customerId" value="{{ $data['customer']->id }}">
        @endif

        <div class="row">

            <div class="col-md-6">
                <div class="">
                    <table class="table table-bordered table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Installment Amount</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody id="oldInstallments">
                            @if ($data)

                                @foreach ($data['installments'] as $installment)

                                    @foreach ($installment->schedule as $schedule)


                                        <tr>

                                            <td>
                                                <input type="text"
                                                    class="form-control datepicker schedule_date schedule_date_{{ $schedule->id }}"
                                                    value="{{ date('d-m-Y', strtotime($schedule->installment_schedule_date)) }}"
                                                    name="schedule[{{ $schedule->id }}]" readonly>
                                            </td>

                                            <td>
                                                <input type="text"
                                                    class="schedule_amt schedule_amt_{{ $schedule->id }} form-control"
                                                    value="{{ $schedule->installment_schedule_amount }}">
                                            </td>

                                            <td>
                                                <div class="text-center mt-2">
                                                    <i class="fa fa-pencil text-primary"
                                                        onclick="updateSingle('{{ $schedule->id }}')"
                                                        aria-hidden="true"></i>
                                                </div>
                                            </td>

                                        </tr>

                                    @endforeach

                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="">
                    <table class="table table-bordered table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th>Installment Amount</th>
                                <th>Date</th>
                                <th>Day</th>
                            </tr>
                        </thead>
                        <tbody id="newScheduleTbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-outline-info btn-lg waves-effect"> <i class="fa fa-save"></i>
                    Save</button>
            </div>

        </div>

    </form>


@endsection


@section('custom-js')
    <script>
        // hide reschedule inputs

        $('#rescheduleBtn').click(function(e) {
            e.preventDefault();
            getTotalAmount();
        });

        function toogleRescheduleInfo() {
            $('#processBtn').toggle();
            $('#type_div').toggle();
            $('#amount_div').toggle();
            $('#start_date_div').toggle();
        }

        toogleRescheduleInfo();


        function getTotalAmount() {
            let totalAmount = 0;

            $('.schedule_amt').each(function(index, element) {

                totalAmount += +$(element).val();
            });

            $('#totalRescheduleAmount').html(totalAmount);

            return totalAmount;
        }

        function validateinputs() {
            let amount = $('#amount').val();
            let start_date = $('#start_date').val();

            if (amount == "") {
                alert('Please enter Amount');
                return true;
            }

            if (start_date == "") {
                alert('Please enter Date');
                return true;
            }

        }


        function updateSingle(id) {

            let amount = $('.schedule_amt_' + id).val();
            let date = $('.schedule_date_' + id).val();

            // console.log(amount);
            // console.log(date);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('reschedule.installment.save') }}",
                data: {
                    id: id,
                    amount: amount,
                    date: date,
                },
                success: function(response) {
                    alert('Updated!');
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }


        function getNewSchedules() {

            let amount = $('#amount').val();
            let type = +$('#type').val();
            let start_date = $('#start_date').val();
            let customerId = $('#customerId').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ route('reschedule.installment.auto') }}",
                data: {
                    amount: amount,
                    start_date: start_date,
                    customerId: customerId,
                    type: type,
                    totalAmount: getTotalAmount(),
                },
                success: function(response) {
                    // console.log(response);
                    appendNewSchedules(response);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function appendNewSchedules(response) {

            $('#newScheduleTbody').html('');

            let data = response;

            for (const d in data) {
                if (Object.hasOwnProperty.call(data, d)) {
                    const obj = data[d];

                    let tr = `
                            <tr>
                                <td>
                                    ${obj.amount}
                                    <input type="hidden" class="form-control" name="newAmount[]" value="${obj.amount}"> 
                                </td>    
                                <td>
                                    <input type="text" class="form-control datepicker" name="newDate[]" value="${obj.date.date}"> 
                                </td>    
                                <td class="text-center">
                                    ${obj.date.day}
                                </td>    
                            </tr>
                        `;

                    $('#newScheduleTbody').append(tr);

                }

                $(".datepicker").datepicker({
                    format: 'dd-mm-yyyy',
                    changeMonth: true,
                    changeYear: true,

                });
            }
        }

        function removePreviousInstallments() {
            $('#oldInstallments').html('');
        }


        $(document).ready(function() {

            // on click Reschedule Button 
            $('#processBtn').click(function(e) {
                e.preventDefault();

                validateinputs();

                getNewSchedules();

                removePreviousInstallments();


            });


        });

    </script>
@endsection
