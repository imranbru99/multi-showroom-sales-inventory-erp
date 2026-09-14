@extends('admin.layouts.master')

@section('content')

<div style="padding-bottom: 10px;"></div>

@php
$message = Session::get('msg');
@endphp

@if (isset($message))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Success!</strong> {{ $message }}
</div>
@endif

@php
Session::forget('msg');
@endphp

<div class="card">            
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            <div class="col-md-6 text-right">
                <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" target="_blank" href="{{ route('staffSetup.print') }}">
                    <i class="fa fa-print"></i> Print
                </a>
                <a style="font-size: 16px;" class="btn btn-outline-info btn-lg" href="{{ route($addNewLink) }}">
                    <i class="fa fa-plus-circle"></i> Add New
                </a>                  
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            @php
            $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="staffTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Company Name</th>
                        <th>Branch Name</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Joining Date</th>
                        <th>National Id</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th width="20px">Status</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @foreach ($staffs as $staff)
                    <tr class="row_{{ $staff->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-nowrap">{{ $staff->company->name }}</td>
                        <td class="text-nowrap">
                            <?php
                                $branchs = [];
                                if($staff->showroom_id){
                                    $branchs = explode(',', $staff->showroom_id);
                                }
                                $branchs = \App\ShowroomSetup::whereIn('id', $branchs)->get();
                            ?>
                            @foreach($branchs as $branch)
                            {{ $branch->name }} <br>
                            @endforeach
                        </td>
                        <td>{{ $staff->code }}</td>
                        <td>{{ $staff->name }}</td>
                        <td class="text-nowrap">{{ date('d-m-Y',strtotime($staff->joining_date))}}</td>
                        <td>{{ $staff->national_id }}</td>
                        <td>{{ $staff->contact }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>{{ $staff->address }}</td>
                        <td>
                            <?php echo \App\Link::status($staff->id, $staff->status) ?>
                        </td>
                        <td>
                            @php
                            echo \App\Link::action($staff->id);
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

            staffId = $(this).parent().data('id');
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
                                url: "{{ route('staffSetup.delete') }}",
                                data: {staffId: staffId},

                                success: function (response) {
                                    swal({
                                        title: "<small class='text-success'>Success!</small>",
                                        type: "success",
                                        text: "Staff Deleted Successfully!",
                                        timer: 1000,
                                        html: true,
                                    });
                                    $('.row_' + staffId).remove();
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
                                text: "Your Staff Is Safe :)",
                                timer: 1000,
                                html: true,
                            });
                        }
                    });
        });
    });

    //ajax status change code
    function statusChange(staffId) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('staffSetup.status') }}",
            data: {staffId: staffId},
            success: function (response) {
                swal({
                    title: "<small class='text-success'>Success!</small>",
                    type: "success",
                    text: "Status Successfully Updated!",
                    timer: 1000,
                    html: true,
                });
            },
            error: function (response) {
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