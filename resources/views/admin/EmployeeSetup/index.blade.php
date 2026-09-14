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
                        <th width="200px">Name</th>
                        <th width="120px">Employee No.</th>
                        <th width="120px">designation</th>
                        <th width="120px">Mobile</th>
                        <th width="150px">Email</th>
                        <th width="130px">Company Name</th>
                        <th width="20px">Resigned</th>
                        <th width="20px">Status</th>
                        <th width="50px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 0;
                    @endphp
                    @foreach ($employies as $employee)
                        <tr class="row_{{ $employee->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->employee_no }}</td>
                            <td>{{ $employee->designationName }}</td>
                            <td>{{ $employee->mobile }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->showroomName }}</td>
                            <td align="center">
                                @if ($employee->resigned == 1)
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td>
                                @php
                                    echo \App\Link::status($employee->id,$employee->status);
                                @endphp
                            </td>
                            <td>
                                @php
                                    echo \App\Link::action($employee->id);
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

                employeeId = $(this).parent().data('id');
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
                            url : "{{ route('employeeSetup.delete') }}",
                            data : {employeeId:employeeId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Dealer Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+employeeId).remove();
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
                            text: "Your Dealer Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
                
        //ajax status change code
        function statusChange(employeeId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('employeeSetup.status') }}",
                data: {employeeId:employeeId},
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