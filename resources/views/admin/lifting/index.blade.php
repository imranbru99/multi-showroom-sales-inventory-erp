@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="liftingTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Voucher No.</th>
                        <th>Voucher Date</th>
                        <th>Supplier</th>
                        <th>Store Name</th>
                        <th>Purchase By</th>
                        <th>Quanty</th>
                        <th>Amount</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                	@php
                		$sl = 0;
                	@endphp
                	@foreach ($liftings as $lifting)
                        @php
                            $storeOrShowroom = DB::table('view_store_and_showroom')
                                ->select('name as storeOrShowroomName')
                                ->where('type',$lifting->store_or_showroom_type)
                                ->where('id',$lifting->store_or_showroom_id)
                                ->first();
                        @endphp
                		<tr class="row_{{ $lifting->id }}">
	                		<td>{{ $sl++ }}</td>
	                		<td>{{ $lifting->vaouchar_no }}</td>
	                		<td>{{ date('d-m-Y', strtotime($lifting->vouchar_date)) }}</td>
	                		<td>{{ $lifting->vendorName }}</td>
                            <td>{{ @$storeOrShowroom->storeOrShowroomName }}</td>
	                		<td>{{ $lifting->purchase_by }}</td>
	                		<td>{{ $lifting->total_qty }}</td>
	                		<td>{{ $lifting->total_price }}</td>
	                		<td>
	                			@php
	                				echo \App\Link::action($lifting->id);
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

                liftingId = $(this).parent().data('id');
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
                function(isConfirm){   
                    if (isConfirm) {
                        $.ajax({
                            type: "POST",
                            url : "{{ route('lifting.delete') }}",
                            data : {liftingId:liftingId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Vendor Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+liftingId).remove();
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
                            text: "Your Lifting Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
    </script>
@endsection