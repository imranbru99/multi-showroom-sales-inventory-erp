@extends('admin.layouts.masterAddEdit')

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

@section('card_body')
    <div class="card-body">

        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd;">Sales
                    Return</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="return-date">Invoice No</label>
                            <input type="hidden" class="form-control" name="return_id"
                                value="{{ $retailSalesReturn->id }}">
                            <input type="text" class="form-control" name="invoice_no"
                                value="{{ $retailSalesReturn->invoice_no }}">
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="return-date">Date</label>
                            <input type="text" class="form-control datepicker" name="date"
                                value="{{ date('d-m-Y', strtotime($retailSalesReturn->date)) }}">
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="project_id">Project</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        <option value="">Select Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" @if ($retailSalesReturn->project_id == $project->id) selected @endif>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="customerId">Customer</label>
                    <select name="customerId" id="customerId" class="form-control chosen-select">
                        <option value="">Select a customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @if ($customer->id == $retailSalesReturn->customer_id)
                                selected
                        @endif
                        >{{ $customer->name }} ({{ $customer->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>


        <table class="table table-bordered mt-5">
            <thead>
                <tr>
                    <th>Sale Date</th>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Product Model</th>
                    <th>Return</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($retailSalesReturn->products as $product)
                    <tr>
                        <td>{{ date('d-m-Y', strtotime($retailSalesReturn->date)) }}</td>
                        <td>{{ $product->product->name }}</td>
                        <td>{{ $product->sales_price }}</td>
                        <td>{{ $product->product->model_no }}</td>
                        <td>
                            <input type="checkbox" name="ids[]" value="{{ $product->sales_id }}" checked>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection

@section('custom-js')
    <script>
        $('#customerId').change(function(e) {
            e.preventDefault();

            let customerId = $(this).val();
            $('tbody').html('');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ route('retailSales.return.customerProducts') }}",
                data: {
                    customerId: customerId
                },

                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(item, index) {
                            const d = new Date(item.sale.sale_date);

                            var tr = '<tr>' +
                                '<td>' + d.getDate() + '-' + (d.getMonth() + 1) + '-' + d
                                .getFullYear() + '</td>' +
                                '<td>' + item.product.name + '</td>' +
                                '<td>' + item.sales_price * item.qty + '</td>' +
                                '<td>' + item.product.model_no + '</td>' +
                                '<td><input type = "checkbox" name = "ids[]" value = "' + item
                                .id + '"> </td>' +
                                '</tr>';
                            $('tbody').append(tr);
                        });
                    }
                },
                error: function(response) {
                    console.log(response);
                }
            });

        });
    </script>


    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$retailSalesReturn->project_id }}');

        $('#project_id').change(function() {
            var selected = $('#project_id').val();
            $('.go_back').attr('href', href + '?project=' + selected);
        });
    </script>
@endsection
