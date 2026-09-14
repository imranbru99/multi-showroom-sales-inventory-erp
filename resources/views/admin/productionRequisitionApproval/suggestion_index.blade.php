@extends('admin.layouts.master')

@section('custom_css')
<style type="text/css">
    .table th {
        background: #00c292;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <h4 class="card-title" id="title">{{ $title }}</h4>
            </div>

            <div class="col-md-4">
                <button class="btn btn-outline-dark btn-lg" id="pendingButton" style="width: 100%">Click For Pending
                    Requisition</button>
                <button class="btn btn-outline-dark btn-lg" id="approveButton" style="width: 100%">Click For Suggest
                    Requisition</button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div id="pendingSection">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">SL</th>
                                <th width="50px">Date</th>
                                <th width="50px">Requisition No.</th>
                                <th width="350px">Requition Name</th>
                                <th width="50px">Total Qty</th>
                                <th width="20px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sl = 1;
                            @endphp
                            @foreach ($productionRequisition as $dealerRequisition)
                            <tr class="row_{{ $dealerRequisition->id }}">
                                <td>{{ $sl++ }}</td>
                                <td>{{ date('d-m-Y', strtotime($dealerRequisition->date)) }}</td>
                                <td>{{ $dealerRequisition->requisition_no }}</td>
                                <td>{{ $dealerRequisition->staff->name }}</td>
                                <td align="right">{{ $dealerRequisition->total_qty }}</td>
                                <td align="center"><button class="btn btn-outline-success btn-sm" data-toggle="modal"
                                        data-target="#detailRequisitionModal"
                                        onclick="showDetailRequisition({{ $dealerRequisition->id }})">Details</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="approveSection">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th width="20px">SL</th>
                                <th width="50px">Date</th>
                                <th width="50px">Requisition No.</th>
                                <th width="350px">Requition Name</th>
                                <th width="50px">Total Qty</th>
                                <th width="20px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sl = 1;
                            @endphp
                            @foreach ($approveproductionRequisition as $approveDealerRequisition)
                            <tr class="row_{{ $approveDealerRequisition->id }}">
                                <td>{{ $sl++ }}</td>
                                <td>{{ date('d-m-Y', strtotime($approveDealerRequisition->date)) }}</td>
                                <td>{{ $approveDealerRequisition->requisition_no }}</td>
                            <td>{{ $approveDealerRequisition->staff->name }}</td>
                                <td align="right">{{ $approveDealerRequisition->total_qty }}</td>
                                <td align="center"><button class="btn btn-outline-success btn-sm" data-toggle="modal"
                                        data-target="#detailRequisitionModal"
                                        onclick="showDetailRequisition({{ $approveDealerRequisition->id }})">Details</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form-horizontal" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}

    {{-- The Modal --}}
    <div class="modal fade" id="detailRequisitionModal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h3 class="modal-title req_Name" id="req_Name"></h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                {{-- Modal body --}}
                <div class="modal-body">
                    <table class="table table-bordered table-sm detailRequisitionProduct">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th width="200px">Model</th>
                                <th width="100px">Qty</th>
                                <th width="100px">Suggestion Qty</th>
                               
                            </tr>
                        </thead>

                        <tbody id="tbody">
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2" align="right" style="vertical-align: middle;">
                                    <h3>Total</h3>
                                    <input class="form-control productionRequisitionId" type="hidden"
                                        name="productionRequisitionId" id="productionRequisitionId" value="">
                                    <input class="form-control" type="hidden" name="approveBy" id="approveBy"
                                        value="{{ Auth::user()->id }}">
                                </td>
                                <td><input style="text-align: right;" class="form-control totalQty" id="totalQty"
                                        type="text" name="totalQty" value=""></td>
                            
                                <td><input style="text-align: right;" class="form-control totalSuggestionQty"
                                        id="totalSuggestionQty" type="number" name="totalSuggestionQty" value="0"
                                        readonly></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Modal footer --}}
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect btnName saveBtn"><i
                            class="fa fa-save"></i> {{ $buttonName }}</button>
                    <button type="button" class="btn btn-outline-danger btn-lg waves-effect"
                        data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</form>
@endsection

