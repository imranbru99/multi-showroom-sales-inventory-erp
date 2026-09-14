@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="areaTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Group Name</th>
                        <th width="150px">Year</th>
                        <th width="150px">Month</th>
                        <th width="150px">Target Amount</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                	@php
                		$sl = 0;
                	@endphp
                	@foreach ($groupSalesTergets as $groupSalesTerget)
                		<tr class="row_{{ $groupSalesTerget->id }}">
                			<td>{{ $sl++ }}</td>
                			<td>{{ $groupSalesTerget->groupName }}</td>
                			<td>{{ $groupSalesTerget->year }}</td>
                			@php
                				$dateObj   = DateTime::createFromFormat('!m', $groupSalesTerget->month);
                				$monthName = $dateObj->format('F');
                			@endphp
                			<td>{{ $monthName }}</td>
                			<td>{{ $groupSalesTerget->total_target_amt }}</td>
                			<td>
                    			@php
                    				echo \App\Link::action($groupSalesTerget->id);
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

                groupSalesTargetId = $(this).parent().data('id');
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
                            url : "{{ route('groupSalesTargetSetup.delete') }}",
                            data : {groupSalesTargetId:groupSalesTargetId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Area Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+groupSalesTargetId).remove();
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
                            text: "Your User is safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
    </script>
@endsection