@extends('admin.layouts.masterReport')

@section('search_card_body')
    <div class="row">
        <div class="col-md-12">
            <input type="hidden" name="print" value="print">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            <label for="from-date">From Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'from_date' }}" name="fromDate" value="{{ date('d-m-Y',strtotime($fromDate)) }}" placeholder="Select Date From">
        </div>
        <div class="col-md-6 form-group">
            <label for="to-date">To Date</label>
            <input  type="text" class="form-control datepicker" id="{{ $print == 'print' ? '' : 'to_date' }}" name="toDate" value="{{ date('d-m-Y',strtotime($toDate)) }}" placeholder="Select Date To">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="search-type">Search Type</label>
            <div class="form-group">
                @php
                    $allSerachType = array('vendor_name' => 'Vendor Name','challan_no' => 'Challan No','invoice_no' => 'Invoice No','product_name' => 'Product Name','model_no' => 'Model No');
                @endphp
                <select class="form-control chosen-select" id="searchType" name="searchType">
                    <option value="">Select Search Type</option>
                    @foreach ($allSerachType as $key => $value)
                        @php
                            $select = "";
                            if ($searchType)
                            {
                                if ($key == $searchType)
                                {
                                    $select = "selected";
                                }
                                else
                                {
                                    $select = "";
                                }
                            }
                        @endphp
                        <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>  
        </div>

        <div class="col-md-6">
            <label for="search-text">Search Text</label>
            <div class="form-group {{ $errors->has('searchText') ? ' has-danger' : '' }}">
                <input type="text" class="form-control" name="searchText" value="{{ $searchText }}">
                @if ($errors->has('searchText'))
                    @foreach($errors->get('searchText') as $error)
                        <div class="form-control-feedback">{{ $error }}</div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@section('print_card_header')
    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
    <input type="hidden" name="toDate" value="{{ $toDate }}">
    <input type="hidden" name="searchType" value="{{ $searchType }}">
    <input type="hidden" name="searchText" value="{{ $searchText }}">
    <input type="hidden" id="print_value" name="print" value="{{ $print }}">
@endsection

@section('print_card_body')
	<table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
		<thead>
			<tr>
                <th width="20px">Sl</th>
                <th width="80px">Date</th>
                <th>Vendor Name</th>
                <th width="100px">Challan No</th>
                <th width="90px">Invoice No</th>
                <th width="110px">Total Amount</th>
                <th width="80px">Action</th>
			</tr>
		</thead>

		<tbody>
            @php
                $sl = 1;
            @endphp

            @foreach ($invoiceReports as $invoiceReport)
                <tr>
                    <td>{{ $sl++ }}</td>
                    <td>{{ date('d-m-Y',strtotime($invoiceReport->date))}}</td>
                    <td>{{ $invoiceReport->vendor_name }}</td>
                    <td>{{ $invoiceReport->challan_no }}</td>
                    <td>{{ $invoiceReport->invoice_no }}</td>
                    <td align="right">{{ $invoiceReport->totalAmount }}</td>
                    <td>
                        <button class="btn btn-outline-success btn-sm" onclick="showModal({{ $invoiceReport->challan_no }});">Details</button>
                    </td>
                </tr>
            @endforeach
		</tbody>
	</table>

    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <table name="totalInvoiceDetailsTable" id="totalInvoiceDetailsTable" class="table table-bordered table-sm">
                        <tbody>
                        </tbody>
                    </table>
                    {{-- <h4 class="modal-title" style="font-weight: bold;">Challan No : </h4> --}}
                    {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <table name="invoiceDetailsTable" id="invoiceDetailsTable" class="table table-bordered table-sm">
                        <thead style="background-color: green; color: white;">
                            <tr>
                                <th width="20px">Sl</th>
                                <th>Product Name</th>
                                <th>Model No</th>
                                <th width="60px">QTY</th>
                                <th width="60px">MRP</th>
                                <th width="100px">Cost Price</th>
                                <th width="100px">Discount</th>
                                <th width="120px">Special Discount</th>
                                <th width="110px">Invoice Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>

                        <tfoot>
                        </tfoot>
                    </table>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        function showModal(challanNo)
        {
            $("#invoiceDetailsTable tbody").empty();
            $("#totalInvoiceDetailsTable tbody").empty();
            $("#invoiceDetailsTable tfoot").empty();
            $('#myModal').modal('show');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                url:'{{ route('invoiceReport.details') }}',
                data:{challanNo:challanNo},
                success: function(data) {
                    var invoiceDetails = data.invoiceDetails;
                    var totalInvoiceDetails = data.totalInvoiceDetails;
                    var sl = 0;
                    var totalAmount = 0;

                    $("#totalInvoiceDetailsTable tbody").append(
                        '<tr>' +
                            '<td width="130px" style="font-weight: bold;">Vendor Name</td>'+
                            '<td colspan="3" width="130px">'+totalInvoiceDetails.vendor_name+'</td>'+
                        '</tr>'+

                        '<tr>' +
                            '<td width="130px" style="font-weight: bold;">Challan No</td>'+
                            '<td>'+totalInvoiceDetails.challan_no+'</td>'+
                            '<td width="130px" style="font-weight: bold;">Invoice No</td>'+
                            '<td>'+totalInvoiceDetails.invoice_no+'</td>'+
                        '</tr>' +
                        
                        '<tr>' +
                            '<td width="130px" style="font-weight: bold;">Total Amount</td>'+
                            '<td colspan="3">'+totalInvoiceDetails.totalAmount+'</td>'+
                        '</tr>'
                    );

                    for (invoiceInfo of invoiceDetails)
                    {
                        sl++;
                        totalAmount = totalAmount + parseInt(invoiceInfo.invoice_amount);
                        $("#invoiceDetailsTable tbody").append(
                            '<tr>' +
                                '<td>'+sl+'</td>'+
                                '<td>'+invoiceInfo.product_name+'</td>'+
                                '<td>'+invoiceInfo.model_no+'</td>'+
                                '<td>'+invoiceInfo.qty+'</td>'+
                                '<td>'+invoiceInfo.mrp+'</td>'+
                                '<td>'+invoiceInfo.cost_price+'</td>'+
                                '<td>'+invoiceInfo.discount_amount+'</td>'+
                                '<td>'+invoiceInfo.special_discount_amount+'</td>'+
                                '<td>'+invoiceInfo.invoice_amount+'</td>'+
                            '</tr>'
                        );
                    }

                    $("#invoiceDetailsTable tfoot").append(
                        '<tr>' +
                            '<td colspan="8">Total Amount</td>'+
                            '<td>'+totalAmount+'</td>'+
                        '</tr>'
                    );
                }
            });
        }
    </script>
@endsection