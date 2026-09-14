@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="row d-flex justify-content-center">
        <div class="col-md-5">
            <div class="form-group d-flex">
                <label class="font-weight-bold text-nowrap mt-2 mr-1" for="project">Select Project *</label>
                <select name="project" id="project" class="form-control chosen-select">
                    <option value="">Select Project *</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @if ($project_id == $project->id) selected @endif>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>




    <div class="card-body">
        <div class="table-responsive">
            <table id="dtbwyj" class="table table-bordered table-striped" name="showroomTable">
                <thead>
                    <tr>
                        <th width="20px" style="background: #00c292;font-weight: bold;padding: 5px;">SL</th>
                        <th>Date</th>
                        <th width="100px">Invoice</th>
                        <th width="150px">Customer Code</th>
                        <th width="150px">Customer Name</th>
                        <th width="105px">Sales Price</th>
                        <th width="70px">Discount</th>
                        <th width="110px">Gift Voucher</th>
                        <th width="125px">Exchange CRT</th>
                        <th width="80px">Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {


            $(function() {

                var table = $('#dtbwyj').DataTable({

                    pageLength: 20,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('retailSales.return.index') }}?project={{ $project_id }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            data: 'customer_code',
                            name: 'customer_code'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'sale_price',
                            name: 'sale_price'
                        },
                        {
                            data: 'discount',
                            name: 'discount'
                        },
                        {
                            data: 'gift_voucher',
                            name: 'gift_voucher'
                        },
                        {
                            data: 'exchange_crt',
                            name: 'exchange_crt'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        },
                    ]

                });

            });







            var updateThis;

            //ajax delete code
            $('#dtbwyj tbody').on('click', 'i.fa-trash', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                retailSalesReturnId = $(this).parent().data('id');
                var tableRow = this;
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this information!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('retailSales.return.delete') }}",
                                data: {
                                    retailSalesReturnId: retailSalesReturnId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Area Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('#row_' + retailSalesReturnId).remove();
                                },
                                error: function(response) {
                                    error = "Failed.";
                                    swal({
                                        title: "<small class='text-danger'>Error!</small>",
                                        type: "error",
                                        text: error,
                                        timer: 1000,
                                        html: true,
                                    });
                                }
                            });
                        } else {
                            swal({
                                title: "Cancelled",
                                type: "error",
                                text: "This Area Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
            });
        });

        //ajax status change code
        function statusChange(retailSalesId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('retailSales.status') }}",
                data: {
                    retailSalesId: retailSalesId
                },
                success: function(response) {
                    swal({
                        title: "<small class='text-success'>Success!</small>",
                        type: "success",
                        text: "Status Successfully Updated!",
                        timer: 1000,
                        html: true,
                    });
                },
                error: function(response) {
                    error = "Failed.";
                    swal({
                        title: "<small class='text-danger'>Error!</small>",
                        type: "error",
                        text: error,
                        timer: 2000,
                        html: true,
                    });
                }
            });
        }
    </script>

    <script>
        $(function() {
            $('#project').change(function(e) {
                e.preventDefault();

                var project = $('#project').val();

                window.location.href = '{{ route('retailSales.return.index') }}' + "?project=" +
                    project;

            });

        });
    </script>


    <script>
        var href = $('.add_new').attr('href');
        $('.add_new').attr('href', href + '?project={{ @$project_id }}');
    </script>
@endsection
