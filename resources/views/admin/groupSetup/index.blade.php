@extends('admin.layouts.masterIndex')

@php
    use App\StaffSetup;
@endphp

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="vehicleTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Name</th>
                        <th>Team Lead Name</th>
                        <th>Team Members Name</th>
                        <th width="20px">Status</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                	@php
                		$sl = 0;
                	@endphp
                	@foreach ($groups as $group)
                		<tr class="row_{{ $group->id }}">
                			<td>{{ $sl++ }}</td>
                			<td>{{ $group->name }}</td>
                			<td>{{ $group->teamLeaderName }}</td>
                            @php
                                $memberName = "";
                                $teamMembers = DB::select(DB::raw('SELECT name FROM tbl_staffs WHERE id IN ('.$group->team_member.')'));
                            
                                $loop = count($teamMembers);
                                foreach ($teamMembers as $teamMember)
                                {
                                    if ($loop == 1)
                                    {
                                        $memberName .= $teamMember->name;
                                    }
                                    else
                                    {
                                        $memberName .= $teamMember->name.", ";
                                        $loop--;
                                    }                                    
                                }
                            @endphp
                			<td>{{ $memberName }}</td>
                			<td>
                				<?php echo \App\Link::status($group->id,$group->status)?>
                			</td>
                			<td>
                    			@php
                    				echo \App\Link::action($group->id);
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

                groupId = $(this).parent().data('id');
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
                            url : "{{ route('groupSetup.delete') }}",
                            data : {groupId:groupId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Vehicle Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+groupId).remove();
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
                            text: "Your Group Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
                
        //ajax status change code
        function statusChange(groupId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('groupSetup.status') }}",
                data: {groupId:groupId},
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