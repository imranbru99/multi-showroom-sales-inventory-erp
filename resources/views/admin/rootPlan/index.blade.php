@extends('admin.layouts.masterIndex')

@section('custom_css')
    <style type="text/css">
        .table th {
            background: #00c292;
            text-align: center;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Date</th>
                        <th>Driver Name</th>
                        <th>Contact No</th>
                        <th>Vehicle No</th>
                        <th>Vehicle Capacity</th>
                        <th>Product Capacity</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rootPlans as $rootPlan)
                        <tr id="{{ $rootPlan->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d-m-Y', strtotime($rootPlan->date)) }}</td>
                            <td>{{ $rootPlan->driver_name }}</td>
                            <td>{{ $rootPlan->contact_no }}</td>
                            <td>{{ $rootPlan->vehicle->registration_no }}</td>
                            <td>{{ $rootPlan->vehicle_capacity }}</td>
                            <td>{{ $rootPlan->product_capacity }}</td>
                            <td>
                                @php
                    				echo \App\Link::action($rootPlan->id);
                    			@endphp
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {

            var updateThis;

            //ajax delete code
            $('#dataTable tbody').on('click', 'i.fa-trash', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                gatePassId = $(this).parent().data('id');
                // console.log(liftingId);
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
                                url: "{{ route('gatePass.delete') }}",
                                data: {
                                    id: gatePassId
                                },

                                success: function(response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "GatePass Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('#' + gatePassId).remove();
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
                                text: "GatePass Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
            });
        });
    </script>
@endsection
