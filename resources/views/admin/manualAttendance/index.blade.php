@extends('admin.layouts.master')

@php
use App\ManualAttendance;
@endphp

@section('content')
<div class="card">            
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            <div class="col-md-6 text-right">
                <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route($addNewLink) }}">
                    <i class="fa fa-plus-circle"></i> Add New
                </a>                  
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped"  name="employeeAllowanceTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Date</th>
                        <th width="140px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($manualAttendences as $manualAttendence)
                    <tr class="{{ $manualAttendence->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $manualAttendence->date }}</td>
                        <td>
                            @php
                            echo \App\Link::action($manualAttendence->id);
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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

            attendanceId = $(this).parent().data('id');
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
                                url: "{{ route('manualAttendance.delete') }}",
                                data: {attendanceId: attendanceId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Data Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.' + attendanceId).remove();
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
                                text: "Your Data Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });
    });

</script>
@endsection