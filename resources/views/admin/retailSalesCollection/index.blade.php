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
                        <th width="20px">SL</th>
                        <th width="150px">Date</th>
                        <th width="150px">Customer Name</th>
                        <th width="150px">Customer Code</th>
                        <th width="150px">MR. No</th>
                        <th width="150px">Collection Amount</th>
                        <th>Collector</th>
                        <th>User</th>
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
                    ajax: "{{ route('retailCollection.index') }}?project={{ $project_id }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'collection_date',
                            name: 'collection_date'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'customer_code',
                            name: 'customer_code'
                        },
                        {
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            data: 'collection_amount',
                            name: 'collection_amount'
                        },
                        {
                            data: 'collector',
                            name: 'collector',
                        },
                        {
                            data: 'user',
                            name: 'user',
                            searchable: false
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

                var table = $('#dtbwyj').DataTable();

                colectionId = $(this).parent().data('id');
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
                                url: "{{ route('retail.collection.delete') }}",
                                data: {
                                    colectionId: colectionId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Area Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    table.clear().draw();
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

                window.location.href = '{{ route('retailCollection.index') }}' + "?project=" +
                    project;

            });

        });
    </script>


    <script>
        var href = $('.add_new').attr('href');
        $('.add_new').attr('href', href + '?project={{ @$project_id }}');
    </script>
@endsection
