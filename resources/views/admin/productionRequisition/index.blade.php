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
                        <th>Requisition Date</th>
                        <th>Requisition No.</th>
                        <th>Requisition Name</th>
                        <th>Total Qty</th>
                        <th width="20px">Action</th>
                    </tr>
                </thead>
                <tbody id="">
                    @php
                        $sl = 1;
                    @endphp
                    @foreach ($productionRequisition as $productionRequisitio)
                        <tr class="row_{{ $productionRequisitio->id }}">
                            <td>{{ $sl++ }}</td>
                            <td>{{ date('d-m-Y', strtotime($productionRequisitio->date)) }}</td>
                            <td>{{ $productionRequisitio->requisition_no }}</td>
                            <td>{{ $productionRequisitio->staffName }}</td>
                            <td>{{ $productionRequisitio->total_qty }}</td>
                          
                            <td align="center">
                                @if ($productionRequisitio->status == 0)
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#detailRequisitionModal" onclick="showDealerRequisitionDetails({{ $productionRequisitio->id }})">Approved <span class="badge badge-light">{{ $productionRequisitio->total_approve_qty }}</span></button>
                                @else
                                    @php
                                        echo \App\Link::action($productionRequisitio->id);
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
                    <h3 class="modal-title p_name" id="p_name"></h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body">
                    <table class="table table-bordered table-sm detailRequisitionProduct">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="80px">Qty</th>
                                
                            </tr>
                        </thead>

                        <tbody id="tbody">
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2" align="right" style="vertical-align: middle;">
                                    <h4>Total</h4>
                                </td>
                                <td id="totalQty" align="right"></td>
                                
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

                productionRequisitionId = $(this).parent().data('id');
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
                            url : "{{ route('productionRequisition.delete') }}",
                            data : {productionRequisitionId:productionRequisitionId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Dealer Requisition Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+productionRequisitionId).remove();
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

        function showDealerRequisitionDetails(productionRequisitionId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('productionRequisition.requisitionProductInfo') }}",
                data:{productionRequisitionId:productionRequisitionId},
                success: function(response) {
                    $('.requisitionProductRow').remove();
                    var productionRequisition = response.productionRequisition;
                    var productionRequisitionInfo = response.productionRequisitionInfo;
                    var approveQty;

                    $('#p_name').html(productionRequisition.staffName);
                    $('#totalQty').html(productionRequisition.total_qty);
                    $('#productionRequisitionId').html(productionRequisition.id);
                   

                    for (var productionRequisitionInf of productionRequisitionInfo)
                    {
                        $(".detailRequisitionProduct tbody").append(
                            '<tr class="requisitionProductRow" id="requisitionProductRow_'+productionRequisitionInf.id+'">' +
                                '<td>'+productionRequisitionInf.productName+'</td>'+
                                '<td>'+productionRequisitionInf.model_no+'</td>'+
                               
                                '<td align="right">'+productionRequisitionInf.qty+'</td>'+
                               
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