@extends('admin.layouts.master')

@section('content')
    <style>
        .table thead {
            position: absolute;
            width: 100%;
            top: 123px;
            left: 9px;
        }

        .table tr {
            width: 163px;
        }

        .table th {
            background: #00c292;
            font-weight: bold !important;
            padding: 5px;
            font-size: 11px;
            /* width: 140px; */
        }

        .table td {
            width: 34.5%;
        }

        .tableFixHead {
            overflow-y: auto;
            overflow-x: hidden;
            height: 450px;
            border: 1px solid #00c292;
            margin-top: 80px;
        }

        .tableFixHead2 {
            height: 610px;
        }

        .info-table tr td {
            padding: 0 10px;
        }

        .info-table {
            margin-top: 30px;
        }

        .export-table thead {
            top: 51px !important;
        }

        .arrowBtn {
            margin-top: 320px;
        }

    </style>

    @include('admin.partials.loader')
    <div class="row">

        <div class="col-md-6">

            <div class="row">

                <div class="col-md-12">

                    <div class="card" style="margin-bottom: 0px;">
                        <div class="card-header">
                            <h1 class="text-center h3">{{ $title }}</h1>
                        </div>

                        <form action="{{ route('bulk.collection.add') }}" method="get">
                            <input type="hidden" name="searched" value="true">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="customers">Customers</label>
                                            <select name="customer" id="customers" class="form-control chosen-select">
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}" @if ($customerId == $customer->id) selected @endif>{{ $customer->name }} -
                                                        {{ $customer->code }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="submit" name="submit" value="Search"
                                            class="mt-4 btn btn-outline-info text-info">
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>






                <div class="col-md-12">
                    <form action="{{ route('bulk.collection.save') }}" method="post">
                        {{ csrf_field() }}

                        @if ($data)
                            <input type="hidden" name="customerId" value="{{ @$data['customer']->id }}">
                        @endif

                        @if ($data)
                            <table class="info-table">
                                <tr>
                                    <td><span class="font-weight-bold">Name</span> : {{ $data['customer']->name }}</td>
                                    <td>||</td>
                                    <td><span class="font-weight-bold">A/C No</span> : {{ $data['customer']->code }}</td>
                                </tr>
                                <tr>
                                    <td><span class="font-weight-bold">Total Collection</span> :
                                        {{ @$data['totalCollection'] }}</td>
                                    <td>||</td>
                                    <td><span class="font-weight-bold">Selected Collection</span> : <span
                                            id="selectedCollection"></span></td>
                                </tr>
                            </table>
                        @endif
                        <div class="tableFixHead">
                            <table class="table table-bordered table-sm dataTable no-footer leftTable">
                                <thead>
                                    <tr>
                                        <th style="width:122px;">Date</th>
                                        <th style="width:142px;">Collection Amount</th>
                                        <th style="width:125px;">Memo No.</th>
                                        <th style="width:122px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data)

                                        @foreach ($data['collection'] as $d)
                                            <tr>
                                                <td style="width:24.5%">{{ $d['date'] }}</td>
                                                <td style="width:29%;">
                                                    {{ $d['amount'] }}
                                                    <input type="hidden" class="amount amount_{{ $d['id'] }}"
                                                        name="amount[{{ $d['id'] }}]" value="{{ $d['amount'] }}">
                                                </td>
                                                <td style="width:25.5%;">
                                                    {{ $d['memo_no'] }}
                                                </td>
                                                <td style="width:50px;" class="text-center">
                                                    <input type="checkbox" onclick="getTotal()" name="id[]" class="id"
                                                        value="{{ $d['id'] }}" checked>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <input type="submit" name="Process" value="Process"
                            class="btn btn-outline-info btn-lg mt-3 text-right">
                    </form>

                </div>
            </div>

        </div>

        <div class="col-md-1">
            <div class="d-flex justify-content-center arrowBtn">
                <button class="btn btn-outline-info" onclick="moveLeft()">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="row mt-2">
                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <label for="date">Start Date</label>
                                <input type="text" id="startDate" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <label for="date">End Date</label>
                                <input type="text" id="endDate" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Memo</label>
                                <input type="text" id="memo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-right mt-4">
                                <button class="btn btn-outline-info btn-lg" id="getExportData">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="tableFixHead tableFixHead2">
                        <table class="table table-bordered table-sm dataTable no-footer export-table rightTable">
                            <thead>
                                <tr>
                                    <th style="width:105px;">Date</th>
                                    <th style="width:120px;">Collection Amount</th>
                                    <th style="width:105px;">Memo No.</th>
                                    <th style="width:92px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="exportData">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection


@section('custom-js')
    <script>
        $('.spinner-div').hide();


        // move rows
        function moveLeft() {

            let trs = "";

            $('.rightTrCheckbox').each(function(index, element) {

                if ($(element).is(':checked')) {

                    let leftTr = $(element).parent().parent();

                    let leftCheckbox = leftTr.children('.rightTdCheckbox').children('.rightTrCheckbox');

                    leftCheckbox.attr("checked", true);

                    let tr = `<tr>${leftTr.html()}</tr>`
                    trs += tr;

                    // console.log(tr);
                    leftTr.remove();

                }

            })

            // console.log(trs);

            $('.leftTable').append(trs);

        }



        // add checked numbers
        function getTotal() {

            let totalAmount = 0;

            $('.id').each(function(index, element) {

                if ($(element).is(':checked')) {

                    let amountValue = $('.amount_' + $(element).val()).val();

                    totalAmount += +amountValue;
                }

            });

            $('#selectedCollection').html(totalAmount);
        }


        $(function() {

            // get export data
            $('#getExportData').click(function(e) {
                e.preventDefault();

                let startDate = $('#startDate').val();
                let endDate = $('#endDate').val();
                let memo = $('#memo').val();

                $('.spinner-div').show();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('bulk.collection.getExportData') }}",
                    data: {
                        startDate: startDate,
                        endDate: endDate,
                        memo: memo,
                    },
                    success: function(response) {
                        let collections = response.collection;
                        renderInTable(collections);
                        $('.spinner-div').hide();
                    },
                    error: function(response) {
                        console.log(response);
                        $('.spinner-div').hide();
                    }
                });
            });


            // render export Data

            function renderInTable(collections) {

                $('#exportData').html('');

                for (const collection of collections) {
                    let tr = `
                                <tr class="rightTr">
                                    <td style="width:24.5%">${collection.date}</td>
                                    <td  style="width:29%;">
                                        ${collection.amount}
                                        <input type="hidden" class="amount amount_${collection.id}" name="amount[${collection.id}]"
                                            value="${collection.amount}">
                                    </td>
                                    <td style="width:25.5%;">
                                        ${collection.memo_no}
                                    </td>
                                    <td style="width:50px;" class="text-center rightTdCheckbox">
                                        <input type="checkbox" onclick="getTotal()" name="id[]" class="id rightTrCheckbox" value="${collection.id}">
                                    </td>
                                </tr>
                            `;

                    $('#exportData').append(tr);

                }

            }

        });

    </script>
@endsection
