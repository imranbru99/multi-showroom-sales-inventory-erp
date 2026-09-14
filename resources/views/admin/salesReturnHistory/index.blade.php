@extends('admin.layouts.masterIndexSalesReturnHistory')

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped"  name="salesReturnHistory">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Return Date</th>
                        <th>Return Serial No.</th>
                        <th>Dealer Name</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sl = 1;
                    @endphp
                    @foreach ($allSalesReturn as $issueNo => $salesReturn)
                        <tr class="row_{{ $issueNo }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ date("d-m-Y", strtotime($salesReturn[0]->return_date)) }}</td>
                            <td>{{ $issueNo }}</td>
                            <td>{{ $salesReturn[0]->dealerName }}</td>
                            <td>{{ $salesReturn->sum('qty') }}</td>
                            <td>{{ $salesReturn->sum('amount') }}</td>
                            <td>
                                @php
                                    echo \App\Link::action($issueNo);
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

                dealerCollectionId = $(this).parent().data('id');
                console.log(dealerCollectionId);
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
                            url : "{{ route('salesReturn.delete') }}",
                            data : {dealerCollectionId:dealerCollectionId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Dealer Collection Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+dealerCollectionId).remove();
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
                            text: "Dealer Collection Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
    </script>
@endsection