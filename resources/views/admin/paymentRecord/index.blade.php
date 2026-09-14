@extends('admin.layouts.master')

@section('content')
<form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="print" value="print">
                </div>
            </div>

            <div class="row">

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="from-date">From Date</label>
                            <input  type="text" class="form-control datepicker" id="{{ $print == "print" ? "" : "from_date" }}" name="fromDate" placeholder="Select Date From" value="{{ date('d-m-Y',strtotime($fromDate)) }}" readonly>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="to-date">To Date</label>
                            <input  type="text" class="form-control datepicker" id="{{ $print == "print" ? "" : "to_date" }}" name="toDate" placeholder="Select Date To" value="{{ date('d-m-Y',strtotime($toDate)) }}" readonly>
                        </div>
                    </div>                                  
                </div>
                <div class="col-md-6">
                    <label for="vendor">Vendor</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="vendor" name="vendor[]" multiple>
                            @foreach ($vendors as $vendorInfo)
                            <?php
                            $select = "";
                            if ($vendor) {
                                if (in_array($vendorInfo->id, $vendor)) {
                                    $select = "selected";
                                } else {
                                    $select = "";
                                }
                            }
                            ?>
                            <option value="{{ $vendorInfo->id }}" {{ $select }}>{{ $vendorInfo->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnSummary" value="Summary"><i class="fa fa-search"></i> Payment Summary</button>
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnRecord" value="Record"><i class="fa fa-search"></i> Payment History</button>
                    </div>
                </div>              
            </div>
        </div>
    </div>
</form>

@if ($btnSummary != "" || $btnRecord != "")
<div class="card" style="margin-bottom: 0px;">              
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">Searched Report</h4></div>
            <div class="col-md-6 text-right">
                <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    @if ($vendor)
                    @foreach ($vendor as $vendorInfo)
                    <input type="hidden" name="vendor[]" value="{{ $vendorInfo }}">
                    @endforeach
                    @endif

                    <input type="hidden" name="fromDate" value="{{ $fromDate }}">
                    <input type="hidden" name="toDate" value="{{ $toDate }}">
                    <input type="hidden" id="print_value" name="print" value="{{ $print }}">

                    @if ($btnSummary == "Summary")
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnPrintSummary" value="Print Summary"><i class="fa fa-print"></i> Print Payment Summary</button>
                    @endif

                    @if ($btnRecord == "Record")
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect" name="btnPrintRecord" value="Print Record"><i class="fa fa-print"></i> Print Payment History</button>
                    @endif
                </form>
            </div>
        </div>
    </div>


    <div class="card-body">
        <div class="table-responsive">
            @if ($btnSummary == "Summary")
            <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">SL#</th>
                        <th>Name</th>
                        <th width="80px">Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentSummaries as $paymentSummary)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $paymentSummary->vendorName }}</td>
                        <td align="right">{{ number_format($paymentSummary->price, 2, '.', '') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @endif



            @if ($btnRecord == "Record")
            <table id="dataTable" name="paymentRecordTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">SL#</th>
                        <th width="100px">Date</th>
                        <th width="100px">Payment No</th>
                        <th>Name</th>
                        <th>Pay Mode</th>
                        <th>Remarks</th>
                        <th width="80px">Payment</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $sl = 0;
                    @endphp
                    @foreach ($productRecords as $productRecord)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ date('d-m-Y',strtotime($productRecord->paymentDate)) }}</td>
                        <td>{{ $productRecord->paymentNo }}</td>
                        <td>{{ $productRecord->vendorName }}</td>
                        <td>{{ $productRecord->paymentType }}</td>
                        <td>{{ $productRecord->remarks }}</td>
                        <td align="right">{{ $productRecord->price }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @endif 
        </div>
    </div>
</div>
@endif
@endsection