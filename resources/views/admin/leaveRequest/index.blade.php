@extends('admin.layouts.masterIndex')

@section('card_body')
<div class="card-body">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped"  name="dealerTable">
            <thead>
                <tr>
                    <th width="20px">SL</th>
                    <th>Employee Name</th>
                    <th width="200px">Leave From</th>
                    <th width="100px">Leave To</th>
                    <th width="20px">Duration</th>
                    <th width="20px" class="text-nowrap">Leave Type</th>
                    <th width="20px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaverequests as $leaverequest)
                <tr class="row_{{ $leaverequest->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $leaverequest->staffName }}</td>
                    <td>{{ $leaverequest->leave_from }}</td>
                    <td>{{ $leaverequest->leave_to }}</td>
                    <td>{{ $leaverequest->duration }}</td>
                    <td>{{ $leaverequest->leaveType }}</td>
                    <td>
                        @php
                        echo \App\Link::action($leaverequest->id);
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
    $(document).ready(function () {
        var updateThis;

        //ajax delete code
        $('#dataTable tbody').on('click', 'i.fa-trash', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            leaveReqId = $(this).parent().data('id');
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
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('leaveRequest.delete') }}",
                                data: {leaveReqId: leaveReqId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Dealer Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + leaveReqId).remove();
                                },
                                error: function (response) {
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
                        } else
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

</script>
@endsection