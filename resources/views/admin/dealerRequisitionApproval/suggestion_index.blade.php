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
                                <th width="350px">Dealer Name</th>
                                <th width="50px">Total Qty</th>
                                <th width="70px">Total Amount</th>
                                <th width="20px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sl = 1;
                            @endphp
                            @foreach ($dealerRequisitions as $dealerRequisition)
                            <tr class="row_{{ $dealerRequisition->id }}">
                                <td>{{ $sl++ }}</td>
                                <td>{{ date('d-m-Y', strtotime($dealerRequisition->date)) }}</td>
                                <td>{{ $dealerRequisition->requisition_no }}</td>
                                <td>{{ $dealerRequisition->dealer->name }}</td>
                                <td align="right">{{ $dealerRequisition->total_qty }}</td>
                                <td align="right">{{ $dealerRequisition->total_amount }}</td>
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
                                <th width="350px">Dealer Name</th>
                                <th width="50px">Total Qty</th>
                                <th width="70px">Total Amount</th>
                                <th width="20px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sl = 1;
                            @endphp
                            @foreach ($approveDealerRequisitions as $approveDealerRequisition)
                            <tr class="row_{{ $approveDealerRequisition->id }}">
                                <td>{{ $sl++ }}</td>
                                <td>{{ date('d-m-Y', strtotime($approveDealerRequisition->date)) }}</td>
                                <td>{{ $approveDealerRequisition->requisition_no }}</td>
                                <td>{{ $approveDealerRequisition->dealer->name }}</td>
                                <td align="right">{{ $approveDealerRequisition->total_qty }}</td>
                                <td align="right">{{ $approveDealerRequisition->total_amount }}</td>
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
                                <th width="100px">Suggestion Qty</th>
                                <th width="100px">Suggestion Amount</th>
                            </tr>
                        </thead>

                        <tbody id="tbody">
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="3" align="right" style="vertical-align: middle;">
                                    <h3>Total</h3>
                                    <input class="form-control dealerRequisitionId" type="hidden"
                                        name="dealerRequisitionId" id="dealerRequisitionId" value="">
                                    <input class="form-control" type="hidden" name="approveBy" id="approveBy"
                                        value="{{ Auth::user()->id }}">
                                </td>
                                <td><input style="text-align: right;" class="form-control totalQty" id="totalQty"
                                        type="text" name="totalQty" value=""></td>
                                <td><input style="text-align: right;" class="form-control totalAmount" id="totalAmount"
                                        type="text" name="totalAmount" value=""></td>
                                <td><input style="text-align: right;" class="form-control totalSuggestionQty"
                                        id="totalSuggestionQty" type="number" name="totalSuggestionQty" value="0"
                                        readonly></td>
                                <td><input style="text-align: right;" class="form-control totalSuggestionAmount"
                                        id="totalSuggestionAmount" type="number" name="totalSuggestionAmount" value="0"
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
                $('#title').html('All Approved Requisitions')
                $('#approveButton').hide();
                $('#pendingButton').show();
                $('#approveSection').show();
                $('#pendingSection').hide();
            });

            $('#pendingButton').click(function(){
                $('#title').html('All Pending Requisitions')
                $('#approveButton').show();
                $('#pendingButton').hide();
                $('#approveSection').hide();
                $('#pendingSection').show();
            });
        });

        function showDetailRequisition(dealerRequisitionId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('dealerRequisitionApproval.dealerRequisitionInfo') }}",
                data:{dealerRequisitionId:dealerRequisitionId},
                success: function(response) {
                    $('.requisitionProductRow').remove();
                    var dealerRequisition = response.dealerRequisition;
                    var dealerRequisitionProducts = response.dealerRequisitionProducts;
                    var approveQty;
                    var approveAmount;

                    $('.dealerName').html(dealerRequisition.dealerName);
                    $('.totalQty').val(dealerRequisition.total_qty);
                    $('.totalAmount').val(dealerRequisition.total_amount);
                    $('.dealerRequisitionId').val(dealerRequisition.id);

                    if (dealerRequisition.total_approve_qty != null)
                    {
                        $('.totalApproveQty').val(dealerRequisition.total_approve_qty);
                    }

                    if (dealerRequisition.total_approve_amount != null)
                    {
                        $('.totalApproveAmount').val(dealerRequisition.total_approve_amount);
                    }

                    if(dealerRequisition.suggested > 0){
                        $('.saveBtn').hide();
                    }

                    if (dealerRequisition.status == 0)
                    {
                        $('.btnName').html('Update');
                    }
                    else
                    {
                        $('.btnName').html('Save');
                    }

                    let totalSuggQty = 0;
                    let totalSuggAmt = 0;


                    for (var dealerRequisitionProduct of dealerRequisitionProducts)
                    {
                        if (dealerRequisitionProduct.approved_qty == null)
                        {
                            approveQty = 0;
                        }
                        else
                        {
                            approveQty = dealerRequisitionProduct.approved_qty;
                        }

                        if (dealerRequisitionProduct.approved_amount == null)
                        {
                            approveAmount = 0;
                        }
                        else
                        {
                            approveAmount = dealerRequisitionProduct.approved_amount;
                        }

                        let suggestedQty = 0;
                        let suggestedAmount = 0;

                        if(+dealerRequisitionProduct.suggested_qty > 0){
                            suggestedQty = +dealerRequisitionProduct.suggested_qty;
                            suggestedAmount = +dealerRequisitionProduct.suggested_qty * +dealerRequisitionProduct.price;
                        }

                        totalSuggQty += +suggestedQty;
                        totalSuggAmt += +suggestedAmount;

                        $(".detailRequisitionProduct tbody").append(
                            '<tr class="requisitionProductRow" id="requisitionProductRow_'+dealerRequisitionProduct.id+'">' +
                                '<td>'+
                                    '<input class="form-control requisitionProductName_'+dealerRequisitionProduct.id+'" type="text" value="'+dealerRequisitionProduct.product.name+'" readonly>'+
                                    '<input class="form-control requisitionProductName_'+dealerRequisitionProduct.id+'" type="hidden" name="dealerRequisitionProductId[]" value="'+dealerRequisitionProduct.id+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input class="form-control requisitionProductModelNo_'+dealerRequisitionProduct.id+'" type="text" value="'+dealerRequisitionProduct.product.model_no+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control requisitionProductPrice_'+dealerRequisitionProduct.id+'" type="text" value="'+dealerRequisitionProduct.price+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control requisitionProductQty_'+dealerRequisitionProduct.id+'" type="text" value="'+dealerRequisitionProduct.qty+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control requisitionAmount_'+dealerRequisitionProduct.id+'" type="text" value="'+dealerRequisitionProduct.amount+'" readonly>'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control SuggestionQty SuggestionQty_'+dealerRequisitionProduct.id+'" oninput="rowSum(); getsuggestionAmount(event)" type="number" name="SuggestionQty[]" value="'+suggestedQty+'">'+
                                '</td>'+
                                '<td>'+
                                    '<input style="text-align: right;" class="form-control SuggestionAmount SuggestionAmount_'+dealerRequisitionProduct.id+'" oninput="rowSum()" type="number" name="SuggestionAmount[]" value="'+suggestedAmount+'" readonly>'+
                                '</td>'+
                            '</tr>'
                        );
                    }

                    $('#totalSuggestionQty').val(totalSuggQty);
                    $('#totalSuggestionAmount').val(totalSuggAmt);
                },
                error: function(response) {

                }
            });
        }

        function findTotalApproveAmount(i)
        {
            var rate = parseFloat($('.requisitionProductPrice_'+i).val());
            var approveQty = parseFloat($('.approveQty_'+i).val());
            var approveAmount = rate * approveQty;

            $('.approveAmount_'+i).val(Math.round(approveAmount));

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

        function getsuggestionAmount(e){

            let suggestionQty = $(e.target).val();

            let tr = $(e.target).parent().parent();

            let trId = tr.attr('id');
            trId = trId.split('_')[1];

            let productPrice = $('.requisitionProductPrice_'+ trId).val();

            let suggestionAmountEl = $('.SuggestionAmount_' + trId);

            let suggestionAmount = +productPrice * +suggestionQty;

            suggestionAmountEl.val(suggestionAmount);

            
            var totalSuggestionAmount = 0;

            $(".SuggestionAmount").each(function () {
            var suggestionAmount = parseFloat($(this).val());
            totalSuggestionAmount += isNaN(suggestionAmount) ? 0 : suggestionAmount;
            });

            $('#totalSuggestionAmount').val(Math.round(totalSuggestionAmount));

        }
</script>
@endsection