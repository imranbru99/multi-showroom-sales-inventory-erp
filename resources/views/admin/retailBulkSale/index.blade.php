@extends('admin.layouts.master')

@section('content')
    <style>
        .table thead {
            position: absolute;
            width: 100%;
            top: -27px;
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
            width: 140px;
        }

        .table td {
            width: 34.5%;
        }

        .tableFixHead {
            overflow-y: auto;
            overflow-x: hidden;
            height: 380px;
            border: 1px solid #00c292;
        }

        .info-table tr td {
            padding: 0 10px;
        }

    </style>


    <div class="card" style="margin-bottom: 0px;">
        <div class="card-header">
            <h1 class="text-center h3">{{ $title }}</h1>
        </div>

        <form action="{{ route('bulk.sale.add') }}" method="get">
            <input type="hidden" name="searched" value="true">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="customers">Customers</label>
                            <select name="customer" id="customers" class="form-control chosen-select">
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        @if ($customer->id == $customerId)
                                            selected
                                        @endif
                                        >{{ $customer->name }} - {{ $customer->code }}
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

    <div class="row mt-2">
        <div class="col-md-5">
            @if ($data)
                <table class="info-table">
                    <tr>
                        <td><span class="font-weight-bold">Name</span> : {{ $data['customer']->name }}</td>
                        <td>||</td>
                        <td><span class="font-weight-bold">A/C No</span> : {{ $data['customer']->code }}</td>
                    </tr>
                    <tr>
                        <td><span class="font-weight-bold">Total Sale</span> : {{ @$data['totalSale'] }}</td>
                        <td>||</td>
                        <td><span class="font-weight-bold">Selected Sale</span> : <span id="selectedCollection"></span></td>
                    </tr>
                </table>
            @endif
        </div>
        <div class="col-md-3 offset-md-2">
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" id="date" class="form-control datepicker">
            </div>
        </div>
        <div class="col-md-1">
            <div class="text-right mt-4">
                <button class="btn btn-outline-info btn-lg" id="getExportData">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Export
                </button>
            </div>
        </div>
    </div>

    <form action="{{ route('bulk.sale.save') }}" method="post">
        {{ csrf_field() }}

        @if ($data)
            <input type="hidden" name="customerId" value="{{ $data['customer']->id }}">
        @endif
        
        <div class="row mt-2">

            <div class="col-md-5">
                <div class="tableFixHead">
                    <table class="table table-bordered table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th style="width:105px;">Date</th>
                                <th style="width:120px;">Sale Amount</th>
                                <th style="width:105px;">Memo No.</th>
                                <th style="width:92px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data)

                                @foreach ($data['sale'] as $d)
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
            </div>

            <div class="col-md-5 offset-md-2">
                <div class="tableFixHead">
                    <table class="table table-bordered table-sm dataTable no-footer">
                        <thead>
                            <tr>
                                <th style="width:105px;">Date</th>
                                <th style="width:120px;">Sale Amount</th>
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

        <div class="row mt-5">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-outline-info btn-lg waves-effect"> <i class="fa fa-save"></i>
                    Save</button>
            </div>
        </div>

    </form>

@endsection


@section('custom-js')
    <script>
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

                let date = $('#date').val();

                // if(date == ""){
                //     alert('Please Select a Date');
                // }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "GET",
                    url: "{{ route('bulk.sale.getExportData') }}",
                    data: {
                        date: date,
                    },
                    success: function(response) {
                        let collections = response.sale;
                        renderInTable(collections);
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });


            // render export Data

            function renderInTable(collections) {

                $('#exportData').html('');

                for (const sale of collections) {
                    let tr = `
                    <tr>
                        <td style="width:24.5%">${sale.date}</td>
                        <td style="width:29%;">
                            ${sale.amount}
                            <input type="hidden" class="amount amount_${sale.id}" name="amount[${sale.id}]"
                                value="${sale.amount}">
                        </td>
                        <td style="width:25.5%;">
                            ${sale.memo_no}
                        </td>
                        <td style="width:50px;" class="text-center">
                            <input type="checkbox" onclick="getTotal()" name="id[]" class="id" value="${sale.id}">
                        </td>
                    </tr>
                `;

                    $('#exportData').append(tr);

                }

            }

        });

    </script>
@endsection
