@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="dealerTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th width="200px">Employee Name</th>
                        <th width="120px">Leave Name</th>
                        <th width="120px">From Date</th>
                        <th width="120px">To Date</th>
                        <th width="150px">Total Leave Days</th>
                        <th width="20px">Status</th>
                        <th width="50px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                    @endphp
                    @foreach ($employeeLeaves as $employeeLeave)
                        <tr class="row_{{ $employeeLeave->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ $employeeLeave->employeeName }}</td>
                            <td>{{ $employeeLeave->leaveName }}</td>
                            <td>{{ $employeeLeave->from_date }}</td>
                            <td>{{ $employeeLeave->to_date }}</td>
                            <td>{{ $employeeLeave->days }}</td>
                            <td>
                                @php
                                    echo \App\Link::status($employeeLeave->id,$employeeLeave->status);
                                @endphp
                            </td>
                            <td>
                                @php
                                    echo \App\Link::action($employeeLeave->id);
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
            var updateThis ;        

            //ajax delete code
            $('#dataTable tbody').on( 'click', 'i.fa-trash', function () {
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });

                employeeLeaveId = $(this).parent().data('id');
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
                function(isConfirm){   
                    if (isConfirm) {
                        $.ajax({
                            type: "POST",
                            url : "{{ route('employeeLeave.delete') }}",
                            data : {employeeLeaveId:employeeLeaveId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Data Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+employeeLeaveId).remove();
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
                    }
                    else
                    { 
                        swal({
                            title: "Cancelled", 
                            type: "error",
                            text: "Your Data Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
                
        //ajax status change code
        function statusChange(employeeLeaveId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('employeeLeave.status') }}",
                data: {employeeLeaveId:employeeLeaveId},
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
@endsection