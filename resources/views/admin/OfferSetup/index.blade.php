@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;

            @endphp

            <table id="dataTable" class="table table-bordered table-striped"  name="categoriesTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Offer Name</th>
                         <th>Product Name</th>
                       <!--  <th>Start Date</th> 
                        <th>End Date</th>  -->
                        <th>Offer Type</th>
                        <th>Amount</th>
                       
                        <th width="20px">Status</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                	@foreach ($offers as $offer)
                    @php
                		$sl = 0;
                        $productName = DB::table('tbl_products')->where('id',$offer->product_id)->first();  
                       if (!empty($productName)){
                          $productName = $productName->name;
                      }else{
                          $productName =0;
                      }
                	@endphp
                		<tr class="row_{{ $offer->id }}">
                			<td>{{ $sl++ }}</td>
                			<td>{{$offer->offerName}}</td>
                            <td>{{$productName}}</td>
                            <!-- <td>{{$offer->start_date}}</td>
                            <td>{{$offer->end_date}}</td> -->
                            <td>{{$offer->offer_type}}</td>
                            <?php
                            if($offer->amount == ''){ ?>
                                  <td>{{$offer->perAmount}}%</td>
                            <?php
                            }else{
                                ?>
                            <td>{{$offer->amount}}</td>
                                <?php
                                }
                            ?>
                			<td>
                				<?php echo \App\Link::status($offer->id,$offer->status)?>
                			</td>
                			<td>
                    			@php
                    				echo \App\Link::action($offer->id);
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

                offerId = $(this).parent().data('id');
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
                            url : "{{ route('offer.delete') }}",
                            data : {offerId:offerId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "This Offer Info Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+offerId).remove();
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
                            text: "Your Offer Info Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
                
        //ajax status change code
        function statusChange(offer_id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: "{{ route('offer.status') }}",
                data: {offerId:offer_id},
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