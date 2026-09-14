@extends('admin.layouts.masterIndex')

@section('custom_css')
    <style type="text/css">
        .table th{
            background: #00c292;
            text-align: center;
        }
    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="table-responsive">
            @php
                $sl = 0;
            @endphp

            <table id="dataTable" class="table table-bordered table-striped" name="productIssueTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th width="100px">Date</th>
                       <!--  <th width="110px">Issue Type</th> -->
                        <th width="100px">Issue No.</th>
                        <th>Dealer Name</th>
                        <th width="100px">Total Qty</th>
                        <th width="110px">Total Amount</th>
                        <th width="100px">User</th>
                        <th width="100px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @php
                        $sl = 1;
                    @endphp
                    @foreach ($issuedProducts as $issuedProduct)
                        <tr class="row_{{ $issuedProduct->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ date('d-m-Y', strtotime($issuedProduct->date)) }}</td>
                           <!--  <td>{{ $issuedProduct->issue_type }}</td> -->
                            <td>{{ $issuedProduct->issue_no }}</td>
                            <td>{{ $issuedProduct->dealerName }}</td>
                            <td align="right">{{ $issuedProduct->total_qty }}</td>
                            <td align="right">{{ $issuedProduct->total_amount }}</td>
                            <td align="center">{{ @$issuedProduct->user->name }}</td>
                            <td>
                                @php
                                    echo \App\Link::action($issuedProduct->id);
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

                productIssueId = $(this).parent().data('id');
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
                            url : "{{ route('productIssue.delete') }}",
                            data : {productIssueId:productIssueId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Issued product Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+productIssueId).remove();
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
                            text: "Issued Product Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });
    </script>
@endsection