@section('custom-js')
<script>
    $(document).ready(function() {
            $('#approveSection').hide();
            $('#pendingButton').hide();

            $('#approveButton').click(function(){
                $('#title').html('All Approve Requisitions')
                $('#approveButton').hide();
                $('#pendingButton').show();
                $('#approveSection').show();
                $('#pendingSection').hide();
            });

            $('#pendingButton').click(function(){
                $('#title').html('All  Pending Requisitions')
                $('#approveButton').show();
                $('#pendingButton').hide();
                $('#approveSection').hide();
                $('#pendingSection').show();
            });
        });

        function showDetailRequisition(productionRequisitionId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('productionRequisitionApproval.productionRequisitionInfo') }}",
                data:{productionRequisitionId:productionRequisitionId},
                success: function(response) {
                    $('.requisitionProductRow').remove();
                    var productionRequisition = response.productionRequisition;
                    var productionRequisitionInfo = response.productionRequisitionInfo;
                    var approveQty;
                  

                    $('.req_Name').html(productionRequisition.staff.name);
                    $('.totalQty').val(productionRequisition.total_qty);
                    $('.productionRequisitionId').val(productionRequisition.id);

                    if (productionRequisition.total_approve_qty != null)
                    {
                        $('.totalApproveQty').val(productionRequisition.total_approve_qty);
                    }


                    if(productionRequisition.suggested > 0){
                        $('.saveBtn').hide();
                    }

                    if (productionRequisition.status == 0)
                    {
                        $('.btnName').html('Update');
                    }
                    else
                    {
                        $('.btnName').html('Save');
                    }

                    let totalSuggQty = 0;
                    


                    for (var productionRequisitionInf of productionRequisitionInfo)
                    {
                        if (productionRequisitionInf.approved_qty == null)
                        {
                            approveQty = 0;
                        }
                        else
                        {
                            approveQty = productionRequisitionInf.approved_qty;
                        }

                        

                        let suggestedQty = 0;

                        if(+productionRequisitionInf.suggested_qty >= 0){
                            suggestedQty = +productionRequisitionInf.suggested_qty;
                        }

                        totalSuggQty += +suggestedQty;

                        $(".detailRequisitionProduct tbody").append(
                            '<tr class="requisitionProductRow" id="requisitionProductRow_'+productionRequisitionInf.id+'">' +
                                '<td>'+
                                    '<input class="form-control requisitionProductName_'+productionRequisitionInf.id+'" type="text" value="'+productionRequisitionInf.product.name+'" readonly>'+
                                    '<input class="form-control requisitionProductName_'+productionRequisitionInf.id+'" type="hidden" name="productionRequisitionProductId[]" value="'+productionRequisitionInf.id+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input class="form-control requisitionProductModelNo_'+productionRequisitionInf.id+'" type="text" value="'+productionRequisitionInf.product.model_no+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control requisitionProductQty_'+productionRequisitionInf.id+'" type="text" value="'+productionRequisitionInf.qty+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control SuggestionQty SuggestionQty_'+productionRequisitionInf.id+'" oninput="rowSum()" type="number" name="SuggestionQty[]" value="'+suggestedQty+'">'+
                                '</td>'+
                               
                                
                            '</tr>'
                        );
                    }

                    $('#totalSuggestionQty').val(totalSuggQty);
                },
                error: function(response) {

                }
            });
        }

        function findTotalApproveAmount(i)
        {
          
            var approveQty = parseFloat($('.approveQty_'+i).val());
         

            rowSum();
        }

        function rowSum()
        {
            var totalApproveQty = 0;            
            // var totalSuggestionAmount = 0;            
            $(".SuggestionQty").each(function () {
                var approveQty = parseFloat($(this).val());
                totalApproveQty += isNaN(approveQty) ? 0 : approveQty;
            });

            // $(".SuggestionAmount").each(function () {
            //     var suggestionAmount = parseFloat($(this).val());
            //     totalSuggestionAmount += isNaN(suggestionAmount) ? 0 : suggestionAmount;
            // });

            $('#totalSuggestionQty').val(totalApproveQty);
            // $('#SuggestionAmount').val(Math.round(totalSuggestionAmount));
        }

        // function getsuggestionAmount(e){

        //     let suggestionQty = $(e.target).val();

        //     let tr = $(e.target).parent().parent();

        //     let trId = tr.attr('id');
        //     trId = trId.split('_')[1];

        //     let productPrice = $('.requisitionProductPrice_'+ trId).val();

        //     let suggestionAmountEl = $('.SuggestionAmount_' + trId);

        //     let suggestionAmount = +productPrice * +suggestionQty;

        //     suggestionAmountEl.val(suggestionAmount);

            
        //     var totalSuggestionAmount = 0;

        //     $(".SuggestionAmount").each(function () {
        //     var suggestionAmount = parseFloat($(this).val());
        //     totalSuggestionAmount += isNaN(suggestionAmount) ? 0 : suggestionAmount;
        //     });

        //     $('#totalSuggestionAmount').val(Math.round(totalSuggestionAmount));

        // }
</script>
@endsection