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
                        <th width="150px">Customer Code</th>
                        <th>Customer Name</th>
                        <th width="120px">National ID</th>
                        <th width="120px">Phone No</th>
                        <th>Address</th>
                        <th>Reference</th>
                        <th width="80px">Status</th>
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
            // Setup - add a text input to each footer cell
            $('#dtbwyj tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
            });

            $(function() {

                var table = $('#dtbwyj').DataTable({

                    initComplete: function() {
                        // Apply the search
                        this.api().columns().every(function() {
                            var that = this;

                            $('input', this.footer()).on('keyup change clear',
                                function() {
                                    if (that.search() !== this.value) {
                                        that
                                            .search(this.value)
                                            .draw();
                                    }
                                });
                        });
                    },

                    pageLength: 20,
                    processing: false,
                    serverSide: true,
                    searching: true,
                    ajax: "{{ route('customerRegistration.index') }}?project={{ $project_id }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'code',
                            name: 'code',
                        },
                        {
                            data: 'name',
                            name: 'name',
                            searchable: false
                        },
                        {
                            data: 'nid',
                            name: 'nid',
                            searchable: false
                        },
                        {
                            data: 'phone_no',
                            name: 'phone_no',
                            searchable: false
                        },
                        {
                            data: 'present_address',
                            name: 'present_address',
                            searchable: false
                        },
                        {
                            data: 'reference',
                            name: 'reference',
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
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

                customerId = $(this).parent().data('id');
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
                                url: "{{ route('customerRegistration.delete') }}",
                                data: {
                                    customerId: customerId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Area Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + customerId).remove();
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
        function statusChange(customerId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('customerRegistration.status') }}",
                data: {
                    customerId: customerId
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
                var table = $('#dtbwyj').DataTable();

                window.location.href = '{{ route('customerRegistration.index') }}' + "?project=" +
                    project;

            });

        });
    </script>

    <script>
        var href = $('.add_new').attr('href');
        $('.add_new').attr('href', href + '?project={{ @$project_id }}');
    </script>
@endsection
