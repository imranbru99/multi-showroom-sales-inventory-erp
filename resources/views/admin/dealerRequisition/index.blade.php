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

            <table id="dataTable" class="table table-bordered table-striped" name="dealerRequisitionTable">
                <thead>
                    <tr>
                        <th width="20px">SL</th>
                        <th>Date</th>
                        <th>Requisition No.</th>
                        <th>Dealer Name</th>
                        <th>Total Qty</th>
                        <th>Total Amount</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @php
                        $sl = 1;
                    @endphp
                    @foreach ($dealerRequisitions as $dealerRequisition)
                        <tr class="row_{{ $dealerRequisition->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ date('d-m-Y', strtotime($dealerRequisition->date)) }}</td>
                            <td>{{ $dealerRequisition->requisition_no }}</td>
                            <td>{{ $dealerRequisition->dealerName }}</td>
                            <td>{{ $dealerRequisition->total_qty }}</td>
                            <td>{{ $dealerRequisition->total_amount }}</td>
                            <td align="center">
                                @if ($dealerRequisition->approved_by2 != null)
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#detailRequisitionModal" onclick="showDealerRequisitionDetails({{ $dealerRequisition->id }})">Approved <span class="badge badge-light">{{ $dealerRequisition->total_approve_qty }}</span></button>
                                @else
                                    @php
                                        echo \App\Link::action($dealerRequisition->id);
                                    @endphp
                                @endif                            
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- The Modal --}}
    <div class="modal fade" id="detailRequisitionModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h3 class="modal-title dealerName" id="dealerName"></h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body">
                    <table class="table table-bordered table-sm detailRequisitionProduct">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="100px">Rate</th>
                                <th width="80px">Qty</th>
                                <th width="100px">Amount</th>
                                <th width="100px">Approve Qty</th>
                                <th width="120px">Approve Amount</th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="3" align="right" style="vertical-align: middle;">
                                    <h4>Total</h4>
                                </td>
                                <td id="totalQty" align="right"></td>
                                <td id="totalAmount" align="right"></td>
                                <td id="totalApproveQty" align="right"></td>
                                <td id="totalApproveAmount" align="right"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Modal footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-lg waves-effect" data-dismiss="modal">Close</button>
                </div>

            </div>
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

                dealerRequisitionId = $(this).parent().data('id');
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
                            url : "{{ route('dealerRequisition.delete') }}",
                            data : {dealerRequisitionId:dealerRequisitionId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Dealer Requisition Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+dealerRequisitionId).remove();
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
                            text: "Dealer Requisition Is Safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            });
        });

        function showDealerRequisitionDetails(dealerRequisitionId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('dealerRequisition.requisitionProductInfo') }}",
                data:{dealerRequisitionId:dealerRequisitionId},
                success: function(response) {
                    $('.requisitionProductRow').remove();
                    var dealerRequisition = response.dealerRequisition;
                    var dealerRequisitionProducts = response.dealerRequisitionProducts;
                    var approveQty;
                    var approveAmount;

                    $('#dealerName').html(dealerRequisition.dealerName);
                    $('#totalQty').html(dealerRequisition.total_qty);
                    $('#totalAmount').html(dealerRequisition.total_amount);
                    $('#dealerRequisitionId').html(dealerRequisition.id);
                    $('#totalApproveQty').html(dealerRequisition.total_approve_qty);
                    $('#totalApproveAmount').html(dealerRequisition.total_approve_amount);

                    for (var dealerRequisitionProduct of dealerRequisitionProducts)
                    {
                        $(".detailRequisitionProduct tbody").append(
                            '<tr class="requisitionProductRow" id="requisitionProductRow_'+dealerRequisitionProduct.id+'">' +
                                '<td>'+dealerRequisitionProduct.productName+'</td>'+
                                '<td>'+dealerRequisitionProduct.model_no+'</td>'+
                                '<td align="right">'+dealerRequisitionProduct.price+'</td>'+
                                '<td align="right">'+dealerRequisitionProduct.qty+'</td>'+
                                '<td align="right">'+dealerRequisitionProduct.amount+'</td>'+
                                '<td align="right">'+dealerRequisitionProduct.approved_qty+'</td>'+
                                '<td align="right">'+dealerRequisitionProduct.approved_amount+'</td>'+
                            '</tr>'
                        );
                    }
                },
                error: function(response) {

                }
            });
        }
    </script>
@endsection